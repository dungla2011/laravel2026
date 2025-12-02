<?php
/**
 * Test convert Excel to PDF using mPDF
 */

require '/var/www/html/vendor/autoload.php';

// Input file
$excelFile = '/share/1.xlsx';
$outputPdf = '/share/1.pdf';

if (!file_exists($excelFile)) {
    die("File không tồn tại: $excelFile\n");
}

try {
    echo "Step 1: Loading Excel file...\n";
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFile);
    $sheet = $spreadsheet->getActiveSheet();
    echo "✓ Excel loaded: " . $sheet->getTitle() . "\n";

    echo "\nStep 2: Checking mPDF...\n";
    if (!class_exists('\Mpdf\Mpdf')) {
        echo "✗ mPDF not found\n";
        echo "Trying to load from vendor...\n";
        
        // Manual load
        if (file_exists('/var/www/html/vendor/mpdf/mpdf/src/Mpdf.php')) {
            require '/var/www/html/vendor/mpdf/mpdf/src/Mpdf.php';
            echo "✓ mPDF loaded manually\n";
        } else {
            die("✗ mPDF source not found\n");
        }
    } else {
        echo "✓ mPDF found\n";
    }

    echo "\nStep 3: Generating HTML...\n";
    $html = generateExcelHtml($sheet);
    echo "✓ HTML generated (" . strlen($html) . " bytes)\n";

    echo "\nStep 4: Creating PDF...\n";
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'orientation' => 'L',
        'margin_top' => 10,
        'margin_bottom' => 10,
        'margin_left' => 10,
        'margin_right' => 10,
    ]);
    
    $mpdf->WriteHTML($html);
    echo "✓ PDF created in memory\n";

    echo "\nStep 5: Saving PDF...\n";
    @mkdir(dirname($outputPdf), 0755, true);
    $mpdf->Output($outputPdf, \Mpdf\Output\Destination::FILE);
    
    if (file_exists($outputPdf)) {
        $size = filesize($outputPdf);
        echo "✓ PDF saved: $outputPdf ($size bytes)\n";
    } else {
        echo "✗ PDF save failed\n";
    }

} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

function generateExcelHtml($sheet) {
    $html = '<!DOCTYPE html><html><head><meta charset="UTF-8" />';
    $html .= '<style>';
    $html .= 'body { font-family: DejaVu Sans, Arial; font-size: 9pt; }';
    $html .= 'table { width: 100%; border-collapse: collapse; margin-top: 10px; }';
    $html .= 'th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }';
    $html .= 'th { background-color: #f0f0f0; font-weight: bold; }';
    $html .= 'h1, h2 { margin: 5px 0; }';
    $html .= '</style>';
    $html .= '</head><body>';
    
    $html .= '<h2>Excel File: ' . htmlspecialchars($sheet->getTitle()) . '</h2>';
    $html .= '<p>Generated at: ' . date('Y-m-d H:i:s') . '</p>';
    $html .= '<table>';
    
    // Header row
    $html .= '<tr>';
    foreach ($sheet->getRowIterator() as $row) {
        foreach ($row->getCellIterator() as $cell) {
            $html .= '<th>' . htmlspecialchars($cell->getValue() ?? '') . '</th>';
        }
        break;
    }
    $html .= '</tr>';
    
    // Data rows
    $rowNum = 0;
    foreach ($sheet->getRowIterator() as $row) {
        $rowNum++;
        if ($rowNum == 1) continue; // Skip header
        
        $html .= '<tr>';
        foreach ($row->getCellIterator() as $cell) {
            $html .= '<td>' . htmlspecialchars($cell->getValue() ?? '') . '</td>';
        }
        $html .= '</tr>';
        
        if ($rowNum > 100) break; // Limit to 100 rows for testing
    }
    
    $html .= '</table>';
    $html .= '</body></html>';
    
    return $html;
}
