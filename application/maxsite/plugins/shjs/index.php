<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function shjs_autoload()
{
	mso_hook_add( 'body_end', 'shjs_head');
	mso_hook_add( 'head_css', 'shjs_head_css');
	$options = mso_get_option('plugin_shjs', 'plugins', array());
	if (isset($options['default_lang']) and $options['default_lang']) mso_hook_add('content', 'shjs_content');
}


# функция выполняется при деинсталяции плагина
function shjs_uninstall($args = array())
{	
	mso_delete_option('plugin_shjs', 'plugins' ); // удалим созданные опции
	return $args;
}

# вспомогательная функция, возвращает
# имена файлов в виде #-списка для select
function shjs_scan_files($cat = 'css')
{
	$CI = & get_instance();
	$CI->load->helper('directory'); 

	$path = getinfo('plugins_dir') . '/shjs/' . $cat;
	$files = directory_map($path, true);
	
	if (!$files) return '';
	
	$all_files = array();
	
	// функция directory_map возвращает не только файлы, но и подкаталоги
	// нам нужно оставить только файлы. Делаем это в цикле
	foreach ($files as $file)
	{
		if (@is_dir($path . $file)) continue; // это каталог
		$file = str_replace('.min.js', '', $file);
		$file = str_replace('.min.css', '', $file);
		$all_files[] = $file;
	}
	
	// отсортируем список для красоты
	sort($all_files);
	
	// преобразуем массив в строчку с разделителем #
	return implode($all_files, '#');
}


# функция отрабатывающая мини-опции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function shjs_mso_options() 
{

	$all_css = shjs_scan_files('css');
	$all_lang = '||Нет #' . shjs_scan_files('lang') ;

	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_shjs', 'plugins', 
		array(
			'css' => array(
							'type' => 'select', 
							'name' => t('Стиль оформления'), 
							'description' => t('Выберите схему подсветки кода'), 
							'values' => $all_css,
							'default' => 'sh_maxsite'
						),
			
			'default_lang' => array(
							'type' => 'select', 
							'name' => t('Язык подсветки по-умолчанию'), 
							'description' => t('Выберите язык, который будет применяться к &lt;pre&gt; и [pre] без указанного class.'), 
							'values' => $all_lang,
							'default' => 'sh_php'
						),			
						
			),
		'Настройки плагина SHJS - Syntax Highlighting', // титул
		'
	Плагин делает код более привлекательным и наглядным. Для использования следует указать его в виде: </p>
<pre>
&lt;pre class="sh_php"&gt; тут PHP-код &lt;/pre&gt;
&lt;pre class="sh_less"&gt; тут LESS-код &lt;/pre&gt;
&lt;pre class="sh_css"&gt; тут CSS-код &lt;/pre&gt;
&lt;pre class="sh_html"&gt; тут HTML-код &lt;/pre&gt;
&lt;pre class="sh_javascript"&gt; тут JavaScript-код &lt;/pre&gt;
</pre>
	<br>
	<p class="info">Если у вас включён плагин <strong>BBCode</strong>, то можно использовать так:</p>
<pre>
[pre class="sh_php"] тут PHP-код [/pre]
[pre class="sh_less"] тут LESS-код [/pre]
[pre class="sh_css"] тут CSS-код [/pre]
[pre class="sh_html"] тут HTML-код [/pre]
[pre class="sh_javascript"] тут JavaScript-код [/pre]
</pre>
	<br>
	<p class="info">Если задать язык по-умолчанию, то можно не указывать class:</p>
<pre>
&lt;pre&gt; тут код &lt;/pre&gt;
[pre] тут код [/pre]
</pre><br>'
	);

	echo '<p class="info">Если вам требуется добавить другие темы оформления и языки, то их можно скачать со страницы <a href="http://shjs.sourceforge.net/doc/download.html" target="_blank">SHJS - Syntax Highlighting</a>. По ссылке <strong>«download a binary distribution»</strong> загрузите полный архив скрипта. В нём будут присутствовать каталоги <strong>«css»</strong> (оформление) и <strong>«lang»</strong> (языки). Загрузите нужные файлы (min-версии) в аналогичные каталоги плагина MaxSite CMS (<strong>application/maxsite/plugins/shjs</strong>).';
	
}

# подключение плагина в head
function shjs_head($arg = array())
{
	echo '<script src="' . getinfo('plugins_url') . 'shjs/sh_main.min.js"></script>
<script>
$(document).ready(function() {
sh_highlightDocument("' . getinfo('plugins_url') . 'shjs/lang/", ".min.js");
});
</script>';
	
	return $arg;
}


function shjs_head_css($arg = array())
{
	$options = mso_get_option('plugin_shjs', 'plugins', array());
	
	if (!isset($options['css']) or !$options['css']) $options['css'] = 'sh_maxsite'; 

	echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'shjs/css/' . $options['css'] . '.min.css">';
	
	return $arg;
}

# замены pre в тексте
function shjs_content($text = '')
{
	$options = mso_get_option('plugin_shjs', 'plugins', array());
	if (!isset($options['default_lang']) or !$options['default_lang']) 
	{
		return $text;
	}
	else
	{
		$text = str_replace('<pre>', '<pre class="' . $options['default_lang'] . '">', $text);
		$text = str_replace('[pre]', '[pre class="' . $options['default_lang'] . '"]', $text);
		
		// замены для совместимости с syntaxhighlighter
		$text = preg_replace('~([<\[])pre lang=js([>\]])~', '$1pre class="sh_javascript"$2', $text);
		$text = preg_replace('~([<\[])pre lang=([a-z]*)([>\]])~', '$1pre class="sh_$2"$3', $text);
		
		$text = preg_replace_callback('~<pre(.*?)>(.*?)<\/pre>~si', 'shjs_pre_callback', $text);
		
		return $text;
	}

}

function shjs_pre_callback($matches)
{
	$m = str_replace("\t", '    ', $matches[2]);
	$m = '<pre' . $matches[1] . '>' . $m . '</pre>';

	return $m;
}


# end of file