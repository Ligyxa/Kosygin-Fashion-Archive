<?php
// delete.php — удаление наряда
require __DIR__ . '/config.php';

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Получаем данные перед удалением, чтобы знать, какие файлы удалять
$stmt = $pdo->prepare("SELECT * FROM garments WHERE id = ?");
$stmt->execute([$id]);
$g = $stmt->fetch();

if ($g) {
    // 1. Удаляем запись из БД
    $del = $pdo->prepare("DELETE FROM garments WHERE id = ?");
    $del->execute([$id]);

    // 2. Удаляем файлы с диска
    // Удаляем обложку
    if (!empty($g['cover_path'])) {
        $file = __DIR__ . str_replace(BASE_URL, '', $g['cover_path']);
        if (file_exists($file)) unlink($file);
    }

    // Удаляем галерею
    if (!empty($g['image_path'])) {
        $images = explode(',', $g['image_path']);
        foreach ($images as $img) {
            $file = __DIR__ . str_replace(BASE_URL, '', trim($img));
            if (file_exists($file)) unlink($file);
        }
    }

    // Удаляем 3D файлы
    if (!empty($g['model_path'])) {
        $modelFile = __DIR__ . str_replace(BASE_URL, '', $g['model_path']);
        if (file_exists($modelFile)) unlink($modelFile);
        
        // Если это был OBJ, удаляем также .mtl и .png
        if (pathinfo($modelFile, PATHINFO_EXTENSION) === 'obj') {
            $mtlFile = str_replace('.obj', '.mtl', $modelFile);
            $pngFile = str_replace('.obj', '.png', $modelFile);
            if (file_exists($mtlFile)) unlink($mtlFile);
            if (file_exists($pngFile)) unlink($pngFile);
        }
    }
}

// Возвращаемся в каталог
header('Location: index.php');
exit;