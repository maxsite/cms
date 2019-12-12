<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function theme_switch_autoload($args = array())
{
	mso_hook_add( 'admin_init', 'theme_switch_admin_init'); # хук на админку
	mso_register_widget('theme_switch_widget', t('Шаблоны сайта')); # регистрируем виджет
	mso_hook_add( 'init', 'theme_switch_init', 50); # хук на init с высоким приоритетом
	mso_hook_add( 'body_start', 'theme_switch_body_start'); # хук на body_start
}

# функция выполняется при активации (вкл) плагина
function theme_switch_activate($args = array())
{	
	mso_create_allow('theme_switch_edit', t('Админ-доступ к редактированию Theme switch'));
	return $args;
}


# функция выполняется при init
function theme_switch_init($args = array())
{	
	global $MSO;
	
	// если есть get ?theme=шаблон , то выставляем новую куку по этому значению
	// идея nicothin (Николай Громов) - http://forum.max-3000.com/viewtopic.php?p=9943#p9943	
	$get = mso_parse_url_get(mso_url_get());
	$get = (isset($get['theme']) and $get['theme']) ? mso_xss_clean($get['theme']) : false;
	
	// проверяем есть ли post
	if ( $post = mso_check_post(array('f_session_id', 'f_theme_switch_submit', 'theme_switch_radio')) or $get)
	{
		if (!$get)
		{
			mso_checkreferer();
			$dir = $post['theme_switch_radio'][0]; // каталог шаблона
		}
		else
		{
			$dir = $get;
		}
		
		// если он есть - проверяем, то пишем куку и редиректимся
		if (file_exists( getinfo('templates_dir') . $dir . '/index.php' )) // есть
		{	
			$opt = mso_get_option('theme_switch', 'plugins', array());
			if ( isset($opt['templates'][$dir]) ) 
			{ 
				// 30 дней = 2592000 секунд 60 * 60 * 24 * 30
				mso_add_to_cookie('theme_switch', $dir, time() + 2592000, true);
			}
		}
	}
	
	// проверяем существование куки theme_switch
	if (isset($_COOKIE['theme_switch'])) 
	{
		$dir = $_COOKIE['theme_switch']; // значение текущего кука
		
		if (file_exists( getinfo('templates_dir') . $dir . '/index.php' )) 
		{
			$opt = mso_get_option('theme_switch', 'plugins', array());
			if ( isset($opt['templates'][$dir]) ) 
			{
				$MSO->config['template'] = $dir;
				
				$functions_file = $MSO->config['templates_dir'] . $dir . '/functions.php';
				if (file_exists($functions_file)) require_once($functions_file);
			}
			else @setcookie('theme_switch', '', time()); // сбросили куку
		}
		else @setcookie('theme_switch', '', time()); // сбросили куку
	}
	
	return $args;
}

# функция выполняется при деинсталяции плагина
function theme_switch_uninstall($args = array())
{	
	mso_delete_option_mask('theme_switch_widget_', 'plugins' ); // удалим созданные опции
	mso_delete_option('theme_switch', 'plugins' ); // удалим созданные опции
	mso_remove_allow('theme_switch_edit'); // удалим созданные разрешения
	
	return $args;
}

# функция выполняется при указаном хуке admin_init
function theme_switch_admin_init($args = array()) 
{
	if ( mso_check_allow('theme_switch_edit') ) 
	{
		$this_plugin_url = 'theme_switch'; // url и hook
		
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		
		mso_admin_menu_add('plugins', $this_plugin_url, t('Theme switch'));

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/theme_switch
		mso_admin_url_hook ($this_plugin_url, 'theme_switch_admin_page');
	}
	
	
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function theme_switch_admin_page($args = array()) 
{
	
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('theme_switch_edit') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Theme switch') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Theme switch') . ' - " . $args; ' );
	require(getinfo('plugins_dir') . 'theme_switch/admin.php');
}


