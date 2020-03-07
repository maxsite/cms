<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// класс для вывода блоков записей
class Block_pages
{
    protected $param; // массив входящих данных для получения записей
    protected $pages; // полученные записи
    protected $pagination; // если используется пагинация

    public $go = false; // признак, что можно делать вывод

    public function __construct($r1 = [], $UNIT = [])
    {
        if ($r1 !== false) $this->get_pages($r1, $UNIT); // сразу получаем записи
    }

    // метод, где получаются записи
    protected function get_pages($r, $UNIT = [])
    {
        // дефолтные значения для получения записей
        $default = array(
            'limit' => 1, // колво записей
            'cut' => '»»»', // ссылка cut
            'pagination' => false, // выводить пагинацию
            'cat_id' => 0, // можно указать рубрики через запятую
            'exclude_cat_id' => 0, // исключенные рубрики через запятую
            'page_id' => 0, // можно указать записи через запятую
            'page_id_autor' => false, // записи автора
            'type' => 'blog', // можно указать тип записей
            'order' => 'page_date_publish', // поле сортировки страниц
            'order_asc' => 'desc', // поле сортировки страниц
            'show_cut' => true, // показывать ссылку cut
            'date_now' => true, // учитывать время публикации
            'exclude_page_allow' => true, // учитывать исключенные ранее страницы
            // 'exclude_page_add' => true, // разрешить добавлять полученные страницы в исключенные
            'function_add_custom_sql' => false, // дополнительная функция для sql-запроса 
            'pages_reverse' => false, // реверс результата 
        );

        $this->param = array_merge($default, $r); // объединяем с дефолтом
        $exclude_page_id = ($this->param['exclude_page_allow']) ? mso_get_val('exclude_page_id') : [];

        $this->pages = mso_get_pages(
            [
                'limit' => $this->param['limit'],
                'cut' => $this->param['cut'],
                'pagination' => $this->param['pagination'],
                'cat_id' => $this->param['cat_id'],
                'exclude_cat_id' => $this->param['exclude_cat_id'],
                'page_id' => $this->param['page_id'],
                'page_id_autor' => $this->param['page_id_autor'],
                'type' => $this->param['type'],
                'order' => $this->param['order'],
                'order_asc' => $this->param['order_asc'],

                'show_cut' => $this->param['show_cut'],
                'show_xcut' => $this->param['show_cut'],

                'date_now' => $this->param['date_now'],

                'custom_type' => 'home',
                'exclude_page_id' => $exclude_page_id, // исключаем получение уже выведенных записей

                'function_add_custom_sql' => $this->param['function_add_custom_sql'],
                'pages_reverse' => $this->param['pages_reverse'],

                // 'get_page_categories' => false,
                // 'get_page_count_comments' => false,

            ],
            $this->pagination
        );

        $this->go = ($this->pages) ? true : false;

        // цепляем $UNIT к каждой записи
        if ($this->pages) {
            for ($i = 0; $i < count($this->pages); $i++) {
                $this->pages[$i]['UNIT'] = $UNIT;
            }
        }
    }

    public function set_pages($pages, $pagination)
    {
        $this->pages = $pages;
        $this->pagination = $pagination;
        if ($pagination) $this->param['pagination'] = true;
        $this->go = ($this->pages) ? true : false;
    }

