<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	$CI = & get_instance();
	
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

<?php
	// для вывода будем использовать html-таблицу
	$CI->load->library('table');
	
	$tmpl = array (
				'table_open'		  => '<table class="page active-plugins" id="pagetable">',
				'row_alt_start'		  => '<tr>',
				'cell_alt_start'	  => '<td>',
			);

	$CI->table->set_template($tmpl); // шаблон таблицы
	
	// заголовки
	$CI->table->set_heading(' ', t('Каталог'), ' ', t('Название'), t('Описание'));
	
	// проходимся по каталогу плагинов и выводим информацию о них
	// выводим две таблицы - верхняя - активные плагины, внизу неактивные
	// поэтому загоняем цикл вывода в отдельную функцию _create_table
	
	$flag_present_plugins = _create_table(true, $opt_url);
	
	if ($flag_present_plugins) 
	{
		$table1 = $CI->table->generate(); // вывод подготовленной таблицы
	
		# добавим строчку для дополнительного действия
		$table1 .= '<p><button type="submit" name="f_deactivate_submit" class="button i-chain-broken">' . t('Выключить') . '</button> <button type="submit" name="f_uninstall_submit" class="button i-remove">' . t('Деинсталировать') . '</button></p>';
	}
	else $table1 = '';
	
	// вторая таблица
	$tmpl = array (
				'table_open'		  => '<table class="page inactive-plugins" id="pagetable2">',
				'row_alt_start'		  => '<tr>',
				'cell_alt_start'	  => '<td>',
			);
			  
	$CI->table->clear();
	$CI->table->set_template($tmpl); // шаблон таблицы
	
	// заголовки
	$CI->table->set_heading(' ', t('Каталог'), ' ', t('Название'), t('Описание'));
	$flag_present_plugins = _create_table(false, $temp);
	
	if ($flag_present_plugins) 
	{
		$table2 = $CI->table->generate(); // вывод подготовленной таблицы
	
		// добавим строчку для дополнительного действия
		$table2 .= '<p><button type="submit" name="f_activate_submit" class="button i-chain">' . t('Включить') . '</button></p>';
	}
	else 
		$table2 = '';	

	echo mso_load_jquery('jquery.cookie.js');
	echo mso_load_script(getinfo('plugins_url'). 'tabs/tabs.js');
	
	echo '<form method="post">' . mso_form_session('f_session_id') . '
<div class="mso-tabs_widget mso-tabs_widget_000"><div class="mso-tabs">
<ul class="mso-tabs-nav">
<li class="mso-tabs-elem mso-tabs-current active-plugins"><span class="i-bell-o">' . t('Активные плагины') . '</span></li>
<li class="mso-tabs-elem inactive-plugins"><span class="i-bell-slash-o">' . t('Неактивные плагины') . '</span></li>
<li class="mso-tabs-elem options-plugins"><span class="i-cog">' . t('Опции плагинов') . '</span></li>
</ul>
<div class="mso-tabs-box mso-tabs-visible">' . $table1 . '</div>
<div class="mso-tabs-box">' . $table2 . '</div>	
<div class="mso-tabs-box tab-options-plugins"><p class="nav">' . trim(implode(' ', $opt_url)) .'</p></div>	
</div></div></form>';


// формирование таблиц
function _create_table($active_plugins = true, &$opt_url = [])
{
	global $MSO;
	
	$CI = & get_instance();
	
	$CI->load->helper('directory');
	
	$plugins_dir = $MSO->config['plugins_dir'];
	
	$opt_url = array();
	
	// все каталоги в массиве $dirs
	$dirs = directory_map($plugins_dir, true);
	sort($dirs);
	
	// пересортируем элементы масива так чтобы активные плагины из 
	// $MSO->active_plugins оказались вверху
	
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
				$name = $name_plu = isset($info['name']) ? mso_strip($info['name']) : '';
				$version = isset($info['version']) ? $info['version'] : '';
				$description = isset($info['description']) ? $info['description'] : '';
				$author = isset($info['author']) ? mso_strip($info['author']) : '';
				$author_url = isset($info['author_url']) ? $info['author_url'] : false;
				$plugin_url = isset($info['plugin_url']) ? $info['plugin_url'] : false;
				$help = isset($info['help']) ? $info['help'] : false;
				$options_url = isset($info['options_url']) ? $info['options_url'] : false;
				$group = isset($info['group']) ? $info['group'] : '';
				$editors = isset($info['editors']) ? ' + ' . $info['editors'] : '';
				
				if ($author_url) $author = '<a href="' . $author_url . '">' . $author . '</a>';
				if ($plugin_url) $name = '<a href="' . $plugin_url . '">' . $name . '</a>';
				
				$act = '<input type="checkbox" name="f_check_submit[' . $dir . ']" id="f_check_submit_' . $dir . '">';
				
				$dir0 = $dir;
				
				if (in_array($dir, $MSO->active_plugins)) 
				{
					if (function_exists($dir . '_mso_options'))
					{
						// есть опции
						$status = '<a class="t120 i-cogs" title="' . t('Настройки плагина') . '" href="' . getinfo('site_admin_url') . 'plugin_options/' . $dir . '"></a>';
						
						$opt_url[] = '<a href="' . getinfo('site_admin_url') . 'plugin_options/' . $dir . '" title="' .htmlspecialchars($name_plu) . '">' . $dir . '</a>  ';
					}
					else
					{
						$status = ' ';
					}
					
					$dir = '<label for="f_check_submit_' . $dir . '"><span class="plugin_on">' . $dir . '</span></label>';
					
					if ($options_url) 
					{	
						$status .= ' <a class="t120 i-cogs" href="' . $options_url . '" title="' . t('Настройки плагина') . '"></a>';
						
						$opt_url[] = '<a href="' . $options_url . '" title="' .htmlspecialchars($name_plu) . '">' . $dir0 . '</a> ';
					}
				}
				else
				{
					$dir = '<label for="f_check_submit_' . $dir . '">' . $dir . '</label>';
					
					// формируем ссылку для включения плагина
					$status = '<a class="i-chain-broken" title="' . t('Включить плагин') . '" href="" onclick="' . "$('#f_check_submit_" . $dir0 . "').attr('checked', 'checked'); $('form').prepend('<input type=hidden name=f_activate_submit value=1>'); $('form').submit(); return false; " . '"></a>';
				}
				
				if ($group) $group = '<div class="t80">' . $group . '</div>';
				
				if ($help) $status .= ' <a class="t120 i-info-circle" href="' . $help . '" target="_blank" title="' . t('Помощь по плагину') . '"></a>';
				
				$CI->table->add_row($act, 
					array('data' => $dir . ' <sup title="' . t('Версия') . '">' . $version . '</sup>', 'class' => 't-nowrap' ), 
					$status, 
					array('data' => $name . $group), 
					array('data' => $description . '<div>' . t('Автор') . ': ' . $author . $editors . '</div>', 'class' => 't90'));
					
				$flag_present_plugins = true;
			}
		}
	}
	
	return $flag_present_plugins;
}

# end of file