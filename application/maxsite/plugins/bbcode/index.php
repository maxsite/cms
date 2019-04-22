<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function bbcode_autoload($args = array())
{
	$options = mso_get_option('plugin_bbcode', 'plugins', array());
	
	if (!array_key_exists('bbcode_level', $options)) $options['bbcode_level'] = 1;
	
	if ( ($options['bbcode_level'] == 1) or ($options['bbcode_level'] == 3) ) mso_hook_add('content', 'bbcode_custom', 20); # хук на вывод контента
	
	if ( ($options['bbcode_level'] == 2) or ($options['bbcode_level'] == 3) ) mso_hook_add('comments_content', 'bbcode_custom', 20);
	
	mso_hook_add('editor_content', 'bbcode_editor_content'); // обработка текста для визуального редактора
}

# функция выполняется при деинсталяции плагина
function bbcode_uninstall($args = array())
{
	mso_delete_option('plugin_bbcode', 'plugins' ); // удалим созданные опции

	return $args;
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
							'name' => t('Где использовать', __FILE__),
							'description' => t('Укажите, где должен работать плагин', __FILE__),
							'values' => t('1||На страницах #2||В комментариях #3||На страницах и в комментариях', __FILE__),
							'default' => '1'
						),
						
			'old_codes' => array(
							'type' => 'checkbox',
							'name' => t('Использовать устаревшие bb-коды', __FILE__),
							'description' => t('Вместо старого варианта, используйте новый:', __FILE__) . '<pre class="t80">
imgleft			[img(left)]my.jpg[/img]
imgright		[img(right)]my.jpg[/img]
imgcenter		[img(center)]my.jpg[/img]

left			[div(t-left)]текст[/div]
right			[div(t-right)]текст[/div]
center			[div(t-center)]текст[/div]
justify			[div(t-justify)]текст[/div]

pleft			[p(t-left)]текст[/p]
pright			[p(t-right)]текст[/p]
pcenter			[p(t-center)]текст[/p]
pjustify		[p(t-justify)]текст[/p]

div=			[div style="стили"]текст[/div]
span=			[span style="стили"]текст[/div]
</pre>',
							'default' => '0'
						),
			),
		t('Настройки плагина bbcode', __FILE__),
		t('Укажите необходимые опции.', __FILE__)
	);
}

