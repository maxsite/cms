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
		$options['height_img'] = (int) $post['f_height_img'];
		
		if ($options['height_img'] < 1 or $options['height_img'] > 500) $options['height_img'] = 125;
		
		// переделаем массив в [default] => Default из info.php [name]
		foreach ($options['templates'] as $dir=>$val)
		{
			if (file_exists( $templates_dir . $dir . '/info.php' ))
			{
				require($templates_dir . $dir . '/info.php');
				$options['templates'][$dir] = $info['name'];
			}
		}
		// pr($options['templates']);
		mso_add_option($options_key, $options, 'plugins' );
		echo '<div class="update">' . t('Обновлено!') . '</div>';
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
				'top': e.pageY - ($('div.tooltip').height())+120,
				'left': e.pageX + 100
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
	width: 260px;
	height: 235px;
	color: #333333;
	padding: 3px 0;
	background: #FFFFFF;
	border: 1px solid silver;
	text-align: center;
	-webkit-box-shadow: #606060 0px 0px 7px; -moz-box-shadow: #606060 0px 0px 7px; box-shadow: #606060 0px 0px 7px; behavior: url(/PIE.php);
	-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; behavior: url(/PIE.php);
}
.tooltip span{
	display: block;
	font-weight: bold;
	color: vavy;
	font-size: 1.5em;
}
</style>

<h1><?= t('Theme switch') ?></h1>
<p class="info"><?= t('Плагин позволяет переключать шаблоны сайта вашим посетителям. Отметьте те шаблоны, которые могут переключаться. Форма переключения настраивается в виджетах.') ?></p>

<?php
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['templates']) ) $options['templates'] = array();
		
		if ( !isset($options['show_panel']) ) $options['show_panel'] = false;
		
		$options['height_img'] = isset($options['height_img']) ? $options['height_img'] : 125;
		
		$form = '';
		
		if ($options['show_panel']) $checked = ' checked="checked"';
			else $checked = '';
		$form .= '<p><label><input type="checkbox" name="f_show_panel"' . $checked . '> ' . t('Отображать верхнюю панель') . '</label></p>';
		
		$form .= '<p><label>' 
				. t('Высота изображений в верхней панели')
				. ' <input type="text" name="f_height_img" size="4" value="' . $options['height_img'] . '"> ' 
				. ' px</label></p><hr>';
		

		// получаем все шаблоны на диске
		// выводим их списком с чекбоксами
		// в опциях сохраняем только те, которые отмечены
		
		$dirs = directory_map($templates_dir, true);

		foreach ($dirs as $dir)
		{
			// обязательный файл index.php
			if (file_exists( $templates_dir . $dir . '/index.php' ))
			{
				if (isset($options['templates'][$dir])) $checked = ' checked="checked"';
					else $checked = '';
				
				if (file_exists( $templates_dir . $dir . '/info.php' ))
				{
					require($templates_dir . $dir . '/info.php');
					$iname = $info['name'];
				}
				else $iname = 'not info.php!';
				
				if (file_exists( $templates_dir . $dir . '/screenshot.jpg' ))
				{
					$scr = '<img src=' . getinfo('templates_url') . $dir . '/screenshot.jpg>';
				}
				else
					$scr = '';
				
				
				$form .= '<p><label rel="tooltip" content="<span>' . $iname . '</span><br>' . $scr . '"><input type="checkbox" name="f_templates[' . $dir . ']"' . $checked . '> ' . $iname . ' (' . $dir . ')</label></p>';
			}
		}

		echo '<form method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения') . '" style="margin: 25px 0 5px 0;">';
		echo '</form>';

?>