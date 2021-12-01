<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="layout-center-wrap mar50-tb">
	<div class="layout-wrap flex flex-wrap">

		<div class="w50 w100-phone bg-primary50 pad30">

			h3(im-envelope mar20-b) Обратная связь

			<div x-data="{result: '', agreement: true}">
				<form x-show="!result" x-transition x-ref="form" @submit.prevent="fetch('<?= mso_receive_ajax(__FILE__) ?>', {method: 'POST', headers: {'X-Requested-With': 'XMLHttpRequest',},body: new FormData($refs.form)}).then(r => r.text()).then(r => result = r);">
					<input type="hidden" name="myform[form][source]" value="Форма 1">

					_ Вы можете связаться с нами через эту форму.

					<div class="mar20-t">
						<label>
							<b>Ваше имя *</b>
							<br><input class="mar3-t w100 form-input" type="text" name="myform[form][name]" required>
						</label>
					</div>

					<div class="mar20-t">
						<label>
							<b>Ваш email *</b> <span class="t-gray mar10-l">(на него мы отправим ответ)</span>
							<br><input class="mar3-t w100 form-input" type="email" name="myform[form][email]" required>
						</label>
					</div>

					<div class="mar20-t">
						<label>
							<b>Тема сообщения</b>
							<br><input class="mar3-t w100 form-input" type="text" name="myform[form][subj]">
						</label>
					</div>

					<div class="mar20-t">
						<label>
							<b>Сообщение *</b>
							<br><textarea class="mar3-t w100 form-input" name="myform[form][text]" required></textarea>
						</label>
					</div>

					<div class="mar20-t">
						<label class="form-checkbox">
							<input type="checkbox" x-model="agreement" type="checkbox">
							<span class="im-check-circle1" :class="{'im-check-circle1': agreement, 'im-circle': !agreement}"></span>Согласен на обработку персональных данных
						</label>
					</div>

					<button class="button button1 mar30-tb" type="submit" :disabled="!agreement">Отправить</button>
				</form>

				<div x-html="result"></div>
			</div>
		</div>

		<div class="w50 w100-phone pad30">
			_ Lorem ipsum dolor sit amet consectetur, adipisicing elit. Fugit saepe sequi, ducimus expedita quae voluptas tenetur distinctio cumque aliquid libero repellat esse iure aut soluta ex culpa voluptates, accusamus laboriosam accusantium animi ab. Quibusdam, ad. Provident, dolores. Earum incidunt et numquam dignissimos dolorem est doloremque, eos consectetur odio cumque voluptas.

			_ Lorem ipsum dolor sit amet consectetur, adipisicing elit. Fugit saepe sequi, ducimus expedita quae voluptas tenetur distinctio cumque aliquid libero repellat esse iure aut soluta ex culpa voluptates, accusamus laboriosam accusantium animi ab. Quibusdam, ad. Provident, dolores. Earum incidunt et numquam dignissimos dolorem est doloremque, eos consectetur odio cumque voluptas.

			_ Lorem ipsum dolor sit amet consectetur, adipisicing elit. Fugit saepe sequi, ducimus expedita quae voluptas tenetur distinctio cumque aliquid libero repellat esse iure aut soluta ex culpa voluptates, accusamus laboriosam accusantium animi ab. Quibusdam, ad. Provident, dolores. Earum incidunt et numquam dignissimos dolorem est doloremque, eos consectetur odio cumque voluptas.
		</div>

	</div>
</div>