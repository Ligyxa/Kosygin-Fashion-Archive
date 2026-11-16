<?php
// card.php — карточка наряда
require __DIR__ . '/config.php';
session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM garments WHERE id = ?");
$stmt->execute([$id]);
$g = $stmt->fetch();

if (!$g) { http_response_code(404); die('Наряд не найден'); }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($g['name']) ?> — Карточка</title>
    <link rel="stylesheet" href="style.css?v=17">
    <script type="importmap">
    { "imports": { "three": "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js", "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/" } }
    </script>
    <style>
        .slider-container { position: relative; width: 100%; height: 500px; background: #fff; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; }
        .slider-container img { max-width: 100%; max-height: 100%; object-fit: contain; display: none; }
        .slider-container img.active { display: block; }
        .slider-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(0, 51, 102, 0.7); color: white; border: none; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; padding: 0; cursor: pointer; border-radius: 50%; font-size: 20px; transition: background 0.2s; }
        .slider-btn:hover { background: rgba(0, 51, 102, 1); }
        .slider-btn.prev { left: 10px; }
        .slider-btn.next { right: 10px; }
        .slider-dots { position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; }
        .dot { width: 10px; height: 10px; background: #ccc; border-radius: 50%; cursor: pointer; }
        .dot.active { background: #003366; }
        
        /* Стили для шапки-ссылки */
        header a.logo-link { text-decoration: none; color: inherit; display: flex; align-items: center; }
    </style>
</head>
<body>
<header>
    <!-- Кликабельный логотип, ведущий в каталог -->
    <a href="index.php" class="logo-link">
        <h1 style="margin: 0; font-size: 22px; font-weight: 600;">Kosygin Fashion Archive</h1>
    </a>
    <nav>
        <?php if (isset($_SESSION['role'])): ?>
            <a href="upload.php">+ Добавить наряд</a>
            <span style="color: #90c2ff; margin-left: 20px;">Вы: <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php">Выйти</a>
        <?php else: ?>
            <a href="login.php">Войти</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <div style="max-width: 1100px; margin: 0 auto 20px auto;">
        <a href="index.php" class="btn" style="background: #0055aa; display: inline-block;">← В каталог</a>
    </div>
    
    <div class="card-view">
        <div style="height: 560px; overflow: hidden;">
            <div class="view-tabs">
                <button class="view-tab active" onclick="switchTab('tab-photo', this)">Фото</button>
                <button class="view-tab" onclick="switchTab('tab-3d', this)">3D-модель</button>
            </div>

            <div id="tab-photo" class="view-content active">
                <?php if (!empty($g['image_path'])): ?>
                    <?php $images = array_filter(explode(',', $g['image_path'])); $imgCount = count($images); ?>
                    <div class="slider-container">
                        <?php foreach ($images as $index => $img): ?>
                            <img src="<?= htmlspecialchars(trim($img)) ?>" class="slider-img <?= $index === 0 ? 'active' : '' ?>">
                        <?php endforeach; ?>
                        <?php if ($imgCount > 1): ?>
                            <button class="slider-btn prev" onclick="moveSlide(-1)">&#10094;</button>
                            <button class="slider-btn next" onclick="moveSlide(1)">&#10095;</button>
                            <div class="slider-dots">
                                <?php for ($i = 0; $i < $imgCount; $i++): ?>
                                    <div class="dot <?= $i === 0 ? 'active' : '' ?>" onclick="setSlide(<?= $i ?>)"></div>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div style="padding: 100px 20px; text-align: center; background: #eee; border-radius: 8px; color: #999; height: 500px; display: flex; align-items: center; justify-content: center;">Фото не загружено</div>
                <?php endif; ?>
            </div>

            <div id="tab-3d" class="view-content">
                <?php if (!empty($g['model_path'])): ?>
                    <div id="canvas-container" data-model="<?= htmlspecialchars($g['model_path']) ?>"></div>
                    <p class="hint" style="text-align: center; margin-top: 10px;">Вращайте модель мышью.</p>
                <?php else: ?>
                    <div style="padding: 100px 20px; text-align: center; background: #eee; border-radius: 8px; color: #999; height: 500px; display: flex; align-items: center; justify-content: center;">3D-модель не загружена</div>
                <?php endif; ?>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; height: 100%;">
            <h1><?= htmlspecialchars($g['name']) ?></h1>
            <dl class="specs">
                <dt>Автор</dt>     <dd><?= htmlspecialchars($g['author']     ?: '—') ?></dd>
                <dt>Кафедра</dt>   <dd><?= htmlspecialchars($g['department'] ?: '—') ?></dd>
                <dt>Сезон</dt>     <dd><?= htmlspecialchars($g['season']     ?: '—') ?></dd>
                <dt>Коллекция</dt> <dd><?= htmlspecialchars($g['collection'] ?: '—') ?></dd>
                <dt>Материал</dt>  <dd><?= htmlspecialchars($g['material']   ?: '—') ?></dd>
                <dt>Цвет</dt>      <dd><?= htmlspecialchars($g['color']      ?: '—') ?></dd>
                <dt>Крой</dt>      <dd><?= htmlspecialchars($g['cut']        ?: '—') ?></dd>
            </dl>

            <?php if (!empty($g['description'])): ?>
                <h3>Описание</h3>
                <p class="description"><?= nl2br(htmlspecialchars($g['description'])) ?></p>
            <?php endif; ?>

            <div style="margin-top: auto; padding-top: 20px;">
                <p class="hint" style="margin-bottom: 12px;">Добавлен: <?= htmlspecialchars($g['created_at'] ?? '—') ?></p>
                <div style="display: flex; gap: 10px; width: 100%; flex-wrap: wrap;">
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'teacher'): ?>
                        <a href="edit.php?id=<?= $id ?>" class="btn" style="background: #279ede; margin: 0;">Редактировать</a>
                    <?php endif; ?>
                    
                    <a href="pdf.php?id=<?= $id ?>" target="_blank" class="btn" style="background: #6420ad; margin: 0;">Скачать PDF</a>
                    
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'teacher'): ?>
                        <a href="delete.php?id=<?= $id ?>" class="btn" style="background: #c0392b; margin: 0; margin-left: auto;" onclick="return confirm('Удалить наряд?');">Удалить</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>© РГУ им. А.Н. Косыгина, 2026</footer>

<script>
    function switchTab(tabId, btn) {
        document.querySelectorAll('.view-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.view-tab').forEach(el => el.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
        btn.classList.add('active');
        if (tabId === 'tab-3d') setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 50);
    }
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slider-img');
    const dots = document.querySelectorAll('.dot');
    function showSlide(index) {
        if (slides.length === 0) return;
        if (index >= slides.length) currentSlide = 0;
        else if (index < 0) currentSlide = slides.length - 1;
        else currentSlide = index;
        slides.forEach(el => el.classList.remove('active'));
        dots.forEach(el => el.classList.remove('active'));
        slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) dots[currentSlide].classList.add('active');
    }
    function moveSlide(step) { showSlide(currentSlide + step); }
    function setSlide(index) { showSlide(index); }
</script>
<script type="module" src="assets/js/viewer.js"></script>
</body>
</html>