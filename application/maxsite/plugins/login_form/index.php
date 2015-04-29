<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 

# функция автоподключения плагина
function login_form_autoload()
{
	# регистрируем виджет
	mso_register_widget('login_form_widget', tf('Форма логина')); 
}

# функция выполняется при деинсталяции плагина
function login_form_uninstall($args = array())
{	
	mso_delete_option_mask('login_form_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function login_form_widget($num = 1) 
{
	$out = '';
	
	$widget = 'login_form_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
		
	if (is_login())
	{
		$out = '<p><strong>' . tf('Привет,') . ' ' . getinfo('users_nik') . '!</strong><br>
				[<a href="' . getinfo('siteurl') . 'admin">' . tf('управление') . '</a>]
				[<a href="' . getinfo('siteurl') . 'logout'.'">' . tf('выйти') . '</a>] 
				</p>';	
	}
	elseif ($comuser = is_login_comuser())
	{
		if (!$comuser['comusers_nik']) $cun = tf('Привет!');
			else $cun = tf('Привет,') . ' ' . $comuser['comusers_nik'] . '!';
			
		$out = '<p><strong>' . $cun . '</strong><br>
				[<a href="' . getinfo('siteurl') . 'users/' . $comuser['comusers_id'] . '">' . tf('своя страница') . '</a>]
				[<a href="' . getinfo('siteurl') . 'logout'.'">' . tf('выйти') . '</a>] 
				</p>';
	}
	else
	{
		$after_form = (isset($options['after_form'])) ? $options['after_form'] : '';
		
		if (isset($options['registration']) and $options['registration'])
		{
			$registration = '<span class="registration"><a href="' . getinfo('siteurl') . 'registration">' . tf('Регистрация') . '</a></span>';
		}
		else $registration = '';
		
		
		$out = mso_login_form(array( 
			'login' => tf('Логин (email):') . ' ', 
			'password' => tf('Пароль:') . ' ', 
			'submit' => '', 
			'form_end' => $after_form,
			'submit_end' => $registration
			), 
			getinfo('siteurl') . mso_current_url(), false);
	}
	
	if ($out)
	{
		if ( isset($options['header']) and $options['header'] ) $out = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>') . $out;
	}
	
	return $out;
}


# форма настройки виджета 
# имя функции = виджет_form
function login_form_widget_form($num = 1) 
{
	$widget = 'login_form_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['after_form']) ) $options['after_form'] = '';
	if ( !isset($options['registration']) ) $options['registration'] = '0';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(tf('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'])), tf('Укажите заголовок виджета'));
	
	$form .= mso_widget_create_form(tf('Регистрация'), form_dropdown( $widget . 'registration', 
			array( 
				'0' => tf('Не показывать ссылку'), 
				'1' => tf('Показывать ссылку'), 
				), 
				$options['registration']), tf('Ссылка будет отображена рядом с кнопкой входа'));
	
	$form .= mso_widget_create_form(tf('Текст после формы'), form_input( array( 'name'=>$widget . 'after_form', 'value'=>$options['after_form'])), tf('Можно использовать HTML'));

	
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function login_form_widget_update($num = 1) 
{
	$widget = 'login_form_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['after_form'] = mso_widget_get_post($widget . 'after_form');
	$newoptions['registration'] = mso_widget_get_post($widget . 'registration');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# End of file
