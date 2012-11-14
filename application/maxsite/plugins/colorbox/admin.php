<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$CI = & get_instance();
	$options_key = 'plugin_colorbox';
	
	if ( $post = mso_check_post(array('f_session_id','f_submit','f_style','f_effect','f_size','f_width','f_height','f_slideshowspeed')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['style'] = $post['f_style'];
		$options['effect'] = $post['f_effect'];
		$options['size'] = $post['f_size'];
		$options['width'] = $post['f_width'];
		$options['height'] = $post['f_height'];
		$options['slideshowspeed'] = $post['f_slideshowspeed'];
	
		mso_add_option($options_key, $options, 'plugins' );
		echo '<div class="update">Обновлено!</div>';
	}
?>
<h1>Настройка ColorBox</h1>
<p class="info">При использовании заданных размеров лайтбокса допустимы значения в процентах (%) и пикселях (px)<br />
Время перехода в слайд-шоу задаётся в миллисекундах (1 секунда = 1000 миллисекунд)</p>
<?php
	$CI = & get_instance();
	$CI->load->helper('form');

		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['style']) ) $options['style'] = '1';
		if ( !isset($options['effect']) ) $options['effect'] = 'elastic';
		if ( !isset($options['size']) ) $options['size'] = '0';
		if ( !isset($options['width']) ) $options['width'] = '75%';
		if ( !isset($options['height']) ) $options['height'] = '75%';
		if ( !isset($options['slideshowspeed']) ) $options['slideshowspeed'] = '2500';

		$form = '';		
		$form .= '<p style="padding-bottom:5px"><strong>Внешний вид: </strong>'. 
				form_dropdown('f_style', 
					array( '1' => 'Стиль #1',
							'2' => 'Стиль #2',
							'3' => 'Стиль #3',
							'4' => 'Стиль #4',
							'5' => 'Стиль #5'), 
					$options['style']).'</p>';

		$form .= '<p style="padding-bottom:15px;border-bottom:1px #CCC solid"><strong>Эффект перехода: </strong>'. 
				form_dropdown('f_effect', 
					array('elastic' => 'Стандартный (elastic)',
						'fade' => 'Плавное появление (fade)',
						'none' => 'Без эффекта (none)'), 
					$options['effect']).'</p>';
				
		$form .= '<p style="padding:15px 0 5px"><strong>Использовать заданные размеры: </strong>'. 
				form_dropdown('f_size', 
					array('1' => 'Да',
						  '0' => 'Нет'), 
					$options['size']).'</p>';

		$form .= '<p style="padding-bottom:5px"><strong>Ширина: </strong><input name="f_width" type="text" value="'.$options['width'].'"></p>';
		$form .= '<p style="padding-bottom:15px;border-bottom:1px #CCC solid"><strong>Высота: </strong><input name="f_height" type="text" value="'.$options['height'].'"></p>';

		$form .= '<p style="padding:15px 0 0"><strong>Время перехода в слайд-шоу: </strong><input name="f_slideshowspeed" type="text" value="'.$options['slideshowspeed'].'"></p>';

		echo '<form action="" method="post">'.mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value=" Сохранить изменения " style="margin:15px 0 5px" />';
		echo '</form>';
?>