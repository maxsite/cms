<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик

# добавление новой рубрики
if ( $post = mso_check_post(array('f_session_id', 'f_new_name', 'f_new_slug', 'f_new_desc', 'f_new_parent', 'f_new_order', 'f_new_submit')) ) 
{
	mso_checkreferer();

	// подготавливаем данные
	$data = array(
		'category_id_parent' => (int) $post['f_new_parent'],
		'category_name' => $post['f_new_name'],
		'category_desc' => $post['f_new_desc'],
		'category_slug' => $post['f_new_slug'],
		'category_menu_order' => (int) $post['f_new_order']
		);

	// выполняем запрос и получаем результат

	require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования

	$result = mso_new_category($data);

	if (isset($result['result']) and $result['result'])
	{
		mso_flush_cache(); // сбросим кэш
		echo '<div class="update pos-fixed pos10-t pos0-r">' . t('Новая рубрика добавлена!') . '</div>';
	}
	else
		echo '<div class="error">' . t('Ошибка добавления!') . ' ' . $result['description'] . ' </div>';
}

?>
<h1><?= t('Рубрики') ?></h1>
<p class="info"><?= t('Настройка рубрик') ?></p>

<?php


// читаем информацию о всех рубриках сайта
$all = mso_cat_array('page', 0, 'category_menu_order', 'asc', 'category_menu_order', 'asc', false, false, false, false, false, false, false); 

// мета данные рубрик
$category_meta_format = '';
$function_meta = false;

if ($fn = mso_fe('custom/my-category-meta.php'))
	$category_meta = include $fn;
else
	$category_meta = false;

// pr($category_meta);

if ($category_meta)
{	
	// нужно получить все заданные мета по данной рубрике
	// заполним их реальным значениями
	$all_keys_meta = array_keys($category_meta); // все ключи в массиве
	$all_keys_cats = array_keys($all); // все id рубрик в массиве

	// добавим в массив $all мета данные в виде category_meta_КЛЮЧ
	foreach($all_keys_cats as $id_cat)
	{
		foreach($all_keys_meta as $key_meta)
		{
			$m = mso_get_meta($key_meta, 'category', $id_cat, 'meta_value');
			// pr($m);
			
			$all[$id_cat]['category_meta'][$key_meta] = $m;
		}
		
		// если есть childs запускаем рекурсию
		if (isset($all[$id_cat]['childs']))
		{
			$all[$id_cat]['childs'] = my_cat_childs($all[$id_cat]['childs'], $all_keys_meta);
		}
	}
	
	// pr($all);
	//  [category_meta] => Array
    //    (
    //        [mytitle] => 
    //        [mydescription] => 
    //    )
	
	$function_meta = 'my_function_meta'; // определим функцию, которая будет обрабатывать данные

	// формируем строчку формата мета
	// значение в виде [@key@]
	foreach($category_meta as $key => $data)
	{
		$category_meta_format .= '<p><label>' . t($data['name']). '<textarea name="cat[[ID]][category_meta][' . $key .']" ' . $data['attr'] . '>[@' . $key .'@]</textarea></label>';
	}
}


# форма для добавления новой рубрики
$add_new = '
<form method="post" class="new-cat mso-cats">' . mso_form_session('f_session_id').'
	<p><button type="button" name="f_new_submit" class="add button i-plus">' . t('Добавить новую рубрику') . '</button></p>
	<div class="form">
		<p><label>' . t('Название') . '<input type="text" name="f_new_name" value=""></label></p>
		<p><label>' . t('Описание') . '<textarea name="f_new_desc"></textarea></label></p>
		<p class="flex">
			<label class="flex-grow3 mar20-r">' . t('Ссылка') . ' <input class="w-auto" type="text" name="f_new_slug" value=""></label>
			<label class="flex-grow1 mar20-r">' . t('Родитель') . ' <input class="w-auto w100px-max" type="number" name="f_new_parent" value=""></label>
			<label class="flex-grow1">' . t('Порядок') . ' <input class="w-auto w100px-max" type="number" name="f_new_order" value=""></label>
		</p>
		<p><button type="submit" name="f_new_submit" class="button do-add_new i-save">' . t('Добавить') . '</button></p>
	</div>
</form>';
	
