<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	$CI = & get_instance();
	
//	if ($_POST) _pr($_POST);
	
	if ( $post = mso_check_post(array('f_session_id')) )
	{
		mso_checkreferer();
		
		
		// есть ли выбранные пункты?
		if (isset($post['f_check_submit']))
		{
			// определяем действие
			if (isset($post['f_activate_submit'])) $act = 'activate';
				elseif (isset($post['f_deactivate_submit'])) $act = 'deactivate';
				elseif (isset($post['f_uninstall_submit'])) $act = 'uninstall';
				else $act = false;
			
			if ($act)
			{
				$out = t('Выполнено:') . ' ';
				foreach ($post['f_check_submit'] as $f_name=>$val)
				{
					if ($act == 'activate') mso_plugin_activate($f_name); # активация плагина
					elseif ($act == 'deactivate') mso_plugin_deactivate($f_name); # деактивация плагина
					elseif ($act == 'uninstall') mso_plugin_uninstall($f_name); # унинстал 
					$out .= ' &#149; ' . $f_name;
				}
				mso_redirect('admin/plugins');
			}
			else
				echo '<div class="error">' . t('Ошибка обновления') . '</div>';
		}
		else
			echo '<div class="error">' . t('Отметьте необходимые плагины') . '</div>';
	}

?>
	<h1><?= t('Плагины') ?></h1>
	<p class="info"><?= t('Плагины расширяют стандартные возможности сайта. Здесь вы можете включить или отключить плагины. Если вы деинсталируете плагин, то это удаляет его настройки, что позволяет избежать «замусоривания» базы данных.') ?></p>
	<p class="info"><?= t('Другие плагины вы можете найти на форуме <a href="http://forum.max-3000.com/viewforum.php?f=6">MaxSite CMS</a> или в <a href="http://alexanderschilling.net/plugins">каталоге плагинов</a>.') ?></p>

