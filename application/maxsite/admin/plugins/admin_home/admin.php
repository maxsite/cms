<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<h1 class="mar20-t"><?= t('Добро пожаловать в MaxSite CMS!') ?></h1>
<div class="flex flex-wrap-tablet">
<ul class="flex-grow3">
<li><a href="https://max-3000.com/"><?= t('Официальный сайт MaxSite CMS') ?></a> &bull; <a href="https://max-3000.com/page/donation"><?= t('Помочь проекту') ?></a></li>
<li><a href="https://github.com/maxsite/cms"><?= t('Исходный код на GitHub') ?></a> &bull; <a href="https://github.com/maxsite/cms/commits/master"><?= t('История изменений') ?></a></li>
<li><a href="https://github.com/maxsite/cms/issues"><?= t('Сообщить о проблеме. Форум') ?></a></li>
<li><a href="https://max-3000.com/doc"><?= t('Документация') ?></a> &bull; <a href="https://max-3000.com/book"><?= t('Основы работы с MaxSite CMS') ?></a></li>
<li><a href="http://maxhub.ru/"><?= t('MaxHub - сообщество MaxSite CMS') ?></a> &bull; <a href="http://maxhub.ru/category/templates"><?= t('Шаблоны') ?></a> &bull; <a href="http://maxhub.ru/category/plugins"><?= t('Плагины') ?></a></li>
<li><a href="https://maxsite.org/demo-templates"><?= t('Демо-каталог шаблонов') ?></a> &bull; <a href="https://maxsite.org/page/templates"><?= t('Готовые шаблоны') ?></a></li>
</ul>
<div class="flex-grow1">
	<iframe src="https://maxsite.github.io/version.html?version=<?= getinfo('version') ?>" scrolling="no" frameborder="no" style="width: 100%; height: 60px; "></iframe>
<?php

if (mso_check_allow('admin_home')) // если есть разрешение на доступ
{
	$show_clear_cache = true;
	
	if ($post = mso_check_post(array('f_session_id', 'f_submit_clear_cache')))
	{
		mso_checkreferer();
		
		$show_clear_cache = false;
		mso_flush_cache(true); // сбросим весь кэш
		
		// удалим пустые каталоги в uploads/_pages
		$CI = & get_instance();
		$CI->load->helper('directory');
		$CI->load->helper('file');
		
		$p = getinfo('uploads_dir') . '_pages/';
		
		if ($a = directory_map($p, 1))
		{
			foreach($a as $m)
			{
				$f = get_filenames($p . $m, false, false);
				
				if (count($f) === 0)
				{
					delete_files($p . $m, true);	
					@rmdir($p . $m);
				}
			}
		}
		
		echo '<p class="i-check"><a href="' . getinfo('site_admin_url') .'home">' . t('Кэш очищен') . '</a></p>';
	}

	if ($show_clear_cache)
	{
		echo '<form method="post">' . mso_form_session('f_session_id') . '<p><button type="submit" name="f_submit_clear_cache" class="button i-stack-overflow">' . t('Очистить кэш системы') . '</button></p></form>';
	}
}

?>
</div></div>
<?php

