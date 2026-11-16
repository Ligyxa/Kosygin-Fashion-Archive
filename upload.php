<?php
// upload.php — добавление нового наряда
require __DIR__ . '/config.php';

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $author      = trim($_POST['author'] ?? '');
    $department  = trim($_POST['department'] ?? '');
    $material    = trim($_POST['material'] ?? '');
    $color       = trim($_POST['color'] ?? '');
    $cut         = trim($_POST['cut'] ?? '');
    $season      = trim($_POST['season'] ?? '');
    $collection  = trim($_POST['collection'] ?? '');

    if ($name === '') {
        $errors[] = 'Введите название наряда.';
    }

    $imagePaths = [];
    $modelPath = null;
    $coverPath = null;

    // 1. Загрузка обложки
    if (!empty($_FILES['cover']['name'])) {
        $cover = $_FILES['cover'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if ($cover['error'] === UPLOAD_ERR_OK && in_array($cover['type'], $allowed)) {
            $ext = pathinfo($cover['name'], PATHINFO_EXTENSION);
            $newName = uniqid('cover_', true) . '.' . strtolower($ext);
            $dest = __DIR__ . '/uploads/' . $newName;
            if (move_uploaded_file($cover['tmp_name'], $dest)) {
                $coverPath = BASE_URL . '/uploads/' . $newName;
            }
        }
    }

    // 2. Загрузка галереи фото
    if (!empty($_FILES['images']['name'][0])) {
        $total = count($_FILES['images']['name']);
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($ext, $allowed) && $_FILES['images']['size'][$i] <= 15 * 1024 * 1024) {
                    $newName = uniqid('img_', true) . '.' . $ext;
                    $dest = __DIR__ . '/uploads/' . $newName;
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $dest)) {
                        $imagePaths[] = BASE_URL . '/uploads/' . $newName;
                    }
                }
            }
        }
    }
    $imagePathString = implode(',', $imagePaths);

    // 3. Загрузка 3D (OBJ / GLB)
    $modelFormat = $_POST['model_format'] ?? '';

    if ($modelFormat === 'obj' && !empty($_FILES['model_obj']['name'])) {
        $obj = $_FILES['model_obj'];
        $mtl = $_FILES['model_mtl'];
        $png = $_FILES['model_png'];

        if (strtolower(pathinfo($obj['name'], PATHINFO_EXTENSION)) !== 'obj') {
            $errors[] = 'Модель: допускается только формат .obj.';
        } elseif ($mtl['error'] !== UPLOAD_ERR_OK || $png['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Для OBJ прикрепите все три файла: .obj, .mtl, .png.';
        } else {
            $baseName = uniqid('model_', true);
            $objDest = __DIR__ . '/assets/models/' . $baseName . '.obj';
            $mtlDest = __DIR__ . '/assets/models/' . $baseName . '.mtl';
            $pngDest = __DIR__ . '/assets/models/' . $baseName . '.png';

            if (move_uploaded_file($obj['tmp_name'], $objDest) && 
                move_uploaded_file($mtl['tmp_name'], $mtlDest) && 
                move_uploaded_file($png['tmp_name'], $pngDest)) {
                
                $modelPath = BASE_URL . '/assets/models/' . $baseName . '.obj';
                $mtlContent = file_get_contents($mtlDest);
                $mtlContent = preg_replace('/map_Kd\s+.*/', 'map_Kd ' . $baseName . '.png', $mtlContent);
                file_put_contents($mtlDest, $mtlContent);
            }
        }
    } elseif ($modelFormat === 'glb' && !empty($_FILES['model_single']['name'])) {
        $single = $_FILES['model_single'];
        $ext = strtolower(pathinfo($single['name'], PATHINFO_EXTENSION));
        
        if ($ext !== 'glb' && $ext !== 'gltf') {
            $errors[] = "Модель: допускается только формат .glb или .gltf.";
        } else {
            $newName = uniqid('model_', true) . '.' . $ext;
            $dest = __DIR__ . '/assets/models/' . $newName;
            if (move_uploaded_file($single['tmp_name'], $dest)) {
                $modelPath = BASE_URL . '/assets/models/' . $newName;
            }
        }
    }

    // 4. Сохранение в БД
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO garments
            (name, description, author, department, material, color, cut, season, collection, image_path, model_path, cover_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $name, $description, $author, $department, $material,
            $color, $cut, $season, $collection, $imagePathString, $modelPath, $coverPath
        ]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить наряд</title>
    <link rel="stylesheet" href="style.css?v=13">
    <style>
        .form-box { max-width: 700px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .form-box label { display: block; margin-top: 16px; font-weight: 600; color: #003366; }
        .form-box input[type="text"], .form-box textarea, .form-box select, .form-box input[type="file"] {
            width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #dce4ec; border-radius: 6px; font-family: inherit; font-size: 14px;
        }
        .form-box textarea { min-height: 100px; resize: vertical; }
        .form-box button[type="submit"] { margin-top: 24px; width: 100%; padding: 12px; font-size: 16px; }
        .msg-success { background: #e8f5e9; border: 1px solid #4caf50; padding: 16px; border-radius: 6px; color: #2e7d32; margin-bottom: 20px; }
        .msg-error { background: #ffebee; border: 1px solid #f44336; padding: 16px; border-radius: 6px; color: #c62828; margin-bottom: 20px; }
        
        .format-buttons { display: flex; gap: 10px; margin-bottom: 10px; }
        .format-btn {
            padding: 8px 20px; background: #fff; border: 2px solid #bdc3c7; border-radius: 6px;
            font-weight: bold; cursor: pointer; color: #7f8c8d; transition: 0.2s;
        }
        .format-btn:hover { border-color: #0055aa; color: #0055aa; }
        .format-btn.active { background: #0055aa; border-color: #0055aa; color: #fff; }
    </style>
</head>
<body>
<header><h1>Kosygin Fashion Archive</h1><nav><a href="index.php">← В каталог</a></nav></header>

<main>
    <div style="max-width: 700px; margin: 0 auto 20px auto;">
        <a href="index.php" class="btn" style="background: #7f8c8d; display: inline-block;">← В каталог</a>
    </div>

    <div class="form-box">
        <h2 style="color: #003366; margin-top: 0;">Добавить новый наряд</h2>
        <?php if ($success): ?>
            <div class="msg-success"><b>Успех!</b> Наряд добавлен.</div>
            <div style="text-align: center;"><a class="btn" href="index.php">В каталог</a> <a class="btn" href="upload.php" style="background: #7f8c8d;">Добавить еще</a></div>
        <?php else: ?>
            <?php if ($errors): ?><div class="msg-error"><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <label>Название наряда *</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div><label>Автор</label><input type="text" name="author" value="<?= htmlspecialchars($_POST['author'] ?? '') ?>"></div>
                    <div><label>Кафедра</label><input type="text" name="department" value="<?= htmlspecialchars($_POST['department'] ?? '') ?>"></div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div><label>Сезон</label><select name="season"><?php foreach (['Осень', 'Зима', 'Весна', 'Лето'] as $s): ?><option <?= (($_POST['season'] ?? '') === $s) ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
                    <div><label>Материал</label><input type="text" name="material" value="<?= htmlspecialchars($_POST['material'] ?? '') ?>"></div>
                    <div><label>Цвет</label><input type="text" name="color" value="<?= htmlspecialchars($_POST['color'] ?? '') ?>"></div>
                </div>

                <label>Крой</label><input type="text" name="cut" value="<?= htmlspecialchars($_POST['cut'] ?? '') ?>">
                <label>Коллекция</label><input type="text" name="collection" value="<?= htmlspecialchars($_POST['collection'] ?? '') ?>" placeholder="Например: Осенняя коллекция 2026">
                <label>Описание</label><textarea name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

                <div style="background: #eef2f5; padding: 20px; border-radius: 8px; margin-top: 24px; border: 1px solid #dce4ec;">
                    <h3 style="margin-top: 0; font-size: 16px; color: #003366;">Медиафайлы</h3>
                    
                    <label>Обложка для каталога (одно главное фото)</label>
                    <input type="file" name="cover" accept="image/jpeg,image/png,image/webp">

                    <label style="margin-top: 16px;">Фотографии наряда (галерея, можно выбрать несколько)</label>
                    <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>

                    <hr style="border: none; border-top: 1px solid #dce4ec; margin: 24px 0;">
                    
                    <h3 style="margin-top: 0; font-size: 16px; color: #003366;">3D-модель</h3>
                    <p style="font-size: 13px; color: #7f8c8d; margin-bottom: 10px;">Выберите формат модели:</p>
                    
                    <input type="hidden" name="model_format" id="model_format" value="">

                    <div class="format-buttons">
                        <button type="button" class="format-btn" onclick="selectFormat('obj', this)">.OBJ</button>
                        <button type="button" class="format-btn" onclick="selectFormat('glb', this)">.GLB</button>
                    </div>

                    <div id="fields-obj" style="display: none; margin-top: 15px; padding: 15px; background: #fff; border-radius: 6px; border: 1px dashed #ccc;">
                        <p style="margin-top: 0; font-size: 13px; color: #e67e22;"><b>Для OBJ нужно прикрепить 3 файла:</b></p>
                        <label>1. Геометрия (.obj)</label><input type="file" name="model_obj" accept=".obj">
                        <label>2. Материалы (.mtl)</label><input type="file" name="model_mtl" accept=".mtl">
                        <label>3. Текстура (.png)</label><input type="file" name="model_png" accept=".png">
                    </div>

                    <div id="fields-single" style="display: none; margin-top: 15px; padding: 15px; background: #fff; border-radius: 6px; border: 1px dashed #ccc;">
                        <label>Выберите файл модели (.glb)</label>
                        <input type="file" name="model_single" id="input-single" accept=".glb">
                    </div>
                </div>

                <button type="submit" class="btn">Сохранить наряд</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<script>
    function selectFormat(format, btn) {
        document.querySelectorAll('.format-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('model_format').value = format;
        document.getElementById('fields-obj').style.display = 'none';
        document.getElementById('fields-single').style.display = 'none';

        if (format === 'obj') {
            document.getElementById('fields-obj').style.display = 'block';
        } else {
            document.getElementById('fields-single').style.display = 'block';
        }
    }
</script>

<footer>© РГУ им. А.Н. Косыгина, 2026</footer>
</body>
</html>