# функция, которая берет настройки из опций виджетов
function theme_switch_widget($num = 1) 
{
	$widget = 'theme_switch_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
	else $options['header'] = '';
	
	return theme_switch_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function theme_switch_widget_form($num = 1) 
{
	$widget = 'theme_switch_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['submit']) ) $options['submit'] = t('Переключить');;
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');

	$form .= mso_widget_create_form(t('Надпись на кнопке'), form_input( array( 'name'=>$widget . 'submit', 'value'=>$options['submit'] ) ), '');
	
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function theme_switch_widget_update($num = 1) 
{
	$widget = 'theme_switch_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['submit'] = mso_widget_get_post($widget . 'submit');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функция виджета
function theme_switch_widget_custom($options = array(), $num = 1)
{
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['submit']) ) $options['submit'] = t('Переключить');
	
	// выводим списком шаблоны, которые отмечены и сохранены в опции theme_switch (через admin.php)
	$opt = mso_get_option('theme_switch', 'plugins', array());
	if ( !isset($opt['templates']) ) $opt['templates'] = array(); 
	
	$current_template = getinfo('template');
	
	$out = '';
	foreach($opt['templates'] as $key=>$val)
	{
		if ( $key == $current_template ) $checked = 'checked="checked"';
			else $checked = '';
					
		$out .= '<label><input type="radio" name="theme_switch_radio[]" value="' . $key . '" id="theme_switch_radio_' . $key . '" ' 
				. $checked . '> ' . $val . '</label><br>' . NR;
	}
	
	if ($out) 
		$out = '<div class="mso-theme_switch">' 
			. $options['header'] 
			. '<form method="post">' 
			. mso_form_session('f_session_id') . $out 
			. '<div class="mar10-t"><button type="submit" name="f_theme_switch_submit" class="button">' . $options['submit'] . '</button></div></form></div>';
	
	return $out;	
}


function theme_switch_body_start($args = '')
{
	$opt = mso_get_option('theme_switch', 'plugins', array());
	if ( !isset($opt['show_panel']) or !$opt['show_panel']) return $args; // не отмечена панель
	
	if ( isset($opt['only_home']) and $opt['only_home']) 
	{
		if (!is_type('home')) return $args; // только на главной
	}
	
	if ( !isset($opt['templates']) ) return $args; // нет выбранных шаблонов
	if ( !isset($opt['show_panel_type']) ) $opt['show_panel_type'] = 'screenshot';
		
	$current_template = getinfo('template');
	
	if ($opt['show_panel_type'] == 'screenshot')
	{
		// скриншотами
		
		$imgs = '';
		
		// извраты со счетчиками, чтобы сделать красивый скролинг к выбранному элементу
		$i = 1;
		$i_cur = 1;
		
		
		foreach($opt['templates'] as $key => $val)
		{
			if ($key == $current_template) 
			{
				$class = 'current';
				$i_cur = $i;
			}
			else 
				$class = '';
			
			$class = trim($class . ' img' . $i);
			
			if (file_exists(getinfo('templates_dir') . $key . '/screenshot.png' )) 
				$fn = 'screenshot.png';
			else 
				$fn = 'screenshot.jpg';
			
			
			$imgs  .= '<a href="' . getinfo('siteurl') . '?theme=' . $key . '" title="' . $val 
				. '" class="' . $class . '"><img src=' . getinfo('templates_url') . $key . '/' . $fn . '></a>';
			
			$i++;
		}
		
		// куда скролируем = на 4 картинки назад
		$i_go = $i_cur - 4;
		if ($i_go < 1) $i_go = 1;
		
		$info_template = '';
		$fn_info = getinfo('templates_dir') . $current_template . '/info.php';
		
		if (file_exists($fn_info)) 
		{
			require($fn_info);
			$info_template .= $info['name'] . ' ' . $info['version'];
		}
		
		$out = mso_load_jquery('jquery.scrollto.js') . '
		
		<div class="theme-switch-panel theme-switch-panel-screenshot"><div class="wrap">'
			. $imgs
		. '</div></div><!-- div class=theme-switch-panel -->
		
		<script>
			$("div.theme-switch-panel").scrollTo("a.current img", 500);
			$("div.theme-switch-panel").scrollTo("a.img' . $i_go . ' img", 800);
		</script>
		
		';
		
		echo $out;
	}
	else
	{
		// выпадающим списком
		$elements = '';
		
		foreach($opt['templates'] as $key => $val)
		{
			$selected = ($key == $current_template) ? ' selected' : '';
			$elements  .= '<option value="' . $key .'"' . $selected .'>' . $val . ' (' . $key . ')</option>';
		}
		
		echo '<div class="theme-switch-panel theme-switch-panel-combo"><div class="wrap">
		<select id="theme_switch_panel_combo_select">' . $elements . '</select>
		</div></div><!-- div class=theme-switch-panel -->';
		
		// ' . getinfo('siteurl') . '?theme=' . $key
		
		// getinfo('siteurl')
		$su = getinfo('siteurl');
		
		echo <<<EOF
<script>
jQuery(function($) {
	
	$('#theme_switch_panel_combo_select').change(function(){
		
		var f = $("#theme_switch_panel_combo_select :selected").val();
		
		if (f)
		{
			location.href = '$su' + '?theme=' + f;
		}
	})
	
});
</script>

EOF;
		
		
		
	}
	
	

	return $args;
}

# функция выводит файл theme_switch.txt указанного шаблона (каталог)
# Пример использования в тексте записи:
# [php] if (function_exists('theme_switch_info_file')) theme_switch_info_file(getinfo('template')); [/php]
function theme_switch_info_file($dir)
{
	$fn = getinfo('templates_dir') . $dir . '/theme_switch.txt';
	if (file_exists($fn)) readfile($fn);
}


# end file