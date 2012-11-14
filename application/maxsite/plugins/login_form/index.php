<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 

# функция автоподключения плагина
function login_form_autoload($args = array())
{
	# регистрируем виджет
	mso_register_widget('login_form_widget', t('Форма логина')); 
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
		$out = '<p><strong>' . t('Привет,') . ' ' . getinfo('users_nik') . '!</strong><br>
				[<a href="' . getinfo('siteurl') . 'admin">' . t('управление') . '</a>]
				[<a href="' . getinfo('siteurl') . 'logout'.'">' . t('выйти') . '</a>] 
				</p>';	
	}
	elseif ($comuser = is_login_comuser())
	{
		if (!$comuser['comusers_nik']) $cun = t('Привет!');
			else $cun = t('Привет,') . ' ' . $comuser['comusers_nik'] . '!';
			
		$out = '<p><strong>' . $cun . '</strong><br>
				[<a href="' . getinfo('siteurl') . 'users/' . $comuser['comusers_id'] . '">' . t('своя страница') . '</a>]
				[<a href="' . getinfo('siteurl') . 'logout'.'">' . t('выйти') . '</a>] 
				</p>';
	}
	else
	{
		$after_form = (isset($options['after_form'])) ? $options['after_form'] : '';
		
		if (isset($options['registration']) and $options['registration'])
		{
			$registration = '</span><span class="text-right registration"><a href="' . getinfo('siteurl') . 'registration">' . tf('Регистрация') . '</a>';
		}
		else $registration = '';
		
		
		$out = mso_login_form(array( 
			'login' => t('Логин (email):') . ' ', 
			'password' => t('Пароль:') . ' ', 
			'submit' => '', 
			'form_end' => $after_form,
			'submit_end' => $registration
			), 
			getinfo('siteurl') . mso_current_url(), false);
	}
	
	if ($out)
	{
		if ( isset($options['header']) and $options['header'] ) $out = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>') . $out;
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
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'])), t('Укажите заголовок виджета'));
	
	$form .= mso_widget_create_form(t('Регистрация'), form_dropdown( $widget . 'registration', 
			array( 
				'0' => t('Не показывать ссылку'), 
				'1' => t('Показывать ссылку'), 
				), 
				$options['registration']), t('Ссылка будет отображена рядом с кнопкой входа'));
	
	$form .= mso_widget_create_form(t('Текст после формы'), form_input( array( 'name'=>$widget . 'after_form', 'value'=>$options['after_form'])), t('Можно использовать HTML'));

	
	
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
