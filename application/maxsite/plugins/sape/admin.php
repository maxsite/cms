<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	
	$options_key = 'sape';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_kod', 'f_articles_url', 'f_articles_template')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['kod'] = $post['f_kod'];
		
		$options['go'] = 0; // признак, что код установлен верно - каталог есть и доступен для записи
		
		// проверим введенный код
		$fn = $_SERVER['DOCUMENT_ROOT'] . '/' . $options['kod'] . '/sape.php';
		
		if (!file_exists($fn)) // нет файла, просто выведем предупреждение
		{
			echo '<div class="error">Введенный вам код, возможно неправильный, или вы не распаковали архив на сервере!</div>';
		}
		else // есть файл, проверим что каталог доступен на запись
		{
			if (!is_writable($_SERVER['DOCUMENT_ROOT'] . '/' . $options['kod'])) 
				echo '<div class="error">Указанный вами каталог недоступен для записи. Установите для него права 777 (разрешающие запись).</div>';
			else
				$options['go'] = 1; // нет ошибок
		}
		
		$options['start'] = isset($post['f_start']) ? 1 : 0;
		$options['context'] = isset($post['f_context']) ? 1 : 0;
		$options['context_comment'] = isset($post['f_context_comment']) ? 1 : 0;
		$options['test'] = isset($post['f_test']) ? 1 : 0;
		$options['multi_site'] = isset($post['f_multi_site']) ? 1 : 0;
		$options['anticheck'] = isset($post['f_anticheck']) ? 1 : 0;
		
		$options['articles'] = isset($post['f_articles']) ? 1 : 0;
		$options['articles_url'] = $post['f_articles_url'];
		$options['articles_template'] = $post['f_articles_template'];

		mso_add_option($options_key, $options, 'plugins' );
		echo '<div class="update">Настройки обновлены!</div>';
	}
	
?>
<h1>Настройка Sape.ru</h1>
<p>С помощью этой страницы вы можете настроить свою работу с <a href="http://www.sape.ru/r.aa92aef9c6.php" target="_blank">sape.ru</a>. Перед началом работы вам следует выполнить следующие действия:</p>
<ol>
<li>Скачать с <a href="http://www.sape.ru/r.aa92aef9c6.php" target="_blank">sape.ru</a> архив с вашим кодом для загрузки на сервер.
<li>Распаковать архив. Внутри него будет лежать папка с именем вроде такого: «8df7s4sd2if89as5v34vbez3e2».
<li>Загрузите эту папку на ваш сервер в корень(!!!) вашего сайта.
<li>Установите права на эту папку «777» (разрешающие запись).
</ol>
<br>
<p><strong>Только после этого вы можете выполнить настройки на этой странице!</strong></p>
<ol>
<li>Укажите свой код (он совпадает с именем папки).
<li>Отметьте будете ли вы использовать контекстные ссылки. (Контекстные ссылки могут быть только при использовании обычных.)
</ol>
<br>
<p><strong>Для размещения блоков вывода ссылок вы можете воспользоваться виджетами или вручную прописать вызов функций в шаблоне.</strong></p>
<p><strong>При использовании виджетов</strong> у вас будет возможность указать количество ссылок для данного виджета (виджетов может быть несколько). Обратите внимание, что в последнем виджете вам следует оставить это поле пустым, чтобы вывести оставшиеся ссылки.</p>
<p><strong>При ручном размещении</strong> вам следует в шаблоне прописать вызов функции <strong>sape_out()</strong></p>
<pre>
	
	if (function_exists('sape_out')) sape_out();
	
</pre>

<p>Для того, чтобы разбить вывод ссылок на несколько частей, следует указать в <strong>sape_out()</strong> количество ссылок для вывода. Обратите внимание, что последний вызов <strong>sape_out()</strong> должен быть без параметров - это выведет все оставшиеся ссылки.</p>
<pre>
	
	if (function_exists('sape_out')) sape_out(3); // первый блок из 3-х ссылок
	
	if (function_exists('sape_out')) sape_out(4); // второй блок из 4-х ссылок
	
	if (function_exists('sape_out')) sape_out(); // последний блок - оставшиеся ссылки
	
</pre>

<p><strong>После размещения всех блоков вы можете проверить верность размещения.</strong> Для этого отметьте опцию «Режим проверки установленного кода» и обновите страницу сайта. С помощью браузера (FireFox) просмотрите исходный код страницы. В каждом установленном блоке вы увидите закомментированное число или строку <strong>&lt;!--check code--&gt;</strong>. Если данной строки нет, значит код установлен неверно. Если строчка есть, то код установлен верно и опцию нужно отключить.</p>

<p><strong>Примечание</strong>. Если вы размещаете код через виджеты, то при включенной проверке в виджете появится текст «Код sape.ru установлен верно!»</p>

