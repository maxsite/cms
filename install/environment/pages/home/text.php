<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="layout-center">

h1 {{ t('Установка MaxSite CMS') }}

hr(dotted)


{% if ( version_compare(PHP_VERSION, '5.3' , '<') ) : %}
	_(t-red) {{ t('Слишком старая версия PHP: ') }} <var>{{ PHP_VERSION }}</var> {{ t('Требуется: ') }} 5.3
	</div>
	{% return %}
{% endif %}


{% if (!function_exists('mb_strlen')) : %}
	_(t-red) {{ t('PHP-библиотека <em>mbstring</em> не найдена на сервере. Она требуется для корректной работы сайта. Вы можете проигнорировать это сообщение учитывая, что в некоторых случаях это может приводить к неверному результату обработки строк.') }}
{% endif %}		


{% if (!v_file_wtitable('application/cache')) : %}
	_(t-red) {{ t('Каталог <code>application/cache</code> не найден или нет разрешения на запись') }}
{% endif %}


{% if (!v_file_wtitable('application/cache/db')) : %}
	_(t-red) {{ t('Каталог <code>application/cache/db</code> не найден или нет разрешения на запись') }}
{% endif %}


{% if (!v_file_wtitable('uploads')) : %}
	_(t-red) {{ t('Каталог <code>uploads</code> не найден или нет разрешения на запись') }}
{% endif %}


{% if (!v_file_wtitable('uploads/_mso_float')) : %}
	_(t-red) {{ t('Каталог <code>uploads/_mso_float</code> не найден или нет разрешения на запись') }}
{% endif %}


{% if (!v_file_wtitable('uploads/_mso_i')) : %}
	_(t-red) {{ t('Каталог <code>uploads/_mso_i</code> не найден или нет разрешения на запись') }}
{% endif %}


{% v_new_robots() %}

{% if (v_new_robots()) : %}
	_(t-green i-check-square-o) {{ t('Файл <code>robots.txt</code> создан') }}
{% else : %}
	_(t-red i-remove) {{ t('Файл <code>robots.txt</code> не создан') }}
{% endif %}


{% v_new_htaccess() %}

{% if (v_new_htaccess()) : %}
	_(t-green i-check-square-o) {{ t('Файл <code>.htaccess</code> создан') }}
{% else : %}
	_(t-red i-remove) {{ t('Файл <code>.htaccess</code> не создан') }}
{% endif %}


{% v_new_sitemap() %}

{% if (v_new_sitemap()) : %}
	_(t-green i-check-square-o) {{ t('Файл <code>sitemap.xml</code> создан') }}
{% else : %}
	_(t-red i-remove) {{ t('Файл <code>sitemap.xml</code> не создан') }}
{% endif %}


{% v_new_mso_config() %}

{% if (v_new_mso_config()) : %}
	_(t-green i-check-square-o) {{ t('Файл <code>application/maxsite/mso_config.php</code> создан') }}
{% else : %}
	_(t-red i-remove) {{ t('Файл <code>application/maxsite/mso_config.php</code> не создан') }}
{% endif %}


{% if (v_get_secret_key() === FALSE) : %}

	_(t-red i-remove) {{ t('Не удалось получить секретную фразу из <code>application/maxsite/mso_config.php</code>. Откройте его и укажите секретную фразу самостоятельно.') }}
	
	</div>
	{% return %}
{% endif %}

{% if (v_file_exists('application/config/database.php')) : %}
	_(t-green i-check-square-o) {{ t('База данных уже установлена') }}
	
	_ <a href="../">{{ t('Перейти к сайту') }}</a> 
	
	</div>
	{% return %}
{% endif %}


<?php

// дефолтные значения полей
$PV['username'] = '';
$PV['password'] = v_rand_str(15);
$PV['email'] = '';
$PV['site_name'] = '';
$PV['demo'] = '1';

$PV['db_hostname'] = 'localhost';
$PV['db_username'] = '';
$PV['db_password'] = '';
$PV['db_database'] = '';
$PV['db_dbprefix'] = 'mso_';

