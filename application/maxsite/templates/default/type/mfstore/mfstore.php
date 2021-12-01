<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

mso_head_meta('title', 'MF Store'); //  meta title страницы

// класс для фона меню некоторых компонентов header
mso_set_val('comp_header_menu_bgtm1', 'bg-primary700');
mso_set_val('comp_header_menu_bgtm1_1', 'bg-primary800');
mso_set_val('comp_header_menu_bgtm2', 'bg-primary700');

mso_set_val('comp_breadcrumbs_add', ['MF Store' => '']); // добавка к «хлебным крошкам»

// делаем шаблон без сайдбара
if ($fn_main = mso_fe('main/no-sidebar/main.php')) mso_set_val('main_file', $fn_main);

// начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-page-only"><div class="mso-page-content pad30-b">';

if (!is_login()) {
    echo '<div class="bg-red300 t-white pad30 mar50-tb">Страница доступна только админам</div>';
    echo '</div></div>';
    // конечная часть шаблона
    if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;

    return;
}

// если нет второго сегмента, то просто выводим список всех модулей
// если есть второй сегмент, то это base64, где хранится адрес модуля относительно store

if ($segment = mso_segment(2)) {

    $bdir = @base64_decode($segment);

    $file = getinfo('template_dir') . 'store/' . $bdir . '/index.php';
    
    if (file_exists($file)) {
        
        $siteurl = getinfo('siteurl');

        $files = glob(getinfo('template_dir') . 'store/*/*/index.php');
        
        // pr($files);

        $arModules = getAllModules($files);

        $aNP = arrayNextPrev($files, $file);

        // pr($aNP);
        $next = '';
        $prev = '';

        if ($aNP['next']) 
            $next = str_replace(getinfo('template_dir') . 'store/', '',  dirname($aNP['next']['val']));
        
        if ($aNP['prev']) 
            $prev = str_replace(getinfo('template_dir') . 'store/', '',  dirname($aNP['prev']['val']));

        // pr($prev);
        // pr($next);

        if ($prev) $prev = '<a class="b-block im-arrow-left" href="'. $siteurl . 'mfstore/' . base64_encode($prev) . '" title="Предыдущий модуль">' . $prev . '</a>';

        if ($next) $next = '<a class="b-block" href="'. $siteurl . 'mfstore/' . base64_encode($next) . '" title="Следующий модуль">' . $next . '<i class="pad10-l im-arrow-right"></i></a>';

        
        echo <<< EOF
<div class="flex flex-wrap-phone bor1 bor-gray300 bor-dashed-b mar30-tb pad20-b">
    <a class="w20 w100-phone im-arrow-left pad10-tb t-center-phone" style="" href="{$siteurl}mfstore">Все модули</a>

    <h2 class="t-center mar0 mar10-b-phone w100-phone">Mодуль <span class="t-gray600 t-italic">{$bdir}</span></h2>
    
    <div class="w20 w100-phone t-right t-center-phone">{$prev}{$next}</div>
</div>
EOF;

        mso_set_val('mso_units_out_modulesDir', 'store/');

        $text_units = file_get_contents($file);
        mso_units_out($text_units);

        if (is_login() and file_exists(__DIR__ . '/admin.php')) require __DIR__ . '/admin.php';
    }
} else {
    $files = glob(getinfo('template_dir') . 'store/*/*/index.php');
    $arModules = getAllModules($files);

    if ($arModules) {

        $currentUrl = mso_current_url() . '/';

        echo '<h1 class="t-center mar30-tb">Mодули MF Store <sup>' . count($files) . ' шт.</sup></h1>';
        echo '<section class="my-modules mar30-tb">';

        foreach ($arModules as $nameBlock => $arBlocks) {
            echo '<div class="t250 t-primary800 t-capitalize mar20-b">◈ ' . $nameBlock . '</div>';

            echo '<div class="flex x-flex flex-wrap mar50-b">';
            foreach ($arBlocks as $elem) {
                $img = getinfo('template_dir') . 'store/' . $elem . '/screenshot.png';

                if (file_exists($img)) 
                    $img = getinfo('template_url') . 'store/' . $elem . '/screenshot.png';
                else
                    $img = mso_holder(300, 50, $elem);

                echo '<a class="w30 flex-as-center w50-tablet w100-phone pad10 mar20-b mar10-r bg-gray100 hover-bg-primary200" href="' . $currentUrl . base64_encode($elem) . '"><img src="' . $img . '" width="w100" alt="' . $elem . '" title="' . $elem . '"></a>';
            }

            echo '</div>';
        }

        echo '</section>';
    }
}

echo '</div></div>';

// для удобства отмечаем просмотренные ссылки
echo '<style>
.my-modules a:visited {color: #a187a9}
.x-flex::after {
  content: " ";
  flex: 0 0 30%;
}

</style>';

// конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;

// получить все модули
function getAllModules($files)
{
    $arModules = [];

    foreach ($files as $file) {
        $bdirBlock = basename(dirname(dirname($file)));
        $bdir = basename(dirname(dirname($file))) . '/' . basename(dirname($file));
        $arModules[$bdirBlock][] = $bdir;
    }

    return $arModules;
}

function arrayNextPrev($arr, $findKey, $useKey = false) {
    
    $keys = array_keys($arr);

    if ($useKey) {
        $find = array_search($findKey, $keys);
    } else {
        $find = array_search($findKey, $arr);
    }
    
    $next = $prev = [];

    if ($find !== false) {
        $nextKey = $find + 1;
        $prevKey = $find - 1;
        
        $values = array_values($arr);
        
        if ($nextKey < count($keys)) {
            $next = [
                'key' => $keys[$nextKey],
                'val' => $values[$nextKey]
            ];
        }
        
        if ($prevKey >= 0) {
            $prev = [
                'key' => $keys[$prevKey],
                'val' => $values[$prevKey]
            ];
        }
    }
    
    return ['prev' => $prev, 'next' => $next];
}

# end of file
