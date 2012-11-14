<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$CI = & get_instance();
	
	$options = mso_get_option('samborsky_feedburner_count', 'plugins', array());
	
	if( $post = mso_check_post(array('submit_set_default')) ){
		mso_checkreferer();
		feedburner_count_set_default();
		$options = mso_get_option('samborsky_feedburner_count', 'plugins', array());
	}
	else if( $post = mso_check_post(array('submit','feed_name','update_interval','template')) ){
		mso_checkreferer();
		
		$options = mso_get_option('samborsky_feedburner_count', 'plugins', array());
		
		$options['feed_name'] = $_POST['feed_name'];
		$options['update_interval'] = $_POST['update_interval'];
		$options['template'] = base64_encode($_POST['template']);
		$options['last_update'] = 0;
		$options['count'] = 'n/a';
		
		mso_add_option('samborsky_feedburner_count',$options,'plugins');
	}
	
	if( $options['template'] )
		$options['template'] = base64_decode($options['template']);
	
?>
<h1>Настройка FeedBurner Count от <a href="http://www.samborsky.com/">samborsky.com</a></h1>
Здравствуйте, последний раз счетчик обновлялся <strong><?= $options['last_update'] ? (round((time() - $options['last_update'])/60) . ' мин. назад') : 'Еще не обновлялся' ?></strong>
<br>
Последнее показание счетчика: <strong><?= $options['count'] ?></strong>

<form method="post">
	<table cellspacing="10">
		<tr>
			<td><strong>Ссылка на фид</strong></td>
			<td><input type="text" size="60" style="width: 90%;" value="<?= $options['feed_name'] ?>" name="feed_name"></td>
			<td>Адрес вашего фида в сервисе Feedburner<br><br>Пример:<br><em>http://feeds2.feedburner.com/max3000</em><br><br>Или укажите логин, к примеру: <br><em>max3000</em></td>
		</tr>
		<tr>
			<td><strong>Интервал обновления данных</strong></td>
			<td><input type="text" size="60" style="width: 90%;" value="<?= $options['update_interval'] ?>" name="update_interval"></td>
			<td>В минутах. Советую установить раз в сутки, т.е. 1440</td>
		</tr>
		<tr>
			<td><strong>Шаблон</strong></td>
			<td><textarea name="template" rows="6" cols="40" style="width: 90%;"><?= $options['template'] ?></textarea></td>
			<td>Макрос количества подписчиков <strong>%COUNT%</strong></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="Сохранить"></td>
			<td><input onclick="return confirm('Точно?')" type="submit" name="submit_set_default" value="Установить настройки по умолчанию"></td>
		</tr>
	</table>
</form>
<br>

<h3>Если что-либо не работает:</h3>
<ul>
	<li>Проверьте, заполнены ли правильно вышеуказанные поля.</li>
	<li>Убедитесь, что у вас активна услуга <strong>Awareness API</strong> в аккаунте <a href="http://www.feedburner.com/">Feedburner</a> (проверить это можно на странице Публикуй - Awareness API).</li>
	<li>В нужное место шаблона поместите следующий код: <br><br><code>&lt;?php if (function_exists('feedburner_count')) feedburner_count(); ?&gt;</code></li>
</ul>
<br>
Если вам понравился этот плагин, напишите о нем у себя на блоге, добавив ссылку на блог автора <a href="http://www.samborsky.com/">www.samborsky.com</a> или на страницу плагина <a href="http://www.samborsky.com/max-3000/223/">FeedBurner Count</a>.
При обнаружении ошибок, пишите в комментарии, в <a href="http://forum.max-3000.com/viewtopic.php?f=6&t=83">ветку форума MaxSite</a>, либо <a href="http://www.samborsky.com/contacts/">напрямую автору</a>.