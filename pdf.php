<?php
// pdf.php — генерация PDF (с учетом фильтров и строгим оглавлением)
require __DIR__ . '/config.php';
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); 
$dompdf = new Dompdf($options);

// Проверяем, передан ли ID для конкретного наряда
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM garments WHERE id = ?");
    $stmt->execute([$id]);
    $garments = $stmt->fetchAll();
    $docTitle = "Карточка наряда";
} else {
    $f_author = trim($_GET['author'] ?? '');
    $f_department = trim($_GET['department'] ?? '');
    $f_season = trim($_GET['season'] ?? '');
    $f_material = trim($_GET['material'] ?? '');
    $f_color = trim($_GET['color'] ?? '');
    $f_cut = trim($_GET['cut'] ?? '');
    $f_collection = trim($_GET['collection'] ?? '');
    $f_search = trim($_GET['search'] ?? '');

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

    $sql .= " ORDER BY id ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $garments = $stmt->fetchAll();
    $docTitle = "Каталог";
}

// Верстаем HTML для PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* Применяем единый шрифт ко всему документу */
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #222; }
        
        /* === СТИЛИ ДЛЯ ТИТУЛЬНОЙ СТРАНИЦЫ (ОГЛАВЛЕНИЕ) === */
        .title-page { page-break-after: always; padding-top: 50px; }
        .main-title { color: #003366; font-size: 32px; border-bottom: 2px solid #003366; padding-bottom: 15px; margin-bottom: 40px; text-align: center; }
        
        .toc-title { font-size: 24px; color: #003366; margin-top: 0; margin-bottom: 20px; text-align: left; }
        .toc-list { list-style-type: decimal; padding-left: 30px; font-size: 20px; line-height: 2; font-weight: normal; }
        .toc-list li { margin-bottom: 10px; }
        .toc-list a { color: #0055aa; text-decoration: none; }

        /* === СТИЛИ ДЛЯ КАРТОЧЕК НАРЯДОВ === */
        .item { 
            padding: 20px; 
            page-break-before: always; 
            text-align: center; 
        }
        .item:first-of-type { page-break-before: avoid; } 
        
        .item img { 
            max-width: 100%; 
            max-height: 450px; 
            display: block; 
            margin: 0 auto 30px auto; 
            border-radius: 12px; 
        }
        
        .title { 
            font-size: 28px; 
            color: #0055aa; 
            margin-bottom: 15px; 
            font-weight: bold;
        }
        
        .meta { 
            font-size: 15px; 
            color: #666; 
            margin-bottom: 30px; 
            line-height: 1.6;
        }
        
        .desc-title {
            font-size: 20px;
            color: #003366;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
        }
        
        .desc { 
            font-size: 16px; 
            line-height: 1.6; 
            text-align: justify; 
            margin: 0 auto;
            max-width: 90%;
            color: #222;
        }
    </style>
</head>
<body>
';

// Если это НЕ конкретный наряд (id=0) и нарядов больше 0 — рисуем титульную страницу с оглавлением
if ($id === 0 && count($garments) > 0) {
    $html .= '<div class="title-page">';
    $html .= '<div class="main-title">Kosygin Fashion Archive<br>' . $docTitle . '</div>';
    $html .= '<div class="toc-title">Содержание:</div>';
    $html .= '<ol class="toc-list">';
    
    foreach ($garments as $index => $g) {
        $num = $index + 1;
        $html .= '<li><a href="#item-' . $num . '">' . htmlspecialchars($g['name']) . '</a></li>';
    }
    
    $html .= '</ol>';
    $html .= '</div>';
}

if (count($garments) === 0) {
    $html .= '<p style="text-align:center; font-size: 20px;">По вашему запросу нарядов не найдено.</p>';
} else {
    foreach ($garments as $index => $g) {
        $num = $index + 1;
        $html .= '<div class="item" id="item-' . $num . '">';
        
        $preview_img = '';
        if (!empty($g['cover_path'])) {
            $preview_img = 'http://localhost' . $g['cover_path'];
        } elseif (!empty($g['image_path'])) {
            $first_img = explode(',', $g['image_path'])[0];
            $preview_img = 'http://localhost' . trim($first_img);
        }

        if ($preview_img) {
            $html .= '<img src="' . htmlspecialchars($preview_img) . '">';
        }

        $html .= '<div class="title">' . $num . '. ' . htmlspecialchars($g['name']) . '</div>';
        
        // Вывод характеристик (я аккуратно сгруппировал их, чтобы не было каши)
        $html .= '<div class="meta">';
        $html .= '<b>Автор:</b> ' . htmlspecialchars($g['author'] ?: '—') . ' | ';
        $html .= '<b>Кафедра:</b> ' . htmlspecialchars($g['department'] ?: '—') . ' | ';
        $html .= '<b>Сезон:</b> ' . htmlspecialchars($g['season'] ?: '—') . '<br>';
        
        $html .= '<b>Коллекция:</b> ' . htmlspecialchars($g['collection'] ?: '—') . ' | ';
        $html .= '<b>Материал:</b> ' . htmlspecialchars($g['material'] ?: '—') . '<br>';
        
        $html .= '<b>Цвет:</b> ' . htmlspecialchars($g['color'] ?: '—') . ' | ';
        $html .= '<b>Крой:</b> ' . htmlspecialchars($g['cut'] ?: '—');
        $html .= '</div>';
        
        if (!empty($g['description'])) {
            $html .= '<div style="margin-top: 30px;">';
            $html .= '<div class="desc-title">Описание</div>';
            $html .= '<div class="desc">' . nl2br(htmlspecialchars($g['description'])) . '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';
    }
}

$html .= '</body></html>';

$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = ($id > 0) ? 'garment_' . $id . '.pdf' : 'fashion_catalog.pdf';
$dompdf->stream($filename, ["Attachment" => false]); 