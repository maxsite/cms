<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

# авторасстановка тэгов
function autotag_default($pee)
{
	$pee = str_replace(array("\r\n", "\r"), "\n", $pee);
	
	// if ( mso_hook_present('content_auto_tag_custom') ) 
	//	return mso_hook('content_auto_tag_custom', $pee);
	
	// $pee = mso_hook('content_auto_tag_do', $pee);

	if ( // отдавать как есть - слово в начале текста
		( strpos($pee, '[volkman]') !== false and strpos(trim($pee), '[volkman]') == 0 ) 
		or ( strpos($pee, '[source]') !== false and strpos(trim($pee), '[source]') == 0 ) 
	)
	{
		$pee = str_replace('[volkman]', '', $pee);
		$pee = str_replace('[source]', '', $pee);
		$pee = mso_clean_html( array('1'=>$pee) );
		$pee = str_replace('MSO_N', "\n", $pee);
		return $pee;
	}
	
	//pr($pee, true); # контроль

	# если html-код в [html_r] код [/html_r]
	# в отличие от [html] — отдаёт полностью исходный html без обработок 
	$pee = str_replace('<p>[html_r]</p>', '[html_r]', $pee);
	$pee = str_replace('<p>[/html_r]</p>', '[/html_r]', $pee);
	$pee = preg_replace_callback('!\[html_r\](.*?)\[\/html_r\]!is', '_clean_html_r_do', $pee );
	
	// в исходном html убираем переносы
	$pee = str_replace("\n", "", $pee);
	$pee = $pee . "\n";

	# если html-код в [html] код [/html]
	$pee = str_replace('<p>[html]</p>', '[html]', $pee);
	$pee = str_replace('<p>[/html]</p>', '[/html]', $pee);
	$pee = preg_replace_callback('!\[html\](.*?)\[\/html\]!is', '_clean_html_do', $pee );
	
	
	# исправляем стили браузера
	$pee = str_replace('<hr style="width: 100%; height: 2px;">', "<hr>", $pee);
	

	# всё приводим к MSO_N - признак переноса
	$pee = str_replace('<br />', '<br>', $pee);
	$pee = str_replace('<br/>', '<br>', $pee);
	$pee = str_replace('<br>', 'MSO_N', $pee);
	$pee = str_replace("\n", 'MSO_N', $pee); // все абзацы тоже <br>
	
	# удаляем двойные br
	$pee = str_replace('MSO_NMSO_NMSO_NMSO_N', 'MSO_N', $pee); 
	$pee = str_replace('MSO_NMSO_NMSO_N', 'MSO_N', $pee); 
	$pee = str_replace('MSO_NMSO_N', 'MSO_N', $pee); 
	

	# все MSO_N это абзацы
	$pee = str_replace('MSO_N', "\n", $pee); 
	
	
	# преформатированный текст
	$pee = preg_replace_callback('!(<pre.*?>)(.*?)(</pre>)!is', '_clean_pre_do', $pee );
	
	
	# удалим перед всеми закрывающими тэгами абзац
	$pee = str_replace("\n</", "</", $pee); 

	//_pr($pee, true);
	
	# отбивка некоторых блоков
	//$pee = str_replace("<pre>", "\n<pre>\n", $pee); 
	//$pee = str_replace("</pre>", "\n</pre>\n", $pee);
	
	# расставим все абзацы по p
	$pee = preg_replace('!(.*)\n!', "\n<p>$1</p>", $pee);
	
	# исправим абзацы ошибочные
	$pee = str_replace("<p></p>", "", $pee); 
	$pee = str_replace("<p><p>", "<p>", $pee); 
	$pee = str_replace("</p></p>", "</p>", $pee); 
	$pee = str_replace("</script></p>", "</script>", $pee); 
	$pee = str_replace("<p>	<div", "<div", $pee); 
	$pee = str_replace("<p> </div>", "</div>", $pee); 
	
	# блочные тэги
	$allblocks = '(?:table|thead|tfoot|caption|colgroup|center|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|code|select|form|map|area|blockquote|address|math|style|input|embed|h1|h2|h3|h4|h5|h6|hr|p|hgroup|section|header|footer|article|aside|nav|main)';
	
	# здесь не нужно ставить <p> и </p>
	$pee = preg_replace('!<p>(<' . $allblocks . '[^>]*>)</p>!', "\n$1", $pee); # <p><tag></p>
	$pee = preg_replace('!<p>(<' . $allblocks . '[^>]*>)!', "\n$1", $pee); # <p><tag> 
	$pee = preg_replace('!<p>(</' . $allblocks . '[^>]*>)!', "\n$1", $pee); # <p></tag> 
	$pee = preg_replace('!(<' . $allblocks . '[^>]*>)</p>!', "\n$1", $pee); # <tag></p>
	$pee = preg_replace('!(</' . $allblocks . '>)</p>!', "$1", $pee); # </tag></p>
	$pee = preg_replace('!(</' . $allblocks . '>) </p>!', "$1", $pee); # </tag></p>
	
	$pee = preg_replace('!<p>&nbsp;&nbsp;(<' . $allblocks . '[^>]*>)!', "\n$1", $pee); # <p>&nbsp;&nbsp;<tag> 
	$pee = preg_replace('!<p>&nbsp;(<' . $allblocks . '[^>]*>)!', "\n$1", $pee); # <p>&nbsp;<tag> 
	
	# если был cut, то уберем с ссылки абзац
	$pee = str_replace('<p><a id="cut"></a></p>', '<a id="cut"></a>', $pee); 
	
	# специфичные ошибки
	$pee = str_replace("<blockquote>\n<p>", "<blockquote>", $pee); 
	$pee = preg_replace('!<li>(.*)</p>\n!', "<li>$1</li>\n", $pee); # <li>...</p>
	$pee = str_replace("<ul>\n\n<li>", "<ul><li>", $pee); 
	$pee = str_replace("</li>\n\n<li>", "</li>\n<li>", $pee);
	
	$pee = preg_replace('!<p><a id="(.*)"></a></p>\n!', "<a id=\"$1\"></a>\n", $pee); # <li>...</p>
	
	
	### подчистим некоторые блочные тэги удалим <p> внутри. MSO_N_BLOCK = </p>
	
	# code
	$pee = preg_replace_callback('!(<code.*?>)(.*?)(</code>)!is', '_clean_block2', $pee );
	$pee = str_replace('MSO_N_BLOCK', "<br>", $pee); // заменим перенос в блочном на <br> 
	
	# blockquote
	$pee = preg_replace_callback('!(<blockquote.*?>)(.*?)(</blockquote>)!is', '_clean_block', $pee );
	$pee = str_replace('MSO_N_BLOCK', "<br>", $pee); // заменим перенос в блочном на ''	
	
	
	# еще раз подчистка
	$pee = str_replace('MSO_N', "\n", $pee); 
	
	$pee = preg_replace('!<p><br(.*)></p>!', "<br$1>", $pee);
	$pee = preg_replace('!<p><br></p>!', "<br>", $pee);
	
	
		
	# завершим [html]
	$pee = str_replace('<p>[html_base64]', '[html_base64]', $pee);
	$pee = str_replace('[/html_base64]</p>', '[/html_base64]', $pee);
	$pee = str_replace('[/html_base64] </p>', '[/html_base64]', $pee);
	
	$pee = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', '_clean_html_posle', $pee );
	
	
	# [br]
	$pee = str_replace('[br]', '<br style="clear:both">', $pee);
	$pee = str_replace('[br none]', '<br>', $pee);
	$pee = str_replace('[br left]', '<br style="clear:left">', $pee);
	$pee = str_replace('[br right]', '<br style="clear:right">', $pee);
	
	$pee = str_replace('<p><br></p>', '<br>', $pee);
	$pee = preg_replace('!<p><br(.*)></p>!', "<br$1>", $pee);
	

	# принудительный пробел
	$pee = str_replace('[nbsp]', '&nbsp;', $pee);
	
	
	# перенос строки в конце текста
	$pee = $pee . "\n";
	
	# кастомный автотэг
	// $pee = mso_hook('content_auto_tag_my', $pee);
	
	// _pr($pee, true); # контроль

	return $pee;

}


