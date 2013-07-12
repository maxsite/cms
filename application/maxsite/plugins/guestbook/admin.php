<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	
	$options_key = 'plugin_guestbook';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['text'] = $post['f_text'];
		$options['slug'] = $post['f_slug'];
		$options['limit'] = $post['f_limit'];
		$options['email'] = $post['f_email'];
		$options['fields'] = $post['f_fields'];
		$options['format'] = $post['f_format'];
		$options['end'] = $post['f_end'];
		$options['start'] = $post['f_start'];
		
		$options['moderation'] = isset($post['f_moderation']) ? 1 : 0;
		
		// fields_arr сразу перобразуем в массив из fields
		$fields = explode("\n", $options['fields']); // разбиваем по строкам
		
		$fields_arr = array();
		
		foreach ($fields as $row)
		{
			$ar_type = explode('|', $row); // разбиваем по |
			// всего должно быть 2 элемента
			if ( isset($ar_type[0]) and trim($ar_type[0]) and isset($ar_type[1]) and trim($ar_type[1]) ) //  элементы
			{
				$f = trim($ar_type[0]);
				
				// поле может быть только строго предопределеное
				if  (
					$f == 'name' or 
					$f == 'text' or 
					$f == 'title' or 
					$f == 'email' or 
					$f == 'icq' or 
					$f == 'site' or 
					$f == 'phone' or 
					$f == 'custom1' or 
					$f == 'custom2' or 
					$f == 'custom3' or 
					$f == 'custom4' or 
					$f == 'custom5'
					) 
					$fields_arr[$f] = trim($ar_type[1]);
			}
		}
		$options['fields_arr'] = $fields_arr;
		
		// pr($options);
		
		mso_add_option($options_key, $options, 'plugins' );
		echo '<div class="update">' . t('Обновлено!') . '</div>';
	}
	
?>
<div class="admin-h-menu">
<?php
	# сделаем меню горизонтальное в текущей закладке
	
	// основной url этого плагина - жестко задается
	$plugin_url = getinfo('site_admin_url') . 'guestbook';
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройки гостевой книги'), 'select', 'i book') . ' ';
	$a .= mso_admin_link_segment_build($plugin_url, 'edit', t('Редактирование отзывов'), 'select', 'i edit');
	echo $a;
?>
</div>

<h1><?= t('Гостевая книга') ?></h1>
<p class="info"><?= t('Плагин позволяет организовать на вашем сайте гостевую книгу.') ?></p>

<?php
		$options = mso_get_option($options_key, 'plugins', array());

		if ( !isset($options['text']) ) $options['text'] = t("<h1>Гостевая книга</h1>\n<p>Оставьте свой отзыв</p>");
 
		if ( !isset($options['slug']) ) $options['slug'] = 'guestbook'; 
		if ( !isset($options['fields']) or !$options['fields'] ) $options['fields'] = t("name | Ваше имя:\ntext | Ваш отзыв:"); 
		if ( !isset($options['limit']) ) $options['limit'] = 10; // отзывов на страницу 
		if ( !isset($options['email']) ) $options['email'] = mso_get_option('admin_email', 'general', '');
		if ( !isset($options['moderation']) ) $options['moderation'] = 1; // модерация

		if ( !isset($options['format']) or !$options['format']) $options['format'] = '<div class="fform guestbook">
<p class="head"><span class="fheader">[name] | [date]</span></p>
<div class="margin10">
<p><span>[text]</span></p>
</div>
<p class="hr"></p>
</div>';

		if ( !isset($options['start']) ) $options['start'] = '<h2>' . t('Отзывы') . '</h2>'; 

		if ( !isset($options['end']) ) $options['end'] = ''; 


		$form = '
				<p><span class="ftitle ffirst2 fheader">' . t('Короткая ссылка:') . '</span>
				<span><input name="f_slug" type="text" value="' . $options['slug'] . '"></span>
				<span>&nbsp;<a href="' . getinfo('siteurl') . $options['slug']  . '" target="_blank" class="i globe">' . t('Просмотр') . '</a></span>
				</p>
				
				<p><span class="ftitle ffirst2 fheader">' . t('Отзывов на страницу:') . '</span>
					<span><input name="f_limit" type="text" value="' . $options['limit'] . '"></span>
				</p>
			
				<p><span class="ftitle ffirst2 fheader">' . t('Уведомлять на email:') . '</span>
					<span><input name="f_email" type="text" value="' . $options['email'] . '"></span>
				</p>';
		
		
		if ($options['moderation']) $check = ' checked';
			else $check = '';
		
		$form .= '<p>
			<span class="ffirst2"> </span>
				<label><input name="f_moderation" type="checkbox"' . $check . '> ' . t('Модерация каждого отзыва') . '</label></p>';
		
		
		$form .= '<p class="header">' . t('Текст перед отзывами (можно использовать HTML):') . '</p><textarea name="f_text" rows="7">' . $options['text'] . '</textarea>';
		
		
		$form .= '<p class="header">' . t('Укажите названия полей') . '</p>
				<p>' . t('Их следует выводить в форме в формате: «поле | название», например: <b>«name | Ваше имя:»</b>. Поля буду выведены в том же порядке. Одно поле в одной строке.') . '</p>';
		
		$form .= '<p>' . t('Все возможные варианты: <b>id, name, text, title, email, icq, site, phone, custom1, custom2, custom3, custom4, custom5, url.</b>') . '</p>';

		$form .= '<textarea name="f_fields" rows="7">' . htmlspecialchars($options['fields']) . '</textarea>';
		
		
		$form .= '<p class="header">' . t('Укажите HTML-формат вывода отзывов') . '</p><p>' . t('Варианты полей: <b>[name], [text], [title], [email], [icq], [site], [phone], [custom1], [custom2], [custom3], [custom4], [custom5], [id], [ip], [date], [browser], [url].</b>') . '</p>';
		
		$form .= '<textarea name="f_format" rows="10">' . htmlspecialchars($options['format']) . '</textarea>';
		
		$form .= '<p class="header">' . t('Текст перед отзывами') .'</p>';
		$form .= '<textarea name="f_start" rows="7">' . htmlspecialchars($options['start']) . '</textarea>';
		
		$form .= '<p class="header">' . t('Текст после всех отзывов') .'</p>';
		$form .= '<textarea name="f_end" rows="7">' . htmlspecialchars($options['end']) . '</textarea>';	
				
		echo '<form method="post" class="fform">' . mso_form_session('f_session_id');
		echo $form;
		echo '<button type="submit" name="f_submit" class="i save">' . t('Сохранить изменения') . '</button>';
		echo '</form>';

# end file