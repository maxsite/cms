<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function bbcode_autoload($args = array())
{
	//mso_create_allow('bbcode_edit', t('Админ-доступ к настройкам') . ' ' . t('bbcode'));
	$options = mso_get_option('plugin_bbcode', 'plugins', array());
	if (!array_key_exists('bbcode_level', $options)) $options['bbcode_level'] = 1;
	if ( ($options['bbcode_level'] == 1) or ($options['bbcode_level'] == 3) ) mso_hook_add( 'content', 'bbcode_custom', 20); # хук на вывод контента
	if ( ($options['bbcode_level'] == 2) or ($options['bbcode_level'] == 3) ) mso_hook_add( 'comments_content', 'bbcode_custom', 20);
	
	mso_hook_add( 'editor_content', 'bbcode_editor_content'); // обработка текста для визуалього редактора
	
}

# функция выполняется при деинсталяции плагина
function bbcode_uninstall($args = array())
{
	 mso_delete_option('plugin_bbcode', 'plugins' ); // удалим созданные опции
	// mso_remove_allow('bbcode_edit'); // удалим созданные разрешения
	return $args;
}

function bbcode_pre_callback($matches)
{
	$m = $matches[2];

	$m = str_replace('[', '&#91;', $m);
	$m = str_replace(']', '&#93;', $m);

	$m = '<pre' . $matches[1] . '>' . $m . '</pre>';

	return $m;
}

function bbcode_mso_options()
{
	/*
	if ( !mso_check_allow('bbcode_edit') )
	{
		echo t('Доступ запрещен');
		return;
	}
	*/

	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_bbcode', 'plugins',
		array(
			'bbcode_level' => array(
							'type' => 'select',
							'name' => t('Где использовать'),
							'description' => t('Укажите, где должен работать плагин'),
							'values' => t('1||На страницах #2||В комментариях #3||На страницах и в комментариях'),
							'default' => '1'
						),
			),
		t('Настройки плагина bbcode'),
		t('Укажите необходимые опции.')
	);
}

