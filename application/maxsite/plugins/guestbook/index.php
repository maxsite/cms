<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function guestbook_autoload($args = array())
{
	mso_hook_add('admin_init', 'guestbook_admin_init'); # хук на админку
	mso_hook_add('custom_page_404', 'guestbook_custom_page_404'); # хук для подключения к шаблону
	mso_register_widget('guestbook_widget', t('Гостевая книга'));
}

# функция выполняется при активации (вкл) плагина
function guestbook_activate($args = array())
{	
	mso_create_allow('guestbook_edit', t('Админ-доступ к гостевой книге'));
	
	$CI = & get_instance();	

	if ( !$CI->db->table_exists('guestbook')) // нет таблицы guestbook
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "guestbook (
		guestbook_id bigint(20) NOT NULL auto_increment,
		guestbook_ip varchar(255) NOT NULL default '',
		guestbook_browser varchar(255) NOT NULL default '',
		guestbook_date datetime default NULL,
		guestbook_approved bigint(20) NOT NULL default '0',
		guestbook_name varchar(255) NOT NULL default '',
		guestbook_text longtext,
		guestbook_title varchar(255) NOT NULL default '',
		guestbook_email varchar(255) NOT NULL default '',
		guestbook_icq varchar(255) NOT NULL default '',
		guestbook_site varchar(255) NOT NULL default '',
		guestbook_phone varchar(255) NOT NULL default '',
		guestbook_custom1 varchar(255) NOT NULL default '',
		guestbook_custom2 varchar(255) NOT NULL default '',
		guestbook_custom3 varchar(255) NOT NULL default '',
		guestbook_custom4 varchar(255) NOT NULL default '',
		guestbook_custom5 varchar(255) NOT NULL default '',
		PRIMARY KEY (guestbook_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}
		
	return $args;
}


# функция выполняется при деинстяляции плагина
function guestbook_uninstall($args = array())
{	
	mso_delete_option('plugin_guestbook', 'plugins' ); // удалим созданные опции
	mso_remove_allow('guestbook_edit'); // удалим созданные разрешения
	mso_delete_option_mask('guestbook_widget_', 'plugins' );
	
	// удалим таблицу
	$CI = &get_instance();
	$CI->load->dbforge();
	$CI->dbforge->drop_table('guestbook');

	return $args;
}

# функция выполняется при указаном хуке admin_init
function guestbook_admin_init($args = array()) 
{
	if ( !mso_check_allow('guestbook_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'guestbook'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Гостевая книга'));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/guestbook
	mso_admin_url_hook ($this_plugin_url, 'guestbook_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function guestbook_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('guestbook_edit') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Гостевая книга') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Гостевая книга') . ' - " . $args; ' );
	
	if ( mso_segment(3) == 'edit') require(getinfo('plugins_dir') . 'guestbook/edit.php');
	elseif ( mso_segment(3) == 'editone') require(getinfo('plugins_dir') . 'guestbook/editone.php');
	else require(getinfo('plugins_dir') . 'guestbook/admin.php');
}

