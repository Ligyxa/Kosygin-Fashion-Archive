<?php
// register.php — страница регистрации
require __DIR__ . '/config.php';
session_start();

// Если уже вошел — перекидываем в каталог
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Получаем роль из формы, но проверяем её безопасность
    $role = $_POST['role'] ?? 'student';
    if ($role !== 'teacher' && $role !== 'student') {
        $role = 'student'; // Если прислали что-то странное, ставим студента
    }

    // Валидация
    if (strlen($username) < 3) {
        $errors[] = 'Логин должен быть не менее 3 символов.';
    }
    if (strlen($password) < 5) {
        $errors[] = 'Пароль должен быть не менее 5 символов.';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'Пароли не совпадают.';
    }

    // Проверка, не занят ли логин
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'Этот логин уже занят. Придумайте другой.';
        }
    }

    // Если ошибок нет — регистрируем
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hash, $role]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация — Kosygin Fashion Archive</title>
    <link rel="stylesheet" href="style.css?v=15">
    <style>
        .login-box { max-width: 400px; margin: 80px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .login-box label { display: block; margin-top: 16px; font-weight: 600; color: #003366; }
        .login-box input, .login-box select { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #dce4ec; border-radius: 6px; font-family: inherit; font-size: 14px; }
        .login-box button { margin-top: 24px; width: 100%; padding: 12px; font-size: 16px; }
        .msg-error { background: #ffebee; border: 1px solid #f44336; padding: 16px; border-radius: 6px; color: #c62828; margin-bottom: 20px; }
        .msg-success { background: #e8f5e9; border: 1px solid #4caf50; padding: 16px; border-radius: 6px; color: #2e7d32; margin-bottom: 20px; }
    </style>
</head>
<body>
<header><h1>Kosygin Fashion Archive</h1><nav><a href="index.php">← В каталог</a></nav></header>
<main>
    <div class="login-box">
        <h2 style="color: #003366; margin-top: 0;">Регистрация</h2>
        
        <?php if ($success): ?>
            <div class="msg-success">
                <b>Успешно!</b> Вы зарегистрированы как <?= $role === 'teacher' ? 'Преподаватель' : 'Студент' ?>.<br>
                Теперь вы можете войти в систему.
            </div>
            <div style="text-align: center;">
                <a href="login.php" class="btn">Перейти ко входу</a>
            </div>
        <?php else: ?>
            <?php if ($errors): ?>
                <div class="msg-error">
                    <ul style="margin:0; padding-left: 20px;">
                        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post">
                <label>Придумайте логин</label>
                <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">

                <label>Придумайте пароль</label>
                <input type="password" name="password" required>

                <label>Повторите пароль</label>
                <input type="password" name="password_confirm" required>

                <label>Кто вы?</label>
                <select name="role">
                    <option value="student" <?= (($_POST['role'] ?? '') === 'student') ? 'selected' : '' ?>>Студент (может только добавлять)</option>
                    <option value="teacher" <?= (($_POST['role'] ?? '') === 'teacher') ? 'selected' : '' ?>>Преподаватель (полный доступ)</option>
                </select>

                <button type="submit" class="btn">Зарегистрироваться</button>
            </form>
            
            <p style="font-size: 14px; text-align: center; margin-top: 20px; color: #666;">
                Уже есть аккаунт? <a href="login.php" style="color: #0055aa;">Войти</a>
            </p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>