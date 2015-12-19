<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// все проверки
if (!is_login()) die('no login');
mso_checkreferer();
if (!mso_check_allow('auto_post')) die('no allow');

// pr($_SERVER);

// такая муть нужна, если сервер не пропускает HTTP_X_FILENAME и HTTP_X_FILENAME_UP_DIR
// тогда он вдруг может пропустить HTTP_X_REQUESTED_FILENAME и HTTP_X_REQUESTED_FILEUPDIR
if (isset($_SERVER['HTTP_X_FILENAME']))
{
	$_fn = $_SERVER['HTTP_X_FILENAME'];
}
elseif (isset($_SERVER['HTTP_X_REQUESTED_FILENAME']))
{
	$_fn = $_SERVER['HTTP_X_REQUESTED_FILENAME'];
}
else die('no file');

if (isset($_SERVER['HTTP_X_FILENAME_UP_DIR']))
{
	$_dr = $_SERVER['HTTP_X_FILENAME_UP_DIR'];
}
elseif (isset($_SERVER['HTTP_X_REQUESTED_FILEUPDIR']))
{
	$_dr = $_SERVER['HTTP_X_REQUESTED_FILEUPDIR'];
}
else die('no updir');


if (!is_dir($_dr)) die('no exist updir');

// файл
$fn = _slug($_fn);

// каталог
$up_dir = getinfo('FCPATH') . $_dr;

// file_put_contents(FCPATH . 'log.txt', $up_dir . $fn); // лог для отладки 

// загрузка 
file_put_contents( $up_dir . $fn, file_get_contents('php://input') );

if (file_exists($up_dir . $fn))
{
	require_once(__DIR__ . '/lib/add-new-page.php');
	add_new_page($up_dir . $fn, $up_dir);
}

function _slug($slug)
{
	$repl = array(
	"А"=>"a", "Б"=>"b",  "В"=>"v",  "Г"=>"g",   "Д"=>"d",
	"Е"=>"e", "Ё"=>"jo", "Ж"=>"zh",
	"З"=>"z", "И"=>"i",  "Й"=>"j",  "К"=>"k",   "Л"=>"l",
	"М"=>"m", "Н"=>"n",  "О"=>"o",  "П"=>"p",   "Р"=>"r",
	"С"=>"s", "Т"=>"t",  "У"=>"u",  "Ф"=>"f",   "Х"=>"h",
	"Ц"=>"c", "Ч"=>"ch", "Ш"=>"sh", "Щ"=>"shh", "Ъ"=>"",
	"Ы"=>"y", "Ь"=>"",   "Э"=>"e",  "Ю"=>"ju", "Я"=>"ja",

	"а"=>"a", "б"=>"b",  "в"=>"v",  "г"=>"g",   "д"=>"d",
	"е"=>"e", "ё"=>"jo", "ж"=>"zh",
	"з"=>"z", "и"=>"i",  "й"=>"j",  "к"=>"k",   "л"=>"l",
	"м"=>"m", "н"=>"n",  "о"=>"o",  "п"=>"p",   "р"=>"r",
	"с"=>"s", "т"=>"t",  "у"=>"u",  "ф"=>"f",   "х"=>"h",
	"ц"=>"c", "ч"=>"ch", "ш"=>"sh", "щ"=>"shh", "ъ"=>"",
	"ы"=>"y", "ь"=>"",   "э"=>"e",  "ю"=>"ju",  "я"=>"ja",

	# украина
	"Є" => "ye", "є" => "ye", "І" => "i", "і" => "i",
	"Ї" => "yi", "ї" => "yi", "Ґ" => "g", "ґ" => "g",
	
	# беларусь
	"Ў"=>"u", "ў"=>"u", "'"=>"",
	
	# румынский
	"ă"=>'a', "î"=>'i', "ş"=>'sh', "ţ"=>'ts', "â"=>'a',
	
	"«"=>"", "»"=>"", "—"=>"-", "`"=>"", " "=>"-",
	"["=>"", "]"=>"", "{"=>"", "}"=>"", "<"=>"", ">"=>"",

	"?"=>"", ","=>"", "*"=>"", "%"=>"", "$"=>"",

	"@"=>"", "!"=>"", ";"=>"", ":"=>"", "^"=>"", "\""=>"",
	"&"=>"", "="=>"", "№"=>"", "\\"=>"", "/"=>"", "#"=>"",
	"("=>"", ")"=>"", "~"=>"", "|"=>"", "+"=>"", "”"=>"", "“"=>"",
	"'"=>"",

	"’"=>"",
	"—"=>"-", // mdash (длинное тире)
	"–"=>"-", // ndash (короткое тире)
	"™"=>"tm", // tm (торговая марка)
	"©"=>"c", // (c) (копирайт)
	"®"=>"r", // (R) (зарегистрированная марка)
	"…"=>"", // (многоточие)
	"“"=>"",
	"”"=>"",
	"„"=>"",
	
	" "=>"-",
	);
		
	$slug = strtr(trim($slug), $repl);
	$slug = htmlentities($slug); // если есть что-то из юникода
	$slug = strtr(trim($slug), $repl);
	$slug = strtolower($slug);
	
	return $slug;
}

return;

# end of file