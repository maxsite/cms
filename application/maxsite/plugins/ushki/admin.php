<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

?>
<h1><?= t('Ушки')?></h1>

<p class="info"><?= t('С помощью ушек вы можете размещать произвольный html/php код в шаблоне, виджете или прочих плагинах. Ушки удобно использовать для вывода счетчика, рекламы и т.п. Просто создайте ушку, а потом укажите её имя в виджете или с помощью кода:') ?></p>
<pre>
&lt;?php
	if (function_exists('ushka')) echo ushka('имя ушки');
?&gt;
</pre>

<p class="info"><?= t('Вы можете вывести произвольную ушку прямо в тексте. Данный код выведет ушку «reklama»:') ?></p>

<pre>
[ushka=reklama]
</pre>

<?php

	$CI = & get_instance();
	
	// ушки хранят свои данные во flat-опциях - файлы в кэше, только отдельном каталоге flat и не имеют времени
	
	$key = 'ushki';
	$type = 'ushki';
	
	// новая ушка
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_new', 'f_ushka_new')) )
	{
		mso_checkreferer();
		
		if ($ushka_new = trim($post['f_ushka_new'])) 
		{
			// текущие ушки
			$ushki = mso_get_float_option($key, $type, array());
			$ushki[] = array('name' => $ushka_new, 'type' => 'html', 'text' => '' ); // добавили новую
			mso_add_float_option($key, $ushki, $type); // и в опции
			echo '<div class="update">' . t('Ушка добавлена!') . '</div>';
		}
		else 
			echo '<div class="error">' . t('Необходимо указать название ушки!') . '</div>';
	}
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_ushka')) )
	{
		mso_checkreferer();
		
		// pr($post);
		
		$ushki = $post['f_ushka'];
		$ushki_new = array();
		
		foreach ($ushki as $us)
		{
			if (!isset($us['delete'])) $ushki_new[] = $us;
		}
		
		// pr($ushki_new);
		mso_add_float_option($key, $ushki_new, $type);

		echo '<div class="update">' . t('Обновлено!') . '</div>';
	}
	

		
		$ushki = mso_get_float_option($key, $type, array());
		
		/*
		[0] => 
				[name] => 'ушка 1'
				[type] => 'html'
				[text] => ''
		[1] => 
				[name] => 'ушка 2'
				[type] => 'php'
				[text] => ''				
		...
		*/
		// pr($ushki);
		
		echo '<form method="post">' . mso_form_session('f_session_id') . '
		<p><strong>' . t('Новая ушка:') . '</strong> ' . ' <input name="f_ushka_new" type="text" value="">
		<button type="submit" name="f_submit_new" class="i add-new">' . t('Добавить новую ушку') . '</button></p>
		</form>';
		
		$form = '';
		
		$form .=  mso_load_jquery('jquery.cookie.js')
			. mso_load_jquery('jquery.showhide.js')
			. '
<script>
$(function () {
$.cookie.json = true; $("div.show").showHide({time: 200, useID: false, clickElem: "a.link", foldElem: "dd.show-text", visible: true});
});
</script>
';
		
		foreach ($ushki as $id => $us)
		{
			$sel_html = $sel_php = '';
			
			if ($us['type'] == 'php') $sel_php = ' selected="selected" ';
				else $sel_html = ' selected="selected" ';
			
			$form .= 
				'<div class="ushka show"><dl>
					<dt class="show-header"><a href="#" class="link">' . $us['name'] . '</a></dt>'
					. '<dd class="show-text">'
						. '<p class="ushki_title"> 
							<input name="f_ushka['.$id.'][name]" type="text" class="ushka_name" value="'. $us['name'] . '">
							<select name="f_ushka[' . $id . '][type]">
								<option value="html"' . $sel_html . '>TEXT/HTML</option>
								<option value="php"' . $sel_php . '>PHP</option>
							</select>
							<label><input name="f_ushka[' . $id . '][delete]" type="checkbox"> ' . t('Удалить') . '</label>
						</p>
						<textarea name="f_ushka[' . $id . '][text]">' . htmlspecialchars($us['text']) . '</textarea>
					</dd>
					
				</dl></div>
				';
		}
		
		if ($form)
		{
			echo '<form method="post">' . mso_form_session('f_session_id');
			echo '<div class="plugin-ushki">';
			echo $form;
			echo '<button type="submit" name="f_submit" class="i save">' . t('Сохранить изменения') . '</button>';
			echo '</div></form>';
		}

?>