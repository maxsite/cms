<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<h1 class="mar20-t"><?= t('Добро пожаловать в MaxSite CMS!') ?></h1>
<div class="flex flex-wrap-tablet">
<ul class="flex-grow3">
<li><a href="http://max-3000.com/"><?= t('Официальный сайт MaxSite CMS') ?></a> &bull; <a href="http://max-3000.com/page/donation"><?= t('Помочь проекту') ?></a></li>
<li><a href="//github.com/maxsite/cms"><?= t('Исходный код на GitHub') ?></a> &bull; <a href="//github.com/maxsite/cms/tree/dev"><?= t('Dev-версия') ?></a> &bull; <a href="//github.com/maxsite/cms/commits/dev"><?= t('История изменений') ?></a></li>
<li><a href="//github.com/maxsite/cms/issues"><?= t('Сообщить о проблеме. Обсуждения') ?></a></li>
<li><a href="http://max-3000.com/page/help"><?= t('Центр помощи') ?></a> &bull; <a href="http://max-3000.com/page/faq"><?= t('ЧАВО для новичков') ?></a> &bull; <a href="http://book.max-3000.com/"><?= t('Обучающая книга') ?></a></li>
<li><a href="//maxhub.ru/"><?= t('MaxHub - сообщество MaxSite CMS') ?></a> &bull; <a href="//maxhub.ru/category/templates"><?= t('Шаблоны') ?></a> &bull; <a href="//maxhub.ru/category/plugins"><?= t('Плагины') ?></a></li>
<li><a href="http://maxsite.org/demo-templates"><?= t('Демо-каталог шаблонов') ?></a> &bull; <a href="http://maxsite.org/page/templates"><?= t('Авторские шаблоны') ?></a></li>
</ul>
<div class="flex-grow1">
	<iframe src="http://maxsite.github.io/version.html?version=<?= getinfo('version') ?>" scrolling="no" frameborder="no" style="width: 100%; height: 60px; "></iframe>
<?php

if (mso_check_allow('admin_home')) // если есть разрешение на доступ
{
	$show_clear_cache = true;
	
	if ($post = mso_check_post(array('f_session_id', 'f_submit_clear_cache')))
	{
		mso_checkreferer();
		
		$show_clear_cache = false;
		mso_flush_cache(true); // сбросим весь кэш
		echo '<p class="i-check"><a href="' . getinfo('site_admin_url') .'home">' . t('Кэш очищен') . '</a></p>';
		// mso_redirect('admin/home');
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
		echo '<h4 class="mar30-t">' . t('Последние черновики') .'</h4><ul>';
		
		foreach($draft_pages as $p)
		{
			$ex_pages[] = $p['page_id'];
			
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
		echo '<h4 class="mar30-t">' . t('Самые старые черновики') .'</h4><ul>';
		
		foreach($draft_pages as $p)
		{
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
		
		// нужно определить процентность по каждой рубрике
		// для этого берем последнюю — это самое большое кол-во записей
		$max = array_pop($m);
		
		// по колву записей — их сумма
		$max1 = array_sum($m); // всего записей

		$m = array_slice($m, 0, 50, true); // срез, чтобы не было слишком много
		
		echo '<h4 class="mar30-t">' . t('Статистика рубрик') .'</h4>';
		
		foreach($m as $id=>$count)
		{
			$k = round($count * 100 / $max1);
			$h = round($count * 100 / $max) + 40;
			
			if ($count == 0) $h = 0;
				
			echo '<a style="background-color: hsl(' . $h . ', 95%, 30%); color: hsl(' . $h .', 10%, 100%)" class="b-inline pad5-tb pad10-rl mar5-b hover-no-underline hover-bg-blue600" href="' . getinfo('site_admin_url') .'page/category/' . $id .'">' . $cats[$id]['category_name'] . ' <sup>' . $count . '</sup> <sub>' . $k .'%</sub></a> ';
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
		$max = count($cat_date);
		$cat_date = array_slice($cat_date, 0, 45, true); // срез, чтобы не было слишком много
		
		echo '<h4 class="mar30-t">' . t('Последние публикации в рубриках') .'</h4><div class="flex flex-wrap">';
		
		$i = 0;
		foreach($cat_date as $id=>$date)
		{
			$i++;
			$h = round($i * 100 / $max);
			
			$ddd = mso_date_convert('j F Y',  $date, false, t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'), t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря'));
			
			echo '<div class="w24 w46-tablet w100-phone mar5-b"><a style="background-color: hsl(' . $h . ', 95%, 30%); color: hsl(' . $h .', 10%, 100%); height: 100%;" class="b-inline w100 pad5-tb pad10-rl hover-no-underline hover-bg-blue600" href="' . getinfo('site_admin_url') .'page/category/' . $id .'">' . $cats[$id]['category_name'] . ' <br><span class="t90">' . $ddd . '</span></a></div>';
		}
	
		echo '</div>';
	}
	

	// получать последние новости — пока принудительно потом сделать опции для всех настроек консоли
	// if (mso_get_option('max_3000_news', 'general', 0))
	// {
		if (!function_exists('rss_get_go'))
			require_once(getinfo('plugins_dir') . 'rss_get/index.php');
		
		$rss = rss_get_go(array(
			'url' => 'http://max-3000.com/feed', 
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
			echo '<h4 class="mar30-t">' . t('Новости MaxSite CMS') . '</h4>';
			echo '<ul>' . $rss . '</ul>';
			
		}
	// }
	
	mso_hook('admin_home');

} //if (mso_check_allow('admin_home')) // если есть разрешение на доступ



# end of file