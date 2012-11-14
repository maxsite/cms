<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Wave
 * (c) http://wave.fantregata.com/
 */


# функция автоподключения плагина
function multipage_autoload($args = array())
{
	$options = mso_get_option('plugin_multipage', 'plugins', array() );
	$options['pag_start'] = ( (isset($options['pag_start']))? ($options['pag_start']) : (0) );
	$options['pag_end']   = ( (isset($options['pag_end']))  ? ($options['pag_end'])   : (1) );
	$options['autoclose'] = ( (isset($options['autoclose']))? ($options['autoclose']) : (1) );
	// Приоритет очень высокий затем, что сначала выпиливаем маленький кусок страницы, потом с ним работаем. Так экономней.
	mso_hook_add( 'content_in', 'multipage_custom', 99);
	if ($options['pag_start'] == 1) mso_hook_add( 'content_start', 'multipage_pagination');
	if ($options['pag_end'] == 1)   mso_hook_add( 'content_end',   'multipage_pagination');
	mso_hook_add( 'admin_init', 'multipage_admin_init');
	//if (!function_exists('autoclose_tags_custom')) require_once (getinfo('plugins_dir') . 'autoclose_tags/index.php');
}



function multipage_admin_init($args = array())
{
	if ( !mso_check_allow('admin_plugin_options') )
	{
		return $args;
	}

	$options = mso_get_option('plugin_multipage', 'plugins', array() );
	if ( isset($options['admin_menu']) and ($options['admin_menu'] == 1))
	{
		$this_plugin_url = 'plugin_options/multipage';
		mso_admin_menu_add('plugins', $this_plugin_url, t('Multipage'));
		mso_admin_url_hook ($this_plugin_url, 'multipage_admin_page');
	}

	return $args;
}



function multipage_uninstall($args = array())
{
	mso_delete_option('plugin_multipage', 'plugins' );
	return $args;
}



function multipage_mso_options() 
{
	mso_admin_plugin_options('plugin_multipage', 'plugins',
		array(
			'pagebreak' => array(
							'type' => 'text',
							'name' => t('Разделитель страниц'),
							'description' => t('Разделитель страниц в тексте: [pagebreak], &lt;!-- Page break --&gt; или как вам будет угодно.'),
							'default' => '[pagebreak]'
						),
			'next_url' => array(
							'type' => 'text',
							'name' => t('«Next» в ссылках'),
							'description' => t('«Next» в ссылках http://site.com/page/slug/next/2 — например: next, page, pageid.'),
							'default' => 'next'
						),
			'process_category' => array(
							'type' => 'select',
							'name' => t('Обрабатывать тексты на главной, в категориях и т.п.'),
							'description' => t('Если не обрабатывать, тексты выводятся только до первого разделителя. Иначе разделитель нужно ставить после [cut] или в виде html-комментария.<br>Не обрабатывать — экономней по ресурсам.'),
							'values' => t('0||Не обрабатывать # 1||Удалять разделители # 2||Выводить до первого разделителя'),
							'default' => '0'
						),
			'autoclose' => array(
							'type' => 'checkbox',
							'name' => t('Автоматически закрывать теги на страницах'),
							'description' => t('Плагин сам закрывает те теги, которые разбивает разделитель, и тем самым спасает от глюков с сайдбарами и т.п.. Экономней делать это вручную, а опцию отключить.'),
							'default' => '1'
						),
			'admin_menu' => array(
							'type' => 'checkbox',
							'name' => t('Показывать меню настройки плагина в админке'),
							'description' => '',
							'default' => '0'
						),
			'pag_start' => array(
							'type' => 'checkbox',
							'name' => t('Выводить листалку над текстом'),
							'description' => '',
							'default' => '0'
						),
			'pag_end' => array(
							'type' => 'checkbox',
							'name' => t('Выводить листалку под текстом'),
							'description' => '',
							'default' => '1'
						),
			'before_pag' => array(
							'type' => 'textarea',
							'name' => t('Текст перед листалкой'),
							'description' => t('Если вы хотите предварить листалку текстом или обернуть в какие-то теги.'),
							'default' => ''
						),
			'after_pag' => array(
							'type' => 'textarea',
							'name' => t('Текст после листалки'),
							'description' => t('А здесь теги закрываются.'),
							'default' => ''
						),
			),
		t('Настройки плагина «Multipage»'),
		t('Укажите необходимые опции.')
	);
}


