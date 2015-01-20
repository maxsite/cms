<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
	
	global $MSO;
	
	$CI = & get_instance();	

	$step = $MSO->data['step'];
	
	$username = '';
	
	$CI->load->helper('string_helper');

	$userpassword = random_string('alnum', 12); // генератор пароля
	
	$useremail = '';
	$namesite = '';
	$demoposts = 0;
	$error = false;

    if ( ($step == 1) and $_POST )
    {
        mso_checkreferer(); // проверка на чужой реферер

        if (isset($_POST['mysubmit']))
        {
            $subdir = isset ($_POST['subdir']) ? mso_strip($_POST['subdir'], true) : false;
            $autoredirect = isset ($_POST['autoredirect']) ? true : false;

            require_once (APPPATH . 'views/install/install-common.php');

            $res = mso_add_htaccess( array(
                'subdir' => $subdir,
                'autoredirect' => $autoredirect
            ));

            if ($res) $step = 2;
        }
    }

    if ( ($step == 3) and $_POST )
    {
        mso_checkreferer(); // проверка на чужой реферер

        if (isset($_POST['mysubmit']))
        {
            $hostname = isset ($_POST['hostname']) ? mso_strip($_POST['hostname'], true) : false;
            $username = isset ($_POST['username']) ? mso_strip($_POST['username'], true) : false;
            $password = $_POST['password'] ? mso_strip($_POST['password'], true) : false;
            $database = isset ($_POST['database']) ? mso_strip($_POST['database'], true) : false;
            $secret_name = isset ($_POST['secretname']) ? mso_strip($_POST['secretname'], true) : false;

            if ( !$hostname or !$username or !$database )
            {
                $step = 2;
                $error = '<h2 class="error">' . t('Ошибочные или неполные данные!', 'install') . '</h2>';
            }

            if ( $step === 3 )
            {
                require_once (APPPATH . 'views/install/install-common.php');

                $res = mso_add_db_setting( array('username'=>$username,
                    'password'=>$password,
                    'hostname'=>$hostname,
                    'database'=>$database
                ) );

                $res = mso_add_secret_key( $secret_name );
                header( 'Location: ' . getinfo('site_url') . 'install/3' ) ;
            }
        }
    }
	
	if ( ($step == 4) and $_POST )
	{
		mso_checkreferer(); // проверка на чужой реферер
		
		if (isset($_POST['mysubmit'])) 
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
				$step = 3;
				$error = '<h2 class="error">' . t('Ошибочные или неполные данные!', 'install') . '</h2>';
			}
			
			if ( $step === 4 )
			{
				require_once (APPPATH . 'views/install/install-common.php');

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
			$step = 3;
	}
	
	mso_nocache_headers();
	
?><!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title>Install MaxSite CMS</title>
	<meta name="generator" content="MaxSite CMS">
	<link rel="stylesheet" href="<?=$MSO->data['url_css']?>" type="text/css" media="screen">
