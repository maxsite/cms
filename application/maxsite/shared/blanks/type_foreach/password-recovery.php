<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
		echo '<h1>'. tf('Восстановление пароля комментатора') . '</h1>';
		
		echo '<p><a href="' . getinfo('siteurl') . 'users">'. tf('Список комментаторов'). '</a></p>';
		
		echo '<form action="" method="post" class="comusers-form">' . mso_form_session('f_session_id');
		echo '<p>'. tf('Если у вас сохранился код активации, то вы можете сразу заполнить все поля. Если код активации утерян, то вначале введите только email и нажмите кнопку «Готово». На указанный email вы получите код активации. После этого вы можете вернуться на эту страницу и заполнить все поля.'). '</p>';
		echo '<p><strong>'. tf('Ваш email'). ':</strong> <input type="text" name="f_comusers_email" value="">*</p>';
		echo '<p><strong>'. tf('Ваш код активации'). ':</strong> <input type="text" name="f_comusers_activate_key" value=""></p>';
		echo '<p><strong>'. tf('Новый пароль'). ':</strong> <input type="text" name="f_comusers_password" value=""></p>';
		echo '<input type="submit" name="f_submit" value="'. tf('Готово'). '"></p></form>';