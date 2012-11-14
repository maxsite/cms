<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="comment-form">
	<form action="" method="post">
		<input type="hidden" name="comments_page_id" value="<?= $page_id ?>">
		<?= mso_form_session('comments_session') ?>
		
		<?php  if (!is_login()) { ?>
		
			<?php if (! $comuser = is_login_comuser()) { ?>
			
				<?php if (mso_get_option('allow_comment_anonim', 'general', '1') ) { ?>
					<div class="comments-noreg">
						
						<?php if (mso_get_option('allow_comment_comusers', 'general', '1')) { ?>
						<label><input type="radio" name="comments_reg" id="comments_reg_1" value="noreg"  checked="checked" class="no-margin"> <span class="black"><?=tf('Не регистрировать/аноним')?></span></label> <br>
						<?php } else { ?>
						<input type="hidden" name="comments_reg" value="noreg">
						<?php } ?>
						
						<label for="comments_author" class="comments_author"><?=tf('Ваше имя')?></label>
						<input type="text" name="comments_author" id="comments_author" class="text" onfocus="document.getElementById('comments_reg_1').checked = 'checked';">
						<p style="margin: 10px 0 0 0;"><span><?php
							if (mso_get_option('new_comment_anonim_moderate', 'general', '1') )
								echo tf('Используйте нормальные имена. Ваш комментарий будет опубликован после проверки.');
							else
								echo tf('Используйте нормальные имена.');
								
						?></span></p>
					</div>		
				<?php } ?>
			
				<?php if (mso_get_option('allow_comment_comusers', 'general', '1')) { ?>
			<div class="comments-reg">
			
				<?php if ( mso_get_option('allow_comment_anonim', 'general', '1') ) {	?>
					<label><input type="radio" name="comments_reg" id="comments_reg_2" value="reg" class="no-margin"> 
				<?php } else { ?>
					<input type="hidden" name="comments_reg" id="comments_reg_2" value="reg" class="no-margin" checked="checked"> 
				<?php } ?>
				
			
					<span class="black"><?=tf('Если вы уже зарегистрированы как комментатор или хотите зарегистрироваться, укажите пароль и свой действующий email. <br><em>(При регистрации на указанный адрес придет письмо с кодом активации и ссылкой на ваш персональный аккаунт, где вы сможете изменить свои данные, включая адрес сайта, ник, описание, контакты и т.д.)</em>')?></span></label><br>
					
					<?php mso_hook('page-comment-form') ?>
					
				<label for="comments_email" class="comments_email"><?= tf('E-mail') ?></label>
				<input type="text" name="comments_email" id="comments_email" value="" class="text" onfocus="document.getElementById('comments_reg_2').checked = 'checked';"><br>

				<label for="comments_password" class="comments_password"><?= tf('Пароль') ?></label>
				<input type="password" name="comments_password" id="comments_password" value="" class="text" onfocus="document.getElementById('comments_reg_2').checked = 'checked';"><br>
				
				
			</div>
				<?php } ?>
			
			<?php  } else { // comusers?>
				
				<input type="hidden" name="comments_email" value="<?= $comuser['comusers_email'] ?>">
				<input type="hidden" name="comments_password" value="<?= $comuser['comusers_password'] ?>">
				<input type="hidden" name="comments_password_md" value="1">
				<input type="hidden" name="comments_reg" value="reg">
				
				<div class="comments-user comments-comuser">
					<?php
						if (!$comuser['comusers_nik']) echo tf('Привет!');
							else echo tf('Привет,') . ' ' . $comuser['comusers_nik'] . '!';
					?> <a href="<?= getinfo('siteurl') ?>logout"><?=tf('Выйти')?></a>
				</div>
			
			<?php  } ?>
			
		<?php  } else { // users?>
			<input type="hidden" name="comments_user_id" value="<?= getinfo('users_id') ?>">
		
			<div class="comments-user">
				<?=tf('Привет')?>, <?= getinfo('users_nik') ?>! <a href="<?= getinfo('siteurl') ?>logout"><?=tf('Выйти')?></a>
			</div>
		
		<?php  } ?>
		
		<div class="comments-textarea">
			
			<label for="comments_content"><?=tf('Ваш комментарий')?></label>
			<?php mso_hook('comments_content_start')  ?>
			<textarea name="comments_content" id="comments_content" rows="10" cols="80"></textarea>

			<?php mso_hook('comments_content_end')  ?>
			
			<div><input name="comments_submit" type="submit" value="<?=tf('Отправить')?>" class="comments_submit"></div>
		</div>
		
	</form>
</div><!-- div class=comment-form -->