</head>
<body>
<div id="container">
<?php
	
	if (($CI->db->conn_id === FALSE) and ($step > 2)) // нет коннекта к БД
	{
	    $step = 2;
	}

    if (file_exists(FCPATH . '/.htaccess') and ($step == 1))
    {
        $step = 2;
    }

    if (($step == 3) and (mso_current_url() != 'install/3'))
    {
        echo '<p class="error">' . t('Ошибка! Неверно настроены ЧПУ!', 'install') . '</p>';

        echo '<p>' . t('Данная ошибка означает, что у вас неверно настроен файл <strong>.htaccess</strong>. Прочтите', 'install') . '<a href="' .  getinfo('site_url') . '_mso_add/' . t('install-ru.txt', 'install') . '">' . t('инструкцию', 'install') . '</a>' . t('по установке.', 'install') . '</p>';

        echo '<p>' . t('После изменений вы можете', 'install') . ' <a href="' . getinfo('site_url') . 'install/2">' . t('попробовать снова', 'install') . '</a>.</p>';

        echo '<hr><p>' . t('Техническая информация о вашем сервере.', 'install') . '</p>';
        echo '<ul>';
        echo '<li><strong>SERVER_SOFTWARE:</strong> ' . $_SERVER['SERVER_SOFTWARE'] . '</li>';
        echo '<li><strong>REQUEST_METHOD:</strong> ' . $_SERVER['REQUEST_METHOD'] . '</li>';
        echo '</ul>';
    }

    if (!file_exists(FCPATH . '/robots.txt'))
    {
        require_once (APPPATH . 'views/install/install-common.php');

        $res = mso_add_robots();
    }

    if (!file_exists(FCPATH . '/sitemap.xml'))
    {
        require_once (APPPATH . 'views/install/install-common.php');

        $res = mso_add_sitemap();
    }

    if ($step == 1) // первый шаг
    {
        echo '<h1>' . t('Добро пожаловать в программу установки <a href="http://max-3000.com/">MaxSite CMS</a>!', 'install') . '</h1>';

        echo '<div class="wrap">';

        if (mso_current_url() == 'install/1' or mso_current_url() == '')
        {
            echo '<p align="center"><a target="_blank" href="' . getinfo('site_url') . '_mso_add/' . t('install-ru.txt', 'install') . '">' . t('Инструкция по установке', 'install') . '</a></p>';
            echo '<p>' . t('На первом шаге будет произведена настройка .htaccess', 'install') . '</p>';

            $this->load->helper('form');

            echo form_open('', array('class' => 'myform', 'id' => 'myform'));

            echo '<p class="f-name"><label><span>' . t('Сайт в подкаталоге', 'install') . ':</span>'
                . form_input( array( 'name'=>'subdir',
                    'id'=>'subdir',
                    'maxlength'=>'100',
                    'size'=>'50',
                    'style'=>'float: left;'))
                . '</label></p><p class="f-desc">' . t('Если вы располагаете сайт НЕ в корне домена, а в его подкаталоге, например «http://www.your-site.com/blog/». Иначе оставьте поле пустым.', 'install') . '</p>';

            echo '<p class="f-name"><label><span>' . t('Авто-редирект', 'install') . ':</span>'
                . form_checkbox( array( 'name'=>'autoredirect',
                    'id'=>'autoredirect',
                    'maxlength'=>'100',
                    'size'=>'50',
                    'style'=>'float: left;'))
                . '</label></p><p class="f-desc">' . t('Автоматический редирект с www.site.com на site.com', 'install') . '</p>';

            echo '<p class="mysubmit-ok"><button type="submit" name="mysubmit" id="mysubmit">' . t('Сохранить настройки htaccess', 'install') . '</button></p>';

            echo form_close();
        }

        echo '</div>';
    }

    if ( $step == 2 )
    {
        echo '<h1>' . t('Добро пожаловать в программу установки <a href="http://max-3000.com/">MaxSite CMS</a>!', 'install') . '</h1>';

        echo '<div class="wrap">';

        echo $error;

        $this->load->helper('form');

        echo form_open('install/3', array('class' => 'myform', 'id' => 'myform'));

        echo '<p>' . t('Параметры подключения базы данных MySQL.', 'install') . '</p>';

        echo '<p class="f-name"><label><span>' . t('Имя сервера', 'install') . ':</span>'
            . form_input( array( 'name'=>'hostname',
                'id'=>'hostname',
                'value'=>'localhost',
                'maxlength'=>'100',
                'size'=>'50',
                'style'=>'float: left;'))
            . '</label></p><p class="f-desc">' . t('Имя узла и порт сервера базы данных', 'install') .  '</p>';

        echo '<p class="f-name"><label><span>' . t('Имя пользователя', 'install') . ':</span>'
            . form_input( array( 'name'=>'username',
                'id'=>'username',
                'maxlength'=>'100',
                'size'=>'50',
                'style'=>'float: left;'))
            . '</label></p><p class="f-desc">' . t('Имя пользователя, для доступа к базе данных', 'install') .  '</p>';

        echo '<p class="f-name"><label><span>' . t('Пароль', 'install') . ':</span>'
            . form_input( array( 'name'=>'password',
                'id'=>'password',
                'maxlength'=>'100',
                'size'=>'50',
                'style'=>'float: left;'))
            . '</label></p><p class="f-desc">' . t('Пароль пользователя для доступа к базе данных', 'install') .  '</p>';

        echo '<p class="f-name"><label><span>' . t('Имя базы данных', 'install') . ':</span>'
            . form_input(array( 'name'=>'database',
                'id'=>'database',
                'maxlength'=>'100',
                'size'=>'50',
                'style'=>'float: left;'))
            . '</label></p><p class="f-desc">' . t('Введите имя базы данных для данного сайта', 'install') .  '</p>';

        echo '<p>' . t('Введите свою секретную фразу. Она используется при шифровании. Учтите, что сменив эту фразу после инсталяции все пароли окажутся недействительными.', 'install') . '</p>';

        echo '<p class="f-name"><label><span>' . t('Секретная фраза', 'install') . ':</span>'
            . form_input(array( 'name'=>'secretname',
                'id'=>'secretname',
                'maxlength'=>'100',
                'size'=>'50',
                'style'=>'float: left;'))
            . '</label></p><p class="f-desc">' . t('Введите свою секретную фразу. Она используется при шифровании.', 'install') .  '</p>';

        echo '<p class="mysubmit-ok"><button type="submit" name="mysubmit" id="mysubmit">' . t('Сохранить начальные данные', 'install') . '</button></p>';

        echo form_close();

        echo '</div>';
    }

    if ( $step == 3 ) // третий шаг настройки
    {
        echo '<h1>'. t('Установка <a href="http://max-3000.com/">MaxSite CMS</a>', 'install') . '</h1>';

        echo '<div class="wrap">';

        echo $error;

        $this->load->helper('form');

        echo form_open('install/4', array('class' => 'myform', 'id' => 'myform'));

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

        if ($show_button) echo '<p class="mysubmit-ok"><button type="submit" name="mysubmit" id="mysubmit">' . t('Установить MaxSite CMS', 'install') . '</button></p>';
        else
            echo '<p class="f5">' . t('Исправьте замечания и обновите эту страницу в браузере (F5)', 'install') . '</p>';

        echo form_close();

        echo '</div>';

    } // конец третьего шага


    // четвертый шаг
    if ($step == 4)
    {

        $text = t('Ваш новый сайт создан: ', 'install') . getinfo('siteurl') . NR;
        $text .= t('Для входа воспользуйтесь данными:', 'install') . NR;
        $text .= t('Логин: ', 'install') . $username . NR;
        $text .= t('Пароль: ', 'install') . $userpassword . NR . NR . NR;
        $text .= t('Сайт поддержки: http://max-3000.com/', 'install');

        mso_flush_cache(); // сбросим кэш

        if (isset($res))
        {
            echo '<h1>' . t('Поздравляем! Всё готово!', 'install') . '</h1>';

            echo '<h2 class="res">' . t('Ваша информация', 'install') . '</h2>';
            echo $res;
            echo '<p class="res"><a href="' . getinfo('siteurl') . '">' . t('Переход к сайту', 'install') . '</a></p>';

            require_once (APPPATH . 'views/install/install-common.php');
            $res = mso_install_success();

            // поскольку это инсталяция, то отправитель - тот же email
            @mso_mail($useremail, t('Новый сайт на MaxSite CMS', 'install'), $text, $useremail);
        }
        else // if (isset($res))
        {
            echo '<h2 class="error">' . t('Ошибка установки', 'install') . '</h2>
             <p style="text-align: center;"><a href="' . getinfo('siteurl') . '">' . t('Вернитесь в начало', 'install') . '</a></p>';
        } // if (isset($res))


    } // конец четвертого шага
?>
	
</div><!-- div id="container" -->
</body>
</html>