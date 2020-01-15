<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$id = mso_segment(3); // номер страницы по сегменту url

// проверим, чтобы это было число
if (!is_numeric($id)) 
	$id = false; // не число
else 
	$id = (int) $id;

if (!$id) // id - ошибочный
{
	echo '<div class="error">' . t('Ошибочный запрос') . '</div>'; 
	return;
}

$permalink_page = mso_get_permalink_page($id);

// id="permalink_page1" и id="permalink_page2" нужны, чтобы при обновлении сразу поменять
// на новую ссылку через post-edit.php
?>



<form method="post" action="<?= getinfo('site_admin_url') ?>page"><?= mso_form_session('f_session_id') ?><input type="hidden" name="f_page_delete" value="<?= $id ?>"><input type="hidden" name="f_submit" value=""><h1><a id="permalink_page1" href="<?= $permalink_page ?>" title="<?= t('Посмотреть на сайте') ?>"><?= t('Редактирование записи') ?></a> <a id="permalink_page2" class="i-external-link t110 icon0" href="<?= $permalink_page ?>" target="_blank" title="<?= t('Открыть в новом окне') ?>"></a><span class="b-inline b-right t80"><button type="submit" name="f_delete" class="button i-remove icon0 pad10 bg-gray hover-bg-red600 " title="<?= t('Удалить запись') ?>" onclick="if(confirm('Удалить страницу?')) {return true;} else {return false;}"></button></span></h1></form>

<?php
	
// файл функций
require_once(getinfo('admin_plugins_dir') . 'admin_page/post-edit-functions.php'); 

$CI = & get_instance();

// проверим текущего юзера и его разрешение на правку чужих страниц
// если admin_page_edit=1, то есть разрешено редактировать в принципе (уже проверили раньше!),
// то смотрим admin_page_edit_other. Если стоит 1, то все разрешено
// если false, значит смотрим автора страницы и если он не равен юзеру, рубим доступ

if ( !mso_check_allow('admin_page_edit_other') )
{
	$current_users_id = getinfo('session');
	$current_users_id = $current_users_id['users_id'];
	
	// получаем данные страницы
	$CI->db->select('page_id');
	$CI->db->from('page');
	$CI->db->where(array('page_id'=>$id, 'page_id_autor'=>$current_users_id));
	$query = $CI->db->get();
	if ($query->num_rows() == 0) // не автор
	{
		echo '<div class="error">' . t('Вам не разрешено редактировать чужие записи!') . '</div>';
		return;
	}
}

// этот код почти полностью повторяет код из new.php
// разница только в том, что указан id
if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_content')) )
{
	mso_checkreferer();
	
	// прием данных вынесен в post-edit.php
	// он используется и для фонового сохранения
	require(getinfo('admin_plugins_dir') . 'admin_page/post-edit.php'); 
	
}
else 
{
	// echo ' | <a href="' . mso_get_permalink_page($id) . '">' . t('Посмотреть запись') . '</a> (<a target="_blank" href="' . mso_get_permalink_page($id) . '">' . t('в новом окне') . '</a>)</p>';
	
	// очистим кэш БД
	$CI->db->cache_delete_all();
	
	// получаем данные записи
	$CI->db->select('*');
	$CI->db->where(array('page_id' => $id));
	$query = $CI->db->get('page');
	if ($query->num_rows() > 0)
	{
		foreach ($query->result_array() as $row)
		{
			// pr($row);
			$f_content = $row['page_content'];
			$f_header = $row['page_title'];
			$f_slug = $row['page_slug'];
			$f_status = $row['page_status'];
			$f_page_type = $row['page_type_id'];
			$f_password = $row['page_password'];
			$f_comment_allow = $row['page_comment_allow'];
			$f_ping_allow = $row['page_ping_allow'];
			$f_feed_allow = $row['page_feed_allow'];
			$f_page_parent = $row['page_id_parent'];
			$f_user_id = $row['page_id_autor'];
			$page_date_publish = $row['page_date_publish'];
			$page_menu_order = $row['page_menu_order'];
		}
		
		$f_cat = mso_get_cat_page($id); // рубрики в виде массива
		$f_tags = implode(', ', mso_get_tags_page($id)); // метки страницы в виде массива			
	}
	else
	{
		echo '<div class="error">' . t('Ошибочная страница (нет такой страницы)') . '</div>';
		return;
	}

}

// получим все опции редактора
$editor_options = mso_get_option('editor_options', 'admin', array());

$f_header = htmlspecialchars($f_header);
$f_tags = htmlspecialchars($f_tags);
$f_all_tags = post_all_tags($editor_options); // все метки

// $fses = mso_form_session('f_session_id'); // сессия

// получаем типы страниц
$post_all_post_types = post_all_post_types($f_page_type);
$all_post_types = $post_all_post_types['all_post_types'];
$page_type_js_obj = $post_all_post_types['page_type_js_obj'];

// получаем все рубрики чекбоксы
require_once( $MSO->config['common_dir'] . 'category.php' );

$all_cat = mso_cat_ul('<label><input name="f_cat[]" type="checkbox" %CHECKED% value="%ID%" title="id = %ID%"> %NAME%</label>', true, $f_cat, $f_cat);

// опция по-умолчанию разрешение комментирования отмечать или нет
//if (isset($editor_options['comment_allow_checked']))
//	$comment_allow_checked = ($editor_options['comment_allow_checked'] == 0) ? '' : 'checked="checked"';
//else
//	$comment_allow_checked = 'checked="checked"';

$f_comment_allow = ($f_comment_allow) ? 'checked="checked"' : '';


// опция по-умолчанию разрешение rss отмечать или нет
if (isset($editor_options['feed_allow_checked']))
	$feed_allow_checked = ($editor_options['feed_allow_checked'] == 0) ? '' : 'checked="checked"';
else
	$feed_allow_checked = 'checked="checked"';
	
$f_feed_allow = ($f_feed_allow) ? $feed_allow_checked : '';

// не используется
$f_ping_allow = ($f_ping_allow) ? 'checked="checked"' : '';
	
$all_users = post_all_users($f_user_id); // получаем список юзеров


$f_status_draft = $f_status_private = $f_status_publish = '';

if ($f_status == 'draft') 
	$f_status_draft = 'checked';
elseif ($f_status == 'private') 
	$f_status_private = 'checked';
else 
	$f_status_publish = 'checked'; // ($f_status == 'publish') 

$name_submit = 'f_submit[' . $id . ']';


// дата публикации
$f_date_change = ''; // сменить дату не нужно - будет время автоматом поставлено текущее

$date_cur = strtotime($page_date_publish);

$date_time = t('Сохранено:') . ' ' . $page_date_publish;

$date_time .= '<br>' . t('На сайте как:') . ' ' . mso_date_convert('Y-m-d H:i:s', $page_date_publish);
$date_time .= '<br>' . t('Текущее время:') . ' ' . date('Y-m-d H:i:s');

$td = post_date_time($page_date_publish);

// pr($td);
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

$f_return = '';

// быстрое сохранение только в режиме редактирования
$f_bsave = ' <button id="bsave" type="button" class="button i-save">' . t('Сохранить в фоне') . '</button>';

// быстрая загрузка
$f_bfiles_upload = ' <button type="text" id="all-files-upload" class="all-files-upload button i-upload">' . t('Быстрая загрузка') . '</button>';

// форма вынесена в отдельный файл, поскольку она одна и таже для new и edit
// из неё получается $do и $posle
require($MSO->config['admin_plugins_dir'] . 'admin_page/form.php');

$f_content = htmlspecialchars($f_content);

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