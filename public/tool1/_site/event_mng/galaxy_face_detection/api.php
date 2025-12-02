<?php
require_once 'conn.php';
header("Content-Type: application/json; charset=UTF-8");
 
try {
    $pdo = getPDOConnection();

    $stmt = $pdo->query("SELECT id, name, face, url_confirm, mtime, image_path FROM tb_face_embeds");
    $faces = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $faces
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
