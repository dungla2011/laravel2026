<?php
/**
 * PDF Digital Signature Extractor Class
 * 
 * Simple class to extract digital signatures from PDF files
 * Hỗ trợ tiếng Việt - Vietnamese Unicode Support
 * 
 * Usage:
 *   require_once 'PDFSignatureExtractor.php';
 *   $extractor = new PDFSignatureExtractor('file.pdf');
 *   $signatures = $extractor->extract();
 *   foreach ($signatures as $sig) {
 *       echo $sig['Name'] . ': ' . $sig['Reason'] . "\n";
 *   }
 */

class PDFSignatureExtractor {
    private $pdfPath;
    private $pdfContent;
    private $signatures = [];
    
    /**
     * Constructor
     * 
     * @param string $pdfPath Path to PDF file
     * @throws Exception If file not found
     */
    public function __construct($pdfPath) {
        if (!file_exists($pdfPath)) {
            throw new Exception("PDF file not found: $pdfPath");
        }
        
        $this->pdfPath = $pdfPath;
        $this->pdfContent = file_get_contents($pdfPath);
        
        if ($this->pdfContent === false) {
            throw new Exception("Cannot read PDF file: $pdfPath");
        }
    }
    
    /**
     * Extract all digital signatures from PDF
     * 
     * @return array Array of signatures, each containing:
     *               - Name: Người ký (signer name)
     *               - Date: Ngày ký (signature date - DD/MM/YYYY HH:MM:SS)
     *               - Reason: Lý do ký (reason for signature)
     *               - Location: Địa điểm (location)
     *               - ContactInfo: Thông tin liên hệ (contact information)
     *               - SignatureType: Loại chữ ký (signature type)
     */
    public function extract() {
        $this->signatures = [];
        $sigIndex = 0;
        
        // Find all /Type/Sig signature objects
        $pattern = '/<<[\s\S]*?\/Type\s*\/Sig[\s\S]*?>>/';
        if (preg_match_all($pattern, $this->pdfContent, $matches)) {
            foreach ($matches[0] as $sigObj) {
                $sigIndex++;
                $info = $this->parsePDFSignature($sigObj);
                if (!empty($info['Date'])) { // Only include if has valid date
                    $this->signatures[$sigIndex] = $info;
                }
            }
        }
        
        return $this->signatures;
    }
    
    /**
     * Get extracted signatures
     * 
     * @return array Signatures
     */
    public function getSignatures() {
        return $this->signatures;
    }
    
    /**
     * Get signature count
     * 
     * @return int Number of signatures
     */
    public function count() {
        return count($this->signatures);
    }
    
    /**
     * Get signature by index
     * 
     * @param int $index Signature index (1-based)
     * @return array|null Signature or null if not found
     */
    public function getSignature($index) {
        return $this->signatures[$index] ?? null;
    }
    
    /**
     * Get PDF file path
     * 
     * @return string PDF file path
     */
    public function getPDFPath() {
        return $this->pdfPath;
    }
    
    /**
     * Get PDF file size in bytes
     * 
     * @return int File size
     */
    public function getPDFSize() {
        return filesize($this->pdfPath);
    }
    
    /**
     * Export signatures to JSON string
     * 
     * @param bool $pretty Pretty-print JSON
     * @return string JSON string
     */
    public function toJSON($pretty = true) {
        $flags = JSON_UNESCAPED_UNICODE;
        if ($pretty) {
            $flags |= JSON_PRETTY_PRINT;
        }
        return json_encode($this->signatures, $flags);
    }
    
