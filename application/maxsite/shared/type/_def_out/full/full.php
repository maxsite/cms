<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

if (!$pages) return;

$p = new Page_out();

$p->reset_counter(count($pages));

// формат можно задать отдельно перед циклом
if ($f = mso_page_foreach('format-full-' . getinfo('type'))) {
    require $f;
} else {
    if ($f = mso_page_foreach('format-full')) {
        require $f;
    } else {
        $p->format('title', mso_get_val('full_format_title_start', '<h2 class="mso-page-title">'), mso_get_val('full_format_title_end', '</h2>'), true);
        $p->format('date', 'D, j F Y ' . tf('г.'), '<span><time datetime="[page_date_publish_iso]">', '</time></span>');
        $p->format('cat', ' -&gt; ', '<br><span>' . tf('Рубрика') . ': ', '</span>');
        $p->format('tag', ' | ', '<br><span>' . tf('Метки') . ': ', '</span>');
        $p->format('feed', tf('Комментарии по RSS'), ' | <span>', '</span>');
        $p->format('edit', 'Edit', ' | <span>', '</span>');
        $p->format('view_count', '<br><span>' . tf('Просмотров') . ': ', '</span>');
        $p->format('comments', tf('Обсудить'), tf('Читать комментарии'), '<div class="mso-comments-link"><span>',  '</span></div>');
    }
}

// исключенные записи
$exclude_page_id = mso_get_val('exclude_page_id', []);

// определяем info_top-файл
$info_top_fn = '';
$info_top_class = ''; // автоматический css-класс для контейнера mso-tf-full-default

// info-top_slug по адресам — это самый высокий приоритет
// category/news = header-only.php
if ($info_top_slug = mso_get_option('info-top_slug', getinfo('template'), '')) {
    // в текущем url нужно отсечь /next/ - пагинацию
    $cur_url = mso_current_url();

    if (strpos($cur_url, '/next/') !== false) {
        $cur_url = explode('/next/', $cur_url);
        $cur_url = $cur_url[0];
    }

    if ($i = mso_text_find_key($info_top_slug, $cur_url)) {
        if ($fn = mso_fe('type_foreach/info-top/' . $i))
            $info_top_fn = $fn; // выставляем путь к файлу
    }
}

if (!$info_top_fn) {
    if ($fn = mso_page_foreach('info-top-' . getinfo('type'))) {
        // для типа может быть свой info-top
        $info_top_fn = $fn;
    } elseif ($info = mso_get_option('info-top_' . getinfo('type'), getinfo('template'), '') and $fn = mso_fe('type_foreach/info-top/' . $info)) {
        // или опция
        $info_top_fn = $fn;
    } elseif ($fn = mso_page_foreach('info-top-full')) {
        $info_top_fn = $fn;
    } elseif ($fn = mso_page_foreach('info-top')) {
        $info_top_fn = $fn;
    }
}

if ($info_top_fn) {
    // css-класс делается на основе имени файла type_foreach-файла
    // (берется только имя файла без расширения)
    // с префиксом mso-tf-
    $key = str_replace('.php', '', basename($info_top_fn));
    $info_top_class = 'mso-tf-' . $key;

    // может быть файл карта css-классов по info-top-файлам
    if ($fn = mso_page_foreach('mapcss')) {
        // файл возвращает обычный массив ['info-top-файл' => 'css-класс', ...]
        // класс добавляется к текущему $info_top_class
        if ($map = require $fn) {
            $addClass = $map[$key] ?? '';

            if ($addClass) $info_top_class .= ' ' . $addClass;
        }
    }
}

if ($f = mso_page_foreach('do-full')) require $f;
if ($f = mso_page_foreach('do-full-' . getinfo('type'))) require $f;

$p->div_start(trim(mso_get_val('container_class', '') . ' ' . $info_top_class));

foreach ($pages as $page) {
    if ($f = mso_page_foreach(getinfo('type'))) {
        require $f; // подключаем кастомный вывод
        continue; // следующая итерация
    }

    $p->load($page);

    $p->div_start(mso_get_val('page_only_class', 'mso-page-only'), '<article>');

    if ($info_top_fn) {
        require $info_top_fn;
    } else {
        $p->html('<header>');
        $p->line('[title]');

        $p->div_start('mso-info mso-info-top');
        $p->line('[date][edit][cat][tag][view_count]');
        $p->div_end('info info-top');

        $p->html('</header>');
    }

    if (getinfo('type') == 'page_404' and mso_segment(1) and $f = mso_page_foreach('page-content-full-segment-' . mso_segment(1))) {
        require $f;
    } elseif ($f = mso_page_foreach('page-content-' . getinfo('type'))) {
        require $f;
    } elseif ($f = mso_page_foreach('page-content-full')) {
        require $f;
    } else {
        if ($f = mso_page_foreach('page-content')) {
            require $f;
        } else {
            // стандартный вывод
            if ($fn = mso_find_ts_file('type/_def_out/full/units/full-default.php')) require $fn;
        }
    }

    $p->div_end(mso_get_val('page_only_class', 'mso-page-only'), '</article>');

    if ($f = mso_page_foreach(getinfo('type') . '-page-only-end')) require $f;

    $exclude_page_id[] = $p->val('page_id');
} // end foreach

if ($f = mso_page_foreach('end-full')) require $f;
if ($f = mso_page_foreach('end-full-' . getinfo('type'))) require $f;

$p->div_end(mso_get_val('container_class', ''));

mso_set_val('exclude_page_id', $exclude_page_id);

# end of file
