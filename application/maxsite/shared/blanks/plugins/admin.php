<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	
	$options_key = '';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['header'] = $post['f_header'];
	
		mso_add_option($options_key, $options, 'plugins' );
		echo '<div class="update">' . t('Обновлено!') . '</div>';
	}
	
?>
<h1><?= t('Плагин') ?></h1>
<p class="info"><?= t('Описание') ?></p>

<?php
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['header']) ) $options['header'] = ''; 

		$form = '';
		$form .= '<h2>' . t('Настройки') . '</h2>';
		$form .= '<p><strong>' . t('Заголовок:') . '</strong> ' . ' <input name="f_header" type="text" value="' . $options['header'] . '"></p>';
		
		echo '<form method="post" class="fform">' . mso_form_session('f_session_id');
		echo $form;
		echo '<button type="submit" name="f_submit">' . t('Сохранить изменения') . '</button>';
		echo '</form>';

?>