if (mso_check_allow('admin_home')) // если есть разрешение на доступ к консоли
{
	// проверим версию PHP
	if (version_compare(PHP_VERSION, '7.1' , '<') )
	{
		echo '<div class="pad10 bg-red100 t-red">' . t('У вас используется устаревшая версия PHP') . ' (' . PHP_VERSION . '). ' . t('Требуется версия PHP 7.1 и выше.') . '</div>';
	}
	
	// свой блок
	if ($admin_block_for_home = mso_get_option('admin_block_for_home', 'general', '')) echo '<div class="admin_block_for_home">' . $admin_block_for_home . '</div>'; 

	// черновики
	require_once( getinfo('common_dir') . 'page.php' ); // функции страниц 
	
	$draft_pages = mso_get_pages(
			array(  
				'type'=> false, 
				'content'=> false, 
				'pagination'=>false, 
				'custom_type'=> 'home', 
				'page_status'=> 'draft', 
				'limit'=> 10, 
				'order'=> 'page_date_publish',
				'order_asc'=>'desc'
				),
				$_temp);
	
	$ex_pages = array(); // массив исключенных записей для следующего блока
	
	if ($draft_pages)
	{
		echo '<h4 class="mar30-t i-coffee">' . t('Последние черновики') .'</h4><ul>';
		
		foreach($draft_pages as $p)
		{
			$ex_pages[] = $p['page_id'];
			
			if (!$p['page_title']) $p['page_title'] = t('Без заголовка');
			
			echo '<li class=""><a href="' . getinfo('site_admin_url') .'page_edit/' . $p['page_id'] .'">' . $p['page_title'] . '</a>';
			
			$ddd = $p['page_date_publish'];
			
			if (($dd = strtotime($p['page_date_publish'])) !== -1) // успешное преобразование
					$ddd = date('Y-m-d', $dd);
					
			echo ' &bull; <span class="t90">' . $ddd;
			
			if (isset($p['page_categories_detail']) and $p['page_categories_detail'])
			{
				echo ' &bull; ';
				
				foreach($p['page_categories_detail'] as $c)
				{
					echo '<a class="mar10-r" href="' . getinfo('siteurl') .'category/' . $c['category_slug'] .'">' . $c['category_name'] . '</a> ';
				}
			}
			
			echo '</span></li>';
		}
		
		echo '</ul>';
	}

	// очень старые черновики
	$draft_pages = mso_get_pages(
			array(  
				'type'=> false, 
				'content'=> false, 
				'pagination'=>false, 
				'custom_type'=> 'home', 
				'limit'=> 10, 
				'page_status'=> 'draft', 
				'order'=> 'page_date_publish',
				'order_asc'=>'asc',
				'exclude_page_id' => $ex_pages
				),
				$_temp);
	
	if ($draft_pages)
	{
		echo '<h4 class="mar50-t i-calendar-o">' . t('Самые старые черновики') .'</h4><ul>';
		
		foreach($draft_pages as $p)
		{
			if (!$p['page_title']) $p['page_title'] = t('Без заголовка');
			
			echo '<li class=""><a href="' . getinfo('site_admin_url') .'page_edit/' . $p['page_id'] .'">' . $p['page_title'] . '</a>';
			
			$ddd = $p['page_date_publish'];
			
			if (($dd = strtotime($p['page_date_publish'])) !== -1) // успешное преобразование
					$ddd = date('Y-m-d', $dd);
					
			echo ' &bull; <span class="t90">' . $ddd;
			
			if (isset($p['page_categories_detail']) and $p['page_categories_detail'])
			{
				echo ' &bull; ';
				
				foreach($p['page_categories_detail'] as $c)
				{
					echo '<a class="mar10-r" href="' . getinfo('siteurl') .'category/' . $c['category_slug'] .'">' . $c['category_name'] . '</a> ';
				}
			}
			
			echo '</span></li>';
		}
		
		echo '</ul>';
	}
	
	
	// комментарии
	$CI = & get_instance();
	
	$CI->db->select('SQL_CALC_FOUND_ROWS comments_id, comments_users_id, comments_comusers_id, comments_author_name, comments_date, comments_content, comments_approved, users.users_nik, comusers.comusers_nik, page.page_title', false);
	$CI->db->from('comments');
	$CI->db->join('users', 'users.users_id = comments.comments_users_id', 'left');
	$CI->db->join('comusers', 'comusers.comusers_id = comments.comments_comusers_id', 'left');
	$CI->db->join('page', 'page.page_id = comments.comments_page_id', 'left');
	
	$CI->db->where('comments_approved', 0);
	$CI->db->order_by('comments_date', 'desc');
	$CI->db->limit(10);
	
	$query = $CI->db->get();
	
	$p_count = mso_sql_found_rows(1);
	
	if ($query->num_rows() > 0)
	{
		$comments_url =  getinfo('site_admin_url') . 'comments';
		
		echo '<h4 class="mar50-t i-eye-slash">' . t('Комментарии') . '</h4>';
		
		echo '<p class="mar10-t"><a href="' . $comments_url . '">' . t('Модерировать все комментарии') . ': '. $p_count['found_rows'] . '</a></p>';
				
		echo '<ul>';
		
		foreach ($query->result_array() as $row)
		{
			$autor = mso_clean_str($row['comments_author_name'] . $row['users_nik'] . $row['comusers_nik'], 'base');

			$content = mso_clean_str($row['comments_content'], 'base');
			$content = mb_substr(strip_tags($content), 0, 300, 'UTF-8');
			
			echo '<li class="mar10-b"><a href="' . $comments_url . '/edit/' . $row['comments_id'] . '">' 
				. $row['comments_date'] 
				. ' / ' . $autor  
				. ' / ' . $row['page_title']
				. '</a>'
				. '<div class="t90">'
				. $content
				. '</div></li>';
		}
		
		echo '</ul>';
	}

	
	// рубрики
	
	require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик
	
	// все рубрики
	$cats = mso_cat_array_single('page', 'category_name', 'ASC', '', true, true, '');
	
	if ($cats)
	{
		$m = array();
		
		foreach($cats as $p)
		{
			$m[$p['category_id']] = count($p['pages']);
		}
		
		asort($m); // здесь id-рубрик в прямом порядке по колву записей
		
		// из-за особенностей array_pop работаем с копией массива
		$mp = $m;
		array_pop($mp);
			
		// по колву записей — их сумма
		$max = array_sum($m); // всего записей

		$m = array_slice($m, 0, 50, true); // срез, чтобы не было слишком много
		
		arsort($m); // обратный порядок
		echo '<h4 class="mar50-t i-bar-chart">' . t('Статистика рубрик') .'</h4>';
		
		foreach($m as $id=>$count)
		{
			if ($max > 0)
				$k = round($count * 100 / $max);
			else
				$k = 0;
			
			echo '<a style="background-color: #b9bed7; color: #111111" class="b-inline pad5-tb pad10-rl mar5-b hover-no-underline hover-bg-blue700 hover-t-white" href="' . getinfo('site_admin_url') .'page/category/' . $id .'">' . $cats[$id]['category_name'] . ' <sup>' . $count . '</sup> <sub>' . $k .'%</sub></a> ';
		}
	
	
		// вывод рубрик, где давно не было записей
		$cat_date = array();
		
		foreach($cats as $id=>$с)
		{
			if (isset($с['pages_detail']) and $pages = $с['pages_detail'])
			{
				$pages = array_pop($pages); // последняя запись
				$cat_date[$id] = $pages['page_date_publish'];
			}
		}
		
		asort($cat_date); //вверху рубрики, где давно не было обновлений
		
		$cat_date = array_slice($cat_date, 0, 45, true); // срез, чтобы не было слишком много
		
		arsort($cat_date); // последние вверху 
		
		echo '<h4 class="mar50-t i-calendar">' . t('Последние публикации в рубриках') .'</h4><div class="flex flex-wrap">';
		
		foreach($cat_date as $id=>$date)
		{
			
			$ddd = mso_date_convert('j F Y',  $date, false, t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'), t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря'));

			echo '<div class="w24 w46-tablet w100-phone mar5-b"><a style="background-color: #b9bed7; color: #111111; height: 100%;" class="b-inline w100 pad5-tb pad10-rl hover-no-underline hover-bg-blue700 hover-t-white" href="' . getinfo('site_admin_url') .'page/category/' . $id .'">' . $cats[$id]['category_name'] . ' <br><span class="t90">' . $ddd . '</span></a></div>';
		}
	
		echo '</div>';
	}
	

	// получать последние новости — пока принудительно потом сделать опции для всех настроек консоли
	// if (mso_get_option('max_3000_news', 'general', 0))
	// {
		if (!function_exists('rss_get_go'))
			require_once(getinfo('plugins_dir') . 'rss_get/index.php');
		
		$rss = rss_get_go(array(
			'url' => 'https://max-3000.com/feed', 
			'format' => '<li><a target="_blank" href="[link]">[title] ([category])</a> &bull; <span class="t90">[pubDate]</span></li>', 
			'fields' => 'title link description pubDate category',
			'count' => 7, 
			'format_date' => 'Y-m-d',  // 'd.m.Y H:i', 
			'max_word_description' => 40, 
			'time_cache' => 720, // 720 минут = 12 часов
			'fields_items' => 'items',
			'charset' => 'UTF-8',
		));
		
		if ($rss)
		{
			echo '<h4 class="mar50-t i-maxcdn">' . t('Новости MaxSite CMS') . '</h4>';
			echo '<ul>' . $rss . '</ul>';
			
		}
	// }
	
	mso_hook('admin_home');

} //if (mso_check_allow('admin_home')) // если есть разрешение на доступ



# end of file