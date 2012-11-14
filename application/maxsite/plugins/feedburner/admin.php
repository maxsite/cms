<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	
	$options_key = 'plugin_feedburner';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['key'] = $post['f_key'];
	
		mso_add_option($options_key, $options, 'plugins' );
		echo '<div class="update">' . t('Обновлено!') . '</div>';
	}
	
?>
<h1><?= t('Плагин FeedBurner') ?></h1>
<p class="info"><?= t('Плагин выполняет перенаправление вашего основного rss на сервис feedburner.com.') ?></p>

<?php
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['key']) ) $options['key'] = ''; 

		$form = '<p><strong>' . t('Адрес вашего фида в FeedBurner.com:') . '</strong></p>
				<p>http://feeds2.feedburner.com/<input name="f_key" type="text" value="' . $options['key'] . '"></p>';
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения') . '" style="margin: 25px 0 5px 0;">';
		echo '</form>';

?>