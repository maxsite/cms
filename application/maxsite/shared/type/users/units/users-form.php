<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

require_once( getinfo('common_dir') . 'comments.php' );

# всё делаем по-новому, чтобы для доступа к странице нужно было пройти авторизацию
# вначале выводим логин и пароль
# если авторизация пройдена, значит выводим страницу с формой
# если авторизация не пройдена, то опять выводим форму


# обработка отправленных данных - возвращает результат
$res_post = mso_comuser_edit(); 

# получим всю информацию о комюзере из сессии или url
$comuser_info = mso_get_comuser();


# отображение формы залогирования
$login_form = !is_login_comuser();

# если нет данных юзера, то выводим форму
if (!$comuser_info) $login_form = true;

if ($f = mso_page_foreach('users-form-head-meta')) require($f);
else
{
	mso_head_meta('title', tf('Форма редактирования комментатора') . '. ' . getinfo('title')); // meta title страницы
}

if (!$comuser_info and mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found'); 

// теперь сам вывод
# начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

echo NR . '<div class="type type_users_form">' . NR;

echo $res_post;
	
if ($comuser_info)
{
	extract($comuser_info[0]);
	
	// pr($comuser_info[0]);
	if ($f = mso_page_foreach('users-form')) require($f); // подключаем кастомный вывод
	else
	{
		if ($comusers_nik) echo '<h1>' . $comusers_nik . '</h1>';
			else echo '<h1>'. tf('Комментатор'). ' ' . $comusers_id . '</h1>';
		
		echo '<p><a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '">' . tf('Персональная страница') . '</a>';
		
		if (!$login_form)
		{
			echo ' | <a href="' . getinfo('siteurl') . 'password-recovery">' . tf('Сменить пароль') . '</a>';
			echo ' | <a href="' . getinfo('siteurl') . 'logout">' . tf('Выход') . '</a>';
		}
		echo '</p>';
		
		// если активация не завершена, то вначале требуем её завершить
		if ($comusers_activate_string != $comusers_activate_key) // нет активации
		{
			echo '<form method="post">' . mso_form_session('f_session_id');
			echo '<p><span style="color: red; font-weight: bold;" class="users-form">'. tf('Введите ключ активации'). ':</span> 
				 <input type="text" style="width: 200px;" class="users-form" name="f_comusers_activate_key"> ';
			echo '<input type="submit" name="f_submit[' . $comusers_id . ']" value="' . tf('Готово') . '"></p></form>';
			
			echo '<p>' . tf('В случае проблем с активацией (не пришел ключ, указали ошибочный email), обращайтесь к администратору по email:') . ' <em>' . mso_get_option('admin_email', 'general', '-') . '</em></p>';
			
			
		}
		else // активация завершена - можно вывести поля для редактирования
		{
			echo '<form method="post" class="comusers-form fform">' . mso_form_session('f_session_id');
			
			if ($login_form) // нужно отобразить форму
			{
				echo '<h3>'. tf('Для редактирования введите свой email и пароль'). '</h3>';
			
				echo '<input type="hidden" value="' . getinfo('siteurl') . 'users/' . $comusers_id . '/edit" name="flogin_redirect">';
				echo mso_form_session('flogin_session_id');
				
				echo '<p><span class="ffirst ftitle">'. tf('Ваш email'). '</span><span><input type="text" name="flogin_user" class="flogin_user"></span></p>';
				
				echo '<p><span class="ffirst ftitle">'. tf('Ваш пароль'). '</span><span><input type="password" name="flogin_password" class="flogin_password"></span></p>';
				
		
				
				//echo '<p><span class="ffirst"></span><span><a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '/lost">' . tf('Я забыл пароль') . '</a></span></p>';
			}
			else
			{
				$CI = & get_instance();
				$CI->load->helper('form');
				
				echo '<input type="hidden" value="' . $comusers_email . '" name="f_comusers_email">';
				echo '<input type="hidden" value="' . $comusers_password . '" name="f_comusers_password">';
				
				echo '<h3>'. tf('Укажите свои данные'). '</h3>';
				
				echo '<p><span class="ffirst ftitle">'. tf('Отображаемый ник'). '</span><span><input type="text" name="f_comusers_nik" value="' . $comusers_nik . '"></span></p>';
				
				echo '<p><span class="ffirst ftitle">'. tf('Сайт (с http://)'). '</span><span><input type="text" name="f_comusers_url" value="' . $comusers_url . '"></p>';
				
				echo '<p><span class="ffirst ftitle">'. tf('Аватарка (с http://, 80x80px)'). '</span><span><input type="text" name="f_comusers_avatar_url" value="' . $comusers_avatar_url . '"></p>';
				
				echo '<p><span class="ffirst ftitle">'. tf('ICQ'). '</span><span><input type="text" name="f_comusers_icq" value="' . $comusers_icq . '"></p>';
				
				echo '<p><span class="ffirst ftitle">'. tf('Twitter'). '</span><span><input type="text" name="f_comusers_msn" value="' . $comusers_msn . '"></p>';
				
				echo '<p><span class="ffirst ftitle">'. tf('Jabber'). '</span><span><input type="text" name="f_comusers_jaber" value="' . $comusers_jaber . '"></p>';
				
				echo '<p><span class="ffirst ftitle">'. tf('Skype'). '</span><span><input type="text" name="f_comusers_skype" value="' . $comusers_skype . '"></p>';
				
				echo '<p><span class="ffirst ftitle">'. tf('Дата рождения'). '</span><span><input type="text" name="f_comusers_date_birth" value="' . $comusers_date_birth . '"></p>';
				
				echo '<p><span class="ffirst ftitle ftop">'. tf('О себе'). ' ('. tf('HTML удаляется'). ')</span><span><textarea name="f_comusers_description">'. NR 
					. htmlspecialchars(strip_tags($comusers_description)) . '</textarea></span></p>';
				
				
				/*
				// если включено любое уведомление, то меняем флаг на «Подписаться»
				
				echo '<p><span class="ffirst ftitle">'. tf('Уведомления'). '</span><span>' 
						
						. form_dropdown('f_comusers_notify', 
						
							array('0'=> tf('Без уведомлений'), '1'=> tf('Подписаться')), $comusers_notify, '') 
						
						. '</span></p>';
				*/


				
				// поскольку чекбоксы не передаются, если они не отмечены, 
				// то передаем скрытно их дефолтные значения

				echo '<input type="hidden" value="0" name="f_comusers_meta[subscribe_my_comments]">';
				
				$check = (isset($comusers_meta['subscribe_my_comments']) and $comusers_meta['subscribe_my_comments']=='1') ? ' checked="checked"' : '';
				
				
				echo '<p><span class="ffirst ftitle">'. tf('Уведомления'). '</span><label>' 
					. '<input type="checkbox" name="f_comusers_meta[subscribe_my_comments]" value="1"' . $check . '>'
					. ' '. tf('новые комментарии, где я участвую') . '</label></p>';
				
				
				echo '<input type="hidden" value="0" name="f_comusers_meta[subscribe_other_comments]">';
				
				
				$check = (isset($comusers_meta['subscribe_other_comments']) and $comusers_meta['subscribe_other_comments']=='1') ? ' checked="checked"' : '';
				
				echo '<p class="nop"><span class="ffirst"></span><label>' 
					. '<input type="checkbox" name="f_comusers_meta[subscribe_other_comments]" value="1"' . $check . '>'
					. ' '. tf('новые комментарии, где я не участвую') . '</label>';
				
				
				echo '<input type="hidden" value="0" name="f_comusers_meta[subscribe_new_pages]">';
				
				$check = (isset($comusers_meta['subscribe_new_pages']) and $comusers_meta['subscribe_new_pages']=='1') ? ' checked="checked"' : '';
				
				echo '<p class="nop"><span class="ffirst"></span><label>' 
					. '<input type="checkbox" name="f_comusers_meta[subscribe_new_pages]" value="1"' . $check . '>'
					. ' '. tf('новые записи сайта') . '</label></p>';
					
					
				echo '<input type="hidden" value="0" name="f_comusers_meta[subscribe_admin]">';
				
				$check = (isset($comusers_meta['subscribe_admin']) and $comusers_meta['subscribe_admin']=='1') ? ' checked="checked"' : '';
				
				echo '<p class="nop"><span class="ffirst"></span><label>' 
					. '<input type="checkbox" name="f_comusers_meta[subscribe_admin]" value="1"' . $check . '>'
					. ' '. tf('рассылка администратора') . '</label></p>';
					
			}
			
			
			if ($login_form)
			{
				echo '<p><span class="ffirst"></span><span><input type="submit" name="flogin_submit" value="' .  tf('Отправить') . '">
				<a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '/lost">' . tf('Я забыл пароль') . '</a>
				</span></p></form>';
	
			}
			else
			{
				echo '<p><span class="ffirst"></span><span class="submit"><input type="submit" name="f_submit[' . $comusers_id . ']" value="' .  tf('Отправить') . '"></span></p></form>';
			}
		}
		
	} // mso_page_foreach

}
else
{
	if ($f = mso_page_foreach('pages-not-found')) 
	{
		require($f); // подключаем кастомный вывод
	}
	else // стандартный вывод
	{
		echo '<h1>' . tf('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . tf('Извините, пользователь с указанным номером не найден.') . '</p>';
		echo mso_hook('page_404');
	}
}

echo NR . '</div><!-- class="type type_users_form" -->' . NR;

# конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	
?>