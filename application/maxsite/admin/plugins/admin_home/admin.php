<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<h1 class="mar20-t"><?= t('Добро пожаловать в MaxSite CMS!') ?></h1>
<div class="flex flex-wrap-tablet">

<ul class="flex-grow3">
	<li><a href="//max-3000.com/"><?= t('Официальный сайт MaxSite CMS') ?></a> &bull; <a href="//max-3000.com/page/donation"><?= t('Помочь проекту') ?></a></li>
	<li><a href="//github.com/maxsite/cms"><?= t('GitHub') ?></a> &bull; <a href="//github.com/maxsite/cms/tree/dev"><?= t('Dev-версия') ?></a> &bull; <a href="//github.com/maxsite/cms/commits/dev"><?= t('История изменений') ?></a></li>
	<li><a href="//github.com/maxsite/cms/issues"><?= t('Сообщить о проблеме') ?></a></li>
	<li><a href="//book.max-3000.com/"><?= t('Обучающая книга по MaxSite CMS') ?></a></li>
	<li><a href="//max-3000.com/page/help"><?= t('Центр помощи') ?></a> &bull; <a href="//max-3000.com/page/faq"><?= t('ЧАВО для новичков') ?></a></li>
	<li><a href="//maxhub.ru/"><?= t('MaxHub - сообщество MaxSite CMS') ?></a></li>
	<li><a href="//forum.max-3000.com/"><?= t('Форум поддержки') ?></a> &bull; <a href="//maxhub.ru/category/templates"><?= t('Шаблоны') ?></a> &bull; <a href="//maxhub.ru/category/plugins"><?= t('Плагины') ?></a></li>
	<li><a href="//wbloger.com/demo-templates"><?= t('Демо-каталог шаблонов') ?></a></li>
</ul>
<div class="flex-grow1">
	<a class="b-block bg-red600 pad20 t-center t-white hover-t-white hover-bg-red500 hover-no-underline" href="//wbloger.com/" target="_blank" title="Создание реактивных сайтов"><b>&lt;/&gt; Заказать сайт или шаблон</b></a>
	<a class="b-block bg-blue600 pad20 mar10-t t-center t-white hover-t-white hover-bg-blue500 hover-no-underline" href="//unicss.maxsite.com.ua/" target="_blank" title="Less/Css-фреймворк UniCSS"><b>UniCSS</b> { Css: Framework } </a>
	
	<a class="b-block mar10-t t-center" href="//maxsite.org/contact" target="_blank" title="">Разместить здесь рекламу</a>
	
</div>
</div>
<?php
	if ($bl = mso_get_option('admin_block_for_home', 'general', '')) echo '<div>' . $bl . '</div>'; 
?>

<iframe src="//max-3000.com/check-latest?<?= getinfo('version') ?>" scrolling="auto" frameborder="no" style="width: 100%; min-height: 100px; margin-top: 20px;"></iframe>

<?php

	# получать последние новости
	$max_3000_news = mso_get_option('max_3000_news', 'general', 0);
	
	if ($max_3000_news)
	{
		if (!defined('MAGPIE_CACHE_AGE'))	define('MAGPIE_CACHE_AGE', 24*60*60); // время кэширования MAGPIE - 1 сутки
		require_once(getinfo('common_dir') . 'magpierss/rss_fetch.inc');
		$rss = @fetch_rss('http://max-3000.com/feed');

		if ($rss and isset($rss->items) and $rss->items)
		{
			$rss = $rss->items;
			$rss = array_slice($rss, 0, 3); // последние три записи
			
			echo '<h2 class="bor-solid-b bor-gray400 mar20-b mar20-t i-rss">' . t('Новости MaxSite CMS') . '</h2>';
			foreach ($rss as $item)
			{
				// title link category description date_timestamp pubdate
				// if (!isset($item['category'])) $item['category'] = '-';
				
				echo '<h5><a href="' . $item['link'] . '">' . $item['title'] 
						. '</a> - ' . date('d.m.Y', $item['date_timestamp']) . '</h5>';
				echo '<p>' . $item['description'] . '</p>';
				echo '<hr class="dotted mar0-t">';
			}
		}
	}
	
	if (mso_check_allow('admin_home')) // если есть разрешение на доступ
	{
		$show_clear_cache = true;
		
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_clear_cache')) )
		{
			mso_checkreferer();
			$show_clear_cache = false;
			mso_flush_cache(); // сбросим кэш
			// echo '<p>' . t('Кэш удален') . '</p><br>';
			mso_redirect('admin/home');
		}

		if ($show_clear_cache)
		{
			echo '<form method="post">' . mso_form_session('f_session_id');
			
			if ($show_clear_cache)
				echo '<p><button type="submit" name="f_submit_clear_cache" class="button i-stack-overflow">' . t('Сбросить кэш системы') . '</button></p>';

			echo '</form>';
		}
		
	} //if (mso_check_allow('admin_home'))
		
	
		
	mso_hook('admin_home');
	
# end of file