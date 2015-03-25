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
	if ($fn = mso_find_ts_file('type/password-recovery/units/form.php')) require($fn);
}

echo NR . '</div><!-- class="type type_password_recovery" -->' . NR;

# конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	
# end file