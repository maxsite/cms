<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function last_comments_autoload($args = array())
{
	global $MSO;
	
	mso_register_widget('last_comments_widget', t('Последние комментарии')); # регистрируем виджет
	mso_hook_add('new_comment', 'last_comments_new_comment'); # хук на новый коммент - нужно сбросить кэш комментариев
	
	// для того, чтобы обновлять только ключи этого виджета, а не всего кэша
	// в $MSO сохраним все созданные ключи кэша
	// при хуке new_comment просто их сбросим
	
	$MSO->data['cache_key']['last_comments'] = array();
}

# функция выполняется при деинсталяции плагина
function last_comments_uninstall($args = array())
{	
	mso_delete_option_mask('last_comments_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# хук на сброс кэша при новом комментарии
function last_comments_new_comment($args = array())
{
	// очистим кэш по нашей маске, то есть файлы начинающиеся с указанной строки
	mso_flush_cache_mask('last_comments_widget_');
	
	return $args;
}


# функция, которая берет настройки из опций виджетов
function last_comments_widget($num = 1) 
{
	$widget = 'last_comments_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
				$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
		else $options['header'] = '';
	
	return last_comments_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function last_comments_widget_form($num = 1) 
{
	$widget = 'last_comments_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = 5;
	if ( !isset($options['words']) ) $options['words'] = 20;
	if ( !isset($options['maxchars']) ) $options['maxchars'] = 20;
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Количество'), form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ), '');
	
	$form .= mso_widget_create_form(t('Количество слов'), form_input( array( 'name'=>$widget . 'words', 'value'=>$options['words'] ) ), '');
	
	$form .= mso_widget_create_form(t('Количество символов в одном слове'), form_input( array( 'name'=>$widget . 'maxchars', 'value'=>$options['maxchars'] ) ), '');
	
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function last_comments_widget_update($num = 1) 
{
	$widget = 'last_comments_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['count'] = (int) mso_widget_get_post($widget . 'count');
	$newoptions['words'] = (int) mso_widget_get_post($widget . 'words');
	$newoptions['maxchars'] = (int) mso_widget_get_post($widget . 'maxchars');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function last_comments_widget_custom($options = array(), $num = 1)
{	

	if (!isset($options['count'])) $options['count'] = 5;
	if (!isset($options['words'])) $options['words'] = 20;
	if (!isset($options['maxchars'])) $options['maxchars'] = 20;
	if (!isset($options['header'])) $options['header'] = '';
	
	$options['count'] = (int) $options['count'];
	if ($options['count'] < 1) $options['count'] = 5;
	
	$options['words'] = (int) $options['words'];
	if ($options['words'] < 1) $options['words'] = 20;
	
	$options['maxchars'] = (int) $options['maxchars'];
	if ($options['maxchars'] < 1) $options['maxchars'] = 20;
	
	$cache_key = 'last_comments_widget_' . $num . mso_md5(serialize($options));
	
	$k = mso_get_cache($cache_key, true);
	if ($k) return $k; // да есть в кэше


	require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев
	
	$comments = mso_get_comments(false, 
			array('limit' => $options['count'], 'order'=>'desc'));

	$out = '';
	
	if ($comments) // есть страницы
	{ 	
		// сгруппируем все комментарии по записям
		$arr_com_page = array();
		$arr_com_page_title = array();
		foreach ($comments as $comment)
		{
			$arr_com_page[ $comment['page_id'] ] [$comment['comments_id']] = $comment;
			$arr_com_page_title[ $comment['page_id'] ]  = $comment['page_title'];
		}
			
		// выводим по странично
		foreach ($arr_com_page as $key=>$comments)  // выводим в цикле
		{
			$out .= '<h2 class="last_comment">' . $arr_com_page_title[$key] . '</h2>' . NR;
			
			$comments = array_reverse($comments); // чтобы комментарии были в привычном порядке сверху вниз
			
			$out .= '<ul class="is_link last_comment">' . NR;
			
			foreach ($comments as $comment)  // выводим в цикле
			{
				extract($comment);
				
				if ($comment['comments_users_id']) 
					$css_style_add = 'last_comment_users ' . ' last_comment_users_' . $comment['comments_users_id'];
				elseif ($comment['comments_comusers_id']) 
					$css_style_add = 'last_comment_comusers ' . ' last_comment_comusers_' . $comment['comments_comusers_id'];
				else 
					$css_style_add = 'last_comment_anonim';
				
				$out .= '<li class="' . $css_style_add . '"><a href="' . getinfo('siteurl') . 'page/' . mso_slug($page_slug) . '#comment-' . $comments_id . '" id="comment-' . $comments_id . '"><strong>';
				
				if ($comments_users_id) // это автор
				{
					$out .= $users_nik;
				}
				elseif ($comments_comusers_id) // это комюзер
				{
					if ($comusers_nik) $out .= $comusers_nik;
						else $out .= t('Комментатор') . ' ' . $comusers_id;
				}
				elseif ($comments_author_name) $out .= $comments_author_name; // аноним . ' (анонимно)'
				else $out .= ' ' . t('Аноним');
				
				$comments_content_1 = strip_tags($comments_content); // удалим тэги
				$comments_content = mso_str_word($comments_content_1, $options['words']); // ограничение на количество слов
				
				// если старый и новый текст после обрезки разные, значит добавим в конце ...
				if ($comments_content_1 != $comments_content) $comments_content .= '...';
				
				// каждое слово нужно проверить на длину и если оно больше maxchars, то добавить пробел в wordwrap
				$words = explode(' ', $comments_content);
				foreach($words as $key=>$word)
					$words[$key] = mso_wordwrap($word, $options['maxchars'], ' ');
				$comments_content = implode(' ', $words);
				
				
				$out .= ' »</strong>  ' . strip_tags($comments_content) . '</a>';
				// $out .=  '<br><em>«' . $page_title . '»</em>';
				$out .= '</li>' . NR; 
			}
			$out .= '</ul>' . NR;
		}
		
		if ($options['header']) $out = $options['header'] . $out;
	}
	
	mso_add_cache($cache_key, $out, false, true); // сразу в кэш добавим
	
	return trim($out);
}

# end file