# функции плагина
function bbcode_custom($text = '')
{
	$text = preg_replace_callback('~\[pre(.*?)\](.*?)\[\/pre\]~si', 'bbcode_pre_callback', $text );

    $preg = array(
		
		# Text
		
		// b - важное выделение в тексте 
		'~\[b (.*?)\](.*?)\[\/b\]~si'			=> '<strong $1>$2</strong>',
		'~\[b\](.*?)\[\/b\]~si'					=> '<strong>$1</strong>',
		
		// просто полужирное начертание
		'~\[bold (.*?)\](.*?)\[\/bold\]~si'			=> '<b $1>$2</b>',
		'~\[bold\](.*?)\[\/bold\]~si'					=> '<b>$1</b>',	
		
		// выжное выделение в тексте - акцент
		'~\[i (.*?)\](.*?)\[\/i\]~si'			=> '<em$1>$2</em>',
		'~\[i\](.*?)\[\/i\]~si'					=> '<em>$1</em>',
		
		// просто выделение курсивом
		'~\[italic (.*?)\](.*?)\[\/italic\]~si'			=> '<i$1>$2</i>',
		'~\[italic\](.*?)\[\/italic\]~si'					=> '<i>$1</i>',
		
		// удаленный элемент текста
		'~\[del (.*?)\](.*?)\[\/del\]~si'			=> '<del $1>$2</del>',
		'~\[del\](.*?)\[\/del\]~si'					=> '<del>$1</del>',	
		
		// просто выделение зачеркиванием
		'~\[s (.*?)\](.*?)\[\/s\]~si'			=> '<s $1>$2</s>',
		'~\[s\](.*?)\[\/s\]~si'					=> '<s>$1</s>',	
			
		'~\[u (.*?)\](.*?)\[\/u\]~si'			=> '<u $1>$2</u>',
		'~\[u\](.*?)\[\/u\]~si'					=> '<u>$1</u>',
		
		'~\[sub\](.*?)\[\/sub\]~si'				=> '<sub>$1</sub>',
		'~\[sup\](.*?)\[\/sup\]~si'				=> '<sup>$1</sup>',
		
		'~\[small\](.*?)\[\/small\]~si'				=> '<small>$1</small>',
		
		
		'~\[color=(.*?)\](.*?)\[\/color\]~si'	=> '<span style="color:$1">$2</span>',
		'~\[size=(.*?)\](.*?)\[\/size\]~si'		=> '<span style="font-size:$1">$2</span>',

		# стиль для блока [div=color: red]текст[/div]
		'~\[div=(.*?)\](.*?)\[\/div\]~si'		=> '<div style="$1">$2</div>',
		'~\[span=(.*?)\](.*?)\[\/span\]~si'		=> '<span style="$1">$2</span>',
		
		# div
		'~\[left (.*?)\](.*?)\[\/left\]~si'		=> '<div style="text-align: left; $1">$2</div>',
		'~\[left\](.*?)\[\/left\]~si'			=> '<div style="text-align: left;">$1</div>',

		'~\[right (.*?)\](.*?)\[\/right\]~si'	=> '<div style="text-align: right; $1">$2</div>',
		'~\[right\](.*?)\[\/right\]~si'			=> '<div style="text-align: right;">$1</div>',

		'~\[center (.*?)\](.*?)\[\/center\]~si'	=> '<div style="text-align: center; $1">$2</div>',
		'~\[center\](.*?)\[\/center\]~si'		=> '<div style="text-align: center;">$1</div>',

		'~\[justify (.*?)\](.*?)\[\/justify\]~si'	=> '<div style="text-align: justify; $1">$2</div>',
		'~\[justify\](.*?)\[\/justify\]~si'			=> '<div style="text-align: justify;">$1</div>',
		
		# p
		'~\[p\](.*?)\[\/p\]~si'					=> '<p>$1</p>', // абзац
		'~\[p=(.*?)\](.*?)\[\/p\]~si'			=> '<p style="$1">$2</p>',
		'~\[p (.*?)\](.*?)\[\/p\]~si'			=> '<p $1>$2</p>',
		
		'~\[pleft\](.*?)\[\/pleft\]~si'			=> '<p style="text-align: left;">$1</p>',
		'~\[pright\](.*?)\[\/pright\]~si'		=> '<p style="text-align: right;">$1</p>',
		'~\[pcenter\](.*?)\[\/pcenter\]~si'		=> '<p style="text-align: center;">$1</p>',
		'~\[pjustify\](.*?)\[\/pjustify\]~si'	=> '<p style="text-align: justify;">$1</p>',
		
		
		# 
		'~\[abbr\](.*?)\[\/abbr\]~si'			=> '<abbr>$1</abbr>',
		'~\[abbr (.*?)\](.*?)\[\/abbr\]~si' 	=> '<abbr title="$1">$2</abbr>',
		
		'~\[q\](.*?)\[\/q\]~si'	  				=> '<q>$1</q>',
		'~\[q (.*?)\](.*?)\[\/q\]~si' 			=> '<q $1">$2</q>',
		
		'~\[cite\](.*?)\[\/cite\]~si'			=> '<cite>$1</cite>',
		'~\[cite (.*?)\](.*?)\[\/cite\]~si' 	=> '<cite $1">$2</cite>',
		
		'~\[address\](.*?)\[\/address\]~si'	  		=> '<address>$1</address>',
		'~\[address (.*?)\](.*?)\[\/address\]~si' 	=> '<address $1">$2</address>',
		
		'~\[dfn\](.*?)\[\/dfn\]~si'	  			=> '<dfn>$1</dfn>',
		'~\[dfn (.*?)\](.*?)\[\/dfn\]~si' 		=> '<dfn $1">$2</dfn>',

		'~\[dl\](.*?)\[\/dl\]~si'				=> '<dl>$1</dl>',
		'~\[dl (.*?)\](.*?)\[\/dl\]~si' 		=> '<dl $1">$2</dl>',
		
		'~\[dt\](.*?)\[\/dt\]~si'				=> '<dt>$1</dt>',
		'~\[dt (.*?)\](.*?)\[\/dt\]~si' 		=> '<dt $1">$2</dt>',
		
		'~\[dd\](.*?)\[\/dd\]~si'				=> '<dd>$1</dd>',
		'~\[dd (.*?)\](.*?)\[\/dd\]~si' 		=> '<dd $1">$2</dd>',
		
		'~\[ins\](.*?)\[\/ins\]~si'	  			=> '<ins>$1</ins>',
		'~\[ins (.*?)\](.*?)\[\/ins\]~si' 		=> '<ins $1">$2</ins>',
				
		'~\[hr\]~si'   							=> '<hr>',
		'~\[line\]~si'	 						=> '<hr>',


		# table
		'~\[table\]~si'	  						=> '<table>',
		'~\[table (.*?)\]~si' 					=> '<table $1>',
		'~\[\/table\]~si'						=> '</table>',

		'~\[tr\]~si'   							=> '<tr>',
		'~\[tr (.*?)\]~si' 						=> '<tr $1>',
		'~\[\/tr\]~si'	 						=> '</tr>',

		'~\[td\]~si'  		 					=> '<td>',
		'~\[td (.*?)\]~si' 						=> '<td $1>',
		'~\[\/td\]~si'	 						=> '</td>',

		'~\[th\]~si'   							=> '<th>',
		'~\[th (.*?)\]~si' 						=> '<th $1>',
		'~\[\/th\]~si'	 						=> '</th>',
		
		
		# [*] [list] [ul] [ol]
		'~\[\*\](.*?)\[\/\*\]~si'				=> '<li>$1</li>',
		'~\[\*\]~si'   							=> '<li>',
		'~\[ul\](.*?)\[\/ul\]~si'				=> "<ul>$1</li></ul>",
		'~\[list\](.*?)\[\/list\]~si'			=> "<ul>$1</li></ul>",
		'~\[ol\](.*?)\[\/ol\]~si'				=> '<ol>$1</li></ol>',
		
		# [li]
		'~\[li\]~si'   							=> '<li>',
		'~\[li (.*?)\]~si' 						=> '<li $1>',
		'~\[\/li\]~si'	 						=> '</li>',
		

		# headers
		'~\[h1\](.*?)\[\/h1\]~si'				=> '<h1>$1</h1>',
		'~\[h1\((.[^ ]*?)\)\](.*?)\[\/h1\]~si'	=> '<h1 class="$1">$2</h1>',
		'~\[h1 (.*?)\](.*?)\[\/h1\]~si' 		=> '<h1 $1>$2</h1>',
		
		'~\[h2\](.*?)\[\/h2\]~si'				=> '<h2>$1</h2>',
		'~\[h2\((.[^ ]*?)\)\](.*?)\[\/h2\]~si'	=> '<h2 class="$1">$2</h2>',
		'~\[h2 (.*?)\](.*?)\[\/h2\]~si' 		=> '<h2 $1>$2</h2>',
		
		'~\[h3\](.*?)\[\/h3\]~si'				=> '<h3>$1</h3>',
		'~\[h3\((.[^ ]*?)\)\](.*?)\[\/h3\]~si'	=> '<h3 class="$1">$2</h3>',
		'~\[h3 (.*?)\](.*?)\[\/h3\]~si' 		=> '<h3 $1>$2</h3>',
		
		'~\[h4\](.*?)\[\/h4\]~si'				=> '<h4>$1</h4>',
		'~\[h4\((.[^ ]*?)\)\](.*?)\[\/h4\]~si'	=> '<h4 class="$1">$2</h4>',
		'~\[h4 (.*?)\](.*?)\[\/h4\]~si' 		=> '<h4 $1>$2</h4>',
		
		'~\[h5\](.*?)\[\/h5\]~si'				=> '<h5>$1</h5>',
		'~\[h5\((.[^ ]*?)\)\](.*?)\[\/h5\]~si'	=> '<h5 class="$1">$2</h5>',
		'~\[h5 (.*?)\](.*?)\[\/h5\]~si' 		=> '<h5 $1>$2</h5>',
		
		'~\[h6\](.*?)\[\/h6\]~si'				=> '<h6>$1</h6>',
		'~\[h6\((.[^ ]*?)\)\](.*?)\[\/h6\]~si'	=> '<h6 class="$1">$2</h6>',
		'~\[h6 (.*?)\](.*?)\[\/h6\]~si' 		=> '<h6 $1>$2</h6>',

		# [code=language][/code]
		'~\[code\](.*?)\[\/code\]~si'			=> '<code>$1</code>',


		# links
		# [url]http://maxsite.org/[/url]
		# [url=http://maxsite.org/]Блог Макса[/url]
		# [url=http://maxsite.org/ rel="nofollow"]Блог Макса[/url]
		# [url rel="nofollow"]http://maxsite.org/[/url]
		
		'~\[url\](.*?)\[\/url\]~si'						=> '<a href="$1">$1</a>', 
		'~\[url=(.[^ ]*?)\](.*?)\[\/url\]~si'			=> '<a href="$1">$2</a>', 
		'~\[url=(.[^\s]*?) (.*?)\](.*?)\[\/url\]~si'	=> '<a href="$1" $2>$3</a>',
		'~\[url (.*?)\](.*?)\[\/url\]~si'				=> '<a href="$2" $1>$2</a>', 


		# images

		'~\[imgleft=(.*?)x(.*?)\](.*?)\[\/imgleft\]~si'	 	=> '<img src="$3" style="float: left; margin: 0 10px 0 0; width: $1px; height: $2px">',
		'~\[imgleft\](.*?)\[\/imgleft\]~si'		 			=> '<img src="$1" style="float: left; margin: 0 10px 0 0;">',
		'~\[imgleft (.*?)\](.*?)\[\/imgleft\]~si'	   		=> '<img src="$2" title="$1" alt="$1" style="float: left; margin: 0 10px 0 0;">',

		'~\[imgright=(.*?)x(.*?)\](.*?)\[\/imgright\]~si'	=> '<img src="$3" style="float: right; margin: 0 0 0 10px; width: $1px; height: $2px">',
		'~\[imgright\](.*?)\[\/imgright\]~si'	 			=> '<img src="$1" style="float: right; margin: 0 0 0 10px;">',
		'~\[imgright (.*?)\](.*?)\[\/imgright\]~si'	   		=> '<img src="$2" title="$1" alt="$1" style="float: right; margin: 0 0 0 10px;">',

		'~\[imgcenter\](.*?)\[\/imgcenter\]~si'	 			=> '<div style="text-align: center"><img src="$1"></div>',
		'~\[imgcenter (.*?)\](.*?)\[\/imgcenter\]~si'  		=> '<div style="text-align: center"><img src="$2" title="$1" alt="$1"></div>',

		# [imgmini=http://site/uploads/sborka-mini.jpg]http://site/uploads/sborka.jpg[/imgmini]
		'~\[imgmini=_(.*?)\](.*?)\[\/imgmini\]~si' 			=> '<a href="$2" target="_blank" class="lightbox"><img src="$1"></a>',
		'~\[imgmini=(.*?)\](.*?)\[\/imgmini\]~si'  			=> '<a href="$2"><img src="$1" class="lightbox"></a>',

		'~\[img=(.*?)x(.*?)\](.*?)\[\/img\]~si'	 			=> '<img src="$3" style="width: $1px; height: $2px">',

		'~\[img (.*?)\](.*?)\[\/img\]~si'			   		=> '<img src="$2" title="$1" alt="$1">',
		'~\[img\](.*?)\[\/img\]~si'				 			=> '<img src="$1" title="" alt="">',

		# [img(right)]http://localhost/uploads/2008-07-11-19-50-56.jpg[/img]
		'~\[img\((.[^ ]*?)\)\](.*?)\[\/img\]~si' 			=> '<img src="$2" alt="" class="$1">',
		
		# [img(right) Описание файла]http://localhost/uploads/2008-07-11-19-50-56.jpg[/img]
		'~\[img\((.[^ ]*?)\) (.*?)\](.*?)\[\/img\]~si' 		=> '<img src="$3" alt="$2" title="$2" class="$1">',


		
		# quoting
		'~\[quote\](.*?)\[\/quote\]~si'												=> '<blockquote>$1</blockquote>',
		'~\[quote=(?:&quot;|"|\')?(.*?)["\']?(?:&quot;|"|\')?\](.*?)\[\/quote\]~si'	=> '<blockquote><strong class="src">$1:</strong>$2</blockquote>',

		
		# [div(class)]текст[/div]
		'~\[div\((.*?)\)\](.*?)\[\/div\]~si' 	=> '<div class="$1">$2</div>',

		# [div style="color: red"]текст[/div] - произвольные атрибуты
		'~\[div (.*?)\](.*?)\[\/div\]~si' 		=> '<div $1>$2</div>',

		# [div(class) атрибуты]текст[/div]
		'~\[div\((.*?)\) (.*?)\](.*?)\[\/div\]~si' 	=> '<div class="$1" $2>$3</div>',
		
		# [span(class)]текст[/span]
		'~\[span\((.*?)\)\](.*?)\[\/span\]~si' 	=> '<span class="$1">$2</span>',

		# [span style="color: red"]текст[/span] - произвольные атрибуты
		'~\[span (.*?)\](.*?)\[\/span\]~si' 	=> '<span $1>$2</span>',

		# [span(class) атрибуты]текст[/span]
		'~\[span\((.*?)\) (.*?)\](.*?)\[\/span\]~si' 	=> '<span class="$1" $2>$3</span>',
		
	);

	if (strpos($text, '[text-demo]') !== false) // есть вхождение [text-demo]
	{
		if (file_exists(getinfo('plugins_dir') . 'bbcode/text-demo.txt') )
		{
			$text_demo = file(getinfo('plugins_dir') . 'bbcode/text-demo.txt');
			//$text_demo = implode("MSO_N", $text_demo);
			$text_demo = implode("\n", $text_demo);
			$text = str_replace('[text-demo]', $text_demo, $text);
		}
	}

	$text = preg_replace(array_keys($preg), array_values($preg), $text);
	
  return $text;

}

function bbcode_editor_content_callback($matches)
{
	$m = $matches[2];

	$m = htmlspecialchars($m);
	
	$m = str_replace("&amp;lt;br&amp;gt;", "<br>", $m);
	$m = str_replace("&amp;", "&", $m);

	$m = '[pre' . $matches[1] . ']' . $m . '[/pre]';

	return $m;
}

function bbcode_editor_content($text = '')
{
	$text = preg_replace_callback('~\[pre(.*?)\](.*?)\[\/pre\]~si', 'bbcode_editor_content_callback', $text);
	
	return $text;
}

# end file