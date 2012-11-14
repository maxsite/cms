<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<h1><?= t('Добро пожаловать в MaxSite CMS!') ?></h1>

<br>

<ul>
	<li><a href="http://max-3000.com/"><?= t('Официальный сайт') ?></a></li>
	<li><a href="http://max-3000.com/page/faq"><?= t('ЧАВО по MaxSite CMS для новичков') ?></a></li>
	<li><a href="http://forum.max-3000.com/"><?= t('Форум поддержки') ?></a></li>
	<li><a href="http://wiki.max-3000.com/"><?= t('Wiki MaxSite CMS') ?></a></li>
	<li><a href="http://max-3000.com/help"><?= t('Центр помощи') ?></a></li>
	<li><a href="http://alexanderschilling.net/plugins"><?= t('Каталог плагинов') ?></a></li>
	<li><a href="http://alexanderschilling.net/themes"><?= t('Каталог шаблонов') ?></a></li>
	
</ul>

<p><?= t('Ваша версия <strong>MaxSite CMS</strong>') ?>: <?= getinfo('version') ?></p>

<?php

	if (mso_check_allow('admin_home')) // если есть разрешение на доступ
	{
		$show_check_version = true;
		$show_clear_cache = true;

		if ( $post = mso_check_post(array('f_session_id', 'f_submit_check_version')) )
		{
			mso_checkreferer();
			$show_check_version = false;
			$url = 'http://max-3000.com/uploads/latest.txt';
			$latest = @file($url); // массив
			if (!$latest)
			{
				echo '<div class="error">'. t('Ошибка соединения с max-3000.com!') . '</div>';
			}
			else
			{
				if (!isset($latest[0]))
				{
					echo '<div class="error">' . t('Полученная информация является ошибочной') . '</div>';
				}
				else
				{
					$info1 = explode('|', $latest[0]);
					
					echo '<p>' . t('Последняя опубликованная версия') . ': <a href="' . $info1[2] . '">' . $info1[0] . '</a> (' . $info1[1] . ')</p>';
					
					if ( $info1[0] > getinfo('version') )
					{
						echo '<p style="margin: 10px 0; font-weight: bold;">';
						echo sprintf( t('Вы можете %sвыполнить обновление'), '<a href="' . $info1[2] . '">' ) . '</a> ';
						echo t('или настроить <a href="http://max-3000.com/page/update-maxsite-cms" target="_blank">автоматическое обновление</a>.');
						
						echo '</p>';
					}
					else
					{
						echo '<p style="margin: 10px 0; font-weight: bold;">' . t('Обновление не требуется.') . '</p>';
					}
				}
			}
		}

		if ( $post = mso_check_post(array('f_session_id', 'f_submit_clear_cache')) )
		{
			mso_checkreferer();
			$show_clear_cache = false;
			mso_flush_cache(); // сбросим кэш
			echo '<p style="margin: 10px 0; font-weight: bold;">' . t('Кэш удален') . '</p><br>';
		}


		if ($show_check_version or $show_clear_cache)
		{
			echo '<form method="post">' . mso_form_session('f_session_id');

			if ($show_check_version)
				echo '<p><input type="submit" name="f_submit_check_version" value="' . t('Проверить последнюю версию MaxSite CMS') . '"></p>';

			if ($show_clear_cache)
				echo '<p><input type="submit" name="f_submit_clear_cache" value="' . t('Сбросить кэш системы') . '"></p>';

			echo '</form>';
		}
		
	} //if (mso_check_allow('admin_home'))
		
	# получать последние новости
	$max_3000_news = mso_get_option('max_3000_news', 'general', 0);
	
	if ($max_3000_news)
	{
		
		if (!defined('MAGPIE_CACHE_AGE'))	define('MAGPIE_CACHE_AGE', 24*60*60); // время кэширования MAGPIE - 1 сутки
		require_once(getinfo('common_dir') . 'magpierss/rss_fetch.inc');
		$rss = @fetch_rss('http://max-3000.com/feed');
		// $rss = fetch_rss('http://max-3000.com/feed-home-full');

		if ($rss and isset($rss->items) and $rss->items)
		{
			$rss = $rss->items;
			$rss = array_slice($rss, 0, 3); // последние три записи
			
			//pr($rss);
			foreach ($rss as $item)
			{
				// title link category description date_timestamp pubdate
				
				echo '<div style="margin-top: 20px;">';
				
				if (!isset($item['category'])) $item['category'] = '-';
				
				echo '<h4 class="admin_home_maxrss"><a href="' . $item['link'] . '">' . $item['title'] 
						. '</a> | ' . $item['category'] . ' | ' . date('Y-m-d , H:i:s', $item['date_timestamp']) . '</h4>';
				
				echo '<div>' . $item['description'] . '</div>';
				echo '</div>';
			}
		}
	}
	
		
	mso_hook('admin_home');
	
?>