<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS (c) http://max-3000.com/
 * Плагин, реализующий добавленный хук с почтой.
 */


# функция автоподключения плагина
function smtp_mail_autoload()
{
	mso_hook_add( 'mail', 'smtp_mail_custom');
}

# функция выполняется при активации (вкл) плагина
function smtp_mail_activate($args = array())
{
	mso_create_allow('smtp_mail_edit', t('Админ-доступ к настройкам') . ' smtp_mail');
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function smtp_mail_deactivate($args = array())
{
	// mso_delete_option('plugin_smtp_mail', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function smtp_mail_uninstall($args = array())
{
	 mso_delete_option('plugin_smtp_mail', 'plugins' ); // удалим созданные опции
	// mso_remove_allow('smtp_mail_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function smtp_mail_mso_options() 
{
	if ( !mso_check_allow('smtp_mail_edit') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}

	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_smtp_mail', 'plugins',
		array(
			'admin_email' => array(
							'type' => 'text',
							'name' => t('E-mail, с которого отправляем почту'),
							'description' => t('Зачастую, со стороннего SMTP сервера можно отправить почту только если адрес принадлежит именно этому серверу.<br>Если пусто — используется тот, что указан в настройках сайта.'),
							'default' => ''
						),
			'protocol' => array(
							'type' => 'select',
							'name' => t('Протокол отправки'),
							'description' => t('Для «smtp» укажите ниже SMTP хост, пользователя и пароль. Для «sendmail» укажите серверный путь к Sendmail.<br>Для «mail» планируются расширенные функции по сравнению со штатной возможностью системы.'),
							'values' => 'smtp # sendmail # mail',
							'default' => 'smtp'
						),
			'mailpath' => array(
							'type' => 'text',
							'name' => t('Серверный путь к Sendmail.'),
							'description' => t('Обычно это «/usr/sbin/sendmail»'),
							'default' => '/usr/sbin/sendmail'
						),
			'smtp_host' => array(
							'type' => 'text',
							'name' => t('SMTP host'),
							'description' => '<b>Gmail:</b><br>ssl://smtp.googlemail.com',//t(''),
							'default' => 'ssl://smtp.googlemail.com'
						),
			'smtp_user' => array(
							'type' => 'text',
							'name' => t('SMTP user'),
							'description' => '<b>Gmail:</b><br>gmail.login@googlemail.com',//t(''),
							'default' => ''
						),
			'smtp_pass' => array(
							'type' => 'text',
							'name' => t('SMTP pass'),
							'description' => t('<b style="color: red;">Примечание:</b> пароль в базе данных хранится в открытом виде.'),
							'default' => ''
						),
			'smtp_port' => array(
							'type' => 'text',
							'name' => t('SMTP port'),
							'description' => t('Может быть, например, 25, 2525 или 587.') . '<br><b>Gmail:</b><br>465',
							'default' => '25'
						),
			'to_uploads' => array(
							'type' => 'checkbox',
							'name' => t('Складывать ли письма в <b>uploads</b>'),
							'description' => t('Письма можно не только отправлять на почту, но и сохранять в каталог <b>uploads</b>, где их можно посмотреть даже если они не дошли на e-mail.'),
							'default' => '0'
						),
			'to_email' => array(
							'type' => 'checkbox',
							'name' => t('Отправлять письма на e-mail'),
							'description' => t('Если письма сохраняются в каталог <b>uploads</b> или просто нужно отключить отправку на e-mail, снимите галочку здесь.'),
							'default' => '1'
						),
			'uploads_subfolder' => array(
							'type' => 'text',
							'name' => t('Каталог в <b>uploads</b>, куда складывать почту'),
							'description' => t('Каталог вы можете создать в разделе «Загрузки». Это может быть, например, <b>mail</b>.<br>Оставьте пустым, если хотите складывать письма в <b>uploads</b>.'),
							'default' => ''
						),
			),
		t('Настройки плагина «SMTP mail»'),
		t('Укажите необходимые опции.')
	);
}

# функции плагина
function smtp_mail_custom($arg = array())
{

	$CI = & get_instance();

	$options = mso_get_option('plugin_smtp_mail', 'plugins', array() );

	if (!isset($options['to_email'])) $options['to_email'] = 1;
	$sent = '!not-sent-';
	$res = false;
	if ($options['to_email'] == 1)
	{
		$CI->load->library('email');

		if ( (!isset($options['admin_email'])) or (trim($options['admin_email']) == '') )
		{
			if ($arg['from']) $admin_email = $arg['from'];
			else $admin_email = mso_get_option('admin_email_server', 'general', 'admin@site.com');
		} else
		{
			$admin_email = trim($options['admin_email']);
		}

		$config['protocol']  = ( isset($options['protocol']) )  ? ( $options['protocol'] )  : ( 'mail' );
		$config['smtp_host'] = ( isset($options['smtp_host']) ) ? ( $options['smtp_host'] ) : ( '' );
		$config['smtp_user'] = ( isset($options['smtp_user']) ) ? ( $options['smtp_user'] ) : ( '' );
		$config['smtp_pass'] = ( isset($options['smtp_pass']) ) ? ( $options['smtp_pass'] ) : ( '' );
		$config['smtp_port'] = ( isset($options['smtp_port']) ) ? ( $options['smtp_port'] ) : ( '25' );
		$config['mailpath']  = ( isset($options['mailpath']) )  ? ( $options['mailpath'] )  : ( '/usr/sbin/sendmail' );

		if ( (isset($arg['preferences']['attach'])) and (trim($arg['preferences']['attach'])) != '' )
		{
			$CI->email->attach($arg['preferences']['attach']);
		}

		if ( ($config['protocol'] == 'smtp') and ( strpos($config['smtp_host'], 'ssl') !== false ) ) $config['newline']="\r\n";
		$config['wordwrap'] = TRUE;
		$config['wrapchars'] = 90;

		$CI->email->initialize($config);

		$CI->email->to($arg['email']);
		$CI->email->from($admin_email, getinfo('name_site'));
		$CI->email->subject($arg['subject']);
		$CI->email->message($arg['message']);
		$CI->email->_safe_mode = true; # иначе CodeIgniter добавляет -f к mail - не будет работать в не safePHP

		$res = $CI->email->send();
		$debug = '';
		if (!$res)
		{
			$debug = '<div style="border: silver solid 1px; padding: 20px; margin: 20px;">' . $CI->email->print_debugger() . '<div>';
			if (isset($arg['preferences']['print_debugger']) and $arg['preferences']['print_debugger'])
			{
				echo $debug;
			}
			$sent = '!error-not-sent-';
		} else
		{
			$sent = '';
		}
		$CI->email->clear(TRUE);
	}

	if ( isset($options['to_uploads']) and ($options['to_uploads'] == 1) )
	{
		$to_save = getinfo('uploads_dir') . ( (isset($options['uploads_subfolder']))?(trim($options['uploads_subfolder']).'/'):('') ) . $sent . strftime("%Y-%m-%d--%H-%M-%S", time()) . '.html';
		$to_save = str_replace('//', '/', $to_save);
		$text = '
				<html>
					<head>
						<title>'. $arg['subject']. '</title>
					</head>
					<body>
						<pre>'.
							(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'') . NR .
							strftime("%Y-%m-%d %H:%M:%S", time()) . NR .
							'<b>' . $arg['subject'] . '</b>' . NR . NR . $arg['message']. NR . NR .
						'</pre>
						' . $debug . '
					</body>
				</html>
				';
		$CI->load->helper('file');
		write_file($to_save, $text);
	}

	return $res;
}

?>