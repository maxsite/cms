<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 *
 * https://github.com/maxsite/cms/issues/171
 * http://ruogp.me/
 * https://habrahabr.ru/post/278459/
 *
 */

# функция автоподключения плагина
function open_graph_autoload()
{
	mso_hook_add('html_attr', 'open_graph_html_attr'); # хук на атрибуты <HTML>
	mso_hook_add('head', 'open_graph_head'); # хук на <HEAD>
}

# функция выполняется при активации (вкл) плагина
function open_graph_activate($args = [])
{
	mso_create_allow('open_graph_edit', t('Админ-доступ к настройкам open_graph'));
	return $args;
}

# функция выполняется при деинсталяции плагина
function open_graph_uninstall($args = [])
{
	mso_delete_option('plugin_open_graph', 'plugins'); // удалим созданные опции
	mso_remove_allow('open_graph_edit'); // удалим созданные разрешения
	return $args;
}

# атрибуты HTML
# отдавать результат по return
function open_graph_html_attr($arg = '')
{
	// если уже есть подключение og, то ничего не делаем
	if (strpos($arg, 'prefix="og: http://ogp.me/ns#"') === false)
		$arg .= ' prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#"';

	return $arg;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function open_graph_mso_options()
{
	if (!mso_check_allow('open_graph_edit')) {
		echo t('Доступ запрещен');

		return;
	}

	# ключ, тип, ключи массива
	mso_admin_plugin_options(
		'plugin_open_graph1',
		'plugins',
		[
			'image_width' => [
				'type' => 'text',
				'name' => t('Ширина изображения'),
				'description' => t('Используется для формирования миниатюры записи'),
				'default' => '900'
			],

			'image_height' => [
				'type' => 'text',
				'name' => t('Высота изображения'),
				'description' => t('Используется для формирования миниатюры записи'),
				'default' => '600'
			],

			'image_type_resize' => [
				'type' => 'select',
				'name' => t('Способ формирования изображения'),
				'description' => t(''),
				'values' => 'resize_full_crop_center # resize_full_crop_top_left # resize_full_crop_top_center # resize_crop # resize_crop_center # resize_h_crop_center # crop # crop_center # resize # resize_w # resize_h',
				'default' => 'resize_full_crop_center'
			],

			'locale' => [
				'type' => 'text',
				'name' => t('Язык локали'),
				'description' => t('Например <code>ru_RU</code> или <code>en_US</code>'),
				'default' => 'ru_RU'
			],

			'twitter_site' => [
				'type' => 'text',
				'name' => t('Имя в Twitter'),
				'description' => t('Указывайте с <code>@</code>'),
				'default' => ''
			],

		],
		t('Настройки плагина Open Graph'), // титул
		t('Протокол Open Graph позволяет странице стать полноценным объектом в социальных сетях.')   // инфо
	);
}


# meta-данные
# выводятся через echo
function open_graph_head($arg = '')
{
	$o = mso_get_option('plugin_open_graph1', 'plugins', array());

	$locale = isset($o['locale']) ? $o['locale'] : 'ru_RU';
	$twitter_site = isset($o['twitter_site']) ? $o['twitter_site'] : '';

	// http://ruogp.me/#types

	if (is_type('home'))
		_meta_content('og:type', 'website');
	else
		_meta_content('og:type', 'article');

	$description = mso_head_meta('description');
	$title = mso_head_meta('title');
	// $url = mso_link_rel('canonical', '', true);
	$url = mso_current_url(true);

	_meta_content('og:title', $title);
	_meta_content('og:description', $description);
	_meta_content('og:url', $url);
	_meta_content('og:locale', $locale);
	_meta_content('og:site_name', getinfo('name_site'));

	_meta_content('twitter:title', $title);
	_meta_content('twitter:description', $description);
	_meta_content('twitter:url', $url);
	_meta_content('twitter:domain', getinfo('siteurl'));

	_meta_content('twitter:site', $twitter_site);
	_meta_content('twitter:creator', $twitter_site);

	if (is_type('page')) {
		// получаем текущую запись — они задаются в type-файле
		if ($pageData = mso_get_val('mso_pages', 0, true)) {
			if (isset($pageData['page_meta']['image_for_page'][0]) and $pageData['page_meta']['image_for_page'][0]) {
				$image_width = isset($o['image_width']) ? $o['image_width'] : 900;
				$image_height = isset($o['image_height']) ? $o['image_height'] : 600;

				$image_type_resize = isset($o['image_type_resize']) ? $o['image_type_resize'] : 'resize_full_crop_center';

				if ($image_type_resize !== 'resize_full_crop_center') {
					$postfix = '-' . $image_width . '-' . $image_height . '-' . $image_type_resize;
				} else {
					$postfix = '-' . $image_width . '-' . $image_height;
				}

				if ($image_url = thumb_generate(
					$pageData['page_meta']['image_for_page'][0], // адрес
					$image_width, //ширина
					$image_height, //высота
					false,
					$image_type_resize, // тип создания
					false,
					'mini',
					$postfix,
					mso_get_option('upload_resize_images_quality', 'general', 90)
				)) {
					_meta_content('og:image', $image_url);
					_meta_content('og:image:width', $image_width);
					_meta_content('og:image:height', $image_height);
					_meta_content('twitter:image', $image_url);
					_meta_content('twitter:card', 'summary_large_image');
				}
			}
		}
	}

	return $arg;
}

# вспомогательная для формирования <meta property="" content="">
function _meta_content($meta = '', $content = '', $echo = true)
{
	if ($echo)
		echo '<meta property="' . $meta . '" content="' . $content . '">' . NR;
	else
		return '<meta property="' . $meta . '" content="' . $content . '">';
}

# end of file
