<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

echo mso_load_jquery('jquery.cookie.js') . mso_load_jquery('jquery.showhide.js')

?>

<script>
$(function () {
$.cookie.json = true; $("div.show").showHide({time: 200, useID: false, clickElem: "a.link", foldElem: ".show-text", visible: true});
});

function UaddText(t, t2, id){var comment = document.getElementById("ut"+id);  if (document.selection) { comment.focus(); sel = document.selection.createRange(); sel.text = t + sel.text + t2; comment.focus(); } else if (comment.selectionStart || comment.selectionStart == "0") { var startPos = comment.selectionStart; var endPos = comment.selectionEnd; var cursorPos = endPos; var scrollTop = comment.scrollTop; if (startPos != endPos) { comment.value = comment.value.substring(0, startPos) + t + comment.value.substring(startPos, endPos) + t2 + comment.value.substring(endPos, comment.value.length); cursorPos = startPos + t.length } else { comment.value = comment.value.substring(0, startPos) + t + t2 + comment.value.substring(endPos, comment.value.length); cursorPos = startPos + t.length; } comment.focus(); comment.selectionStart = cursorPos; comment.selectionEnd = cursorPos; comment.scrollTop = scrollTop; } else { comment.value += t + t2; } }

</script>

<h1><?= t('Ушки')?></h1>

<div class="show"><div class="show-header"><a href="#" class="link"><?= t('Описание') ?></a></div><div class="show-text">
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
</div></div>

<?php
	// новая ушка
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_new', 'f_ushka_new')) )
	{
		mso_checkreferer();
		
		if ($ushka_new = trim($post['f_ushka_new'])) 
		{
			// текущие ушки
			$ushki = mso_get_float_option('ushki', 'ushki', array());
			
			$ushki[] = array('name' => $ushka_new, 'type' => 'html', 'text' => '' ); // добавили новую
			
			mso_add_float_option('ushki', $ushki, 'ushki'); // и в опции
			
			echo '<div class="update pos-fixed w200px pad10 pos20-r pos0-t t-center">' . t('Ушка добавлена!') . '</div>';
		}
		else 
			echo '<div class="error pos-fixed w200px pad10 pos20-r pos0-t t-center">' . t('Необходимо указать название ушки!') . '</div>';
	}
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_ushka')) )
	{
		mso_checkreferer();
		
		$ushki = $post['f_ushka'];
		$ushki_new = array();
		
		foreach ($ushki as $us)
		{
			if (!isset($us['delete'])) $ushki_new[] = $us;
		}
		
		mso_add_float_option('ushki', $ushki_new, 'ushki');

		echo '<div class="update pos-fixed w200px pad10 pos20-r pos0-t t-center">' . t('Обновлено!') . '</div>';
	}
	
	$ushki = mso_get_float_option('ushki', 'ushki', array());
	
	usort($ushki, "ushki_cmp");
	
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
	
	echo '<form method="post">' . mso_form_session('f_session_id') 
	. '<div class="mar20-tb t-lh100"><input name="f_ushka_new" type="text" value="" placeholder="' . t('Новая ушка...') . '"> <button type="submit" name="f_submit_new" class="button i-plus pad8-tb">' . t('Добавить новую ушку') . '</button></div></form>';
	
	$form = '';
	
	$alpha = $class_alpha =	$beta = '';
	$classA = 'mar20-t';
	
	foreach ($ushki as $id => $us)
	{
		$sel_html = $sel_php = '';
		
		if ($us['type'] == 'php') 
			$sel_php = ' selected="selected" ';
		else 
			$sel_html = ' selected="selected" ';
		
		$ra = explode("\n", htmlspecialchars($us['text']));
		$rr = count($ra);
		
		// если в строке больше 80 символов, добавляем row
		foreach($ra as $rs)
		{
			$l = mb_strlen($rs, 'UTF8');
			if ($l > 80) $rr = $rr + floor($l / 80);
		}	
		
		if ($rr > 20) $rr = 20;
		if ($rr < 2)  $rr = 2;
		
		$alpha = mb_substr($us['name'], 0, 1);
		if ($alpha !== $beta) $class_alpha = $classA;
				
		$form .= 
'<div class="ushka show ' . $class_alpha . '"><dl>
	<dt class="show-header"><a href="#" class="link"><span class="">' . $us['name'] . '</span></a></dt>'
	. '<dd class="show-text">'
		. '<p class="ushki_title"> 
			<input name="f_ushka['.$id.'][name]" type="text" class="ushka_name" value="'. $us['name'] . '">
			<select name="f_ushka[' . $id . '][type]">
				<option value="html"' . $sel_html . '>TEXT/HTML</option>
				<option value="php"' . $sel_php . '>PHP</option>
			</select>
			<label class="mar20-r"><input class="mar10-l" name="f_ushka[' . $id . '][delete]" type="checkbox"> ' . t('Удалить') . '</label>
			<button type="button" class="button t90 pad5-tb pad10-rl" title="' . tf('Абзац') . '" onClick="UaddText(\'<p>\', \'</p>\', ' . $id . ')">P</button>
			<button type="button" class="button t90 pad5-tb pad10-rl" title="' . tf('Жирный') . '" onClick="UaddText(\'<b>\', \'</b>\', ' . $id . ')">B</button>
			<button type="button" class="button t90 pad5-tb pad10-rl" title="' . tf('Курсив') . '" onClick="UaddText(\'<i>\', \'</i>\', ' . $id . ')">I</button>
			<button type="button" class="button t90 pad5-tb pad10-rl" title="' . tf('Ссылка') . '" onClick="UaddText(\'<a href=&quot;&quot;>\', \'</a>\', ' . $id . ')">A</button>
			<button type="button" class="button t90 pad5-tb pad10-rl" title="' . tf('Блок DIV') . '" onClick="UaddText(\'<div>\', \'</div>\', ' . $id . ')">D</button>
			<button type="button" class="button t90 pad5-tb pad10-rl" title="' . tf('Элемент SPAN') . '" onClick="UaddText(\'<span>\', \'</span>\', ' . $id . ') ">S</button>
			<button type="button" class="button t90 pad5-tb pad10-rl" title="' . tf('Заголовок H1') . '" onClick="UaddText(\'<h1>\', \'</h1>\', ' . $id . ')">H</button>
		</p>
		<textarea id="ut' . $id . '" name="f_ushka[' . $id . '][text]" rows="' . $rr . '">' . htmlspecialchars($us['text']) . '</textarea>
	</dd>
</dl></div>';
			
		$class_alpha = '';
		$beta = $alpha;
	}
	
	
	if ($form)
	{
		echo '<form method="post">' . mso_form_session('f_session_id');
		echo '<div class="plugin-ushki">';
		echo str_replace("\t", '', $form);
		echo '<button type="submit" name="f_submit" class="button mar20-t i-save">' . t('Сохранить изменения') . '</button>';
		echo '</div></form>';
	}

// функция сортировки
function ushki_cmp($a, $b)
{
	return strcasecmp($a["name"], $b["name"]);
}
	
# end of file