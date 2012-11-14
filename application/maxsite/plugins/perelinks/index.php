<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function perelinks_autoload($args = array())
{
	mso_hook_add( 'content_content', 'perelinks_custom'); # хук на админку
	mso_hook_add( 'admin_init', 'perelinks_admin_init'); # хук на админку
}

# функция выполняется при активации (вкл) плагина
function perelinks_activate($args = array())
{	
	mso_create_allow('perelinks_edit', t('Доступ к настройкам «perelinks»'));
	return $args;
}

function perelinks_uninstall($args = array())
{
	mso_delete_option('plugin_perelinks', 'plugins' ); // удалим созданные опции
	return $args;
}


# функция выполняется при указаном хуке admin_init
function perelinks_admin_init($args = array())
{
	if ( !mso_check_allow('perelinks_edit') )
	{
		return $args;
	}

	$this_plugin_url = 'perelinks'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('Плагин perelinks'));
	mso_admin_url_hook ($this_plugin_url, 'perelinks_admin_page');

	return $args;
}



# функция вызываемая при хуке, указанном в mso_admin_url_hook
function perelinks_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('perelinks_edit') )
	{
		echo t('Доступ запрещен');
		return $args;
	}

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Плагин perelinks') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Плагин perelinks') . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'perelinks/admin.php');
}


# функции плагина
function perelinks_custom($content = '')
{
	// получаем список всех титлов - возможно из кэша
	// после этого выполняем замену всех этих вхождений в тексте на ссылки

	global $page; // текущая страница - это массив

	$options = mso_get_option('perelinks', 'plugins', array() ); // получаем опции
	$options['linkcount'] = isset($options['linkcount']) ? (int)$options['linkcount'] : 0;
	$options['wordcount'] = isset($options['wordcount']) ? (int)$options['wordcount'] : 0;
	$options['allowlate'] = isset($options['allowlate']) ? (int)$options['allowlate'] : 1;
	$options['stopwords'] = isset($options['stopwords']) ? $options['stopwords'] : 'будет нужно';
	if (isset($options['stopwords'])) $stopwords = explode(' ', $options['stopwords']);

	$cache_key = 'perelinks_custom';
	if ( $k = mso_get_cache($cache_key) ) 
	{
		$all_title = $k;
	}
	else
	{
		$CI = & get_instance();
		$CI->db->select('page_title, page_slug');
		if ($options['allowlate'] > 0)
		{
			$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
			// $CI->db->where('page_date_publish <', 'NOW()', false);
		}
		else
		{
			$CI->db->where('page_date_publish <', $page['page_date_publish']);
		}
		$CI->db->where('page_status', 'publish');
		$CI->db->from('page');
		$query = $CI->db->get();

		$all_title = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$title = mb_strtolower($row['page_title'], 'UTF-8');
				$title = str_replace(array('\\', '|', '/', '?', '%', '*', '`', ',', '.', '$', '!', '\'', '"', '«', '»', '—') , '', $title);

				$a_words = explode(' ', $title);
				$a_words = array_unique($a_words);

				$title = array();
				foreach ($a_words as $word)
				{
					if ((mb_strlen($word, 'UTF-8') > 3) and (!in_array($word, $stopwords))) $title[] = $word;
				}

				foreach ($title as $word)
				{
					$all_title[$word][] = $row['page_slug'];
				}
			}
		}
		mso_add_cache($cache_key, $all_title);
	}

	$curr_page_slug = $page['page_slug']; // текущая страница - для ссылки
	$my_site = getinfo('siteurl') . 'page/';

	// ищем вхождения
	$linkcounter = 0;
	foreach ($all_title as $key => $word)
	{

		$r = '| (' . preg_quote($key) . ') |siu';

		if ( preg_match($r , $content) )
		{
			if (!in_array($curr_page_slug, $word))
			{
				if ($options['wordcount'] > 0) $r = '| (' . preg_quote($key) . ') (.*$)|siu'; //Если только первое найденное слово-дубликат делать ссылкой
				$content = preg_replace($r, ' <a href="' . $my_site . $word[0] . '" class="perelink">\1</a> \2', $content);
				$linkcounter++;
			}
		}

		if (($linkcounter > 0) and ($linkcounter == $options['linkcount'])) break;

	}

	return  $content;
}

?>