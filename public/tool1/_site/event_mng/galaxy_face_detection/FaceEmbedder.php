<?php
require_once 'conn.php';

class FaceEmbedder {
    private $pythonPath;
    private $scriptPath;

    public function __construct($pythonPath = 'python3', $scriptPath = 'face.py') {
        $this->pythonPath = $pythonPath;
        $this->scriptPath = $scriptPath;
    }

    public function getEmbedding($imagePath) {
        $command = escapeshellcmd("{$this->pythonPath} {$this->scriptPath} " . escapeshellarg($imagePath));
        $output = shell_exec($command);
        if ($output === null) {
            throw new Exception("Python script failed or returned no output.");
        }

        $embedding = array_map('floatval', explode(",", trim($output)));
        return $embedding;
    }


    function saveFaceEmbedding(PDO $pdo, string $name, array $embedding, string $urlConfirm, string $imagePath): bool {
        // Chuyển mảng embedding thành chuỗi JSON để lưu vào trường `face`
        $faceJson = json_encode($embedding);
    
        // Thời gian hiện tại
        $mtime = date('Y-m-d H:i:s');
    
        $sql = "INSERT INTO tb_face_embeds (name, face, url_confirm, mtime, image_path)
                VALUES (:name, :face, :url_confirm, :mtime, :image_path)";
    
        $stmt = $pdo->prepare($sql);
    
        return $stmt->execute([
            ':name' => $name,
            ':face' => $faceJson,
            ':url_confirm' => $urlConfirm,
            ':mtime' => $mtime,
            ':image_path' => $imagePath,
        ]);
    }
    
}



//--------------------------------------------
$embedder = new FaceEmbedder();
$embedding = $embedder->getEmbedding(__DIR__.'/s.png');

print_r($embedding); // Mảng 128 hoặc 192 giá trị float


$pdo = getPDOConnection();

// Giả sử bạn đã có:
$name = "Khách hàng mới";
$urlConfirm = "https://events.dav.edu.vn/user-confirm-event/data/vm279495|ms34020c";
$imagePath = "s.png";

if ($embedder->saveFaceEmbedding($pdo, $name, $embedding, $urlConfirm, $imagePath)) {
    echo "Lưu thành công!";
} else {
    echo "Lỗi khi lưu vào CSDL.";
}


