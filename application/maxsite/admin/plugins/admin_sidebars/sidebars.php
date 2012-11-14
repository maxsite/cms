<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
	
	$CI = & get_instance();
	
	// проверяем входящие данные
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_sidebars')) )
	{
		# защита рефера
		mso_checkreferer();
		
		# защита сессии - если нужно убрать коммент
		// if ($MSO->data['session']['session_id'] != $fo_session_id) mso_redirect('loginform');
		
		$sidebars = $post['f_sidebars'];
		
		# перебираем поулченные сайдбары
		foreach ($sidebars as $sidebar => $widgets)
		{
			# готовим опцию для каждого
			$option = array();
			
			$widgets = explode("\n", $widgets); // в массив, потому что указано через Enter
			// проверяем виджеты
			foreach ($widgets as $widget)
			{
				$widget = trim($widget); // удлаим лишнее
				if ($widget) $option[] = $widget; // добавим в опцию
			}
			
			// pr($option);
			mso_add_option('sidebars-' . mso_slug($sidebar), $option, 'sidebars'); // добавили
		}
		
		echo '<div class="update">' . t('Обновлено!') . '</div>';
		
		// pr($sidebars);

		// поскольку мы обновили опции, то обновляем и их кэш
		mso_refresh_options();
	}
?>

<h1><?= t('Настройки сайдбаров') ?></h1>
<p class="info"><?= t('Добавьте в сайдбары необходимые виджеты. Каждый виджет в одной строчке. Виджеты будут отображаться в указанном вами порядке. Если указанные виджеты не существуют, то они будут проигнорированы при выводе в сайдбаре.') ?></p>
<p class="info"><?= t('Если вы указываете несколько одинаковых виджетов, то через пробел указывайте их номера.') ?></p>
<p class="info"><?= t('Для виджета можно указать <a href="http://max-3000.com/page/uslovija-otobrazhenija-vidzheta" target="_blank">условия отображения</a>.') ?></p>


<?php

	// mso_hook('widgets_show_form');
	// pr($MSO->sidebars);
	
	$error = '';
	$all_name_sidebars = array(); // все сайдбары
	
	$form = '';
	
	if ($MSO->sidebars)
	{ // есть сайдбары
	
		foreach ($MSO->sidebars as $name => $sidebar)
		{
			// у сайддара уже может быть определены виджеты - считываем их из опций
			// потому что мы их будем там хранить
			// это простой массив с именами виджетов
			$options = mso_get_option('sidebars-' . mso_slug($name), 'sidebars', array());
			$count_rows = count($options) + 1;
			if ($count_rows < 5) $count_rows = 5;
			$options = implode("\n", $options); // разделим по строкам 

			$form .= '<h2>' . $sidebar['title'] . ':</h2>';
			$form .= '<textarea class="admin_sidebars" id="f_sidebars[' . $name  . ']" name="f_sidebars[' . $name . ']" rows="' . $count_rows . '">';
			$form .= htmlspecialchars($options);
			$form .= '</textarea>';
			$all_name_sidebars[$name] = $sidebar['title'];
		}
	}
	else 
	{
		$error .= '<div class="error">' . t('Сайдбары не определены. Обычно они регистрируются в файле <b>functions.php</b> вашего шаблона. Например:') . ' <br><b>mso_register_sidebar(\'1\', \'' . t('Первый сайдбар') . '\');</b></div>';
	}
	
	
	//pr($MSO->widgets);
	
	// сортируем по титлу
	$all_w = $MSO->widgets;
	asort($all_w);
	
	if ($all_w)
	{ // есть виджеты
	
		$form .= '
		<script>
			function addText(t, tarea)
			{
				var elem = document.getElementById(tarea);
				elem.value = elem.value + "\n" + t;
			}
		</script>' . NR;

		$form .= '<p class="br"><input type="submit" name="f_submit" value="' . t('Сохранить изменения') . '" ></p>';
		
		$form .= '<h2>' . t('Доступные виджеты (добавляйте только функцию)') . '</h2><table class="widgets-allow">';
		foreach ($all_w as $function => $title)
		{
			// $form .= '<li><b>' . $function . '</b> (' . $title . ')</li>';
			$form .= '<tr><td><strong>' . $title . '</strong>&nbsp;</td>';
			
			foreach($all_name_sidebars as $sid=>$sid_title)
			{
				$form .= '<td><input type="button" value=" ' . $sid_title . ' " title="' . t('Добавить') . ' «' . $function . '» ' . t('в') . ' «' . $sid_title 
						. '»" onClick="addText(\'' . $function . '\', \'f_sidebars[' . $sid . ']\') ">&nbsp;&nbsp;</td>' . NR;
			}
			$form .= '<td><em>' . $function . '</em> </td></tr>' . NR;
		}
		$form .= '</table>';
	}
	else 
	{
		$error .= '<div class="error">' . t('К сожалению у вас нет доступных виджетов. Обычно они определяются в плагинах.') . '</div>';
	}
	
	if (!$error)
	{
		// добавляем форму, а также текущую сессию
		echo '<form method="post">' . mso_form_session('f_session_id');
		echo $form;
	//	echo '<p class="br"><input type="submit" name="f_submit" value="' . t('Сохранить изменения') . '" ></p>';
		echo '</form>';
	}
	else
	{
		echo $error;
	}

?>