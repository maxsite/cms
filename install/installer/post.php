<?php if (!defined('INSTALLER')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

if ($_POST) {
	// есть разница с тем что должно быть
	if ($diff = array_diff_key($_POST, $PV)) {
		die(t('incorrect post'));
	}

	// сохраняем переданные значения
	$PV = $_POST;

	// проверяем на ошибки
	$errors = [];

	if (!filter_var($PV['email'], FILTER_VALIDATE_EMAIL)) {
		$errors[] = t('incorrect email');
		$PV['email'] = '';
	}

	if (strlen($PV['password']) < 8) {
		$errors[] = t('incorrect password');
		$PV['password'] = '';
	}

	if (strlen($PV['username']) < 3) {
		$errors[] = t('incorrect login');
		$PV['username'] = '';
	}

	if ($errors) {
		foreach ($errors as $error) {
			echo '<p class="t-red">⚠ ' . $error . '</p>';
		}
	} else {
		// ошибок нет, пробуем законетится с базой
		// $PV['db_hostname'] = 'localhost';
		// $PV['db_username'] = '';
		// $PV['db_password'] = '';
		// $PV['db_database'] = '';
		// $PV['db_dbprefix'] = 'mso_';

		$mysqli = @new mysqli($PV['db_hostname'], $PV['db_username'], $PV['db_password'], $PV['db_database']);

		if ($mysqli->connect_error) {
			echo '<p class="t-red">⚠ ' . t('incorrect db') . ' ' . htmlspecialchars($mysqli->connect_error) . ' (' . htmlspecialchars($mysqli->connect_errno) . ')' . '</p>';
		} else {
			echo '<p class="t-green">✔ ' . t('ok db') . '</p>';

			$mysqli->set_charset("utf8");

			// нужно проверить существование таблиц 
			$tables = ['groups', 'users', 'meta', 'page_type', 'cat2obj', 'category', 'page', 'comusers', 'comments'];

			if ($errors = checkTableExists($mysqli, $tables, $PV['db_dbprefix'])) {
				foreach ($errors as $error) {
					echo '<p class="t-red mar30-l mar0-b">⚠ ' . $error . '</p>';
				}
			} else {
				newDatabase($PV);

				$sqls = file_get_contents(INSTALLER . 'distr/model.sql.txt');
				$sqls = explode('###', $sqls);

				// пароль нужно делать на онове секретной фразы
				// секретная фразу из файла
				$pas = $mysqli->real_escape_string(strrev(md5($PV['password'] . checkSecretKey(false))));

				foreach ($sqls as $sql) {
					$sql = trim($sql);

					if ($sql) {
						$sql = str_replace('_PREFIX_', $mysqli->real_escape_string($PV['db_dbprefix']), $sql);
						$sql = str_replace('_USERNAME_', $mysqli->real_escape_string($PV['username']), $sql);
						$sql = str_replace('_USERPASSWORD_', $pas, $sql);
						$sql = str_replace('_USEREMAIL_', $mysqli->real_escape_string($PV['email']), $sql);
						$sql = str_replace('_SITENAME_', $mysqli->real_escape_string($PV['site_name']), $sql);
						$sql = str_replace('_IP_', $_SERVER['REMOTE_ADDR'], $sql);
						$sql = str_replace('_TEXT1_', $mysqli->real_escape_string(t('_text1_')), $sql);
						$sql = str_replace('_TEXT2_', $mysqli->real_escape_string(t('_text2_')), $sql);
						$sql = str_replace('_TEXT3_', $mysqli->real_escape_string(t('_text3_')), $sql);
						$sql = str_replace('_TEXT4_', $mysqli->real_escape_string(t('_text4_')), $sql);
						$sql = str_replace('_TEXT5_', $mysqli->real_escape_string(t('_text5_')), $sql);
						$sql = str_replace('_TEXT6_', $mysqli->real_escape_string(t('_text6_')), $sql);
						$sql = str_replace('_TEXT7_', $mysqli->real_escape_string(t('_text7_')), $sql);
						$sql = str_replace('_TEXT8_', $mysqli->real_escape_string(t('_text8_')), $sql);

						$mysqli->query($sql);
					}
				}
				
				echo '<p class="t-green">✔ ' . t('ok sql') . '</p>';
				echo '<p><a href="../">' . t('go site') . '</a></p>';

				$message = t('new site') . ' ' . $PV['site_name'] . "\r\n"
					. 'http://' . getHostSite() . " \r\n"
					. t('login data') . "\r\n"
					. t('slogin') . ' ' . $PV['username'] . "\r\n"
					. t('spassword') . ' '  . $PV['password'] . "\r\n" . "\r\n" . "\r\n"
					. t('site maxsite');

				sendEmail($PV['email'], t('new site maxsite'), $message);
			}

			$mysqli->close();

			$showForm = false;
		}
	}
}

# end of file
