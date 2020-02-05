<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 *
 * Alexander Schilling
 * (c) http://maxsite.thedignity.biz/
 *
 * Icons
 * (c) http://icondock.com/
 */

# функция автоподключения плагина
function addzakl_autoload()
{
	if (is_type('page')) {
		$options = mso_get_option('plugin_addzakl', 'plugins', array());

		if (!isset($options['priory'])) $options['priory'] = 10;
		
		mso_hook_add('content_end', 'addzakl_content_end', $options['priory']);
	}
}

# функция выполняется при деинсталяции плагина
function addzakl_uninstall()
{
	mso_delete_option('plugin_addzakl', 'plugins'); // удалим созданные опции
}

function addzakl_mso_options()
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options(
		'plugin_addzakl',
		'plugins',
		array(
			'size' => array(
				'type' => 'select',
				'name' => t('Размеры иконок'),
				'description' => t('Выберите размеры иконок'),
				'values' => '16 # 24 # 32',  // правила для select как в ini-файлах
				'default' => '24'
			),
			'text-do' => array(
				'type' => 'text',
				'name' => t('Текст перед иконками'),
				'description' => t('Укажите произвольный текст перед иконками. Можно использовать HTML'),
				'default' => ''
			),
			'text-posle' => array(
				'type' => 'text',
				'name' => t('Текст после иконками'),
				'description' => t('Укажите произвольный текст после иконок'),
				'default' => ''
			),

			'priory' => array(
				'type' => 'text',
				'name' => t('Приоритет блока'),
				'description' => t('Позволяет расположить блок до или после аналогичных. Используйте значения от 1 до 90. Чем больше значение, тем выше блок. По умолчанию значение равно 10.'),
				'default' => '10'
			),
			'temp' => array(
				'type' => 'info',
				'title' => t('Выберите какие кнопки следует отображать'),
				'text' => '',
			),


			'twitter' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/twitter.png"> twitter',
				'description' => '',
				'default' => '1'
			),

			'facebook' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/facebook.png"> facebook',
				'description' => '',
				'default' => '1'
			),

			'vkontakte' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/vkontakte.png"> vkontakte',
				'description' => '',
				'default' => '0'
			),
			'odnoklassniki' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/odnoklassniki.png"> odnoklassniki',
				'description' => '',
				'default' => '0'
			),
			'mail-ru' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/mail-ru.png"> mail-ru',
				'description' => '',
				'default' => '0'
			),
			'yaru' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/yaru.png"> yaru',
				'description' => '',
				'default' => '0'
			),
			'rutvit' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/rutvit.png"> rutvit',
				'description' => '',
				'default' => '0'
			),
			'myspace' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/myspace.png"> myspace',
				'description' => '',
				'default' => '0'
			),

			'technorati' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/technorati.png"> technorati',
				'description' => '',
				'default' => '0'
			),
			'digg' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/digg.png"> digg',
				'description' => '',
				'default' => '0'
			),

			'pikabu' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/pikabu.png"> pikabu',
				'description' => '',
				'default' => '0'
			),
			'blogger' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/blogger.png"> blogger',
				'description' => '',
				'default' => '0'
			),
			'liveinternet' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/liveinternet.png"> liveinternet',
				'description' => '',
				'default' => '0'
			),
			'livejournal' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/livejournal.png"> livejournal',
				'description' => '',
				'default' => '0'
			),

			'google-bookmarks' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/google-bookmarks.png"> google-bookmarks',
				'description' => '',
				'default' => '0'
			),

			'yahoo-bookmarks' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/yahoo-bookmarks.png"> yahoo-bookmarks',
				'description' => '',
				'default' => '0'
			),
			'yandex' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/yandex.png"> yandex',
				'description' => '',
				'default' => '0'
			),
			'delicious' => array(
				'type' => 'checkbox',
				'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'addzakl/images24/delicious.png"> delicious',
				'description' => '',
				'default' => '0'
			),
		),
		t('Закладки на соц.сервисы'), // титул
		t('Укажите необходимые опции.')   // инфо
	);
}

