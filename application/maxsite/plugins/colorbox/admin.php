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
<h1><?= t('Настройка ColorBox') ?></h1>
<p class="info"><?= t('При использовании заданных размеров лайтбокса допустимы значения в процентах (%) и пикселях (px)<br />
Время перехода в слайд-шоу задаётся в миллисекундах (1 секунда = 1000 миллисекунд)') ?></p>

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
		$form .= '<p><span class="ffirst1 ftitle fheader">Внешний вид:</span><span>'. 
				form_dropdown('f_style', 
					array( '1' => 'Стиль #1',
							'2' => 'Стиль #2',
							'3' => 'Стиль #3',
							'4' => 'Стиль #4',
							'5' => 'Стиль #5'), 
					$options['style']) . '</span></p>';

		$form .= '<p><span class="ffirst1 ftitle fheader">Эффект перехода:</span><span>'. 
				form_dropdown('f_effect', 
					array('elastic' => 'Стандартный (elastic)',
						'fade' => 'Плавное появление (fade)',
						'none' => 'Без эффекта (none)'), 
					$options['effect']) . '</span></p>';
				
		$form .= '<p><span class="ffirst1 ftitle fheader">Использовать заданные размеры:</span><span>'. 
				form_dropdown('f_size', 
					array('1' => 'Да',
						  '0' => 'Нет'), 
					$options['size']) . '</span></p>';

		$form .= '<p><span class="ffirst1 ftitle fheader">Ширина:</span><span><input name="f_width" type="text" value="'.$options['width'].'"></span></p>';
		
		$form .= '<p><span class="ffirst1 ftitle fheader">Высота:</span><span><input name="f_height" type="text" value="'.$options['height'].'"></span></p>';

		$form .= '<p><span class="ffirst1 ftitle fheader">Время перехода в слайд-шоу:</span><span><input name="f_slideshowspeed" type="text" value="'.$options['slideshowspeed'].'"></span></p>';

		echo '<form method="post" class="fform">' . mso_form_session('f_session_id');
		echo $form;
		echo '<p><span class="ffirst1"></span><span><button type="submit" name="f_submit" class="i save">' . t('Сохранить изменения') . '</button></span></p>';
		echo '</form>';
		
# end file