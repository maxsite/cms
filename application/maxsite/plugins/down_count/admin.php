<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	
	$options_key = 'plugin_down_count';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_file', 'f_prefix', 'f_format')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['file'] = $post['f_file'];
		$options['prefix'] = $post['f_prefix'];
		$options['format'] = $post['f_format'];
		$options['referer'] = isset( $post['f_referer']) ? 1 : 0;
		$options['real_title'] = isset( $post['f_real_title']) ? 1 : 0;
	
		mso_add_option($options_key, $options, 'plugins');
		
		echo '<div class="update">' . t('Обновлено!') . '</div>';
	}
	
?>
<h1><?= t('Счетчик переходов') ?></h1>
<p class="info"><?= t('С помощью этого плагина вы можете подсчитывать количество скачиваний или переходов по ссылке. Для использования плагина обрамите нужную ссылку в код [dc]ваша ссылка[/dc]') ?></p>

<?php
		
		$options = mso_get_option($options_key, 'plugins', array());
		
		if ( !isset($options['file']) ) $options['file'] = 'dc.dat'; // путь к файлу данных
		if ( !isset($options['prefix']) ) $options['prefix'] = 'dc'; // префикса
		if ( !isset($options['format']) ) $options['format'] = ' <sup title="' . t('Количество переходов') . '">%COUNT%</sup>'; // формат количества
		if ( !isset($options['referer']) ) $options['referer'] = 1; // запретить скачку с чужого сайта
		if ( !isset($options['real_title']) ) $options['real_title'] = 1; // выводить в title реальный адрес

		$form = '';

		$form .= '<h2>' . t('Настройки') . '</h2>';
		
		$form .= '<p><strong>' . t('Файл для хранения количества скачиваний:') . '</strong><br>' . 
			getinfo('uploads_dir') . ' <input name="f_file" type="text" value="' . $options['file'] . '"></p>';
			
		$form .= '<p><strong>' . t('Префикс URL:') . '</strong> ' . getinfo('siteurl') . ' <input name="f_prefix" type="text" value="' . $options['prefix'] . '">/' . t('ссылка') . '</p>';
		
		$form .= '<p><strong>Формат количества переходов:</strong> <input name="f_format" style="width: 400px;" type="text" value="' . htmlspecialchars($options['format']) . '"></p>';
		
		
		$chk = $options['referer'] ? ' checked="checked"  ' : '';
		$form .= '<p><label><input name="f_referer" type="checkbox" ' . $chk . '> <strong>' . t('Запретить переходы с чужих сайтов') . '</strong></label></p>';

		$chk = $options['real_title'] ? ' checked="checked"  ' : '';
		$form .= '<p><label><input name="f_real_title" type="checkbox" ' . $chk . '> <strong>' . t('Выводить в title реальный адрес') . '</strong></label></p>';
		
		echo '<form method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<button type="submit" name="f_submit" style="margin: 25px 0 5px 0;">' . t('Сохранить изменения') . '</button>';
		echo '</form>';
		
		// выведем ниже формы всю статистику

		// массив данных: url => array ( count=>77 )
		$data = down_count_get_data();
		
		if ($data)
		{
			$CI->load->library('table');
			$tmpl = array (
					'table_open'		  => '<table class="page tablesorter">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
					);
			$CI->table->set_template($tmpl);
			$CI->table->set_heading('URL', t('переходов'));

			echo '<br><h2>' . t('Статистика переходов') . '</h2>';
			foreach($data as $url => $aaa)
			{
				$CI->table->add_row(
									htmlspecialchars(mso_xss_clean($url)),
									$data[$url]['count']
									);
			}
			echo $CI->table->generate();
			
			
			echo mso_load_jquery('jquery.tablesorter.js') . '
			<script>
			$(function() {
				$("table.tablesorter").tablesorter();
			});
			</script>';				
			
			
		}

?>