# функции плагина
function addzakl_content_end($args = array())
{
	$options = mso_get_option('plugin_addzakl', 'plugins', []);

	$def_options = array(
		'size' => 24,
		'text-do' => '',
		'text-posle' => '',

		'twitter' => 1,
		'facebook' => 1,
		'vkontakte' => 0,
		'odnoklassniki' => 0,
		'mail-ru' => 0,
		'yaru' => 0,
		'myspace' => 0,
		'technorati' => 0,
		'digg' => 0,
		'pikabu' => 0,
		'blogger' => 0,
		'liveinternet' => 0,
		'livejournal' => 0,
		'google-bookmarks' => 0,
		'yahoo-bookmarks' => 0,
		'yandex' => 0,
		'delicious' => 0,
	);

	$options = array_merge($def_options, $options);

	$size = (int) $options['size']; // размер икнонок

	$sep = ' ';  # разделитель мужду кнопками - можно указать свой

	# ширина и высота картинок
	$width_height = ' width="' . $size . '" height="' . $size . '"';

	if ($size == 16) // если размер 16, то каталог /images/
		$path = getinfo('plugins_url') . 'addzakl/images/'; # путь к картинкам
	else // каталог /imagesXX/
		$path = getinfo('plugins_url') . 'addzakl/images' . $size . '/'; # путь к картинкам

	$pageData = mso_get_val('mso_pages', 0, true);

	$post_title = urlencode(stripslashes($pageData['page_title'] . ' - ' . mso_get_option('name_site', 'general')));
	$post_link = getinfo('siteurl') . mso_current_url();
	$out = '';

	if ($options['twitter']) {
		$img_src = 'twitter.png';

		$link = '<a rel="nofollow" href="https://twitter.com/intent/tweet?text=' . urlencode(stripslashes(mb_substr($pageData['page_title'], 0, 139 - mb_strlen($post_link, 'UTF8'), 'UTF8') . ' ' . $post_link)) . '">';

		$out .= $link . '<img title="Добавить в Twitter" alt="twitter.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['facebook']) {
		$img_src = 'facebook.png';
		$link = '<a rel="nofollow" href="//www.facebook.com/sharer.php?u=' . $post_link . '">';
		$out .= $sep . $link . '<img title="Поделиться в Facebook" alt="facebook.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['vkontakte']) {
		$img_src = 'vkontakte.png';
		$link = '<a rel="nofollow" href="//vkontakte.ru/share.php?url=' . $post_link . '&amp;title=' . $post_title  . '">';
		$out .= $sep . $link . '<img title="Поделиться В Контакте" alt="vkontakte.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['odnoklassniki']) {
		$img_src = 'odnoklassniki.png';
		$link = '<a rel="nofollow" href="//www.odnoklassniki.ru/dk?st.cmd=addShare&amp;st._surl=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Добавить в Одноклассники" alt="odnoklassniki.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['mail-ru']) {
		$img_src = 'mail-ru.png';
		$link = '<a rel="nofollow" href="//connect.mail.ru/share?url=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Поделиться в Моем Мире@Mail.Ru" alt="mail.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['yaru']) {
		$img_src = 'yaru.png';
		$link = '<a rel="nofollow" href="//my.ya.ru/posts_add_link.xml?URL=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Поделиться в Я.ру" alt="ya.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['myspace']) {
		$img_src = 'myspace.png';
		$link = '<a rel="nofollow" href="//www.myspace.com/Modules/PostTo/Pages/?u=' . $post_link . '&amp;t=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Добавить в MySpace" alt="myspace.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['technorati']) {
		$img_src = 'technorati.png';
		$link = '<a rel="nofollow" href="//www.technorati.com/faves?add=' . $post_link . '">';
		$out .= $sep . $link . '<img title="Добавить в Technorati" alt="technorati.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['digg']) {
		$img_src = 'digg.png';
		$link = '<a rel="nofollow" href="//digg.com/submit?url=' . $post_link .  '">';
		$out .= $sep . $link . '<img title="Добавить в Digg" alt="digg.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['pikabu']) {
		$img_src = 'pikabu.png';
		$link = '<a rel="nofollow" href="//pikabu.ru/add_story.php?story_url=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Добавить в Pikabu" alt="pikabu.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['blogger']) {
		$img_src = 'blogger.png';
		$link = '<a rel="nofollow" href="//www.blogger.com/blog_this.pyra?t&amp;u=' . $post_link . '&amp;n=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Опубликовать в Blogger.com" alt="blogger.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['liveinternet']) {
		$img_src = 'liveinternet.png';
		$link = '<a rel="nofollow" href="//www.liveinternet.ru/journal_post.php?action=n_add&amp;cnurl=' . $post_link . '&amp;cntitle=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Опубликовать в LiveInternet" alt="liveinternet.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['livejournal']) {
		$img_src = 'livejournal.png';
		$link = '<a rel="nofollow" href="//www.livejournal.com/update.bml?event=' . $post_link . '&amp;subject=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Опубликовать в LiveJournal" alt="livejournal.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['google-bookmarks']) {
		$img_src = 'google-bookmarks.png';
		$link = '<a rel="nofollow" href="//www.google.com/bookmarks/mark?op=edit&amp;bkmk=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Сохранить закладку в Google" alt="google.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['yahoo-bookmarks']) {
		$img_src = 'yahoo-bookmarks.png';
		$link = '<a rel="nofollow" href="//bookmarks.yahoo.com/toolbar/savebm?u=' . $post_link . '&amp;t=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Добавить в Yahoo! Закладки" alt="yahoo.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['yandex']) {
		$img_src = 'yandex.png';
		$link = '<a rel="nofollow" href="//zakladki.yandex.ru/newlink.xml?url=' . $post_link . '&amp;name=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Добавить в Яндекс.Закладки" alt="yandex.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['delicious']) {
		$img_src = 'delicious.png';
		$link = '<a rel="nofollow" href="//del.icio.us/post?url=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Сохранить закладку в Delicious" alt="del.icio.us" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($out)
		echo '<div class="addzakl">' . $options['text-do'] . $out . $options['text-posle'] . '</div>';

	return $args;
}

# end of file