# аналогично mso_clean_html_do, только без замен — [html_r] ... [/html_r]
function _clean_html_r_do($matches)
{
	return '[html_base64]' . base64_encode($matches[1]) . '[/html_base64]';
}

# предподготовка html в тексте между [html] ... [/html]
# конвертируем все символы в реальный html
# после этого кодируем его в одну строчку base64
# после всех операций в mso_balance_tags декодируем его в обычный текст mso_clean_html_posle
# кодирование нужно для того, чтобы корректно пропустить весь остальной текст
function _clean_html_do($matches)
{
	$arr1 = array('&amp;', '&lt;', '&gt;', '<br />', '<br>', '&nbsp;');
	$arr2 = array('&',     '<',    '>',    "\n",     "\n",   ' ');
	$m = trim( str_replace($arr1, $arr2, $matches[1]) );
	$m = '[html_base64]' . base64_encode($m) . '[/html_base64]';
	return $m;
}


# pre, которое загоняется в [html_base64]
function _clean_pre_do($matches)
{
	$text = trim($matches[2]);

	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', '', $text);
	$text = str_replace('[', '&#91;', $text);
	$text = str_replace(']', '&#93;', $text);
	$text = str_replace("<br>", "MSO_N", $text);
	$text = str_replace("<br />", "<br>", $text);
	$text = str_replace("<br/>", "<br>", $text);
	$text = str_replace("<br>", "MSO_N", $text);
	
	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('>', '&gt;', $text);
	$text = str_replace('&lt;pre', '<pre', $text);
	$text = str_replace('&lt;/pre', '</pre', $text);
	$text = str_replace('pre&gt;', 'pre>', $text);

	
	//pr($text);
	$text = $matches[1] . '[html_base64]' . base64_encode($text) . '[/html_base64]'. $matches[3];

	//_pr($text);
	return $text;
}

# аналогично, только еще и [] меняем 
function _clean_block2($matches)
{

	if ( is_array($matches) )
		$text = "" . $matches[1] . $matches[2] . $matches[3] . "\n";
	else
		$text = $matches;

	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', 'MSO_N_BLOCK', $text);
	$text = str_replace('[', '&#91;', $text);
	$text = str_replace(']', '&#93;', $text);
	$text = str_replace("<br />", "<br>", $text);
	$text = str_replace("<br/>", "<br>", $text);
	$text = str_replace("<br>", "MSO_N", $text);

	return $text;
}

# подчистка блоковых тэгов
# удаляем в них <p>
function _clean_block($matches)
{

	if ( is_array($matches) )
		$text = "" . $matches[1] . $matches[2] . $matches[3] . "\n";
	else
		$text = $matches;

	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', 'MSO_N_BLOCK', $text);
	$text = str_replace("<br>", "MSO_N", $text);
	$text = str_replace("<br />", "<br>", $text);
	$text = str_replace("<br/>", "<br>", $text);
	$text = str_replace("<br>", "MSO_N", $text);
	//$text = str_replace("\n", "MSO_N", $text);

	return $text;
}

# декодирование
function _clean_html_posle($matches)
{
	return base64_decode($matches[1]);
}