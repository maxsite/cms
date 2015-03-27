<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
?>

<h1><?= tf('Восстановление пароля') ?></h1>

<p><a href="<?= getinfo('siteurl') ?>users"><?= tf('Список комментаторов')?></a></p>

<form method="post" class="comusers-form fform">
<?= mso_form_session('f_session_id') ?>

<p><?= tf('Если у вас сохранился код активации, то вы можете сразу заполнить все поля. Если код активации утерян, то вначале введите только email и нажмите кнопку «Готово». На указанный email вы получите код активации. После этого вы можете вернуться на эту страницу и заполнить все поля.') ?></p>

<p><span class="ffirst ftitle"><?= tf('Ваш email') ?></span><span><input type="text" name="f_comusers_email" value=""></span></p>
	
<p><span class="ffirst ftitle"><?= tf('Ваш код активации') ?></span><span><input type="text" name="f_comusers_activate_key" value=""></span></p>

<p><span class="ffirst ftitle"><?= tf('Новый пароль') ?></span><span><input type="text" name="f_comusers_password" value=""></span></p>

<p><span class="ffirst"></span><span><input type="submit" name="f_submit" value="<?= tf('Готово') ?>"></span></p>

</form>
