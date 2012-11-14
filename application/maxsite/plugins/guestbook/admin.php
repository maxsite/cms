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
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройки гостевой книги'), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'edit', t('Редактирование отзывов'), 'select');
	echo $a;
?>
</div>

<h1><?= t('Гостевая книга') ?></h1>
<p class="info"><?= t('Плагин позволяет организовать на вашем сайте гостевую книгу.') ?></p>

<?php
		$options = mso_get_option($options_key, 'plugins', array());

		if ( !isset($options['text']) ) $options['text'] = t("<h1>Гостевая книга</h1>\n<p>Оставьте свой отзыв</p>");
 
		if ( !isset($options['slug']) ) $options['slug'] = 'guestbook'; 
		if ( !isset($options['fields']) ) $options['fields'] = t("name | Ваше имя:\ntext | Ваш отзыв:"); 
		if ( !isset($options['limit']) ) $options['limit'] = 10; // отзывов на страницу 
		if ( !isset($options['email']) ) $options['email'] = mso_get_option('admin_email', 'general', '');
		if ( !isset($options['moderation']) ) $options['moderation'] = 1; // модерация

		if ( !isset($options['format']) ) $options['format'] = '<tr><td colspan="2" class="header"><a id="guestbook-[id]"></a>[date]</td></tr>
<tr><td class="t1"><b>Имя:</b></td><td class="t2">[name]</td></tr>
<tr><td class="t1"><b>Текст:</b></td><td class="t2">[text]</td></tr>
<tr><td colspan="2" class="space">&nbsp;</td></tr>'; 

		if ( !isset($options['start']) ) $options['start'] = '<h2 class="guestbook">' . t('Отзывы') . '</h2><table class="guestbook">'; 

		if ( !isset($options['end']) ) $options['end'] = '</table>'; 


		$form = '';
		
		$form .= '<p><strong>' . t('Короткая ссылка:') . '</strong> ' . ' <input name="f_slug" type="text" value="' . $options['slug'] . '"> <a href="' . getinfo('siteurl') . $options['slug']  . '" target="_blank">' . t('Просмотр') . '</a></p>';
		
		$form .= '<p><strong>' . t('Отзывов на страницу:') . '</strong> ' . ' <input name="f_limit" type="text" value="' . $options['limit'] . '"></p>';
		
		$form .= '<p><strong>' . t('Уведомлять на email:') . '</strong> ' . ' <input name="f_email" type="text" value="' . $options['email'] . '"></p>';
		
		
		if ($options['moderation']) $check = ' checked';
			else $check = '';
		
		$form .= '<p><label><input name="f_moderation" type="checkbox"' . $check . '> <strong>' . t('Модерация каждого отзыва') . '</strong></label></p>';
		
		
		$form .= '<br><p>' . t('Текст перед отзывами (можно использовать HTML):') . '</p><p>' . ' <textarea name="f_text" style="width: 99%; height: 200px;">' . $options['text'] . '</textarea></p>';
		
		
		$form .= '<br><p>' . t('Укажите названия полей, которые следует выводить в форме в формате: «поле | название», например: <strong>«name | Ваше имя:»</strong>. Поля буду выведены в том же порядке. Одно поле в одной строке.') . '</p>';
		$form .= '<p>' . t('Все возможные варианты полей: <strong>name, text, title, email, icq, site, phone, custom1, custom2, custom3, custom4, custom5.</strong>') . '</p>';

		$form .= '<p><textarea name="f_fields" style="width: 99%; height: 200px;">' . $options['fields'] . '</textarea></p>';
		
		
		$form .= '<br><p>' . t('Укажите формат вывода отзывов. Можно использовать HTML.</p><p>Варианты: <strong>[name], [text], [title], [email], [icq], [site], [phone], [custom1], [custom2], [custom3], [custom4], [custom5], [id], [ip], [date], [browser].</strong>') . '</p>';
		$form .= '<p><textarea name="f_format" style="width: 99%; height: 200px;">' . htmlspecialchars($options['format']) . '</textarea></p>';
		
		$form .= '<br><p>' . t('Текст перед циклом вывода отзывов. Можно использовать HTML.') .'</p>';
		$form .= '<p><textarea name="f_start" style="width: 99%; height: 100px;">' . htmlspecialchars($options['start']) . '</textarea></p>';
		
		$form .= '<br><p>' . t('Текст после цикла вывода отзывов. Можно использовать HTML.') .'</p>';
		$form .= '<p><textarea name="f_end" style="width: 99%; height: 100px;">' . htmlspecialchars($options['end']) . '</textarea></p>';	
				
		echo '<form method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<button type="submit" name="f_submit" style="margin: 25px 0 5px 0;">' . t('Сохранить изменения') . '</button>';
		echo '</form>';

?>