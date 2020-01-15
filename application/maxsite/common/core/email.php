<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// функция отправки письма по email
function mso_mail($email = '', $subject = '', $message = '', $from = false, $preferences = [])
{
	$arg = [
		'email' => $email,
		'subject' => $subject,
		'message' => $message,
		'from' => $from,
		'preferences' => $preferences
	];

	// если определен хук mail, то через него отправляем данные
	if (mso_hook_present('mail')) return mso_hook('mail', $arg);

	$CI = &get_instance();
	$CI->load->library('email');

	$CI->email->clear(true);

	if (isset($preferences['attach'])) // письмо с вложением?
	{
		if (is_array($preferences['attach'])) {
			// множественное вложение
			foreach ($preferences['attach'] as $attach) {
				if (trim($attach)) $CI->email->attach($attach);
			}
		} elseif (trim($preferences['attach'])) {
			$CI->email->attach($preferences['attach']);
		}
	}

	$config['wordwrap'] = isset($preferences['wordwrap']) ? $preferences['wordwrap'] : TRUE;
	$config['wrapchars'] = isset($preferences['wrapchars']) ? $preferences['wrapchars'] : 90;

	// можно отправлять письмо в html-формате
	if (isset($preferences['mailtype']) and $preferences['mailtype'])
		$config['mailtype'] = $preferences['mailtype'];

	$CI->email->initialize($config);
	$CI->email->to($email);

	// (переделка из-за ужесточения правил почтовиков)
	// $from теперь это reply-to: 
	// а from: всегда равен admin_email_server из настроек сайта
	$admin_email = mso_get_option('admin_email_server', 'general', 'admin@site.com');
	$from_name = $preferences['from_name'] ?? ''; //getinfo('name_site');

	$CI->email->from($admin_email, getinfo('name_site'));

	// если указан $from, то трактуем его как Reply-to
	if ($from) $CI->email->reply_to($from, $from_name);

	$CI->email->subject($subject);
	$CI->email->message($message);
	$CI->email->_safe_mode = true; // иначе CodeIgniter добавляет -f к mail - не будет работать в не safePHP

	$res = @$CI->email->send();

	// mail($email, $subject, $message); // проверка

	if (!$res) {
		if (isset($preferences['print_debugger']) and $preferences['print_debugger']) {
			echo $CI->email->print_debugger();
		}
	}

	$arg['res'] = $res;
	mso_hook('mail_res', $arg); // хук, если нужно отслеживать отправку почты

	return $res;
}

# end of file
