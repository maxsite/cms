<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


предусмотреть в плагине возможность менять формат вывода
 
для этого использовать файл шаблона custom/plugins/forms/form.php

продумать варианты

1-й основа html со вставками php-переменных


<p><label class="ffirst ftitle ftop" for="id-<?= ++$id ?>"<?= $require_title ?>><?= $description . $require ?></label><span><textarea name="forms_fields[<?= $key ?>]" id="id-<?= $id ?>"<?= $placeholder . $required. $attr ?>><?= $pole_value ?></textarea></span></p><?= $tip ?>



2-й массив данных, который содержит предопределенные поля

$f['start_1'] = '';
$f['end_1'] = '';
$f['start_1'] = '';

			$out .= NR . '<p><label class="ffirst ftitle ftop" for="id-' . ++$id . '"' . $require_title . '>' . $description . $require . '</label><span><textarea name="forms_fields[' . $key . ']" id="id-' . $id . '"' . $placeholder . $required. $attr . '>' . $pole_value . '</textarea></span></p>' . $tip;
 
 
 3-й текстовый формат, который хранится в файле в массив данных
 один раз загружается и после автозамены
 
 
 
$f['format_textarea'] = '<p><label class="ffirst ftitle ftop" for="{id}"{require_title}>{description}{require}</label><span><textarea name="forms_fields[{key}]" id="{id}"{placeholder}{required}{attr}>{pole_value}</textarea></span></p>{tip}';


	$out .= NR . '<p><label class="ffirst ftitle ftop" for="id-' . ++$id . '"' . $require_title . '>' . $description . $require . '</label><span><textarea name="forms_fields[' . $key . ']" id="id-' . $id . '"' . $placeholder . $required. $attr . '>' . $pole_value . '</textarea></span></p>' . $tip;
 
 
$f['format_tip'] = '<p class="nop"><span class="ffirst"></span><span class="fhint">{tip}</span></p>';

где {id} заменяется на $id, {attr} на $attr и т.п.


 