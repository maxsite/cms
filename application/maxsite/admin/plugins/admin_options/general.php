<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

?>

<h1><?= t('Основные настройки') ?></h1>
<p class="info"><?= t('Здесь вы можете указать основные настройки сайта.') ?></p>

<?php

	function _time_zone_current_time()
	{
		return 
			  '<br>' . t('Время сервера:') . ' <strong>' . date('H:i:s Y-m-d P') . '</strong>'
			. '<br>' . t('С учётом поправки:') . ' <strong>' . mso_date_convert('H:i:s Y-m-d', date('Y-m-d H:i:s')) . '</strong>';
	}
	
	function _all_type_pages()
	{
		$CI = & get_instance();
		
		$CI->db->order_by('page_type_name', 'asc');
		$query = $CI->db->get('page_type');
		
		$out = '';
		
		foreach ($query->result_array() as $row)
		{
			$out .= '# ' . $row['page_type_name'] . '||' .   htmlspecialchars(t($row['page_type_desc']), ENT_QUOTES) . ' - ' . $row['page_type_name'];
		}
		
		return $out;
	}


	// $CI = & get_instance();
	require_once( getinfo('common_dir') . 'inifile.php' ); // функции для работы с ini-файлом
	
	// проверяем входящие данные
	if (mso_check_post_ini()) 
	{
		mso_redirect('admin/options');
	}
	
	$all = mso_get_ini_file( $MSO->config['admin_plugins_dir'] . 'admin_options/general.ini');
	echo mso_view_ini($all); // вывод таблицы ini 

# end of file