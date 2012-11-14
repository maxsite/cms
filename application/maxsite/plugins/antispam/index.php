<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function antispam_autoload($args = array())
{
	mso_hook_add( 'admin_init', 'antispam_admin_init'); # хук на админку
	mso_hook_add( 'new_comments_check_spam', 'antispam_check_spam'); # хук новый комментарий
	mso_hook_add( 'new_comments_check_spam_comusers', 'antispam_check_spam_comusers'); # хук новый комментарий для комюзера
}

# функция выполняется при активации (вкл) плагина
function antispam_activate($args = array())
{	
	mso_create_allow('antispam_edit', t('Админ-доступ к antispam'));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function antispam_deactivate($args = array())
{	
	mso_delete_option('plugin_antispam', 'plugins' ); // удалим созданные опции
	return $args;
}


# функция выполняется при указаном хуке admin_init
function antispam_admin_init($args = array()) 
{
	if ( !mso_check_allow('antispam_admin_page') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'plugin_antispam'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Антиспам'));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/_null
	mso_admin_url_hook ($this_plugin_url, 'antispam_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function antispam_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('antispam_admin_page') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Антиспам') . ' "; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Антиспам') . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'antispam/admin.php');
}

# функция логгинга - сохраняем в файл все спамовские входы
function antispam_log($file = '', $msg = '')
{
	if ($file)
	{
		$fn = getinfo('uploads_dir') . $file;
		$fp = fopen( $fn, "a+");
		fwrite($fp,  '====================' . "\n" . $msg . "\n\n");
		fclose($fp);
	}
}


# функция проверки 
function antispam_check_spam($arg = array())
{
	$options_key = 'plugin_antispam';
	
	$options = mso_get_option($options_key, 'plugins', array()); // все опции
	
	if ( !isset($options['antispam_on']) ) $options['antispam_on'] = false; // включен ли антиспам
	if 	(!$options['antispam_on']) return;
	
	if ( !isset($options['logging']) ) $options['logging'] = false; // разрешено ли логирование?
	if ( !isset($options['moderation_links']) ) $options['moderation_links'] = true; // модерировать все ссылки
	if ( !isset($options['logging_file']) ) $options['logging_file'] = ''; // разрешено ли логирование?
	if ( !isset($options['black_ip']) ) $options['black_ip'] = ''; // черный список IP
	if ( !isset($options['black_words']) ) $options['black_words'] = ''; // черный список слов
	if ( !isset($options['moderation_words']) ) $options['moderation_words'] = ''; // список слов модерации
	
	$black_ip = explode("\n", trim($options['black_ip']));
	
	if (in_array($arg['comments_author_ip'], $black_ip)) 
	{
		if ($options['logging']) antispam_log($options['logging_file'], 
												  'BLACK_IP: ' . $arg['comments_author_ip'] . NR 
												. 'PAGE_ID: ' . $arg['comments_page_id'] . NR
												. 'DATE: ' . $arg['comments_date'] . NR
												. 'CONTENT: ' . NR . $arg['comments_content']
												);
		return array('check_spam'=>true, 'message'=>t('Для вашего IP комментирование запрещено!'));
	}
	
	$black_words = explode("\n", trim($options['black_words']));
	
	foreach ($black_words as $word)
	{
		if (
			($word and mb_stristr($arg['comments_content'], $word, false, 'UTF-8'))
			or
			($word and $arg['comments_author'] and mb_stristr($arg['comments_author'], $word, false, 'UTF-8'))
		) // есть какое-то вхождение
		{
			if ($options['logging']) antispam_log($options['logging_file'], 
												  'BLACK WORD: ' . $word . NR 
												. 'IP: ' . $arg['comments_author_ip'] . NR 
												. 'PAGE_ID: ' . $arg['comments_page_id'] . NR
												. 'DATE: ' . $arg['comments_date'] . NR
												. 'CONTENT: ' . NR . $arg['comments_content']
												);
			return array('check_spam'=>true, 'message'=>t('Вы используете запрещенные слова!'));
		} 
	}
	
	if ($options['moderation_links'])
	{
		// Если в комментарии хоть одна ссылка - сразу на модерацию
		$check_a = (strpos( $arg['comments_content'], '<a') === false ) ? false : true;
		if ($check_a) return array('moderation'=>1); // отправим на модерацию
	}


	$moderation_words = explode("\n", trim($options['moderation_words']));
	
	foreach ($moderation_words as $word)
	{
		if ($word and mb_stristr($arg['comments_content'], $word, false, 'UTF-8')) // есть какое-то вхождение
		{
			return array('moderation'=>1);
		} 
	}

}

# модерирование комюзеров
function antispam_check_spam_comusers($arg = array())
{
	# входящий параметр 
	# array( 'comments_page_id' => $comments_page_id, 'comments_comusers_id' => $comusers_id, 
	# 'comments_com_approved' => $comments_com_approved
	
	# выход: 1 разрешить 0 - модерация
	
	// смотрим есть ли id в списке модерируемом. если есть, то возвращаем на модерацию = 0
	$options_key = 'plugin_antispam';
	
	$options = mso_get_option($options_key, 'plugins', array()); // все опции
	
	if ( !isset($options['antispam_on']) ) return $arg['comments_com_approved']; // включен ли антиспам
	if ( !isset($options['moderation_comusers']) ) return $arg['comments_com_approved']; // нет списка

	$nums = explode("\n", trim($options['moderation_comusers'])); // список комюзеров
	
	foreach ($nums as $num)
	{
		if ( ( (int) trim($num)) == $arg['comments_comusers_id']) return 0;
	}
	
	return 1;
}

# end file