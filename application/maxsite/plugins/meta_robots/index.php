<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

# функция автоподключения плагина
function meta_robots_autoload()
{
	mso_hook_add('head', 'meta_robots_head'); 
}

# функция выполняется при активации (вкл) плагина
function meta_robots_activate()
{
	mso_create_allow('meta_robots_edit', t('Админ-доступ к настройкам MetaRobots'));
}

# функция выполняется при деактивации (выкл) плагина
function meta_robots_deactivate()
{
	mso_delete_option('plugin_meta_robots', 'plugins'); // удалим созданные опции
}

# функция выполняется при деинсталяции плагина
function meta_robots_uninstall()
{
	mso_delete_option('plugin_meta_robots', 'plugins'); // удалим созданные опции
	mso_remove_allow('meta_robots_edit'); // удалим созданные разрешения
}

# опции
function meta_robots_mso_options()
{
	if (!mso_check_allow('meta_robots_edit')) {
		echo t('Доступ запрещен');
		return;
	}

	# ключ, тип, ключи массива
	mso_admin_plugin_options(
		'plugin_meta_robots',
		'plugins',
		[
			'templates' => [
				'type' => 'textarea',
				'rows' => 10,
				'name' => t('Шаблоны URL'),
				'description' => t('Шаблоны URL указываются в виде <pre>адрес | meta-значение</pre> Адреса указываются относительно сайта. Значения meta могут быть любыми. Например: <pre>page/about | noindex, nofollow</pre>
				Можно использовать регулярные выражения. Произвольный сегмент указывается как «(*)». 
				<pre>category/(*) | noindex, nofollow</pre>
				'),
				'default' => ''
			],
		],
		t('Настройки плагина MetaRobots'), // титул
		t('Плагин позволяет задавать meta-тэг robots (в секции HEAD) по шаблонам URL. См. <a href="https://developers.google.com/search/reference/robots_meta_tag?hl=ru#directives" rel="nofollow" target="_blank">документацию</a>')   // инфо
	);
}

# основная функция
function meta_robots_head($arg = [])
{
	$options = mso_get_option('plugin_meta_robots', 'plugins', []);

	$current_url = mso_current_url(); // текущий адрес
	if ($current_url === '') return $arg; // главная

	if (!isset($options['templates'])) $options['templates'] = '';
	$templates = explode("\n", trim($options['templates'])); // по строкам

	$meta = ''; // результат

	foreach ($templates as $template) {
		$template = trim($template);
		if (!$template) continue;

		$r = explode('|', $template);
		$url = $r[0] ?? '';
		$robots = $r[1] ?? '';

		if (!$url or !$robots) continue;

		$reg = str_replace('(*)', '(.[^/]*)', trim($url));
		$reg = '~' . str_replace(')(', '){1}/(', $reg) . '\z~siu';

		// pr($current_url);
		// pr($reg);

		if (preg_match($reg, $current_url)) {
			$meta = trim($robots);
			break;
		}
	}

	if ($meta) echo '<meta name="robots" content="' . $meta . '">';

	return $arg;
}

# end of file
