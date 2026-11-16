<?php
// login.php — страница входа
require __DIR__ . '/config.php';
session_start();

// Если уже вошел — перекидываем в каталог
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход — Kosygin Fashion Archive</title>
    <link rel="stylesheet" href="style.css?v=16">
    <style>
        .login-box { max-width: 400px; margin: 100px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .login-box label { display: block; margin-top: 16px; font-weight: 600; color: #003366; }
        .login-box input { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #dce4ec; border-radius: 6px; }
        .login-box button { margin-top: 24px; width: 100%; padding: 12px; font-size: 16px; }
        .msg-error { background: #ffebee; border: 1px solid #f44336; padding: 16px; border-radius: 6px; color: #c62828; margin-bottom: 20px; }
    </style>
</head>
<body>
<header><h1>Kosygin Fashion Archive</h1><nav><a href="index.php">← В каталог</a></nav></header>
<main>
    <div class="login-box">
        <h2 style="color: #003366; margin-top: 0;">Вход в систему</h2>
        
        <?php if ($error): ?>
            <div class="msg-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Логин</label>
            <input type="text" name="username" required>

            <label>Пароль</label>
            <input type="password" name="password" required>

            <button type="submit" class="btn">Войти</button>
        </form>

        <!-- Ссылка на регистрацию -->
        <p style="font-size: 14px; text-align: center; margin-top: 24px; color: #666;">
            Нет аккаунта? <a href="register.php" style="color: #0055aa; font-weight: bold; text-decoration: none;">Зарегистрироваться</a>
        </p>
    </div>
</main>
</body>
</html>