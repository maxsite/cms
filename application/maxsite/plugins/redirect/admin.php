<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$options_key = 'redirect';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_all', 'f_all404')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['all'] = $post['f_all'];
		$options['all404'] = $post['f_all404'];
		
		mso_add_option($options_key, $options, 'plugins' );
		echo '<div class="update">' . t('Обновлено!') . '</div>';
	}
	
?>
<h1><?= t('Редиректы') ?></h1>
<p class="info"><?= t('С помощью этого плагина вы можете организовать редиректы со своего сайта. Укажите исходный и конечный адрес через «|», например:') ?></p>
<pre>http://mysite.com/about | http://newsite.com/hello</pre><br>
<p class="info"><?= t('При переходе к странице вашего сайта «http://mysite.com/about» будет осуществлен автоматический редирект на указанный «http://newsite.com/hello».') ?></p>
<p class="info"><?= t('Третьим параметром вы можете указать тип редиректа: 301 или 302.') ?></p>
<pre>http://mysite.com/about | http://newsite.com/hello | 301</pre><br>
<p class="info"><?= t('Также можно использовать регулярные выражения.') ?></p>
<pre>http://mysite.com/category/(.*) | http://newsite.com/$1 | 301</pre><br>

<?php

		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['all']) ) $options['all'] = '';
		if ( !isset($options['all404']) ) $options['all404'] = '';

		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo '<textarea name="f_all" style="width: 100%; height: 300px;">' .  $options['all'] . '</textarea>';
		
		echo '<br><br><p class="info">' . t('Здесь можно указать редиректы, которые сработают только при несуществующем типе данных (custom_page_404).') . '</p>';

		echo '<textarea name="f_all404" style="width: 100%; height: 300px;">' .  $options['all404'] . '</textarea>';
		
		echo '<br><br><input type="submit" name="f_submit" value="' . t('Сохранить изменения') . '">';
		echo '</form>';

?>