    /**
     * Export signatures to CSV string
     * 
     * @return string CSV string
     */
    public function toCSV() {
        $output = "Signature #,Name,Date,Reason,Location,Contact Info,Type\n";
        
        foreach ($this->signatures as $idx => $sig) {
            $name = $sig['Name'] ?? 'N/A';
            $date = ($sig['Date'] ?? '');
            $reason = $sig['Reason'] ?? '';
            $location = $sig['Location'] ?? '';
            $contact = $sig['ContactInfo'] ?? '';
            $type = $sig['SignatureType'] ?? '';
            
            // Escape CSV values
            $values = [$idx, $name, $date, $reason, $location, $contact, $type];
            $escaped = array_map(function($v) {
                return '"' . str_replace('"', '""', $v) . '"';
            }, $values);
            
            $output .= implode(',', $escaped) . "\n";
        }
        
        return $output;
    }
    
    /**
     * Export signatures to XML string
     * 
     * @return string XML string
     */
    public function toXML() {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<signatures>\n";
        $xml .= "  <metadata>\n";
        $xml .= "    <file>" . htmlspecialchars($this->pdfPath) . "</file>\n";
        $xml .= "    <size>" . $this->getPDFSize() . "</size>\n";
        $xml .= "    <extraction_date>" . date('Y-m-d H:i:s') . "</extraction_date>\n";
        $xml .= "  </metadata>\n";
        $xml .= "  <signature_list count=\"" . count($this->signatures) . "\">\n";
        
        foreach ($this->signatures as $idx => $sig) {
            $xml .= "    <signature number=\"$idx\">\n";
            $xml .= "      <name>" . htmlspecialchars($sig['Name'] ?? 'N/A') . "</name>\n";
            $xml .= "      <date>" . htmlspecialchars($sig['Date'] ?? '') . "</date>\n";
            $xml .= "      <reason>" . htmlspecialchars($sig['Reason'] ?? '') . "</reason>\n";
            $xml .= "      <location>" . htmlspecialchars($sig['Location'] ?? '') . "</location>\n";
            $xml .= "      <contact_info>" . htmlspecialchars($sig['ContactInfo'] ?? '') . "</contact_info>\n";
            $xml .= "      <type>" . htmlspecialchars($sig['SignatureType'] ?? '') . "</type>\n";
            $xml .= "    </signature>\n";
        }
        
        $xml .= "  </signature_list>\n";
        $xml .= "</signatures>\n";
        
        return $xml;
    }
    
    /**
     * Parse a single signature object from PDF
     * @internal
     */
    private function parsePDFSignature($sigObj) {
        $info = [
            'Name' => null,
            'Date' => null,
            'Reason' => null,
            'Location' => null,
            'ContactInfo' => null,
            'SignatureType' => null,
        ];
        
        // Extract Name
        if (preg_match('/\/Name\s*\(([^\)]+)\)/', $sigObj, $m)) {
            $info['Name'] = $this->decodeUTF16BE($m[1]);
        }
        
        // Extract M (date)
        if (preg_match('/\/M\s*\(([^\)]+)\)/', $sigObj, $m)) {
            $info['Date'] = $this->formatPDFDate($m[1]);
        }
        
        // Extract Reason
        if (preg_match('/\/Reason\s*\(([^\)]*?)\)/s', $sigObj, $m)) {
            $reason = $m[1];
            $info['Reason'] = $this->decodeUTF16BE($reason);
        }
        
        // Extract Location
        if (preg_match('/\/Location\s*\(([^\)]*?)\)/s', $sigObj, $m)) {
            $location = $m[1];
            $info['Location'] = $this->decodeUTF16BE($location);
        }
        
        // Extract SubFilter (signature type)
        if (preg_match('/\/SubFilter\s*\/(\w+)/', $sigObj, $m)) {
            $info['SignatureType'] = $m[1];
        }
        
        // Extract ContactInfo
        if (preg_match('/\/ContactInfo\s*\(([^\)]*?)\)/s', $sigObj, $m)) {
            $contact = $m[1];
            $info['ContactInfo'] = $this->decodeUTF16BE($contact);
        }
        
        return $info;
    }
    
