<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="mso-comment-leave">{{ mso_get_option('leave_a_comment', 'templates',  tf('Оставьте комментарий!')) }}</div>

<div class="mso-comment-form">
	<form class="mso-form" method="post">
		<input type="hidden" name="comments_page_id" value="{{ $page['page_id'] }}">
		{{ mso_form_session('comments_session') }}

		<div class="mso-comments-textarea">
		
			{%  if (is_login()) : %}
				<input type="hidden" name="comments_user_id" value="{{ getinfo('users_id') }}">
				<div class="mso-comments-user">
					{{ tf('Привет') }}, {{ getinfo('users_nik') }}! <a class="mso-comments-logout" href="{{ getinfo('siteurl') }}logout">{{ tf('Выйти') }}</a>
				</div>
			{% endif %}
		
			{% if ($comuser = is_login_comuser()) : %}
				<input type="hidden" name="comments_email" value="{{ $comuser['comusers_email'] }}">
				<input type="hidden" name="comments_password" value="{{ mso_de_code($comuser['comusers_password'], 'decode') }}">
				<input type="hidden" name="comments_password_md" value="1">
				<input type="hidden" name="comments_reg" value="reg">
				
				<div class="mso-comments-user mso-comments-comuser">
				
					{% if (!$comuser['comusers_nik']) : %} 
						{{ tf('Привет!') }}
					{% else : %}
						{{ tf('Привет,') }} <a href="{{ getinfo('siteurl') . 'users/' . $comuser['comusers_id'] }}">{{ $comuser['comusers_nik'] }}</a>!
					{% endif %}
					
					<a class="mso-comments-logout" href="{{ getinfo('siteurl') }}logout">{{ tf('Выйти') }}</a>
				</div>
			{% endif %}
		
			{% mso_hook('comments_content_start') %}
			
			<textarea name="comments_content" id="comments_content" rows="10"></textarea>
			
			<!-- нет залогирования -->
			{% if (!is_login() and (!$comuser = is_login_comuser())) : %}
			
				<!-- обычная форма -->
				{%  if (!mso_get_option('form_comment_easy', 'general', '0')) : %}
					<div class="mso-comments-auth">
						
						{% if (mso_get_option('allow_comment_anonim', 'general', '1') ) : %}
						
							{% $t_hidden = mso_get_option('allow_comment_comusers', 'general', '1') ? 'type="radio" checked="checked"' : 'type="hidden"'; %}
							<p>
								<label><input {{ $t_hidden }} name="comments_reg" id="comments_reg_1" value="noreg"> {{ tf('Ваше имя') }}</label>
								
								<input type="text" name="comments_author" onfocus="document.getElementById('comments_reg_1').checked = 'checked';" placeholder="{{ tf('Ваше имя') }}">
								<br><i>{{ $to_moderate }}</i>
							</p>
						
						{% endif %}
					
						
						{% if (mso_get_option('allow_comment_comusers', 'general', '1')) : %}
						
							{% $t_hidden = mso_get_option('allow_comment_anonim', 'general', '1') ? 'type="radio"' : 'type="hidden" checked="checked"'; %}
							<p>
								<label><input {{ $t_hidden }} name="comments_reg" id="comments_reg_2" value="reg"> {{ tf('Вход/регистрация') }} <a href="{{ getinfo('siteurl') }}login">{{ tf('(войти без комментирования)') }}</a></label>
							</p>
							
							<p>
								<label for="comments_email">{{ tf('E-mail') }}</label>
								<input type="email" name="comments_email" id="comments_email" onfocus="document.getElementById('comments_reg_2').checked = 'checked';"> 
										
								&nbsp;&nbsp;
								
								<button type="button" title="{{ tf('Использовать email как пароль') }}" onclick="document.getElementById('comments_reg_2').checked = 'checked'; document.getElementById('comments_password').value=document.getElementById('comments_email').value; ">&gt;</button>
								
								&nbsp;&nbsp;
								
								<label for="comments_password">{{ tf('Пароль') }}</label>
								<input type="password" name="comments_password" id="comments_password" onfocus="document.getElementById('comments_reg_2').checked = 'checked';">
							</p>
							
							<p>
								<label for="comments_comusers_nik">{{ tf('Ваше имя') }}</label>
								<input type="text" name="comments_comusers_nik" id="comments_comusers_nik" onfocus="document.getElementById('comments_reg_2').checked = 'checked';">
								
								&nbsp;&nbsp;
								
								<label for="comments_comusers_url">{{ tf('Сайт') }}</label>
								<input type="url" name="comments_comusers_url" id="comments_comusers_url" onfocus="document.getElementById('comments_reg_2').checked = 'checked';">
							</p>
						
						{% endif %}


					{% if ($form_comment_comuser = mso_get_option('form_comment_comuser', 'general', '')) : %} 
						<p><i>{{ $form_comment_comuser }}</i></p>
					{% endif %}
						
					</div> <!-- class="mso-comments-auth"-->
					
				{% endif %}	<!-- / обычная форма-->
								

				<!-- простая форма -->
				{%  if (mso_get_option('form_comment_easy', 'general', '0')) : %}
					<div class="mso-comments-auth">

						{% if (mso_get_option('allow_comment_anonim', 'general', '1') ) : %}
						
							<input type="hidden" name="comments_reg" id="comments_reg_1" value="noreg">
							
							<p><input type="text" name="comments_author" placeholder="{{ tf('Ваше имя') }}" class="mso-comments-input-author"></p>
								
							<p><i>{{ $to_moderate }}</i></p>
						
						{% endif %}
						
						{% if (mso_get_option('allow_comment_comusers', 'general', '1')) : %}
							<p>{{ $to_login }}</p>
						{% endif %}
						
					</div> <!-- class="mso-comments-auth"-->
				{% endif %}	<!-- / простая форма-->

				{% if (mso_hook_present('page-comment-form')) : %}
					<p class="mso-page-comment-form">{% mso_hook('page-comment-form') %}</p>
				{% endif %}

			{% endif %} <!-- / нет залогирования -->

			{% mso_hook('comments_content_end') %}
			
			<div class="mar10-tb"><button name="comments_submit" type="submit">{{ tf('Отправить') }}</button></div>
			
		</div><!-- div class="mso-comments-textarea" -->
	</form>
</div><!-- div class=mso-comment-form -->
