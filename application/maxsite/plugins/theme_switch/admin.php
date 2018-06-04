<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	
	$CI->load->helper('directory');
	
	$options_key = 'theme_switch';
	
	$templates_dir = getinfo('templates_dir');
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_templates')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['templates'] = $post['f_templates'];
		
		$options['show_panel'] = isset($post['f_show_panel']) ? 1 : 0;
		$options['only_home'] = isset($post['f_only_home']) ? 1 : 0;
		$options['show_panel_type'] = isset($post['f_show_panel_type']) ? $post['f_show_panel_type'] : 'screenshot';
		
		// переделаем массив в [default] => Default из info.php [name]
		foreach ($options['templates'] as $dir=>$val)
		{
			if (file_exists( $templates_dir . $dir . '/info.php' ))
			{
				require($templates_dir . $dir . '/info.php');
				$options['templates'][$dir] = $info['name'];
			}
		}
		
		mso_add_option($options_key, $options, 'plugins' );
		
		echo '<div class="update pos-fixed pos10-t pos0-r">' . t('Обновлено!') . '</div>';
	}
	
?>
<!-- (c) http://prootime.ru/demos/vspl/index.html -->

<script>
$(document).ready(function(){
$('[rel=tooltip]').bind('mouseover', function(){
$(this).css({'color': 'red'});
var theMessage = $(this).attr('content');
$('<div class="tooltip">' + theMessage + '</div>').appendTo('body').fadeIn('slow');
$(this).bind('mousemove', function(e){
			$('div.tooltip').css({
				'top': e.pageY - ($('div.tooltip').height()) + 120,
				'left': e.pageX + 40
			});
		});
	}).bind('mouseout', function(){
		$(this).css({'color': 'black'});
		$('div.tooltip').fadeOut('slow', function(){
			$(this).remove();
		});
	});
   });
</script>

<style>
.tooltip{
	position: absolute;
	width: 250px;
	text-align: center;
	box-shadow: #444444 5px 5px 7px;
}
.tooltip span{
	display: block;
	font-weight: bold;
	color: #ffffff;
	background: #162D39;
	font-size: 1rem;
	padding: 5px;
}
</style>

<h1><?= t('Theme switch') ?></h1>
<p class="info"><?= t('Плагин позволяет переключать шаблоны сайта вашим посетителям. Отметьте те шаблоны, которые могут переключаться. Форма переключения настраивается в виджетах.') ?></p>

<?php

	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['templates']) ) $options['templates'] = array();
	
	if ( !isset($options['show_panel']) ) $options['show_panel'] = false;
	if ( !isset($options['only_home']) ) $options['only_home'] = false;
	
	if ( !isset($options['show_panel_type']) ) $options['show_panel_type'] = 'screenshot';
	
	$form = $form1 = $checked = $checked1 = $checked2 = $checked3 = '';
	
	if ($options['show_panel']) $checked = ' checked';
	if ($options['only_home'])  $checked3 = ' checked';
	
	if ($options['show_panel_type'] == 'screenshot')
		$checked1 = ' checked';
	else
		$checked2 = ' checked';
		
	$form1 .= '<p><label><input type="checkbox" name="f_show_panel"' . $checked . '> ' . t('Отображать верхнюю панель') . '</label> &nbsp;&nbsp;<label><input type="checkbox" name="f_only_home"' . $checked3 . '> ' . t('Только на главной') . '</label></p>
	<p><label><input type="radio" name="f_show_panel_type" value="screenshot"' . $checked1 . '> ' . t('скриншотами') . '</label> &nbsp;&nbsp;<label><input type="radio" name="f_show_panel_type" value="combo"' . $checked2 . '> ' . t('выпадающим списком') . '</label></p><hr>';
	
	// получаем все шаблоны на диске
	// выводим их списком с чекбоксами
	// в опциях сохраняем только те, которые отмечены
	
	$dirs = directory_map($templates_dir, true);

	foreach ($dirs as $dir)
	{
		// обязательный файл index.php
		if (file_exists( $templates_dir . $dir . '/index.php' ))
		{
			if (isset($options['templates'][$dir])) 
				$checked = ' checked="checked"';
			else 
				$checked = '';
			
			if (file_exists( $templates_dir . $dir . '/info.php' ))
			{
				require($templates_dir . $dir . '/info.php');
				$iname = $info['name'];
			}
			else 
				$iname = 'not info.php!';
			
			if (file_exists( $templates_dir . $dir . '/screenshot.png' ))
			{
				$scr = '<img src=' . getinfo('templates_url') . $dir . '/screenshot.png>';
			}
			elseif (file_exists( $templates_dir . $dir . '/screenshot.jpg' ))
			{
				$scr = '<img src=' . getinfo('templates_url') . $dir . '/screenshot.jpg>';
			}
			else
				$scr = '';
			
			$form .= '<p class="mar5 w48 w100-tablet"><label rel="tooltip" content="<span>' . $iname . '</span>' . $scr . '"><input type="checkbox" name="f_templates[' . $dir . ']"' . $checked . '> ' . $iname . ' (' . $dir . ')</label></p>';
		}
	}

	echo '<form method="post">' . mso_form_session('f_session_id');
	echo '<div>' . $form1 . '</div>';
	echo '<div class="flex flex-wrap">' . $form . '</div>';
	echo '<button class="button i-save mar30-tb" type="submit" name="f_submit">' . t('Сохранить изменения') . '</button>';
	echo '</form>';

?>