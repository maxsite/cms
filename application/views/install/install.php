<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
	
	global $MSO;
	
	$CI = & get_instance();	

	$step = $MSO->data['step'];
	
	// pr($step);
	// pr($_SERVER);
	//pr(mso_current_url());
	//pr($MSO);
	
	$username = '';
	
	$CI->load->helper('string_helper');
	
	$userpassword = random_string('alnum', 12); // генератор пароля
	
	$useremail = '';
	$namesite = '';
	$demoposts = 0;
	$error = false;
	
	if ( ($step == 3) and $_POST ) 
	{
		mso_checkreferer(); // проверка на чужой реферер
		
		if ($_POST['mysubmit']) 
		{
			$username = isset ($_POST['username']) ? mso_strip($_POST['username'], true) : false;
			$userpassword = isset ($_POST['userpassword']) ? mso_strip($_POST['userpassword'], true) : false;
			$useremail = $_POST['useremail'] ? mso_strip($_POST['useremail'], true) : false;
			$namesite = isset ($_POST['namesite']) ? mso_strip($_POST['namesite'], true) : false;
			$demoposts = isset ($_POST['demoposts']) ? (int) mso_strip($_POST['demoposts'], true) : 0;
			
			if ( !mso_valid_email($useremail) ) $useremail = false; 
			if ( strlen($userpassword) < 6) $userpassword = false; 
			
			if ( !$useremail or !$username or !$userpassword or !$namesite ) 
			{
				$step = 2;
				$error = '<h2 class="error">' . t('Ошибочные или неполные данные!', 'install') . '</h2>';
			}
			
			if ( $step === 3 ) 
			{
				require_once (APPPATH . 'views/install/install-common.php');
				
				//require_once ('install-common.php');
				
				$res = mso_install_newsite( array('username'=>$username, 
										   'userpassword'=>mso_md5($userpassword), 
										   'userpassword_orig'=>$userpassword, 
										   'useremail'=>$useremail,
										   'namesite'=>$namesite,
										   'demoposts'=>$demoposts,
										   'ip'=>$_SERVER['REMOTE_ADDR']
										  ) );
				
			}
		}
		else
			$step == 2;
	}
	
	mso_nocache_headers();
	
?><!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Install MaxSite CMS</title>
	<meta name="generator" content="MaxSite CMS">
	<link rel="stylesheet" href="<?=$MSO->data['url_css']?>" type="text/css" media="screen">
</head>
<body>
<div id="container">



