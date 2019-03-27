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
		
		mso_add_option('sidebars-' . mso_slug($sidebar), $option, 'sidebars'); // добавили
	}
	
	echo '<div class="update">' . t('Обновлено!') . '</div>';
	
	// pr($sidebars);

	// поскольку мы обновили опции, то обновляем и их кэш
	mso_refresh_options();
}
?>

<h1><?= t('Настройки сайдбаров') ?></h1>
<p><?= t('Добавьте в сайдбары необходимые виджеты. Каждый виджет в одной строчке. Виджеты будут отображаться в указанном вами порядке. Если указанные виджеты не существуют, то они будут проигнорированы при выводе в сайдбаре.') ?></p>
<p><?= t('Если вы указываете несколько одинаковых виджетов, то через пробел указывайте их номера (имена). Для виджета можно указать условия отображения и дополнительный CSS-класс. См. <a href="http://max-3000.com/book/sidebars" target="_blank">документацию</a>.') ?></p>

<?php

	$error = '';
	$all_name_sidebars = array(); // все сайдбары
	
	$form = '';
	$select = ''; // option для select
	
	// сортируем по титлу
	$all_w = $MSO->widgets;
	asort($all_w);
	
	if ($all_w) // есть виджеты
	{
		$form .= '
<script>
$(function(){
	function addText(t, tarea){
		var elem = document.getElementById(tarea);
		elem.value = elem.value + "\n" + t;
		L = $(elem).val();
		$(elem).attr("rows", L.split("\n").length + 1);
	}
	$("select.all_widgets").change(function(){
		f = $(this).val();
		if (f)
		{
			s = $(this).attr("data-id-sb");
			addText(f, "f_sidebars[" + s + "]");
		}
	});
});
</script>';

		// формируем select со списком виджетов
		foreach ($all_w as $function => $title)
		{
			$select .= '<option value="' . $function . '">' . $title . ' (' . $function . ')' . '</option>';
		}
	}
	else 
	{
		$error .= '<div class="error">' . t('К сожалению у вас нет доступных виджетов. Обычно они определяются в плагинах.') . '</div>';
	}
	
	
	if ($MSO->sidebars) // есть сайдбары
	{ 
	
		foreach ($MSO->sidebars as $name => $sidebar)
		{
			// у сайддара уже может быть определены виджеты - считываем их из опций
			// потому что мы их будем там хранить
			// это простой массив с именами виджетов
			$options = mso_get_option('sidebars-' . mso_slug($name), 'sidebars', array());
			$count_rows = count($options) + 1;
			if ($count_rows < 2) $count_rows = 2;
			$options = implode("\n", $options); // разделим по строкам 

			$form .= '<h2>' . $sidebar['title'] . '</h2>'
					. '<p class="add-widget">Добавить виджет <select class="all_widgets" data-id-sb="' . $name . '"><option value="">' . t('—') . '</option>' . $select . '</select></p>';
			
			$form .= '<textarea id="f_sidebars[' . $name  . ']" name="f_sidebars[' . $name . ']" rows="' . $count_rows . '">';
			$form .= htmlspecialchars($options);
			$form .= '</textarea>';
			
			$all_name_sidebars[$name] = $sidebar['title'];
		}
		
		$form .= '<p><button type="submit" name="f_submit" class="button i-save">' . t('Сохранить изменения') . '</button></p>';
	}
	else 
	{
		$error .= '<div class="error">' . t('Сайдбары не определены. Обычно они регистрируются в файле <b>functions.php</b> вашего шаблона. Например:') . ' <br><b>mso_register_sidebar(\'1\', \'' . t('Первый сайдбар') . '\');</b></div>';
	}
	
	
	if (!$error)
	{
		// добавляем форму, а также текущую сессию
		echo '<div class="sidebars"><form method="post">'
			. mso_form_session('f_session_id')
			. $form
			. '</form></div>';
	}
	else
	{
		echo $error;
	}

# end of file