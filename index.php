<?php
// index.php — каталог нарядов с фильтрацией
require __DIR__ . '/config.php';
session_start();

// --- Сбор фильтров из GET-запроса ---
$f_author = trim($_GET['author'] ?? '');
$f_department = trim($_GET['department'] ?? '');
$f_season = trim($_GET['season'] ?? '');
$f_material = trim($_GET['material'] ?? '');
$f_color = trim($_GET['color'] ?? '');
$f_cut = trim($_GET['cut'] ?? '');
$f_collection = trim($_GET['collection'] ?? '');
$f_search = trim($_GET['search'] ?? '');

// --- Формируем SQL-запрос с учетом фильтров ---
$sql = "SELECT * FROM garments WHERE 1=1";
$params = [];

if ($f_author !== '') { $sql .= " AND author LIKE ?"; $params[] = "%$f_author%"; }
if ($f_department !== '') { $sql .= " AND department LIKE ?"; $params[] = "%$f_department%"; }
if ($f_season !== '') { $sql .= " AND season = ?"; $params[] = $f_season; }
if ($f_material !== '') { $sql .= " AND material LIKE ?"; $params[] = "%$f_material%"; }
if ($f_color !== '') { $sql .= " AND color LIKE ?"; $params[] = "%$f_color%"; }
if ($f_cut !== '') { $sql .= " AND cut LIKE ?"; $params[] = "%$f_cut%"; }
if ($f_collection !== '') { $sql .= " AND collection LIKE ?"; $params[] = "%$f_collection%"; }
if ($f_search !== '') { $sql .= " AND (description LIKE ? OR name LIKE ?)"; $params[] = "%$f_search%"; $params[] = "%$f_search%"; }

$sql .= " ORDER BY created_at DESC, id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$garments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kosygin Fashion Archive — Каталог</title>
    <link rel="stylesheet" href="style.css?v=17">
    <style>
        .filters-box { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; align-items: end; }
        .filter-group { display: flex; flex-direction: column; }
        .filter-group label { font-size: 12px; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; text-transform: uppercase; }
        .filter-group input, .filter-group select { padding: 8px 10px; border: 1px solid #dce4ec; border-radius: 6px; font-size: 14px; font-family: inherit; }
        .filter-actions { display: flex; gap: 10px; grid-column: 1 / -1; margin-top: 10px; }
        .btn-filter { background: #0055aa; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-reset { background: #bdc3c7; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; text-align: center; }
        .btn-filter:hover { background: #003366; }
        .btn-reset:hover { background: #95a5a6; }
        
        /* Стили для шапки-ссылки */
        header a.logo-link { text-decoration: none; color: inherit; display: flex; align-items: center; }
    </style>
</head>
<body>
<header>
    <!-- Кликабельный логотип -->
    <a href="index.php" class="logo-link">
        <h1 style="margin: 0; font-size: 22px; font-weight: 600;">Kosygin Fashion Archive</h1>
    </a>
    <nav>
        <?php if (isset($_SESSION['role'])): ?>
            <a href="upload.php">+ Добавить наряд</a>
            <span style="color: #90c2ff; margin-left: 20px;">Вы: <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] === 'teacher' ? 'Преподаватель' : 'Студент' ?>)</span>
            <a href="logout.php">Выйти</a>
        <?php else: ?>
            <a href="login.php">Войти</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Наряды</h2>
        
        <?php if (count($garments) > 0): ?>
            <?php 
                $pdf_query = $_GET; 
                $pdf_url = 'pdf.php' . (empty($pdf_query) ? '' : '?' . http_build_query($pdf_query));
            ?>
            <a href="<?= htmlspecialchars($pdf_url) ?>" target="_blank" class="btn" style="background: #6420ad;">Скачать PDF-каталог</a>
        <?php endif; ?>
    </div>

    <!-- ПАНЕЛЬ ФИЛЬТРОВ -->
    <form method="GET" action="index.php" class="filters-box">
        <div class="filter-group"><label>Автор</label><input type="text" name="author" value="<?= htmlspecialchars($f_author) ?>"></div>
        <div class="filter-group"><label>Кафедра</label><input type="text" name="department" value="<?= htmlspecialchars($f_department) ?>"></div>
        <div class="filter-group">
            <label>Сезон</label>
            <select name="season">
                <option value="">Все сезоны</option>
                <?php foreach (['Осень', 'Зима', 'Весна', 'Лето'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($f_season === $s) ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group"><label>Материал</label><input type="text" name="material" value="<?= htmlspecialchars($f_material) ?>"></div>
        <div class="filter-group"><label>Цвет</label><input type="text" name="color" value="<?= htmlspecialchars($f_color) ?>"></div>
        <div class="filter-group"><label>Крой</label><input type="text" name="cut" value="<?= htmlspecialchars($f_cut) ?>"></div>
        <div class="filter-group"><label>Коллекция</label><input type="text" name="collection" value="<?= htmlspecialchars($f_collection) ?>" placeholder="Осенняя..."></div>
        <div class="filter-group"><label>Поиск</label><input type="text" name="search" value="<?= htmlspecialchars($f_search) ?>"></div>
        
        <div class="filter-actions">
            <button type="submit" class="btn-filter">Применить фильтры</button>
            <a href="index.php" class="btn-reset">Сбросить</a>
        </div>
    </form>

    <?php if (count($garments) === 0): ?>
        <div class="empty-state">
            <p>По вашему запросу ничего не найдено.</p>
            <p><a class="btn" href="index.php">Сбросить фильтры</a></p>
        </div>
    <?php else: ?>
        <p class="count">Найдено нарядов: <strong><?= count($garments) ?></strong></p>

        <div class="catalog">
            <?php foreach ($garments as $g): ?>
                <div class="card">
                    <?php 
                    $preview_img = '';
                    if (!empty($g['cover_path'])) {
                        $preview_img = $g['cover_path'];
                    } elseif (!empty($g['image_path'])) {
                        $first_img = explode(',', $g['image_path'])[0];
                        $preview_img = trim($first_img);
                    }
                    ?>

                    <?php if ($preview_img): ?>
                        <img src="<?= htmlspecialchars($preview_img) ?>" alt="..." style="object-fit: contain; height: 350px; width: 100%; background: #fff; border-radius: 8px;">
                    <?php else: ?>
                        <div class="no-image" style="height: 350px; background: #fff; display: flex; align-items: center; justify-content: center; border-radius: 8px; color: #999;">Нет фото</div>
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($g['name']) ?></h3>
                    <p class="meta"><?= htmlspecialchars($g['author'] ?: 'Автор не указан') ?> · <?= htmlspecialchars($g['season'] ?: '—') ?></p>
                    <a class="btn" href="card.php?id=<?= (int)$g['id'] ?>">Открыть карточку</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<footer>© РГУ им. А.Н. Косыгина, 2026</footer>

<script>
    // Плавный скролл наверх при клике на логотип в каталоге
    document.querySelector('header a.logo-link').addEventListener('click', function(e) {
        // Если мы уже на странице каталога, предотвращаем перезагрузку и скроллим
        if (window.location.pathname.endsWith('index.php') || window.location.pathname.endsWith('/fashion-archive/')) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
</script>
</body>
</html>