    /**
     * Decode UTF-16BE string from PDF
     * Handles:
     * - Plain ASCII text
     * - Raw UTF-16BE bytes (with BOM feff)
     * - Escape sequences (\nnn notation)
     * @internal
     */
    private function decodeUTF16BE($hexStr) {
        // Handle hex string format (<...>)
        if (preg_match('/^<([0-9A-Fa-f]+)>$/', $hexStr, $m)) {
            $bytes = [];
            for ($i = 0; $i < strlen($m[1]); $i += 2) {
                $bytes[] = hexdec(substr($m[1], $i, 2));
            }
            return $this->decodeUTF16BEBytes($bytes);
        }
        
        // Check if this is plain ASCII (no high bytes except BOM)
        $hasHighByte = false;
        for ($i = 0; $i < strlen($hexStr); $i++) {
            $byte = ord($hexStr[$i]);
            if ($byte > 0x7F && $byte !== 0xFE && $byte !== 0xFF) {
                $hasHighByte = true;
                break;
            }
        }
        
        // If no high byte and no BOM, it's plain ASCII
        if (!$hasHighByte && !(strlen($hexStr) >= 2 && ord($hexStr[0]) === 0xFE && ord($hexStr[1]) === 0xFF)) {
            return trim($hexStr);
        }
        
        // Extract bytes from data
        $bytes = [];
        $i = 0;
        $len = strlen($hexStr);
        
        while ($i < $len) {
            if ($hexStr[$i] === '\\' && $i + 3 < $len && ctype_digit(substr($hexStr, $i + 1, 3))) {
                // Octal escape: \nnn
                $octal = substr($hexStr, $i + 1, 3);
                $bytes[] = octdec($octal);
                $i += 4;
            } else {
                // Raw byte
                $bytes[] = ord($hexStr[$i]);
                $i++;
            }
        }
        
        return $this->decodeUTF16BEBytes($bytes);
    }
    
    /**
     * Decode array of bytes from UTF-16BE to UTF-8
     * @internal
     */
    private function decodeUTF16BEBytes($bytes) {
        // Remove BOM if present
        if (count($bytes) >= 2 && $bytes[0] === 0xFE && $bytes[1] === 0xFF) {
            array_shift($bytes);
            array_shift($bytes);
        }
        
        $result = '';
        $i = 0;
        
        while ($i < count($bytes)) {
            $high = $bytes[$i] ?? 0;
            $low = $bytes[$i + 1] ?? 0;
            
            // UTF-16BE character: high byte, low byte
            $codepoint = ($high << 8) | $low;
            
            if ($codepoint === 0x0000) {
                // Null terminator
                break;
            } else if ($codepoint < 0x0080) {
                // ASCII (high byte = 0)
                $result .= chr($low);
            } else if ($codepoint < 0x0800) {
                // 2-byte UTF-8
                $result .= chr(0xC0 | ($codepoint >> 6));
                $result .= chr(0x80 | ($codepoint & 0x3F));
            } else if ($codepoint < 0x10000) {
                // 3-byte UTF-8
                $result .= chr(0xE0 | ($codepoint >> 12));
                $result .= chr(0x80 | (($codepoint >> 6) & 0x3F));
                $result .= chr(0x80 | ($codepoint & 0x3F));
            }
            
            $i += 2;
        }
        
        return trim($result);
    }
    
    /**
     * Format PDF date to readable format
     * PDF format: D:20250731084328+07'00'
     * Output format: 31/07/2025 08:43:28
     * @internal
     */
    private function formatPDFDate($dateStr) {
        $dateStr = str_replace('D:', '', $dateStr);
        
        if (preg_match('/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})([\+\-]\d{2})\'(\d{2})\'/', $dateStr, $m)) {
            $year = $m[1];
            $month = $m[2];
            $day = $m[3];
            $hour = $m[4];
            $min = $m[5];
            $sec = $m[6];
            
            return "$day/$month/$year $hour:$min:$sec";
        }
        
        return $dateStr;
    }
}

?>