# Для тех, кто не понимает, что такое теги и как их закрывать.
function autoclose_tags_on_page($content = '')
{
	//if (is_type('page')) return $content;

	preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU", $content, $result);
	$openedtags = $result[1];

	preg_match_all("#</([a-z]+)>#iU", $content, $result);
	$closedtags = $result[1];
	$len_opened = count($openedtags);

	if(count($closedtags) == $len_opened){
		return $content;
	}

	$openedtags = array_reverse($openedtags);
	for ($i=0; $i < $len_opened; $i++) {
		if (!in_array($openedtags[$i], $closedtags)) {
			$content .= '</'.$openedtags[$i].'>';
		} else {
			unset($closedtags[array_search($openedtags[$i],$closedtags)]);
		}
	}
	return $content;

}


# функции плагина
function multipage_custom($text = '')
{
/*
// Как Макс борется с ограничениями на размер поста.
	// Было
	if ($r['cut'])
		$output = preg_replace('~(.*?)\[mso_xcut\](.*?)~s', "$1", $output);
	else
		$output = preg_replace('~(.*?)\[mso_xcut\](.*?)~s', "$2", $output);

	// Стало
	if (strpos($output, '[mso_xcut]') !== false)
	{
		$xcontent = explode('[mso_xcut]', $output);
		if ($r['cut']) $output = $xcontent[0];
			else $output = $xcontent[1];
	}
*/
	global $multipage_pagination, $MSO;
	$options          = mso_get_option('plugin_multipage', 'plugins', array() );
	$pagebreak        = ( (isset($options['pagebreak']))?($options['pagebreak']):('[pagebreak]') );
	$break_length     = strlen($pagebreak);
	$next_url         = ( (isset($options['next_url']))?($options['next_url']):('next') );
	$process_category = ( (isset($options['process_category']))?($options['process_category']):(0) );
	$pattern          = '/^(.*?)' . preg_quote($pagebreak) . '(.*?)$/u';
	$autoclose        = ( (isset($options['autoclose']))? ($options['autoclose']) : (1) );


	if ( !is_type('page' ) )
	{
		if ($process_category == 0) return $text;
		if ($process_category == 1) return preg_replace('/' . preg_quote($pagebreak) . '/u', '', $text);
		if ($process_category == 2) return preg_replace($pattern, '$1', $text);
		if ($process_category == 3) //return preg_replace($pattern, '$1', $text) . '<br>[cut]'; //TODO Выловить в тексте имеющийся кат\хкат и вставлять его.
		{
			if (strpos($text, $pagebreak) === false) return $text;
			//$pat  = '|' . preg_quote('[cut') . '.*?' . preg_quote(']') . '|u';
			//$cut1 = preg_match($pat, $text, &$cut);
			//pr($text);
			//echo $cut[0];
			//Оказывается, здесь уже нет ката в том виде, который.
		}
	}

	if (strpos($text, $pagebreak) !== false)
	{
		$pages_count = substr_count($text, $pagebreak) + 1; //Всего страниц
		$is_next   = count($MSO->data['uri_segment']) - 1;  //Номер предпоследнего сегмента
		if ( ($is_next > 1) and (mso_segment($is_next) == $next_url) and ( ((int)mso_segment($is_next + 1)) > 0) ) //Если предпоследний сегмент next_url…
		{
			$current_page = (int)mso_segment($is_next + 1);
		}
		else $current_page = 1;                             //Текущая страница

		//$pat  = '/(.*?)' . preg_quote($pagebreak) . '/u';
		//$text = preg_replace($pat, '$2', $text, --$current_page);
		//$text = preg_replace($pattern, '$1', $text);
		if ($current_page > 1)
		{
			for ($i = 1; $i < $current_page; $i++)
			{
				//if (!$i) continue;
				$text = substr($text, strpos($text, $pagebreak) + $break_length);
			}
		}

		// Если [уже или всего] первая страница, удаляем всё после первого pb. Вместе с ним
		// Если она же и последняя, то даже и не трогаем.
		if ($current_page < $pages_count) $text = substr($text, 0, strpos($text, $pagebreak));

		if ($autoclose) autoclose_tags_on_page($text);

		$multipage_pagination['maxcount'] = $pages_count;
		$multipage_pagination['limit']    = 1;
		$multipage_pagination['next_url'] = ( (isset($options['next_url']))?($options['next_url']):('next') );
	}
	return $text;
}



function multipage_pagination()
{
	if ( !is_type('page') ) return true;
	global $multipage_pagination;
	if (isset($multipage_pagination))
	{
		$options = mso_get_option('plugin_multipage', 'plugins', array() );
		if ( isset($options['before_pag']) ) echo $options['before_pag'];
		mso_hook('pagination', $multipage_pagination);
		if ( isset($options['after_pag']) ) echo $options['after_pag'];
	}

}

# end file