<?php
	// для вывода будем использовать html-таблицу
	$CI->load->library('table');
	
	$tmpl = array (
					'table_open'		  => '<table class="page tablesorter" border="0" width="99%" id="pagetable">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );

	$CI->table->set_template($tmpl); // шаблон таблицы
	
	// заголовки
	$CI->table->set_heading(' ', t('Каталог'), ' ', t('Название'), t('Версия'), t('Автор'), t('Описание'));
	
	// проходимся по каталогу плагинов и выводим информацию о них
	
	
	// выводим две таблицы - верхняя - активные плагины, внизу неактивные
	// поэтому загоняем цикл вывода в отдельную функцию 
	
	function _create_table($active_plugins = true)
	{
		global $MSO;
		
		$CI = & get_instance();
		
		$CI->load->helper('directory');
		
		$plugins_dir = $MSO->config['plugins_dir'];
		
		// все каталоги в массиве $dirs
		$dirs = directory_map($plugins_dir, true);
		sort($dirs);
		
		# пересортируем элементы масива так чтобы активные плагины из 
		# $MSO->active_plugins оказались вверху
		
		$dirs = array_unique(array_merge($MSO->active_plugins, $dirs));
		
		$flag_present_plugins = false; // признак, что пустая таблица
		
		foreach ($dirs as $dir)
		{
			if (!is_dir($plugins_dir . $dir)) continue; // если не каталог ничего не делаем
			
			if ($active_plugins and !in_array($dir, $MSO->active_plugins)) continue;
			if (!$active_plugins and in_array($dir, $MSO->active_plugins)) continue;
			
			$info_f = $plugins_dir . $dir . '/info.php';
			
			if (file_exists($info_f))
			{
				require($info_f);
				
				if (isset( $info )) 
				{
					/* 
						[name] => Demo
						[description] => Демонстрационный плагин
						[version] => 1.0
						[author] => Максим
						[plugin_url] => http://maxsite.org/
						[author_url] => http://maxsite.org/
						[group] => template
						[help] => ссылка на хелп
						'options_url' => getinfo('site_admin_url') . 'plugin_XXX', // ссылка на страницу опций
					*/
					
					$name = isset($info['name']) ? mso_strip($info['name']) : '';
					$version = isset($info['version']) ? $info['version'] : '';
					$description = isset($info['description']) ? $info['description'] : '';
					$author = isset($info['author']) ? mso_strip($info['author']) : '';
					$author_url = isset($info['author_url']) ? $info['author_url'] : false;
					$plugin_url = isset($info['plugin_url']) ? $info['plugin_url'] : false;
					$help = isset($info['help']) ? $info['help'] : false;
					$options_url = isset($info['options_url']) ? $info['options_url'] : false;
					
					if ($author_url) $author = '<a href="' . $author_url . '">' . $author . '</a>';
					if ($plugin_url) $name = '<a href="' . $plugin_url . '">' . $name . '</a>';
					
					
					$act = '<input type="checkbox" name="f_check_submit[' . $dir . ']" id="f_check_submit_' . $dir . '">';
					
					$dir0 = $dir;
					
					
					if (in_array($dir, $MSO->active_plugins)) 
					{
						// $status = '<span style="color: green;"><strong>' . t('вкл') . '</strong></span>';
						
						if (function_exists($dir . '_mso_options'))
							// есть опции
							$status = '<a title="' . t('Настройки плагина') . '" href="' . getinfo('site_admin_url') . 'plugin_options/' . $dir . '">' . t('опции') . '</a>';
						else 
							$status = ' ';
						
						$dir = '<label for="f_check_submit_' . $dir . '"><span class="plugin_on">' . $dir . '</span></label>';
						
						if ($options_url) $status .= ' <a href="' . $options_url . '" title="' . t('Настройки плагина') . '">' . t('опции') . '</a>';
						
					}
					else
					{
						
						$dir = '<label for="f_check_submit_' . $dir . '">' . $dir . '</label>';
						
						// формируем ссылку для включения плагина
						$status = '<span class="gray"><a href="" onclick="' . "
							$('#f_check_submit_" . $dir0 . "').attr('checked', 'checked'); 
							$('form').prepend('<input type=hidden name=f_activate_submit value=1>');
							$('form').submit(); 
							return false;
						" . '">' . t('включить') . '</a></span>';
						
						
						$description = '<span class="gray">' . $description . '</span>';
						$dir = '<span class="gray">' . $dir . '</span>';
						$version = '<span class="gray">' . $version . '</span>';
						$name = '<span class="gray">' . $name . '</span>';
						$author = '<span class="gray">' . $author . '</span>';
						
					}
					
					if ($help) $status .= ' <a href="' . $help . '" target="_blank" title="' . t('Помощь по плагину') . '">(?)</a>';
					
					$CI->table->add_row($act, $dir, $status, $name, $version, $author, $description);
					$flag_present_plugins = true;
				}
			}
		}
		
		return $flag_present_plugins;
	}
	
	$CI->table->set_caption('<h2>'.t('Активные плагины') . '</h2>');
	$flag_present_plugins = _create_table(true);
	
	if ($flag_present_plugins) 
	{
		$table1 = $CI->table->generate(); // вывод подготовленной таблицы
	
		# добавим строчку для дополнительного действия
		$table1 .= '<p>
					<input type="submit" name="f_deactivate_submit" value="&nbsp;- &nbsp;&nbsp;' . t('Выключить') . '">
					<input type="submit" name="f_uninstall_submit" value="&nbsp;x&nbsp;&nbsp;' . t('Деинсталировать') . '">
					</p>';
	
	}
	else $table1 = '';
	
	// вторая таблица
	$tmpl = array (
					'table_open'		  => '<table class="page tablesorter inactive-plugins" id="pagetable2">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );
			  
	$CI->table->clear();
	$CI->table->set_template($tmpl); // шаблон таблицы
	$CI->table->set_caption('<h2>' . t('Неактивные плагины') . '</h2>');
	
	// заголовки
	$CI->table->set_heading(' ', t('Каталог'), ' ', t('Название'), t('Версия'), t('Автор'), t('Описание'));
	$flag_present_plugins = _create_table(false);
	
	if ($flag_present_plugins) 
	{
		$table2 = $CI->table->generate(); // вывод подготовленной таблицы
	
		# добавим строчку для дополнительного действия
		$table2 .= '<p><input type="submit" name="f_activate_submit" value="&nbsp;+ &nbsp;&nbsp;' . t('Включить') . '"></p>';
	}
	else $table2 = '';
		
	
	echo mso_load_jquery('jquery.tablesorter.js') . '
		<script>
		$(function() {
			$("table.tablesorter").tablesorter( {headers: { 0: {sorter: false}, 2: {sorter: false} }});
		});
		</script>';
		
	// добавляем форму, а также текущую сессию
	echo '<form method="post">' . mso_form_session('f_session_id');
	echo $table1 . $table2; // вывод таблиц
	echo '</form>';
	
?>