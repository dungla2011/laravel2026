<?php


use App\Models\FileUpload;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'tailieuchuan.net';


require_once "/var/www/html/public/index.php";

if(!isCli()){
    die(" NOT CLI!");
}


/**
 * Nhận dạng nội dung từ file PDF nhiều trang sử dụng Tesseract OCR
 */
class PDFOcr
{
    private $imagick;
    private $tesseract;
    private $outputPath;

    public function __construct($outputPath = 'output.txt')
    {
        // Kiểm tra các extension cần thiết
        if (!extension_loaded('imagick')) {
            throw new Exception('Imagick extension is required');
        }

        $this->outputPath = $outputPath;
        $this->imagick = new Imagick();
    }

    /**
     * Xử lý file PDF và thực hiện OCR
     */
    public function processPDF($pdfPath)
    {
        try {
            // Đọc file PDF
            $this->imagick->readImage($pdfPath);

            // Tạo file output
            $outputFile = fopen($this->outputPath, 'w');

            // Xử lý từng trang
            foreach ($this->imagick as $pageNum => $page) {
                // Chuyển trang PDF thành ảnh
                $page->setImageFormat('png');
                $page->setResolution(300, 300);

                // Lưu ảnh tạm
                $tempImage = "temp_page_{$pageNum}.png";
                $page->writeImage($tempImage);

                // Thực hiện OCR
                $text = $this->performOCR($tempImage);

                // Ghi kết quả vào file
                fwrite($outputFile, "=== Trang " . ($pageNum + 1) . " ===\n");
                fwrite($outputFile, $text . "\n\n");

                // Xóa file tạm
                unlink($tempImage);
            }

            fclose($outputFile);
            $this->imagick->clear();

            return true;

        } catch (Exception $e) {
            throw new Exception("Lỗi xử lý PDF: " . $e->getMessage());
        }
    }

    /**
     * Thực hiện OCR trên một ảnh
     */
    private function performOCR($imagePath)
    {
        // Sử dụng command line để gọi tesseract
        $output = array();
        $command = "tesseract " . escapeshellarg($imagePath) . " stdout -l vie+eng";
        exec($command, $output);

        return implode("\n", $output);
    }
}

// Sử dụng
try {
    $pdfOcr = new PDFOcr('/share/112.txt');
    $pdfOcr->processPDF('/share/11.pdf');
    echo "Đã xử lý xong. Kết quả được lưu trong file ketqua.txt";
} catch (Exception $e) {
    echo "\n\nLỗi: " . $e->getMessage();
}
