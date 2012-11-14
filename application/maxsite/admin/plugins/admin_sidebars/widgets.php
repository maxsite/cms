<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
	
	$CI = & get_instance();
	
	// проверяем входящие данные
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_update_widgets')) )
	{
		# защита рефера
		mso_checkreferer();
		
		# защита сессии - если нужно убрать коммент
		// if ($MSO->data['session']['session_id'] != $fo_session_id) mso_redirect('loginform');
		
		$widgets = $post['f_update_widgets'];
		
		# перебираем полученные виджеты
		foreach ($widgets as $widget=>$val)
		{
			// [calendar_widget-1] => 
			// [randomtext_widget-3] => 
			
			// разбиваем полученное значение на функцию и номер - они указываются через -
			$arr_w = explode('--', $widget); // в массив

			if ( sizeof($arr_w) > 1 ) // два или больше элементов
			{
				$widget = trim( $arr_w[0] ); // первый - функция
				$num = mso_slug( trim( $arr_w[1] ) ); // второй - номер виджета
				$num = str_replace('--', '-', $num); 
			}
			else 
			{
				$num = 0; // номер виджета не указан, значит 0
			}
			
			$func = $widget . '_update'; // функция именуется по этому принципу
			//$num = (int) $num;

			if ( function_exists($func) ) $func($num);
		}
		
		echo '<div class="update">' . t('Обновлено!') . '</div>';
	}
	
?>

<h1><?= t('Настройки виджетов') ?></h1>
<p class="info"><?= t('Здесь вы можете настроить виджеты. Для открытия настроек виджета, кликните на его заголовок.') ?></p>

<?php

	echo mso_load_jquery('jquery.cookie.js');
	echo mso_load_jquery('showhide.jquery.js');
	
	echo '
<script>

$(function () {

	$("div.admin_widgets").hide();
	
	$.showHide({
		blockElem: ".admin-edit-widgets-form",
		blockinElem: ".admin_widgets",
		clickElem: ".link",
		cookieName: "admin-sidebars-widgets",
	});
});

</script>
';
	


	$error = '';
	
	if ($MSO->sidebars)
	{ // есть сайдбары
		$form = '';
		foreach ($MSO->sidebars as $name => $sidebar)
		{
			// у сайддара уже может быть определены виджеты - считываем их из опций
			// потому что мы их будем там хранить
			// это простой массив с именами виджетов
			$widgets = mso_get_option('sidebars-' . mso_slug($name), 'sidebars', array());
			
			$form .= '<div class="admin-edit-widgets">';
			
			$form .= '<h2>' . $sidebar['title'] . ':</h2>';
			
			foreach ($widgets as $widget)
			{
				// имя виджета может содержать номер через пробел
				// проверим это
				$arr_w = explode(' ', $widget); // в массив
				if ( sizeof($arr_w) > 1 ) // два или больше элементов
				{
					$widget = trim( $arr_w[0] ); // первый - функция
					$num_orig = trim( $arr_w[1] ); // второй - номер виджета
					
					$num = mso_slug($num_orig);
					$num = str_replace('--', '-', $num); 
					
					$num_orig = str_replace('_', ' ', $num_orig); // заменим _ на пробел
					
				}
				else 
				{
					$num = 0; // номер виджета не указан, значит 0
				}
				
				$func = $widget . '_form'; // функция вывода формы
				
				if ( function_exists($func) ) 
				{
					$form .= '<div class="admin-edit-widgets-form">';
					
					$d_id = 'd_' . $func . '_' . $num; 
					$a_js = '<a href="#" class="link">';
					
					if ($num) $form .= '<h3>' . $a_js . $MSO->widgets[$widget] . ' (' . $num_orig . ')</a></h3>';
						else $form .= '<h3>' . $a_js . $MSO->widgets[$widget] . '</a></h3>';
					
					$form .= '<div class="admin_widgets" id="' . $d_id . '">';
					
					$res = $func($num);
					
					if ($res) $form .= $res;
						else $form .= '<p>' . t('Виджет не содержит настроек') . '</p>';
					
					$form .= '<input type="hidden" name="f_update_widgets[' . $widget . '--' . $num . ']" value="">';
					
					$form .= '</div>' . NR; // div id=
					
					$form .= '</div>' . NR . NR;
				}
			}
			
			$form .= '</div>' . NR;
		}
	}
	else 
	{
		$error .= '<div class="error">' . t('Сайдбары не определены. Обычно они регистрируются в файле <strong>functions.php</strong> вашего шаблона. Например:') . ' <br><strong>mso_register_sidebar(\'1\', \'' . t('Первый сайдбар') . '\');</strong></div>';
	}
	
	if (!$error)
	{
		// добавляем форму, а также текущую сессию
		echo '<form method="post" class="fform admin_widgets">' . mso_form_session('f_session_id');
		echo $form;
		echo '<p class="br"><input type="submit" name="f_submit" value="' . t('Сохранить изменения') . '"></p>';
		echo '</form>';
	}
	else
	{
		echo $error;
	}

?>