# функции плагина
function bbcode_custom($text = '')
{
	$options = mso_get_option('plugin_bbcode', 'plugins', array());
	$old_codes = isset($options['old_codes']) ? $options['old_codes'] : 1;

	$text = preg_replace_callback('~\[pre(.*?)\](.*?)\[\/pre\]~si', 'bbcode_pre_callback', $text );

    $preg = array(
		// b - важное выделение в тексте 
		'~\[b (.*?)\](.*?)\[\/b\]~si'			=> '<strong $1>$2</strong>',
		'~\[b\](.*?)\[\/b\]~si'					=> '<strong>$1</strong>',
		
		// просто полужирное начертание
		'~\[bold (.*?)\](.*?)\[\/bold\]~si'		=> '<b $1>$2</b>',
		'~\[bold\](.*?)\[\/bold\]~si'			=> '<b>$1</b>',	
		
		// выжное выделение в тексте - акцент
		'~\[i (.*?)\](.*?)\[\/i\]~si'			=> '<em$1>$2</em>',
		'~\[i\](.*?)\[\/i\]~si'					=> '<em>$1</em>',
		
		// просто выделение курсивом
		'~\[italic (.*?)\](.*?)\[\/italic\]~si'	=> '<i$1>$2</i>',
		'~\[italic\](.*?)\[\/italic\]~si'		=> '<i>$1</i>',
		
		'~\[color=(.*?)\](.*?)\[\/color\]~si'	=> '<span style="color:$1">$2</span>',
		'~\[size=(.*?)\](.*?)\[\/size\]~si'		=> '<span style="font-size:$1">$2</span>',
		
		'~\[hr\]~si'   							=> '<hr>',
		'~\[line\]~si'	 						=> '<hr>',		
		
		# quoting
		'~\[quote\](.*?)\[\/quote\]~si'		=> '<blockquote>$1</blockquote>',
		'~\[quote=(?:&quot;|"|\')?(.*?)["\']?(?:&quot;|"|\')?\](.*?)\[\/quote\]~si'	=> '<blockquote><strong class="src">$1</strong>$2</blockquote>',
		
		# абривиатура
		'~\[abbr\](.*?)\[\/abbr\]~si'			=> '<abbr>$1</abbr>',
		'~\[abbr (.*?)\](.*?)\[\/abbr\]~si' 	=> '<abbr title="$1">$2</abbr>',

		
		# списки [*] [list] [ul] [ol]
		'~\[\*\](.*?)\[\/\*\]~si'				=> '<li>$1</li>',
		'~\[\*\]~si'   							=> '<li>',
		'~\[list\](.*?)\[\/list\]~si'			=> "<ul>$1</li></ul>",
		
		# [li]
		// '~\[li\]~si'   							=> '<li>',
		// '~\[li (.*?)\]~si' 						=> '<li $1>',
		// '~\[\/li\]~si'	 						=> '</li>',
		
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

		# ссылки
		# [url]http://maxsite.org/[/url]
		# [url=http://maxsite.org/]Блог Макса[/url]
		# [url=http://maxsite.org/ rel="nofollow"]Блог Макса[/url]
		# [url rel="nofollow"]http://maxsite.org/[/url]
		
		'~\[url\](.*?)\[\/url\]~si'						=> '<a href="$1">$1</a>', 
		'~\[url=(.[^ ]*?)\](.*?)\[\/url\]~si'			=> '<a href="$1">$2</a>', 
		'~\[url=(.[^\s]*?) (.*?)\](.*?)\[\/url\]~si'	=> '<a href="$1" $2>$3</a>',
		'~\[url (.*?)\](.*?)\[\/url\]~si'				=> '<a href="$2" $1>$2</a>', 
		
		# schema.org -> itemprop
		# [schema#Recipe] — основной контейнер 
		'~\[schema#(.*?)\](.*?)\[\/schema\]~si'	=> '<div itemscope itemtype="//schema.org/$1">$2</div>',
		
		# p
		'~\[p=(.*?)\](.*?)\[\/p\]~si'			=> '<p style="$1">$2</p>',
		'~\[p\((.*?)\)\](.*?)\[\/p\]~si' 	=> '<p class="$1">$2</p>', // [p(класс)] [/p] 
		
		# специальные замены - значение формируется автоматом
		'~\[getinfo siteurl\]~si' => getinfo('siteurl'), // адрес сайта
		'~\[getinfo template_url\]~si' => getinfo('template_url'), // адрес шаблона
		'~\[getinfo uploads_url\]~si' => getinfo('uploads_url'), // адрес uploads
		'~\[getinfo shared_url\]~si' => getinfo('shared_url'), // адрес shared
		

		# [imgmini=mini-адрес]адрес[/imgmini]
		'~\[imgmini=_(.*?)\](.*?)\[\/imgmini\]~si' 			=> '<a href="$2" target="_blank" class="lightbox"><img src="$1"></a>',
		'~\[imgmini=(.*?)\](.*?)\[\/imgmini\]~si'  			=> '<a href="$2"><img src="$1" class="lightbox"></a>',

		# [img#image]адрес[/img]
		'~\[img#(.[^ ]*?)\](.*?)\[\/img\]~si' => '<img itemprop="$1" src="$2" alt="" title="">',
		
		# [img#image Привет]адрес[/img]
		'~\[img#(.[^ ]*?) (.*?)\](.*?)\[\/img\]~si' => '<img itemprop="$1" src="$3" alt="$2" title="$2">',
		
		# [img(right)#image]адрес[/img] убрал [^ ]
		'~\[img\((.*?)\)#(.[^ ]*?)\](.*?)\[\/img\]~si' => '<img class="$1" itemprop="$2" src="$3" alt="" title="">',
		
		# [img(right)#image Описание файла]адрес[/img]
		'~\[img\((.*?)\)#(.[^ ]*?) (.*?)\](.*?)\[\/img\]~si' => '<img class="$1" itemprop="$2" src="$4" alt="$3" title="$3">',
		
		# обычные img
		# [img=200x300]адрес[/img]
		'~\[img=(.*?)x(.*?)\](.*?)\[\/img\]~si'	 			=> '<img src="$3" style="width: $1px; height: $2px">',
		
		# [img картинка]адрес[/img]
		'~\[img (.*?)\](.*?)\[\/img\]~si'			   		=> '<img src="$2" title="$1" alt="$1">',
		
		# [img]адрес[/img]
		'~\[img\](.*?)\[\/img\]~si'				 			=> '<img src="$1" title="" alt="">',

		# [img(right)]адрес[/img]
		'~\[img\((.*?)\)\](.*?)\[\/img\]~si' 			=> '<img class="$1" src="$2" alt="">',
		
		# [img(right) Описание файла]адрес[/img]
		'~\[img\((.*?)\) (.*?)\](.*?)\[\/img\]~si' 		=> '<img class="$1" src="$3" alt="$2" title="$2">',
		
		# [image class="left" title="картинка"]адрес[/image] - произвольные атрибуты
		'~\[image (.*?)\](.*?)\[\/image\]~si' 	=> '<img src="$2" $1>',
		
		
		// универсальные замены для тэгов:
		
		# [span#recipeIngredient]текст[/span]
		# <span itemprop="recipeIngredient">текст</span>
		'~\[(span|div|p|ul|ol|li|h1|h2|h3|h4|h5|h6|code|del|u|s|sub|sup|small|q|cite|address|dfn|dl|dt|dd|ins|blockquote)#(.*?)\](.*?)\[\/\1\]~si' => '<$1 itemprop="$2">$3</$1>',

		
		# [span(класс)#recipeIngredient]текст[/span]
		# <span class="класс" itemprop="recipeIngredient">текст</span>
		'~\[(span|div|p|ul|ol|li|h1|h2|h3|h4|h5|h6|code|del|u|s|sub|sup|small|q|cite|address|dfn|dl|dt|dd|ins|blockquote)\((.*?)\)#(.*?)\](.*?)\[\/\1\]~si' => '<$1 class="$2" itemprop="$3">$4</$1>',
		
		
		# [span(класс)]текст[/span]
		# <span class="класс">текст</span>
		'~\[(span|div|p|ul|ol|li|h1|h2|h3|h4|h5|h6|code|del|u|s|sub|sup|small|q|cite|address|dfn|dl|dt|dd|ins|blockquote)\((.*?)\)\](.*?)\[\/\1\]~si' => '<$1 class="$2">$3</$1>',
	
		# [span любые_атрибуты]текст[/span]
		# <span любые_атрибуты>текст</span>
		'~\[(span|div|p|ul|ol|li|h1|h2|h3|h4|h5|h6|code|del|u|s|sub|sup|small|q|cite|address|dfn|dl|dt|dd|ins|blockquote) (.*?)\](.*?)\[\/\1\]~si' => '<$1 $2>$3</$1>',
		
		# [span(класс) любые_атрибуты]текст[/span]
		# <span  class="класс" любые_атрибуты>текст</span>
		'~\[(span|div|p|ul|ol|li|h1|h2|h3|h4|h5|h6|code|del|u|s|sub|sup|small|q|cite|address|dfn|dl|dt|dd|ins|blockquote)\((.*?)\) (.*?)\](.*?)\[\/\1\]~si' => '<$1 class="$2" $3>$4</$1>',
		
		# [span]текст[/span]
		# <span>текст</span>
		'~\[(span|div|p|ul|ol|li|h1|h2|h3|h4|h5|h6|code|del|u|s|sub|sup|small|q|cite|address|dfn|dl|dt|dd|ins|blockquote)\](.*?)\[\/\1\]~si' => '<$1>$2</$1>',
	);

	
	// устаревшие коды
	if ($old_codes)
	{
		/*
		устаревшие 			следует использовать

			imgleft			[img(left)]my.jpg[/img]
			imgright		[img(right)]my.jpg[/img]
			imgcenter		[img(center)]my.jpg[/img]

			left			[div(t-left)]текст[/div]
			right			[div(t-right)]текст[/div]
			center			[div(t-center)]текст[/div]
			justify			[div(t-justify)]текст[/div]
			
			pleft			[p(t-left)]текст[/p]
			pright			[p(t-right)]текст[/p]
			pcenter			[p(t-center)]текст[/p]
			pjustify		[p(t-justify)]текст[/p]
			
			div=			[div style="стили"]текст[/div]
			span=			[span style="стили"]текст[/div]

		*/
		
		$preg_old = array(
			'~\[div=(.*?)\](.*?)\[\/div\]~si'		=> '<div style="$1">$2</div>',
			'~\[span=(.*?)\](.*?)\[\/span\]~si'		=> '<span style="$1">$2</span>',
					
			'~\[left (.*?)\](.*?)\[\/left\]~si'		=> '<div style="text-align: left; $1">$2</div>',
			'~\[left\](.*?)\[\/left\]~si'			=> '<div style="text-align: left;">$1</div>',

			'~\[right (.*?)\](.*?)\[\/right\]~si'	=> '<div style="text-align: right; $1">$2</div>',
			'~\[right\](.*?)\[\/right\]~si'			=> '<div style="text-align: right;">$1</div>',

			'~\[center (.*?)\](.*?)\[\/center\]~si'	=> '<div style="text-align: center; $1">$2</div>',
			'~\[center\](.*?)\[\/center\]~si'		=> '<div style="text-align: center;">$1</div>',

			'~\[justify (.*?)\](.*?)\[\/justify\]~si'	=> '<div style="text-align: justify; $1">$2</div>',
			'~\[justify\](.*?)\[\/justify\]~si'			=> '<div style="text-align: justify;">$1</div>',
					
			'~\[pleft\](.*?)\[\/pleft\]~si'			=> '<p style="text-align: left;">$1</p>',
			'~\[pright\](.*?)\[\/pright\]~si'		=> '<p style="text-align: right;">$1</p>',
			'~\[pcenter\](.*?)\[\/pcenter\]~si'		=> '<p style="text-align: center;">$1</p>',
			'~\[pjustify\](.*?)\[\/pjustify\]~si'	=> '<p style="text-align: justify;">$1</p>',

			'~\[imgleft=(.*?)x(.*?)\](.*?)\[\/imgleft\]~si'	 	=> '<img src="$3" style="float: left; margin: 0 10px 0 0; width: $1px; height: $2px">',
			'~\[imgleft\](.*?)\[\/imgleft\]~si'		 			=> '<img src="$1" style="float: left; margin: 0 10px 0 0;">',
			'~\[imgleft (.*?)\](.*?)\[\/imgleft\]~si'	   		=> '<img src="$2" title="$1" alt="$1" style="float: left; margin: 0 10px 0 0;">',
			'~\[imgright=(.*?)x(.*?)\](.*?)\[\/imgright\]~si'	=> '<img src="$3" style="float: right; margin: 0 0 0 10px; width: $1px; height: $2px">',
			'~\[imgright\](.*?)\[\/imgright\]~si'	 			=> '<img src="$1" style="float: right; margin: 0 0 0 10px;">',
			'~\[imgright (.*?)\](.*?)\[\/imgright\]~si'	   		=> '<img src="$2" title="$1" alt="$1" style="float: right; margin: 0 0 0 10px;">',
			'~\[imgcenter\](.*?)\[\/imgcenter\]~si'	 			=> '<div style="text-align: center"><img src="$1"></div>',
			'~\[imgcenter (.*?)\](.*?)\[\/imgcenter\]~si'  		=> '<div style="text-align: center"><img src="$2" title="$1" alt="$1"></div>',		
		);
		
		$preg = array_merge($preg, $preg_old);
	}
	
	
	if (strpos($text, '[text-demo]') !== false) // есть вхождение [text-demo]
	{
		if (file_exists(getinfo('plugins_dir') . 'bbcode/text-demo.txt') )
		{
			$text_demo = file_get_contents(getinfo('plugins_dir') . 'bbcode/text-demo.txt');
			$text = str_replace('[text-demo]', $text_demo, $text);
		}
	}
	
	if (strpos($text, '[text-normalize]') !== false) // есть вхождение [text-normalize]
	{
		if (file_exists(getinfo('plugins_dir') . 'bbcode/text-normalize.txt') )
		{
			$text_normalize = file_get_contents(getinfo('plugins_dir') . 'bbcode/text-normalize.txt');
			$text = str_replace('[text-normalize]', $text_normalize, $text);
		}
	}

	$text = preg_replace(array_keys($preg), array_values($preg), $text);
	
	# другие сложные патерны и замены
	
	// создание ul/li списка по принципу меню
	$pattern = '~\[create_list\((.*?)\)\](.*?)\[/create_list\]~si'; // с указаным css-классом
	$text = preg_replace_callback($pattern, 'bbcode_create_list_callback', $text);
	
	$pattern = '~\[create_list\](.*?)\[/create_list\]~si'; // без класса
	$text = preg_replace_callback($pattern, 'bbcode_create_list_callback', $text);	

	// !TODO! - ДОДЕЛАТЬ JS-СКРИПТ 
	// [show Показать...@Скрыть...] текст [/show]
	// $pattern = '~\[show (.*?)@(.*?)\](.*?)\[\/show\]~si';
	// $text = preg_replace_callback($pattern, 'bbcode_show_callback', $text);	
	
	// [show Показать...] текст [/show]
	$pattern = '~\[show (.*?)\](.*?)\[\/show\]~si';
	$text = preg_replace_callback($pattern, 'bbcode_show_callback', $text);
	
	// [show] текст [/show]
	$pattern = '~\[show\](.*?)\[\/show\]~si';
	$text = preg_replace_callback($pattern, 'bbcode_show_callback', $text);	

	
	// по хуку bbcode можно выполнить свои замены
	$text = mso_hook('bbcode', $text);
	
	// pr($text, 1);
	
	return $text;
}