if ($_POST) 
{
	// есть разница с тем что должно быть
	if ($diff = array_diff_key($_POST, $PV)) 
	{
		echo t('Ошибочный post-запрос') . '</div>';
		return;
	}
	
	// сохраняем переданные значения
	$PV = $_POST;
	
	// проверяем на ошибки
	$errors = array();
	
	if (!v_valid_email($PV['email'])) 
	{
		$errors[] = 'Неверный адрес email';
		$PV['email'] = '';
	}
	
	if (strlen($PV['password']) < 8) 
	{
		$errors[] = t('Слишком короткий пароль. Должен быть не менее 8 символов');
		$PV['password'] = '';
	}
	
	if (strlen($PV['username']) < 3) 
	{
		$errors[] = t('Слишком короткий логин админа. Должен быть не менее 3 символов');
		$PV['username'] = '';
	}	
	
	
	if ($errors)
	{
		foreach($errors as $error)
		{
			echo '<p class="t-red i-remove">' . $error . '</p>';
		}
	}
	else
	{
		// ошибок нет, пробуем законетится с базой
		// $PV['db_hostname'] = 'localhost';
		// $PV['db_username'] = '';
		// $PV['db_password'] = '';
		// $PV['db_database'] = '';
		// $PV['db_dbprefix'] = 'mso_';
		
		$mysqli = @ new mysqli($PV['db_hostname'], $PV['db_username'], $PV['db_password'], $PV['db_database']);
		
		if ($mysqli->connect_error) 
		{
			echo '<p class="t-red i-remove">' . t('Ошибка подключения к базе данных: ') . $mysqli->connect_error . ' (' . $mysqli->connect_errno . ')' . '</p>';
		}
		else
		{
			echo '<p class="t-green i-check-square-o">' . t('Соединение с базой успешно установлено') . '</p>';
			
			$mysqli->set_charset("utf8");
			
			// нужно проверить существование таблиц 
			// 'links — может уже не нужно?'
			$tables = array('groups', 'users', 'meta', 'page_type', 'cat2obj', 'category', 'page', 'comusers', 'comments');
			
			if ($errors = v_mysql_table_exists($mysqli, $tables, $PV['db_dbprefix']))
			{
				foreach($errors as $error)
				{
					echo '<p class="t-red i-remove mar30-l mar0-b">' . $error . '</p>';
				}
			}
			else
			{
				v_new_database($PV);
				
				// таблиц нет, можно создавать
				$sqls = v_load_sql('install/distr/sql/model.sql');
				$sqls = explode('###', $sqls);
				
				$charset_collate = ' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
				
				// пароль нужно делать на онове секретной фразы
				// секретная фразу из файла
				$pas = $mysqli->real_escape_string(strrev( md5($PV['password'] . v_get_secret_key())));
				
				foreach($sqls as $sql)
				{
					$sql = trim($sql);
					
					if ($sql)
					{
						$sql = str_replace('_PREFIX_', $mysqli->real_escape_string($PV['db_dbprefix']), $sql);
						$sql = str_replace('_CHARSETCOLLATE_', $charset_collate, $sql);
						$sql = str_replace('_USERNAME_', $mysqli->real_escape_string($PV['username']), $sql);
						$sql = str_replace('_USERPASSWORD_', $pas, $sql);
						$sql = str_replace('_USEREMAIL_', $mysqli->real_escape_string($PV['email']), $sql);
						$sql = str_replace('_IP_', $_SERVER['REMOTE_ADDR'], $sql);
						
						$mysqli->query($sql); 
					}
				}
				
				// добавим опции в таблицу опций
				$table = $mysqli->real_escape_string($PV['db_dbprefix'] . 'options');
				
				$value = $mysqli->real_escape_string($PV['username']);
				$sql = "INSERT INTO {$table} (options_key, options_type, options_value) VALUES ('admin_nick', 'general', '{$value}')";
				$mysqli->query($sql);
				
				
				$value = $mysqli->real_escape_string($PV['site_name']);
				$sql = "INSERT INTO {$table} (options_key, options_type, options_value) VALUES ('name_site', 'general', '{$value}')";
				
				$mysqli->query($sql);
				
				
				$value = $mysqli->real_escape_string($PV['site_name']);
				$sql = "INSERT INTO {$table} (options_key, options_type, options_value) VALUES ('title', 'general', '{$value}')";
				
				$mysqli->query($sql);
				
				$value = $mysqli->real_escape_string($PV['email']);
				$sql = "INSERT INTO {$table} (options_key, options_type, options_value) VALUES ('admin_email', 'general', '{$value}')";
				
				$mysqli->query($sql);
				
				// демо-данные
				if ($PV['demo'])
				{
					$sqls = v_load_sql('install/distr/sql/demo.sql');
					$sqls = explode('###', $sqls);
					
					foreach($sqls as $sql)
					{
						$sql = trim($sql);
						
						if ($sql)
						{
							$sql = str_replace('_PREFIX_', $mysqli->real_escape_string($PV['db_dbprefix']), $sql);
							
							$mysqli->query($sql); 
						}
					}
				}
				
				
				echo '<p class="t-green i-check-square-o">' . t('Все требуемые sql-запросы выполнены') . '</p>';
				
				
				// отпраляем уведомление на email
				$message = t('Ваш новый сайт создан: ') . $PV['site_name'] . "\r\n"
						. 'http://' . v_get_host() . " \r\n"
						. t('Для входа воспользуйтесь данными:') . "\r\n"
						. t('Логин: ') . $PV['username'] . "\r\n"
						. t('Пароль: ') . $PV['password'] . "\r\n" . "\r\n" . "\r\n"
						. t('Сайт поддержки: http://max-3000.com/');
						
				v_email($PV['email'], t('Новый сайт на MaxSite CMS'), $message);
			}

			$mysqli->close();
			
			// если файл database.php есть, форму уже не показываем
			if (v_file_exists('application/config/database.php')) 
			{
				echo '<p class="t-green i-check-square-o">' . t('База данных установлена') . '</p><p><a href="../">' . t('Перейти к сайту') . '</a></p></div>';
				
				return;
			}
		}
	}
}

