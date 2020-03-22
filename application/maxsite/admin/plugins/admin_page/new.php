<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
	Новая запись
	- проверяем POST, если есть, то это данные для новой записи
	- готовим данные в переменных $f_ для формы отображения см. forms.php
	- подключаем редактор
*/

$id = 0; // поскольку это новая запись

// дефолтные настройки новой записи
$f_content = $f_header = $f_tags = $f_slug = $f_page_parent = $f_password = '';
$f_status = $f_page_type = $f_comment_allow = $f_ping_allow = $f_feed_allow = '1';
$page_menu_order = '0';
$f_cat = array();
$f_user_id = $MSO->data['session']['users_id'];
	

// $CI = & get_instance();

// файл функций
require_once(getinfo('admin_plugins_dir') . 'admin_page/post-edit-functions.php'); 



if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_content')) )
{
	mso_checkreferer(); // проверка на реферер
	
	// подготовка данных из POST
	$data = post_prepare($post);
	
	// функции редактирования
	require_once( getinfo('common_dir') . 'functions-edit.php' ); 
	
	// добавление
	$result = mso_new_page($data);
		
	// pr($result);
	
	// результат
	if (isset($result['result']) and $result['result'])
	{
		if (isset($result['result'][0])) 
		{
			$url = '<a href="' 
					. mso_get_permalink_page($result['result'][0])
					. '" target="_blank">' . t('Посмотреть запись') . '</a> | '
					. '<a href="' . $MSO->config['site_admin_url'] . 'page_edit/' 
					. $result['result'][0] . '">' . t('Изменить') . '</a>';
		}
		else
		{
			$url = '';
		}
		
		echo '<div class="update">' . t('Запись добавлена!') . ' ' . $url . '</div>';
		
		if ($url and isset($post['f_return'])) // редирект на edit?
		{
			mso_redirect($MSO->config['site_admin_url'] . 'page_edit/' . $result['result'][0], true);
		}
		
		// дефолтные настройки новой записи
		$f_content = $f_header = $f_tags = $f_slug = $f_page_parent = $f_password = '';
		$f_status = $f_page_type = $f_comment_allow = $f_ping_allow = $f_feed_allow = '1';
		$page_menu_order = '0';
		$f_cat = array();
		$f_user_id = $MSO->data['session']['users_id'];
		
		// еще дата опубликования
		// и дата удаления
	}
	else
	{
		if (isset($result['description']) and $result['description'] == 'Existing page') 
			echo '<div class="error">' . t('Такая запись уже существует') . '</div>';
		else
			echo '<div class="error">' . t('Ошибка создания страницы') . '</div>';
	}
}


echo '<h1>' . t('Новая запись') . ' <a class="t100" href="' . getinfo('site_admin_url') . 'auto_post">или AutoPost</a></h1>';

// получим все опции редактора
$editor_options = mso_get_option('editor_options', 'admin', array());

$f_all_tags = post_all_tags($editor_options); // все метки

// $fses = mso_form_session('f_session_id'); // сессия

// получаем типы страниц
$post_all_post_types = post_all_post_types($f_page_type);
$all_post_types = $post_all_post_types['all_post_types'];
$page_type_js_obj = $post_all_post_types['page_type_js_obj'];

// получаем все рубрики чекбоксами
// require_once($MSO->config['common_dir'] . 'category.php');

$all_cat = mso_cat_ul('<label><input name="f_cat[]" type="checkbox" %CHECKED% value="%ID%" title="id = %ID%"> %NAME%</label>', true, $f_cat, $f_cat);

// опция по-умолчанию разрешение комментирования отмечать или нет
if (isset($editor_options['comment_allow_checked']))
	$comment_allow_checked = ($editor_options['comment_allow_checked'] == 0) ? '' : 'checked="checked"';
else
	$comment_allow_checked = 'checked="checked"';

$f_comment_allow = ($f_comment_allow) ? $comment_allow_checked : '';

// опция по-умолчанию разрешение rss отмечать или нет
if (isset($editor_options['feed_allow_checked']))
	$feed_allow_checked = ($editor_options['feed_allow_checked'] == 0) ? '' : 'checked="checked"';
else
	$feed_allow_checked = 'checked="checked"';
	
$f_feed_allow = ($f_feed_allow) ? $feed_allow_checked : '';

// не используется
$f_ping_allow = ($f_ping_allow) ? 'checked="checked"' : '';

$all_users = post_all_users($f_user_id); // получаем список юзеров

$name_submit = 'f_submit';

// дата публикации
$f_date_change = ''; // сменить дату не нужно - будет время автоматом поставлено текущее
		
$date_time = t('Текущее время:') . ' ' . date('Y-m-d H:i:s');

$td = post_date_time();
$date_y = $td['date_y'];
$date_m = $td['date_m'];
$date_d = $td['date_d'];
$time_h = $td['time_h'];
$time_m = $td['time_m'];
$time_s = $td['time_s'];

// получаем все страницы, для того чтобы отобразить их в паренте
$all_pages = post_all_pages($editor_options, $f_page_parent);

// мета большие,вынесена в отдельный файл
// из неё получается $all_meta = '<p>Нет</p>';
require($MSO->config['admin_plugins_dir'] . 'admin_page/all_meta.php');

// закладка файлы вынесена отдельно
// её результат — переменная $all_files
require($MSO->config['admin_plugins_dir'] . 'admin_page/all-files.php');

// начальный статус записи зависит от опции
$page_status_default = mso_get_option('page_status_default', 'templates', 'publish');

$f_status_publish = $f_status_draft = $f_status_private = '';

if ($page_status_default == 'draft') $f_status_draft = 'checked';
elseif ($page_status_default == 'private') $f_status_private = 'checked';
else $f_status_publish = 'checked';



$f_return = '<input name="f_return" type="checkbox" checked="checked" title="' . t('После сохранения вернуться к редактированию') . '">';

// быстрое сохранение только в режиме редактирования
$f_bsave = '';

// быстрая загрузка только в режиме редактирования
$f_bfiles_upload = '';

// форма вынесена в отдельный файл, поскольку она одна и таже для new и edit
// из неё получается $do и $posle
require($MSO->config['admin_plugins_dir'] . 'admin_page/form.php');

$ad_config = array(
			'action'=> '',
			'content' => $f_content,
			'do' => $do,
			'do_script' => $do_script,
			'posle' => $posle,
			);

// отображаем редактор
// есть ли хук на редактор: если да, то получаем эту функцию
// если нет, то отображаем стандартный editor_markitup
if (mso_hook_present('editor_custom')) 
	mso_hook('editor_custom', $ad_config);
else 
	editor_markitup($ad_config);
	
# end of file