    // метод, выводящий записи
    public function output($r = [])
    {
        if (!$this->pages) return; // нет записей, выходим

        // дефолтный формат вывода
        $default = [
            'title_start' => '<h3 class="home-last-page">',
            'title_end' => '</h3>',

            'date' => 'D, j F Y г. в H:i',
            'date_start' => '<span class="date"><time datetime="[page_date_publish_iso]">',
            'date_end' => '</time></span>',

            'cat_start' => ' | <span class="cat">',
            'cat_end' => '</span>',
            'cat_sep' => ', ',

            'tag_start' => ' | <span class="tag">',
            'tag_end' => '</span>',
            'tag_sep' => ', ',
            'tag_class' => '',

            'author_start' => '',
            'author_end' => '',

            'read' => '»»»',
            'read_start' => '',
            'read_end' => '',

            'comments_count_start' => '',
            'comments_count_end' => '',

            'thumb' => true, // использовать миниатюры
            'thumb_width' => 320,
            'thumb_height' => 180,
            'thumb_class' => 'thumb left', // css-класс картинки
            'thumb_link_class' => '', // css-класс ссылки 
            'thumb_link' => true, // формировать ссылку на запись 
            'thumb_add_start' => '', // произвольная добавка перед img 
            'thumb_add_end' => '', // произвольная добавка после img 
            'thumb_type_resize' => 'resize_full_crop_center', // тип создания миниатюры  

            // имя файла формируется как placehold_path + placehold_file
            'placehold' => false, // если нет картинки, выводим плейсхолд (true) или ничего (false)
            'placehold_path' => 'http://placehold.it/', // путь к плейсхолдеру
            // getinfo('template_url') . 'images/placehold/'
            'placehold_pattern' => '[W]x[H].png', // шаблон плейсхолдера : width x height .png
            // где [W] меняется на ширину, [H] — высоту, [RND] - число 1..10
            'placehold_file' => false,  // файл плейсхолдера, если false, то будет: width x height .png
            // если равно data, то свой плейсхолдер
            'placehold_data_bg' => '#CCCCCC', // цвет фона, если data

            'block_start' => '', // html вначале
            'block_end' => '', // html в конце

            'line1' => '[thumb]', // первая линия — перед контентом
            'line1_start' => '',
            'line1_end' => '',

            'line2' => '[title]', // вторая линия — перед контентом
            'line2_start' => '',
            'line2_end' => '',

            'line3' => '[date] [cat]', // третья линия — перед контентом
            'line3_start' => '',
            'line3_end' => '',

            'line4' => '', // четвертая линия — после контента
            'line4_start' => '',
            'line4_end' => '',

            'line5' => '', // пятая линия — после контента
            'line5_start' => '',
            'line5_end' => '',

            // вывод контента
            // если указано любое значение, то вывод по этому варианту иначе обычный вывод до cut
            'content' => true, // разрешить вывод контента 
            'content_chars' => 0, // колво символов 
            'content_words' => 0, // колво слов 
            'content_cut' => ' ...', // завершение в контенте 
            'content_start' => '<div class="mso-page-content">', // обрамляющий блок до
            'content_end' => '</div>', // обрамляющий блок после

            'clearfix' => false, // отбивать после вывода $p->clearfix();

            'page_start' => '', // html в начале вывода записи 
            'page_end' => '', // html в конце вывода записи

            'pagination_start' => '', // html в начале пагинации 
            'pagination_end' => '', // html в конце пагинации
            'pagination_in_block' => true, // пагинации внутри block_start и block_end

            'exclude_page_add' => true, // разрешить добавлять полученные страницы в исключенные

            // триггеры, которые срабатывают в начале указанного номера записи
            // trigger@3 = привет1
            // trigger@5 = привет2

        ];

        $r = array_merge($default, $r); // объединяем

        $p = new Page_out; // шаблонизатор

        eval(mso_tmpl_prepare($r['block_start'], false));

        // формат записи
        $p->format('title', $r['title_start'], $r['title_end']);
        $p->format('date', $r['date'], $r['date_start'], $r['date_end']);
        $p->format('author', $r['author_start'], $r['author_end']);
        $p->format('cat', $r['cat_sep'], $r['cat_start'], $r['cat_end']);
        $p->format('tag', $r['tag_sep'], $r['tag_start'], $r['tag_end'], $r['tag_class']);
        $p->format('read', $r['read'], $r['read_start'], $r['read_end']);
        $p->format('comments_count', $r['comments_count_start'], $r['comments_count_end']);

        if ($r['exclude_page_add']) $exclude_page_id = mso_get_val('exclude_page_id', []);

        foreach ($this->pages as $page) {
            $p->load($page); // загружаем данные записи

            if (isset($r['UNIT']['trigger@' . $p->num])) {
                eval(mso_tmpl_prepare($r['UNIT']['trigger@' . $p->num], false));
            }

            if ($r['thumb']) {
                // миниатюра
                // плейсхолд
                if ($r['placehold']) {
                    if ($r['placehold_file']) {
                        if ($r['placehold_file'] == 'data') {
                            // сами генерируем плейсхолд
                            // mso_holder($width = 100, $height = 100, $text = true, $background_color = '#CCCCCC', $text_color = '#777777', $font_size = 5)
                            $t_placehold = mso_holder($r['thumb_width'], $r['thumb_height'], false, $r['placehold_data_bg']);
                        } else {
                            $t_placehold = $r['placehold_path'] . $r['placehold_file'];
                        }
                    } else {
                        $t_placehold_pattern = str_replace('[W]', $r['thumb_width'], $r['placehold_pattern']);
                        $t_placehold_pattern = str_replace('[H]', $r['thumb_height'], $t_placehold_pattern);
                        $t_placehold_pattern = str_replace('[RND]', rand(1, 10), $t_placehold_pattern);

                        $t_placehold = $r['placehold_path'] . $t_placehold_pattern;
                    }
                } else {
                    $t_placehold = false;
                }

                // если используется thumb_type_resize != resize_full_crop_center, то меняем постфикс
                $thumb_postfix = true;

                if ($r['thumb_type_resize'] !== 'resize_full_crop_center') {

                    $thumb_postfix = '-' . $r['thumb_width'] . '-' . $r['thumb_height'] . '-' . $r['thumb_type_resize'];
                }

                if (
                    $thumb = thumb_generate(
                        $p->meta_val('image_for_page'), // адрес
                        $r['thumb_width'], //ширина
                        $r['thumb_height'], //высота
                        $t_placehold,
                        $r['thumb_type_resize'], // тип создания
                        false,
                        'mini',
                        $thumb_postfix,
                        mso_get_option('upload_resize_images_quality', 'general', 90)
                    )
                ) {
                    if ($r['thumb_link'])
                        $p->thumb = '<a class="' . $r['thumb_link_class'] . '" href="' . mso_page_url($p->val('page_slug')) . '" title="' . htmlspecialchars($p->val('page_title')) . '">' . $r['thumb_add_start'] . '<img src="' . $thumb . '" class="' . $r['thumb_class'] . '" alt="' . htmlspecialchars($p->val('page_title')) . '">' . $r['thumb_add_end'] . '</a>';
                    else
                        $p->thumb = $r['thumb_add_start'] . '<img src="' . $thumb . '" class="' . $r['thumb_class'] . '" alt="' . htmlspecialchars($p->val('page_title')) . '">' . $r['thumb_add_end'];

                    $p->thumb_url = $thumb;
                    $r['thumb_url'] = $thumb;
                }
            }

            // eval(mso_tmpl_prepare($r['page_start'], false));
            $p->line($r['page_start']); // можно использовать все []-коды
            $p->line($r['line1'], $r['line1_start'], $r['line1_end']);
            $p->line($r['line2'], $r['line2_start'], $r['line2_end']);
            $p->line($r['line3'], $r['line3_start'], $r['line3_end']);

            if ($r['content']) {
                if ($r['content_chars']) {
                    $p->content_chars($r['content_chars'], $r['content_cut'], $r['content_start'], $r['content_end']);  // текст обрезанный
                } elseif ($r['content_words']) {
                    $p->content_words($r['content_words'], $r['content_cut'], $r['content_start'], $r['content_end']);  // текст обрезанный
                } else {
                    $p->content($r['content_start'], $r['content_end']);
                }
            }

            $p->line($r['line4'], $r['line4_start'], $r['line4_end']);
            $p->line($r['line5'], $r['line5_start'], $r['line5_end']);

            if ($r['clearfix']) $p->clearfix();

            // eval(mso_tmpl_prepare($r['page_end'], false));
            $p->line($r['page_end']); // можно использовать все []-коды

            // сохраняем id записей, чтобы их исключить из вывода			
            if ($r['exclude_page_add']) $exclude_page_id[] = $p->val('page_id');
        }

        if ($r['exclude_page_add']) mso_set_val('exclude_page_id', $exclude_page_id);

        if ($r['pagination_in_block'] == false)
            eval(mso_tmpl_prepare($r['block_end'], false));

        if ($this->param['pagination']) {
            if (mso_hook_present('pagination')) {
                eval(mso_tmpl_prepare($r['pagination_start'], false));
                mso_hook('pagination', $this->pagination);
                eval(mso_tmpl_prepare($r['pagination_end'], false));
            }
        }

        if ($r['pagination_in_block'] == true)
            eval(mso_tmpl_prepare($r['block_end'], false));
    }
}

# end of file
