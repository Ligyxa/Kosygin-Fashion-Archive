<?php

$DB_HOST = 'localhost';
$DB_NAME = 'fashion_archive';
$DB_USER = 'root';
$DB_PASS = ''; // В Wampserver по умолчанию пароль пустой

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // На время разработки показываем ошибку. В продакшене лучше писать в лог.
    die('Ошибка подключения к БД: ' . $e->getMessage());
}

// Базовый URL проекта (без слэша в конце)
define('BASE_URL', '/fashion-archive');

// Папки для загрузки файлов (абсолютные пути на диске)
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('MODELS_DIR', __DIR__ . '/assets/models');