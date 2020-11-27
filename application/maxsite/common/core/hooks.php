<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// подключение функции к хуку
// приоритет по-умолчанию 10.
// если нужно, чтобы хук сработал раньше всех, то ставим более 10
// если нужно сработать последним - ставим приоритет менее 10
// http://forum.max-3000.com/viewtopic.php?p=9550#p9550
function mso_hook_add($hook, $func, $priory = 10)
{
	global $MSO;

	$priory = (int) $priory;
    
	if ($priory > 0)
		$MSO->hooks[$hook][$func] = $priory;
	else
		$MSO->hooks[$hook][$func] = 0;

	ksort($MSO->hooks[$hook]);
	arsort($MSO->hooks[$hook]);
}

// прописываем хук к admin_url_+hook
function mso_admin_url_hook($hook, $func, $priory = 0)
{
	// нельзя указывать хуки на зарезервированные адреса: ???
	$hook = strtolower($hook);
	$no_hook = [''];

	if (!in_array($hook, $no_hook))
		mso_hook_add('admin_url_' . $hook, $func, $priory);
}

// выполнение хуков
// название хука - переменная для результата
function mso_hook($hook = '', $result = '', $result_if_no_hook = '_mso_result_if_no_hook')
{
	global $MSO;

	if ($hook == '') return $result;

	$arr = array_keys($MSO->hooks);

	if (!in_array($hook, $arr)) // если хука нет
	{
		if ($result_if_no_hook != '_mso_result_if_no_hook') // если указана $result_if_no_hook
			return $result_if_no_hook;
		else
			return $result;
	}

	//_mso_profiler_start('' .$hook, true);
	//$i = 1;

	foreach ($MSO->hooks[$hook] as $func => $val) {
		//_mso_profiler_start('-- ' . $hook . ' - ' . $func . $i);

		if (function_exists($func)) {
            $result = $func($result);
        } else {
            // признак /**msofunc**/ , что это динамическая функция
            // делаем из неё анонимную и выполняем
            if (strpos($func, '/**msofunc**/') !== false) {
                $lambda = function($args) use ($func) { return eval($func); };
                $result = $lambda($result);
            }
        }

		//_mso_profiler_end('-- ' . $hook . ' - ' . $func . $i);
		// $i++;
	}

	//_mso_profiler_end('' . $hook);

	return $result;
}

// проверяет существование хука
function mso_hook_present($hook = '')
{
	global $MSO;

	if ($hook == '') return false;

	$arr = array_keys($MSO->hooks);

	if (!in_array($hook, $arr))
		return false;
	else
		return true;
}

// удаляет из хука функцию
// если функция не указана, то удаляются все функции из хука
function mso_remove_hook($hook = '', $func = '')
{
	global $MSO;

	if ($hook == '') return false;

	$arr = array_keys($MSO->hooks);

	if (!in_array($hook, $arr)) return false; // хука нет

	if ($func == '') {
		// удалить весь хук
		unset($MSO->hooks[$hook]);
	} else {
		if (!in_array($hook, $arr)) return false; // нет такой функции
		unset($MSO->hooks[$hook][$func]);
	}

	return true;
}

// динамическое создание функции на хук
// тело функции дожно работать как нормальный php
// функция принимает только один аргумент $args
function mso_hook_add_dinamic($hook = '', $func = '', $priory = 10)
{
	if ($hook == '') return false;
	if ($func == '') return false;

    // PHP8
	// $func_name = @create_function('$args', $func);
	// return mso_hook_add($hook, $func_name, $priory);
    
    // добавляем в тело функции php-комментарий, который будет меткой для выполнения в mso_hook()
	return mso_hook_add($hook, '/**msofunc**/' . $func, $priory);
}

# end of file
