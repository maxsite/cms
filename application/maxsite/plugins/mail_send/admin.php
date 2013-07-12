<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	
	$options_key = 'mail_send';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_subject', 'f_from', 'f_files', 'f_message', 'f_list')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['maillist'] = $post['f_list'];
		$options['subject'] = $post['f_subject'];
		$options['files'] = $post['f_files'];
		$options['message'] = $post['f_message'];
		$options['from'] = $post['f_from'];
		
		mso_add_option($options_key, $options, 'plugins' ); // сохраним в опциях введенные данные
	
		if (isset($post['f_submit_send'])) // разослать
		{
			$error = '';
			
			if (!$post['f_subject']) $error .= 'Нужно указать тему письма. '; 
			if (!$post['f_message']) $error .= 'Нужно указать текст письма. ';
			
			if (!$post['f_list']) $error .= 'Список рассылки пуст. '; 
			 
			if (!$post['f_from']) $error .= 'Нужно указать email отправителя. '; 
			if ($post['f_from'] and !mso_valid_email($post['f_from'])) $error .= 'Указан неверный email отправителя. '; 
			
			if ($error)
			{
				echo '<div class="error">' . t('Ошибки: ') . $error . '</div>';
			}
			else
			{
				// отправка
				// проходимся по всему списку
				// проверяем корректность email
				// если все ок, отправляем
				
				$preferences = array();
				if ($post['f_files']) // указан файл
				{
					$fn = getinfo('uploads_dir') . $post['f_files'];
					if (file_exists($fn)) $preferences['attach'] = $fn;
				}
				
				$list = explode("\n", $post['f_list']);
				
				foreach($list as $email)
				{
					$res = true;
					
					// проверяем валидность email
					// если ок, то отправляем
					// если возврат false, всё рубим - проблема с почтой 
					if (mso_valid_email($email)) 
					{
						$res = mso_mail($email, $post['f_subject'], $post['f_message'], $post['f_from'], $preferences);
						
						if ($res)
							echo '<div class="update">' . t('Отправлено: ') . $email . '</div>';
						else
						{
							echo '<div class="error">' . t('Ошибка отправки почты на сервере.') . '</div>';
							break;
						}
					}
					else 
						echo '<div class="error">' . t('Неверный адрес: ') . $email . '</div>';
					
				}
			}
		}
	}
	
?>
<h1><?= t('Mail Send') ?></h1>
<p class="info"><?= t('C помощью этого плагина вы можете организовать рассылку email-сообщений по списку.') ?></p>

<?php

		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['maillist']) ) $options['maillist'] = '';
		if ( !isset($options['subject']) ) $options['subject'] = '';
		if ( !isset($options['files']) ) $options['files'] = '';
		if ( !isset($options['message']) ) $options['message'] = '';
		if ( !isset($options['from']) ) $options['from'] = mso_get_option('admin_email_server', 'general', '');

		echo '<form class="fform" method="post">' . mso_form_session('f_session_id');

		echo '<p><span class="fheader">' . t('Тема письма:') . '</span></p><p><span><input name="f_subject" type="text" value="' 
			. $options['subject'] . '"></span></p>';

		echo '<p class="hr"><span class="fheader">' . t('От кого (email):') . '</span></p><p><span><input name="f_from" type="text" style="width: 100%" value="' . $options['from'] . '"></span></p>';

		echo '<p class="hr"><span class="fheader">' . t('Файл вложения (путь к файлу в «uploads»):') . '</span></p><p><span><input name="f_files" type="text" value="' . $options['files'] . '"></span></p>';
		
		echo '<p class="hr"><span class="fheader">' . t('Текст письма:') . '</span></p><p><span><textarea name="f_message" rows="10">' . htmlspecialchars($options['message']) . '</textarea></span></p>';

		echo '<p class="hr"><span class="fheader">' . t('Список рассылки (один email в строке):') . '</span></p><p><span><textarea name="f_list" rows="10">' . htmlspecialchars($options['maillist']). '</textarea></span></p>';
		
		
		echo '<p class="hr"><span><button type="submit" name="f_submit_send" class="i send">' . t('Разослать') . '</button>';
		echo '<button type="submit" name="f_save_list" class="i save">' . t('Только сохранить список') . '</button></span></p>';
		echo '</form>';
		
# end file