# шаблон вывода информации об одной рубрике
$format = '
<div class="li" id="catli[ID]">
	<span class="id" title="' . t('ID рубрики') . '">[ID]</span>.
	<a class="edit" id="cat[[ID]][title]" title="' . t('Нажмите, чтобы перейти к редактированию') . '">[TITLE_HTML]</a>
	<span>[ ' . t('ссылка: ') . '<a href="' . getinfo('siteurl') . 'category/[SLUG_HTML]" id="cat[[ID]][page]" target="_blank" title="' . t('Смотреть рубрику на сайте') . '">[SLUG_HTML]</a> ]</span>
	<span>[ ' . t('записей: ') . '<a href="' . getinfo('site_admin_url') . 'page/category/[ID]" title="' . t('Список записей') . '" target="_blank">[COUNT]</a> ]</span>
</div>
<div class="form" id="catform[ID]">
	<p><label>' . t('Название') . '<input type="text" name="cat[[ID]][category_name]" value="[TITLE_HTML]"></label></p>
	<p><label>' . t('Описание') . '<textarea name="cat[[ID]][category_desc]">[DESCR_HTML]</textarea></label></p>
	<p class="flex">
		<label class="flex-grow3 mar20-r">' . t('Ссылка') . ' <input class="w-auto" type="text" name="cat[[ID]][category_slug]" value="[SLUG_HTML]" id="cat[[ID]][slug]"></label>
		<label class="flex-grow1 mar20-r">' . t('Родитель') . ' <input class="w-auto w100px-max" type="number" name="cat[[ID]][category_parent]" value="[ID_PARENT]"></label>
		<label class="flex-grow1">' . t('Порядок') . ' <input class="w-auto w100px-max" type="number" name="cat[[ID]][category_order]" value="[MENU_ORDER]"></label>
	</p>
	' . $category_meta_format . '
	
	<p><button type="button" class="button do-save i-save" data-id="[ID]">' . t('Сохранить') . '</button> <button type="button" class="button do-remove i-remove" data-id="[ID]">' . t('Удалить') . '</button></p>
</div>
<div class="msg pos-fixed pos10-t pos0-r" id="cat[[ID]][msg]"></div>
';



# формируем список-дерево с рубриками
$all_cats = mso_create_list($all,
	array(
		'childs'=>'childs',
		'format'=>$format,
		'format_current'=>$format,
		'class_ul'=>'rubrics',
		'class_li'=>'',

		'class_ul_style'=>'',
		'class_child_style'=>'',
		'class_li_style'=>'',

		'title'=>'category_name',
		'link'=>'category_slug',
		'current_id'=>true,
		'prefix'=>'category/',
		'count'=>'pages_count',
		'id'=>'category_id',
		'slug'=>'category_slug',
		'menu_order'=>'category_menu_order',
		'id_parent'=>'category_id_parent',
		'function1' => $function_meta
	));

# формируем тело страницы
echo $add_new;
echo $all_cats;

echo '
<script>
var
rubrics_ajax = "' . getinfo('ajax') . base64_encode('admin/plugins/admin_cat/do-ajax.php') . '",
current_url = "' . mso_current_url(true) . '",
cat_msg = {
	delete_confirm: "' . t('Вы действительно хотите удалить эту рубрику?') . '",
	delete_error: "' . t('Ошибка удаления!') . '",
	delete_ok: "' . t('Рубрика удалена!') . '",
	save_error: "' . t('Ошибка сохранения!') . '",
};
</script>';

echo mso_load_script(getinfo('admin_url') . 'plugins/admin_cat/script.js');


// функция для цикла мета
function my_function_meta($e, $elem, $data)
{
	if (isset($elem['category_meta']))
	{
		// [@mytitle@]
		foreach($elem['category_meta'] as $key => $val)
		{
			//pr($val);
			$e = str_replace('[@' . $key . '@]', htmlspecialchars($val), $e);
		}
	}
	
	// pr($elem, 1);
	// pr($e, 1);
	return $e;
}

// рекурсивная для childs
function my_cat_childs($a, $all_keys_meta)
{
	$all_keys_cats = array_keys($a); // все id рубрик в массиве
	
	foreach($all_keys_cats as $id_cat)
	{
		foreach($all_keys_meta as $key_meta)
		{
			$m = mso_get_meta($key_meta, 'category', $id_cat, 'meta_value');
			$a[$id_cat]['category_meta'][$key_meta] = $m;
		}
		
		// если есть childs запускаем рекурсию
		if (isset($a[$id_cat]['childs']))
		{
			$a[$id_cat]['childs'] = my_cat_childs($a[$id_cat]['childs'], $all_keys_meta);
		}
	}
	
	return $a;
}



# end of file