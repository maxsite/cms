<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


	$CI = & get_instance();
	
	$options_key = 'plugin_antispam';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['antispam_on'] = isset( $post['f_antispam_on']) ? 1 : 0;
		$options['logging'] = isset( $post['f_logging']) ? 1 : 0;
		$options['moderation_links'] = isset( $post['f_moderation_links']) ? 1 : 0;
		$options['logging_file'] = $post['f_logging_file'];
		$options['black_ip'] = $post['f_black_ip'];
		$options['black_words'] = $post['f_black_words'];
		$options['moderation_words'] = $post['f_moderation_words'];
		$options['moderation_comusers'] = $post['f_moderation_comusers'];
	
	
		mso_add_option($options_key, $options, 'plugins' );
		
		echo '<div class="update">' . t('Обновлено!') . '</div>';
	}
	
?>
<h1><?= t('Антиспам') ?></h1>
<p class="info"><?= t('С помощью этого плагина вы можете активно бороться со спамерами. Обратите внимание, что комментарии авторов публикуются без модерации.') ?></p>

<?php
		
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['antispam_on']) ) $options['antispam_on'] = false; // включен ли антиспам
		if ( !isset($options['logging']) ) $options['logging'] = false; // разрешен ли логинг в файл?
		if ( !isset($options['logging_file']) ) $options['logging_file'] = 'antispam.log'; // путь к файлу логинга
		if ( !isset($options['black_ip']) ) $options['black_ip'] = ''; // черный список IP
		if ( !isset($options['black_words']) ) $options['black_words'] = ''; // черный список слов
		if ( !isset($options['moderation_words']) ) $options['moderation_words'] = ''; // список слов для модерации
		if ( !isset($options['moderation_comusers']) ) $options['moderation_comusers'] = ''; // список слов для модерации комюзеров
		if ( !isset($options['moderation_links']) ) $options['moderation_links'] = true; // модерация всех ссылок

		
		$form = '';

		$form .= '<h2>' . t('Настройки') . '</h2>';
		
		$chk = $options['antispam_on'] ? ' checked="checked"  ' : '';
		$form .= '<p><label><input name="f_antispam_on" type="checkbox" ' . $chk . '> <strong>' . t('Включить антиспам') . '</strong></label>';
		
		$chk = $options['logging'] ? ' checked="checked"  ' : '';
		$form .= '<p><label><input name="f_logging" type="checkbox" ' . $chk . '> <strong>' . t('Вести лог отловленных спамов') . '</strong></label>';
		
		$chk = $options['moderation_links'] ? ' checked="checked"  ' : '';
		$form .= '<p><label><input name="f_moderation_links" type="checkbox" ' . $chk . '> <strong>' . t('Отправлять комментарий на модерацию, если в нем встречается хоть одна ссылка.') . '</strong></label>';
		
		$form .= '<p><strong>' . t('Файл для логов:') . '</strong> ' . getinfo('uploads_dir') . ' <input name="f_logging_file" type="text" value="' . $options['logging_file'] . '">';
		if (file_exists( getinfo('uploads_dir') . $options['logging_file'] ))
			$form .= ' <a href="' . getinfo('uploads_url') . $options['logging_file'] . '" target="_blank">' . t('Посмотреть') . '</a>';
		
		
		$form .= '<br><br><h2>' . t('Черный список IP') . '</h2>';
		$form .= '<p>' . t('Укажите IP, с которых недопустимы комментарии. Один IP в одной строчке.') . '</p>';
		$form .= '<textarea name="f_black_ip" rows="7" style="width: 99%;">';
		$form .= htmlspecialchars($options['black_ip']);
		$form .= '</textarea>';
		
		$form .= '<br><br><h2>' . t('Черный список слов') . '</h2>';
		$form .= '<p>' . t('Укажите слова, которые нельзя использовать в комментариях. Одно слово в одной строчке.') . '</p>';
		$form .= '<textarea name="f_black_words" rows="7" style="width: 99%;">';
		$form .= htmlspecialchars($options['black_words']);
		$form .= '</textarea>';		
		
		$form .= '<br><br><h2>' . t('Слова для модерации') . '</h2>';
		$form .= '<p>' . t('Укажите слова, которые принудительно отравляют комментарий на премодерацию. Одно слово в одной строчке. Обратите внимание, что этот список проверяется только если пройдена проверка на Черные списки.') . '</p>';
		$form .= '<textarea name="f_moderation_words" rows="7" style="width: 99%;">';
		$form .= htmlspecialchars($options['moderation_words']);
		$form .= '</textarea>';		
		
		$form .= '<br><br><h2>' . t('Номера комюзеров, которые всегда попадают в модерацию') . '</h2>';
		$form .= '<p>' . t('Укажите номера комюзеров, которые принудительно отравляют комментарий на премодерацию. Один номер в одной строчке. Обратите внимание, что этот список проверяется только если пройдена проверка на Черные списки.') . '</p>';
		$form .= '<textarea name="f_moderation_comusers" rows="7" style="width: 99%;">';
		$form .= htmlspecialchars($options['moderation_comusers']);
		$form .= '</textarea>';	
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<br><button type="submit" name="f_submit" style="margin: 25px 0 5px 0;">' . t('Сохранить изменения') . '</button>';
		echo '</form>';

?>