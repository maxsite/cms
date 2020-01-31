<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$form_id = 'form' . crc32(__FILE__);

$ajax = getinfo('ajax') . base64_encode(str_replace('.php', '-ajax.php', str_replace(str_replace('\\', '/', getinfo('base_dir')), '', str_replace('\\', '/', __FILE__))));

?>

div(layout-center-wrap mar50-tb) || div(layout-wrap) || div(flex flex-wrap)

<div class="w48 w60-tablet w100-phone">

	h3(fas fa-envelope mar20-b) Контакты

	<form class="mso-form" id="<?= $form_id ?>">
		<input type="hidden" name="myform[form][source]" value="Форма 2">

		_ Вы можете связаться с нами через эту форму.

		<div class="mar20-t"><label>
				<b>Ваше имя *</b>
				<br><input class="mar3-t w100" type="text" name="myform[form][name]" required>
			</label></div>

		<div class="mar20-t"><label>
				<b>Ваш email *</b> <span class="t-gray mar10-l">(на него мы отправим ответ)</span>
				<br><input class="mar3-t w100" type="email" name="myform[form][email]" required>
			</label></div>

		<div class="mar20-t"><label>
				<b>Тема сообщения</b>
				<br><input class="mar3-t w100" type="text" name="myform[form][subj]">
			</label></div>

		<div class="mar20-t"><label>
				<b>Сообщение *</b>
				<br><textarea class="mar3-t w100" name="myform[form][text]" required></textarea>
			</label></div>

		<div class="mar20-t"><label class="t-gray600">
				<input type="checkbox" checked required> Нажимая кнопку «Отправить», вы соглашаетесь на обработку персональных данных
			</label></div>

		<button class="mar30-tb" type="submit">Отправить</button>
	</form>

	<div id="<?= $form_id ?>Result"></div>

</div>

<div class="w48 w38-tablet w100-phone">

	h3(fas fa-info-circle mar20-b) Наш офис

	_ Не стесняйтесь спрашивать, есть ли у вас есть вопросы относительно наших услуг.

	h4 Адрес
	_ Франция, Париж, ул.Робеспьера, 42

	h4 Соцсети

	ul(out-list)
	* <a class="fab fa-twitter" href="#">Twitter</a>
	* <a class="fab fa-facebook" href="#">Facebook</a>
	* <a class="fab fa-instagram" href="#">Instagram</a>
	/ul

	h4 Контакты

	ul(out-list)
	* <i class="fas fa-envelope"></i> Email: <a class="" href="mailto:info@site.com">info@site.com</a>
	* <i class="fas fa-phone"></i> Phone: <a class="" href="tel:38xxx1234567">+38 xxx 123 45 67</a>
	* <i class="fab fa-telegram"></i> Telegram: <a class="" href="tg:mf">@mf</a>
	* <i class="fab fa-viber"></i> Viber: <a class="" href="viber://chat?number=38xxx1234567">+38xxx1234567</a>
	* <i class="fab fa-whatsapp"></i> Whatsapp: <a class="" href="whatsapp://send?phone=38xxx1234567">+38xxx1234567</a>
	/ul


	div(mar20-tb)
	<?php
	// в html-коде карты для iframe нужно поставить размеры width="100%" height="100%"
	if (function_exists('ushka')) echo ushka('maps', '', '«Ушка maps»');
	?>
	/div

</div>

/div || /div || /div

<script>
	$(document).ready(function() {
		$('#<?= $form_id ?>').submit(function() {
			$.post(
				"<?= $ajax ?>",
				$("#<?= $form_id ?>").serialize(),
				function(msg) {
					$('#<?= $form_id ?>').slideUp('slow');
					$('#<?= $form_id ?>Result').html(msg);
				}
			);
			return false;
		});
	});
</script>