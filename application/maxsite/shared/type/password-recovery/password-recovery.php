<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

require_once( getinfo('common_dir') . 'comments.php' );


$res_post = mso_comuser_lost(array('password_recovery' => true)); // обработка отправленных данных - возвращает результат


if ($f = mso_page_foreach('password-recovery-head-meta')) require($f);
else
{
	mso_head_meta('title', tf('Восстановление пароля') . '. '.  getinfo('title')); // meta title страницы
}


// теперь сам вывод
# начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

echo NR . '<div class="type type_password_recovery">' . NR;

echo $res_post;
	
	if ($f = mso_page_foreach('password-recovery')) 
	{
		require($f); // подключаем кастомный вывод
	}
	else
	{
		echo '<h1>'. tf('Восстановление пароля комментатора') . '</h1>';
		
		echo '<p><a href="' . getinfo('siteurl') . 'users">'. tf('Список комментаторов'). '</a></p>';
		
		echo '<form method="post" class="comusers-form fform">' . mso_form_session('f_session_id');
		echo '<p>'. tf('Если у вас сохранился код активации, то вы можете сразу заполнить все поля. Если код активации утерян, то вначале введите только email и нажмите кнопку «Готово». На указанный email вы получите код активации. После этого вы можете вернуться на эту страницу и заполнить все поля.'). '</p>';
		
		echo '<p><span class="ffirst ftitle">'. tf('Ваш email'). '</span><span><input type="text" name="f_comusers_email" value=""></span></p>';
			
		echo '<p><span class="ffirst ftitle">'. tf('Ваш код активации'). '</span><span><input type="text" name="f_comusers_activate_key" 
		value=""></span></p>';
		
		echo '<p><span class="ffirst ftitle">'. tf('Новый пароль'). '</span><span><input type="text" name="f_comusers_password" value=""></span></p>';
		
		echo '<p><span class="ffirst"></span><span><input type="submit" name="f_submit" value="'. tf('Готово'). '"></span></p></form>';

	}
		

echo NR . '</div><!-- class="type type_password_recovery" -->' . NR;

# конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	
# end file