<p>После проверки кода, вы можете войти в свой аккаунт на sape.ru и добавить свой сайт. В течение некоторого времени, робот <a href="http://www.sape.ru/r.aa92aef9c6.php" target="_blank">sape.ru</a> его проиндексирует.</p>

<p><strong>Обратите внимание! Помощь по установке кода <a href="http://www.sape.ru/r.aa92aef9c6.php" target="_blank">sape.ru</a>, любые подсказки и разъяснения по этому поводу я оказываю только на платной основе.</strong></p>
<br>

<?php
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['kod']) ) $options['kod'] = ''; 
		if ( !isset($options['context']) ) $options['context'] = true; 
		if ( !isset($options['context_comment']) ) $options['context_comment'] = true; 
		if ( !isset($options['test']) ) $options['test'] = false; 
		if ( !isset($options['multi_site']) ) $options['multi_site'] = false; 
		if ( !isset($options['start']) ) $options['start'] = true; 
		if ( !isset($options['anticheck']) ) $options['anticheck'] = false; 
		
		if ( !isset($options['articles']) ) $options['articles'] = false; 
		if ( !isset($options['articles_url']) ) $options['articles_url'] = ''; 
		if ( !isset($options['articles_template']) ) $options['articles_template'] = ''; 
		
		$checked_context = $options['context'] ? ' checked="checked" ' : '';
		$checked_context_comment = $options['context_comment'] ? ' checked="checked" ' : '';
		$checked_test = $options['test'] ? ' checked="checked" ' : '';
		$checked_multi_site = $options['multi_site'] ? ' checked="checked" ' : '';
		$checked_start = $options['start'] ? ' checked="checked" ' : '';
		$checked_anticheck = $options['anticheck'] ? ' checked="checked" ' : '';
		
		$checked_articles = $options['articles'] ? ' checked="checked" ' : '';
		
		$form = '';
		$form .= '<p><strong>Ваш номер/код в <a href="http://www.sape.ru/r.aa92aef9c6.php" target="_blank">sape.ru</a>:</strong> ' . ' <input name="f_kod" type="text" style="width: 300px;" value="' . $options['kod'] . '"></p>';
		
		$form .= '<p><label><input name="f_start" type="checkbox"' . $checked_start . '> Включить плагин</label></p>';
		$form .= '<p><label><input name="f_context" type="checkbox"' . $checked_context . '> Использовать контекстные ссылки</label></p>';
		$form .= '<p><label><input name="f_multi_site" type="checkbox"' . $checked_multi_site . '> Включите если код Сапы использыется <a href="http://help.sape.ru/sape/faq/1031" target="_blank">для нескольких сайтов</a></label></p>';
		$form .= '<p><label><input name="f_context_comment" type="checkbox"' . $checked_context_comment . '> Использовать контекстные ссылки в комментариях</label></p>';
		$form .= '<p><label><input name="f_test" type="checkbox"' . $checked_test . '> Режим проверки установленного кода</label></p>';
		$form .= '<p><label><input name="f_anticheck" type="checkbox"' . $checked_anticheck . '> Включить антиобнаружитель продажных ссылок</label></p>';
		
		
		$form .= '<hr>';
		$form .= '<p>Размещение статей имеет некоторые свои особености. Вначале вам нужно разместить полученный код сапы на своем сайте. После этого выставьте все параметры ниже. Не забудьте расположить виджет Sape.ru с выводом статей, иначе робот Cапы не позволит добавить ваш сайт в систему. После всех этих приготовлений, вы можете указать в Сапе свой сайт.</p>';
				
		$form .= '<p><label><input name="f_articles" type="checkbox"' . $checked_articles . '> Включить публикацию статей</label>
		<br>Не забудьте создать виджет «Sape.ru», где указать «Вывод статей».</p>';		
		
		$form .= '<p><strong>Ссылка для статей</strong> <input name="f_articles_url" type="text" value="' . $options['articles_url'] . '">, например «reklama» - http://сайт/<u>reklama</u>
		<br>Данный параметр должен совпадать с заданным шаблоном URL в сапе. Например вы задали в сапе: «/articles/{id}», значит здесь нужно указать «articles». Указывать нужно только один сегмент URL. Обратите внимание, что менять «.htaccess» <strong>не нужно</strong>!</p>';
		
		$form .= '<p><strong>Ссылка на шаблон статей</strong> <input name="f_articles_template" type="text" value="' . $options['articles_template'] . '">, например «sape-articles-template» - http://сайт/<u>sape-articles-template</u>
		<br>В этом поле укажите произвольный адрес. Этот адрес нужно указать в настройках сапы в «Шаблонах статей». Шаблон для сапы будет сгенерирован автоматически.</p>';		
		
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value=" Сохранить изменения " style="margin: 25px 0 5px 0;">';
		echo '</form>';

?>