# подключаем свой файл к шаблону
function guestbook_custom_page_404($args = false)
{
	$options = mso_get_option('plugin_guestbook', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'guestbook'; 
	
	if ( mso_segment(1)==$options['slug'] ) 
	{
		require( getinfo('plugins_dir') . 'guestbook/guestbook.php' ); // подключили свой файл вывода
		return true; // выходим с true
	}

	return $args;
}

# функция, которая берет настройки из опций виджетов
function guestbook_widget($num = 1) 
{
	$widget = 'guestbook_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
	else $options['header'] = '';
	
	return guestbook_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function guestbook_widget_form($num = 1) 
{
	$widget = 'guestbook_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['limit']) ) $options['limit'] = 5;
	if ( !isset($options['max-word']) ) $options['max-word'] = 20;

	if ( !isset($options['format']) ) $options['format'] = '<p><b><a href="[url]">[name]</a></b></p>
<p>[text]<br><i>[date]</i></p>
<hr>';

	if ( !isset($options['format-date']) ) $options['format-date'] = 'Y-m-d H:i:s';
	if ( !isset($options['text-do']) ) $options['text-do'] = '';
	if ( !isset($options['text-posle']) ) $options['text-posle'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'])));

	$form .= mso_widget_create_form(t('Количество отзывов'), form_input( array( 'name'=>$widget . 'limit', 'value'=>$options['limit'])));
	
	$form .= mso_widget_create_form(t('Слов в тексте'), form_input( array( 'name'=>$widget . 'max-word', 'value'=>$options['max-word'])), t('Сколько максимально слов будет выводить в поле [text]'));
	
	
	$form .= mso_widget_create_form(t('Формат вывода'), form_textarea( array( 'name'=>$widget . 'format', 'value'=>$options['format'])), t('Варианты: [id] [ip] [browser] [date] [name] [text] [title] [email] [icq] [site] [phone] [custom1] [custom2] [custom3] [custom4] [custom5] [url]'));
	
	$form .= mso_widget_create_form(t('Формат даты'), form_input( array( 'name'=>$widget . 'format-date', 'value'=>$options['format-date'])), t('По-умолчанию: Y-m-d H:i:s Полное описание см. на <a href="http://www.php.net/manual/ru/function.date.php" target="_blank">php.net</a>'));
	
	$form .= mso_widget_create_form(t('Текст в начале блока'), form_textarea( array( 'name'=>$widget . 'text-do', 'value'=>$options['text-do'])), t(''));
	
	$form .= mso_widget_create_form(t('Текст в конце блока'), form_textarea( array( 'name'=>$widget . 'text-posle', 'value'=>$options['text-posle'])), t(''));

	
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function guestbook_widget_update($num = 1) 
{
	$widget = 'guestbook_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['limit'] = mso_widget_get_post($widget . 'limit');
	$newoptions['max-word'] = mso_widget_get_post($widget . 'max-word');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['format-date'] = mso_widget_get_post($widget . 'format-date');
	$newoptions['text-do'] = mso_widget_get_post($widget . 'text-do');
	$newoptions['text-posle'] = mso_widget_get_post($widget . 'text-posle');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function guestbook_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'guestbook_widget_custom' . serialize($options) . $num;
	
	$k = mso_get_cache($cache_key);
	
	if ($k) return $k; // да есть в кэше
	
	$out = '';
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['limit']) ) $options['limit'] = 10;
	if ( !isset($options['max-word']) ) $options['max-word'] = 20;
	if ( !isset($options['text-do']) ) $options['text-do'] = ''; 
	if ( !isset($options['text-posle']) ) $options['text-posle'] = ''; 
	if ( !isset($options['format-date']) ) $options['format-date'] = 'Y-m-d H:i:s'; 
	
	if ( !isset($options['format']) ) $options['format'] = '<p><b><a href="[url]">[name]</a></b></p>
<p>[text]<br><i>[date]</i></p>
<hr>'; 
	
	$options_guestbook = mso_get_option('plugin_guestbook', 'plugins', array());
	if ( !isset($options_guestbook['slug']) ) $options_guestbook['slug'] = 'guestbook'; 
	
	$CI = & get_instance();
	
	$CI->db->from('guestbook');
	$CI->db->where('guestbook_approved', '1');
	$CI->db->order_by('guestbook_date', 'desc');
	$CI->db->limit($options['limit']);

	$query = $CI->db->get();

	if ($query->num_rows() > 0)	
	{	
		$books = $query->result_array();
		
		foreach ($books as $book) 
		{
			// pr($book);
			
			$text = str_replace("\n", "<br>", htmlspecialchars($book['guestbook_text']));
			
			if ($options['max-word']) $text = mso_str_word($text, $options['max-word']);
			
			$out .= str_replace( 
				array(
					'[id]', 
					'[ip]', 
					'[browser]', 
					'[date]', 
					'[name]', 
					'[text]', 
					'[title]', 
					'[email]', 
					'[icq]', 
					'[site]', 
					'[phone]', 
					'[custom1]', 
					'[custom2]', 
					'[custom3]', 
					'[custom4]', 
					'[custom5]', 
					'[url]'
					),
				array(
					$book['guestbook_id'],
					$book['guestbook_ip'],
					$book['guestbook_browser'],
					mso_date_convert($options['format-date'], $book['guestbook_date']),
					htmlspecialchars($book['guestbook_name']),
					$text,
					htmlspecialchars($book['guestbook_title']),
					htmlspecialchars($book['guestbook_email']),
					htmlspecialchars($book['guestbook_icq']),
					htmlspecialchars($book['guestbook_site']),
					htmlspecialchars($book['guestbook_phone']),
					htmlspecialchars($book['guestbook_custom1']),
					htmlspecialchars($book['guestbook_custom2']),
					htmlspecialchars($book['guestbook_custom3']),
					htmlspecialchars($book['guestbook_custom4']),
					htmlspecialchars($book['guestbook_custom5']),
					getinfo('siteurl') . $options_guestbook['slug'] . '#guestbook-' . $book['guestbook_id'] // http://site/guestbook#guestbook-164
					
				), $options['format']);
		}
		
		
		
	}
	
	if ($out)
		$out = $options['header'] . $options['text-do'] . $out . $options['text-posle'];
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}


# end file