<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function sape_autoload($args = array())
{
	mso_hook_add( 'init', 'sape_init'); # хук на инициализацию
	mso_hook_add( 'admin_init', 'sape_admin_init'); # хук на админку
	mso_register_widget('sape_widget', 'Sape.ru'); # регистрируем виджет
}

# функция выполняется при активации (вкл) плагина
function sape_activate($args = array())
{	
	mso_create_allow('sape_edit', t('Админ-доступ к редактированию sape'));
	return $args;
}

# функция выполняется при деинсталяции плагина
function sape_uninstall($args = array())
{	
	mso_delete_option_mask('sape_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при указаном хуке admin_init
function sape_admin_init($args = array()) 
{
	if ( mso_check_allow('plugin_sape') ) 
	{
		$this_plugin_url = 'plugin_sape'; // url и hook
		mso_admin_menu_add('plugins', $this_plugin_url, 'Sape.ru');
		mso_admin_url_hook ($this_plugin_url, 'sape_admin_page');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function sape_admin_page($args = array()) 
{

	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('plugin_sape') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Настройка Sape.ru"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Настройка Sape.ru - " . $args; ' );
	require(getinfo('plugins_dir') . 'sape/admin.php');
}


# подключаем функции сапы
function sape_init($args = array()) 
{
	global $SAPE, $SAPE_CONTENT, $SAPE_ARTICLE;
	
	$options = mso_get_option('sape', 'plugins', array() ); // получаем опции
	
	if (isset($options['kod']) 
		and isset($options['go']) and $options['go'] 
		and isset($options['start']) and $options['start']) // можно подключать
	{
	
		// если вкючен античек
		if (isset($options['anticheck']) and $options['anticheck'])
		{
		// анализируем входящий url на предмет ?
		// если есть, то делаем редирект на то, что до ?
			// таким образом обнаружить продажную ссылку будет невозможно
			if (isset($_SERVER['argv']) and $_SERVER['argv']) // есть какие-то параметры - делаем редирект
			{
				$url = $_SERVER['REQUEST_URI']; // /?nono  /about/?momo
				
				$url = explode('?', $url);
				if (isset($url[0])) $url = $url[0];
				else $url = '';
				
				$url = '/' . trim(str_replace('/', ' ', $url));
				$url = str_replace(' ', '/', $url);
				$url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
				
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: ' . $url);
				exit;
			}
		}
		
		if ( !defined('_SAPE_USER') ) define('_SAPE_USER', $options['kod']);
		
		// если файла сапы нет, то выходим
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . _SAPE_USER . '/sape.php')) return $args;
		
		require_once($_SERVER['DOCUMENT_ROOT'] . '/' . _SAPE_USER . '/sape.php');
		
		$sa['charset'] = 'UTF-8';
		
		//$sa['fetch_remote_type'] = 'file_get_contents'; // file_get_contents|curl|socket
		
		if (isset($options['test']) and $options['test']) $sa['force_show_code'] = true;
		
		if (isset($options['multi_site']) and $options['multi_site']) $sa['multi_site'] = true;

		$SAPE = new SAPE_client($sa);
		
		if (isset($options['context']) and $options['context'])
		{
			if ( !isset($SAPE_CONTENT) ) $SAPE_CONTENT = new SAPE_context(array('charset' => 'UTF-8'));
			mso_hook_add( 'content_content', 'sape_content'); # хук на конечный текст для вывода
			
			if (isset($options['context_comment']) and $options['context_comment'])
				mso_hook_add( 'comments_content_out', 'sape_content'); 
				# хук на конечный текст для вывода в комментариях
		}
		else
		{
			$SAPE_CONTENT = false;
		}
		
		
		if (isset($options['articles']) and $options['articles'] )
		{
		
			if ( !isset($SAPE_ARTICLE) ) $SAPE_ARTICLE = new SAPE_articles();
			
			// это вывод рекламной статьи
			if (isset($options['articles_url']) and mso_segment(1) == $options['articles_url'] )
			{
				# хук для подключения к шаблону
				mso_hook_add('custom_page_404', 'sape_articles_custom_page_404');
			}
			
			// или запрос шаблона от робота сапы
			if ( isset($options['articles_template']) and mso_segment(1) == $options['articles_template'] )
			{
				# хук для подключения к шаблону
				mso_hook_add('custom_page_404', 'sape_articles_template_custom_page_404');
			}
		}
		else
		{
			$SAPE_ARTICLE = false;
		}
		
	}
	
	return $args;
}

# функция, которая берет настройки из опций виджетов
function sape_widget($num = 1) 
{
	$widget = 'sape_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
		else $options['header'] = '';
	
	return sape_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function sape_widget_form($num = 1) 
{
	$widget = 'sape_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = '';
	if ( !isset($options['htmldo']) ) $options['htmldo'] = '';
	if ( !isset($options['htmlposle']) ) $options['htmlposle'] = '';
	if ( !isset($options['links_or_articles']) ) $options['links_or_articles'] = 'links';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'])));
	
	$form .= mso_widget_create_form(t('Количество ссылок'), form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'])), t('Если этот виджет последний или единственный, то оставьте это поле пустым или 0'));
	
	$form .= mso_widget_create_form(t('HTML до'), form_input( array( 'name'=>$widget . 'htmldo', 'value'=>$options['htmldo'])), '');
	
	$form .= mso_widget_create_form(t('HTML после'), form_input( array( 'name'=>$widget . 'htmlposle', 'value'=>$options['htmlposle'])), '');
	
	$form .= mso_widget_create_form(t('Выводить'), form_dropdown( $widget . 'links_or_articles', array('links'=>t('Ссылки'), 'articles'=>t('Статьи')), $options['links_or_articles']), '');
	
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function sape_widget_update($num = 1) 
{
	$widget = 'sape_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['count'] = (int) mso_widget_get_post($widget . 'count');
	$newoptions['htmldo'] = mso_widget_get_post($widget . 'htmldo');
	$newoptions['htmlposle'] = mso_widget_get_post($widget . 'htmlposle');
	$newoptions['links_or_articles'] = mso_widget_get_post($widget . 'links_or_articles');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функция вывода виджета
function sape_widget_custom($options = array(), $num = 1)
{
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = 0;
	if ( !isset($options['htmldo']) ) $options['htmldo'] = '';
	if ( !isset($options['htmlposle']) ) $options['htmlposle'] = '';
	if ( !isset($options['links_or_articles']) ) $options['links_or_articles'] = 'links';
	
	if ($options['links_or_articles'] == 'links')
	{
		$out = sape_out($options['count'] , false); // получаем ссылки
		
		if ($out == '<!--check code-->') // вернулся проверочный код
		{
			$out = '<!--check code-->Код <a href="http://www.sape.ru/r.aa92aef9c6.php" target="_blank">sape.ru</a> установлен верно!';
			return $out;
		}
		elseif ($out and $options['header']) $out = $options['header'] . $options['htmldo'] . $out . $options['htmlposle'];
		return $out;
	}
	elseif ($options['links_or_articles'] == 'articles')
	{
		global $SAPE_ARTICLE;
		
		//pr($SAPE_ARTICLE);
		
		$out = $SAPE_ARTICLE->return_announcements();
		
		// это чеккод
		if ($out == $SAPE_ARTICLE->_data['index']['checkCode'])
		{
			return $out;
		}
		
		$out_check = strip_tags($out); // есть тексты
		
		if ($out_check and $options['header']) 
			$out = $options['header'] . $options['htmldo'] . $out . $options['htmlposle'];
		
		return $out;
	}
}

# функция вывода блока ссылок
function sape_out($count = 0, $echo = true)
{
	global $SAPE;
	
	$out = '';
	
	if (isset($SAPE) and $SAPE)
	{
		if ($count)
		{
			$out = $SAPE->return_links($count);
		}
		else
		{
			$out = $SAPE->return_links();
		}
	}
	
	if ($echo) echo $out;
		else return $out;	
}

# функция вывода контента
function sape_content($text = '')
{
	global $SAPE_CONTENT;
	
	if ($SAPE_CONTENT)
	{
		# $text = 'TEXT-DO ' . $SAPE_CONTENT->replace_in_text_segment($text) . ' TEXT-POSLE'; // контроль
		$text = $SAPE_CONTENT->replace_in_text_segment($text);
	}
	
	return $text;
}


# подключаем свой файл к шаблону
function sape_articles_template_custom_page_404($args = false)
{
	require( getinfo('plugins_dir') . 'sape/articles_template.php' ); // подключили свой файл вывода
	return true; // выходим с true
}

function sape_articles_custom_page_404($args = false)
{
	global $SAPE_ARTICLE;
	
	$SAPE_ARTICLE->process_request();
	
	return true; // выходим с true
}


# end file