<?php 
	if ( $step == 1) // первый шаг
	{ 
		echo '<h1>' . t('Добро пожаловать в программу установки <a href="http://max-3000.com/">MaxSite CMS</a>', 'install') . '</h1>';
		
		if (mso_current_url() == 'install/1' or mso_current_url() == '')
		{
			echo '<p>' . t('На первом шаге программа проверит верно ли у вас настроены ЧПУ («человекопонятный урл» - веб-адреса, удобные для восприятия человеком).', 'install') . '</p>';
			echo '<p>' . t('При отстутствии ошибок вам будет предложено указать начальные данные.', 'install') . '</p>';
			
			echo '<p><a href="' . getinfo('site_url') . 'install/2">' . t('Перейти к установке', 'install') . '</a>';
			echo ' | <a target="_blank" href="' . getinfo('site_url') . t('install-ru.txt', 'install') . '">' . t('Инструкция по установке', 'install') . '</a></p>';
		}
		else
		{
			echo '<p class="error">' . t('Ошибка! Неверно настроены ЧПУ!', 'install') . '</p>';
			
			echo '<p>' . t('Данная ошибка означает, что у вас неверно настроен файл <strong>.htaccess</strong>. Прочтите', 'install') . '<a href="' .  getinfo('site_url') . t('install-ru.txt', 'install') . '">' . t('инструкцию', 'install') . '</a>' . t('по установке.', 'install') . '</p>';
			
			echo '<p>' . t('После изменений вы можете', 'install') . ' <a href="' . getinfo('site_url') . 'install/2">' . t('попробовать снова', 'install') . '</a>.</p>';
			
			echo '<hr><p>' . t('Техническая информация о вашем сервере.', 'install') . '</p>';
			echo '<ul>';
			echo '<li><strong>SERVER_SOFTWARE:</strong> ' . $_SERVER['SERVER_SOFTWARE'] . '</li>';
			echo '<li><strong>REQUEST_METHOD:</strong> ' . $_SERVER['REQUEST_METHOD'] . '</li>';
			echo '</ul>';
		}
	}

	if ( $step == 2 ) // второй шаг настройки
	{  ?>
	
	<h1><?= t('Добро пожаловать в программу установки <a href="http://max-3000.com/">MaxSite CMS</a>', 'install') ?></h1>
	<?= $error ?>
	<?php 
		
		$this->load->helper('form');

		echo form_open('install/3', array('class' => 'myform', 'id' => 'myform'));
		
		echo '<p class="f-name"><label><span>' . t('Логин админа', 'install') . ':</span>' 
			. form_input( array( 'name'=>'username', 
								'id'=>'username', 
								'value'=>$username,
								'maxlength'=>'100',
								'size'=>'50',
								'style'=>'float: left;'))
			. '</label></p><p class="f-desc">' . t('Английские буквы, цифры, без пробелов', 'install') .  '</p>';
	
		echo '<p class="f-name"><label><span>' . t('Пароль', 'install') . ':</span>' 
			. form_input( array( 'name'=>'userpassword', 
								'id'=>'userpassword', 
								'value'=>$userpassword,
								'maxlength'=>'100',
								'size'=>'50',
								'style'=>'float: left;'))
			. '</label></p><p class="f-desc">' . t('Английские буквы, цифры, без пробелов. Минимум 6 символов', 'install') .  '</p>';					
	
		echo '<p class="f-name"><label><span>' . t('E-mail', 'install') . ':</span>' 
			. form_input( array( 'name'=>'useremail', 
								'id'=>'useremail', 
								'value'=>$useremail,
								'maxlength'=>'100',
								'size'=>'50',
								'style'=>'float: left;'))
			. '</label></p><p class="f-desc">' . t('На него отправится сообщение с паролем', 'install') .  '</p>';						
						
		echo '<p class="f-name"><label><span>' . t('Название сайта', 'install') . ':</span>' 
			. form_input(array( 'name'=>'namesite', 
								'id'=>'namesite', 
								'value'=>$namesite,
								'maxlength'=>'100',
								'size'=>'50',
								'style'=>'float: left;'))
			. '</label></p><p class="f-desc">' . t('Укажите название своего сайта', 'install') .  '</p>';					


		echo '<p class="f-ch"><label>' 
			. form_checkbox('demoposts', '1', $demoposts) . ' '
			. t('Установить демонстрационные данные', 'install') 
			. '</label></p>';
		
		
		// echo '<br>';
		
		// сразу выполним проверку на все права файла 
		// 
		$show_button = true;
		echo '<div class="proverka">';
		
		
			if (version_compare(PHP_VERSION, '5.1.6', '<')) 
			{
				echo '<p class="error"><span>X</span> ' . t('Старая версия PHP ', 'install') . PHP_VERSION . '</p>';
				$show_button = false;
			}
			else
				echo '<p class="ok"><span>√</span> ' . t('Версия PHP ', 'install') . PHP_VERSION . ' - OK!</p>';

			
			if (file_exists( $MSO->config['base_dir'] . 'mso_config.php' )) 
			{
				echo '<p class="ok"><span>√</span> ' . t('Файл', 'install') . ' «application/maxsite/mso_config.php» - OK!' . '</p>';
				
				require_once ($MSO->config['base_dir'] . 'mso_config.php');
				
				if ($MSO->config['secret_key'])
					echo '<p class="ok"><span>√</span> ' . t('Секретная фраза', 'install') . ' - OK!</p>';
				else 
				{
					echo '<p class="error"><span>X</span> ' . t('Не указана секретная фраза в', 'install') . ' «application/maxsite/mso_config.php»!</p>';
					$show_button = false;
				}
			}
			else
			{
				echo '<p class="error"><span>X</span> ' . t('Файл', 'install') . ' «<em>' . $MSO->config['base_dir'] . 'mso_config.php' . '</em>» - ' . t('не найден!', 'install') . '</p>';
				$show_button = false;
			}	
			
			
			$cache_path = getinfo('cache_dir');
			if ( !is_dir($cache_path) or !is_writable($cache_path))
			{
				echo '<p class="error"><span>X</span> ' . t('Каталог', 'install') . ' «<em>' . $cache_path . '</em>» - ' . t('не найден или нет разрешения на запись (777)!', 'install') . '</p>';
				$show_button = false;
			}
			else
			{
				echo '<p class="ok"><span>√</span> ' . t('Каталог кэша', 'install') . ' - OK!</p>';
			}
			
			$path = getinfo('uploads_dir');
			if ( !is_dir($path) or !is_writable($path))
			{
				echo '<p class="error"><span>X</span> ' . t('Каталог', 'install') . ' «<em>' . $path . '</em>» - ' . t('не найден или нет разрешения на запись (777)!', 'install') . '</p>';
				$show_button = false;
			}
			else
			{
				echo '<p class="ok"><span>√</span> ' . t('Каталог', 'install') . ' «uploads» - OK!</p>';
			}
			// в uploads _mso_float
			if ( !is_dir($path . '_mso_float') or !is_writable($path . '_mso_float'))
			{
				echo '<p class="error"><span>X</span> ' . t('Каталог', 'install') . ' «<em>' . $path . '_mso_float' . '</em>» - ' . t('не найден или нет разрешения на запись (777)!', 'install') . '</p>';
				$show_button = false;
			}
			else
			{
				echo '<p class="ok"><span>√</span> ' . t('Каталог', 'install') . ' «uploads/_mso_float» - OK!</p>';
			}
			// в uploads _mso_i
			if ( !is_dir($path . '_mso_i') or !is_writable($path . '_mso_i'))
			{
				echo '<p class="error"><span>X</span> ' . t('Каталог', 'install') . ' «<em>' . $path . '_mso_i' . '</em>» - ' . t('не найден или нет разрешения на запись (777)!', 'install') . '</p>';
				$show_button = false;
			}
			else
			{
				echo '<p class="ok"><span>√</span> ' . t('Каталог', 'install') . ' «uploads/_mso_i» - OK!</p>';
			}			
			
			# CodeIgniter 1.7.1
			# $path = realpath(dirname(FCPATH)) . '/.htaccess';
			
			$path = FCPATH . '.htaccess';
			if (!file_exists($path))
			{
				echo '<p class="error">' . t('Файл', 'install') . ' «<em>' . $path . '</em>» - ' . t('не найден!', 'install') . '</p>';
				//$show_button = false;
			}
			else
			{
				echo '<p class="ok"><span>√</span> ' . t('Файл', 'install') . ' «.htaccess» - OK!</p>';
			}
			
			
			$path = FCPATH . 'sitemap.xml';
			if ( !file_exists($path) or !is_writable($path))
			{
				echo '<p class="error">' . t('Файл', 'install') . ' «<em>' . $path . '</em>» - ' . t('не найден или нет разрешения на запись!', 'install') . '</p>';
				$show_button = false;
			}
			else
			{
				# echo '<p class="ok">Файл «sitemap.xml» - OK!</p>';
			}
			
			if (!function_exists('mb_strlen'))
			{
				echo '<p class="error">' . t('PHP-библиотека <em>mbstring</em> не найдена на сервере! Она требуется для корректной работы сайта. Вы можете проигнорировать это сообщение учитывая, что в некоторых случаях это может приводить к неверному результату обработки строк.', 'install') . '</p>';
			}
			
			// if ($show_button) echo '<p class="ok">Проверка выполнена!</p>';
			
		echo '</div>';
		
		if ($show_button) echo '<p class="mysubmit-ok">' . form_submit('mysubmit', t('Установить MaxSite CMS', 'install'), 'id="mysubmit"') . '</p>';
			else echo '<p class="f5">' . t('Исправьте замечания и обновите эту страницу в браузере (F5)', 'install') . '</p>';
		
		echo form_close();

	} // конец первого шага
	
	
	// третий шаг
	if ($step == 3) 
	{
	
	$text = t('Ваш новый сайт создан: ', 'install') . getinfo('siteurl') . NR;
	$text .= t('Для входа воспользуйтесь данными:', 'install') . NR;
	$text .= t('Логин: ', 'install') . $username . NR;
	$text .= t('Пароль: ', 'install') . $userpassword . NR . NR . NR;
	$text .= t('Сайт поддержки: http://max-3000.com/', 'install');
	
	mso_flush_cache(); // сбросим кэш

	if (isset($res)) 
	{ 
?>
	
	<h1><?= t('Поздравляем! Всё готово!', 'install') ?></h1>
	<h2 class="res"><?= t('Ваша информация', 'install') ?></h2>
	<?= $res ?>
	<br><p class="res"><a href="<?= getinfo('siteurl') ?>"><?= t('Переход к сайту', 'install') ?></a></p>
	<p class="res"><?= t('Не забудьте открыть файл «application/maxsite/mso_config.php» и измените', 'install') ?> <em>$mso_install = true;</em></p>
	<?php 
		// поскольку это инсталяция, то отправитель - тот же email
		@mso_mail($useremail, t('Новый сайт на MaxSite CMS', 'install'), $text, $useremail); 
	
	}
	else { // if (isset($res))
		echo '<h2 class="error">' . t('Ошибка установки', 'install') . '</h2>
		 <p style="text-align: center;"><a href="' . getinfo('siteurl') . '">' . t('Вернитесь в начало', 'install') . '</a></p>';
	}; // if (isset($res))
	
	?>
<?php } // конец третьего шага ?>
</div><!-- div id="container" -->
</body>
</html>