# callback-функция для pre
function bbcode_pre_callback($matches)
{
	$m = $matches[2];

	$m = str_replace('[', '&#91;', $m);
	$m = str_replace(']', '&#93;', $m);

	$m = '<pre' . $matches[1] . '>' . $m . '</pre>';

	return $m;
}

# callback-функция для create_list
function bbcode_create_list_callback($matches)
{
	// содержимое create_list в $matches[1]
	
	// два параметра, значит указан css-класс
	if (isset($matches[2])) $text = $matches[2];
		else $text = $matches[1];
		
	$arr1 = array('<p>', '</p>', '',   '<br />', '<br>', '&nbsp;', '&amp;', '&lt;', '&gt;', '&quot;');
	$arr2 = array('',    '',     "\t", "\n",     "\n",   ' ',      '&',     '<',    '>',    '"');
	
	$text = trim(str_replace($arr1, $arr2, $text));
	
	// указан css-класс
	if (isset($matches[2]) and $matches[1]) $class = ' class="' . $matches[1] . '"';
		else $class = '';
	
	$text = '<ul' . $class . '>' . mso_menu_build($text) . '</ul>';
	
	return $text;
}


function bbcode_show_callback($matches)
{
	static $js = false;
	
	$out = '';
	
	if (!$js)
	{
		$out .=  mso_load_jquery('jquery.cookie.js');
		$out .=  mso_load_jquery('jquery.showhide.js');
		
		$out .= ' <script> $(function () {
$.cookie.json = true; $("div.mso-show").showHide({time: 400, useID: false, clickElem: "a.mso-show-link", foldElem: "div.mso-show-text", visible: false});
}); </script> ' ;
		
		$js = true;
	}
	
	// !TODO! - ДОДЕЛАТЬ JS-СКРИПТ 
	// [show Показать...@Скрыть...] текст [/show]

	
	// несколько параметров, значит указаны текст  заголовок
	if (isset($matches[3]))
	{
		$header = $matches[1];
		// $header_hide = $matches[2];
		$text = $matches[3];
	}
	elseif (isset($matches[2]))
	{
		$header = $matches[1];
		// $header_hide = tf('Свернуть...');
		$text = $matches[2];
	}
	else
	{
		$header = tf('Показать/Скрыть...', __FILE__);
		// $header_hide = tf('Свернуть...');
		$text = $matches[1];
	}
		
	$out .= '<div class="mso-show">'
				. '<p class="mso-show-header"><a href="#" class="mso-show-link">' 
				. $header
				. '</a></p>'
				. '<div class="mso-show-text">'
				. $text
				. '</div>'
			. '</div>' . NR;
	
	return $out;
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

# end of file