?>

h4 Укажите данные для установки

<form class="w100" method="post">

	<p><label>Логин админа <input type="text" class="w100" name="username" value="{{ $PV['username'] }}" placeholder="логин..." required></label></p>
	
	<p><label>Пароль (более 8 символов)<input type="password" class="w100" name="password" value="{{ $PV['password'] }}" placeholder="пароль..." required></label></p>
	
	<p><label>Email <input type="email" class="w100" name="email" value="{{ $PV['email'] }}" placeholder="email..." required></label></p>
	
	<p><label>Название сайта <input type="text" class="w100" name="site_name" value="{{ $PV['site_name'] }}" placeholder="название сайта..." required></label></p>
	
	<input type="hidden" name="demo" value="0">
	<p><label><input type="checkbox" name="demo" value="1" > Установить демонстрационые данные</label></p>
	
	
	h5(bold) Параметры базы данных MySQL
	
	_(t-gray600) Эти параметры вы можете получить у своего хостера.
	
	<p><label>Сервер БД (обычно это <i>localhost</i>) <input type="text" class="w100" name="db_hostname" value="{{ $PV['db_hostname'] }}" placeholder="сервер базы данных..." required></label></p>
	
	<p><label>Пользователь <input type="text" class="w100" name="db_username" value="{{ $PV['db_username'] }}" placeholder="пользователь базы..." required></label></p>
	
	<p><label>Пароль <input type="text" class="w100" name="db_password" value="{{ $PV['db_password'] }}" placeholder="пароль пользователя..."></label></p>
	
	<p><label>Имя базы данных <input type="text" class="w100"  name="db_database" value="{{ $PV['db_database'] }}" placeholder="имя базы данных..." required></label></p>
	
	<p><label>Префикс таблиц MaxSite CMS<input type="text" class="w100" name="db_dbprefix" value="{{ $PV['db_dbprefix'] }}" placeholder="префикс таблиц..." required></label></p>
	
	<p class="mar30-t"><button type="submit" class="button i-check">Готово</button></p>
	
</form>

</div>
