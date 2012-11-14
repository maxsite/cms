<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


function pagination2_autoload($a = array()) 
{
	mso_hook_add('pagination', 'pagination2_go', 5);
	return $a;
}


function pagination2_go($r = array()) 
{
	if ( !isset($r['maxcount']) ) return $r;

	$r_orig = $r; // сохраним исходный,	чтобы его же отдать дальше
	
	$options = mso_get_option('pagination2', 'plugins', array() ); // получаем опции
	
	if ( !isset($r['old']) ) 
		$r['old'] = isset($options['old']) ? $options['old'] : t('Старее »»»');
	
	if ( !isset($r['new']) ) 
		$r['new'] = isset($options['new']) ? $options['new'] : t('««« Новее');

	
	if ( !isset($r['sep']) ) // разделитель
		$r['sep'] = isset($options['sep']) ? $options['sep'] : t(' | ');
		

	# раньше - позже
	if ($ran1 = mso_url_paged_inc($r['maxcount'], -1)) 
			$ran1 = '<span class="new"><a href="' . $ran1 . '" title="' . $r['new'] . '">' . $r['new'] . '</a></span>';
	if ($ran2 = mso_url_paged_inc($r['maxcount'], 1))  
			$ran2 = '<span class="old"><a href="' . $ran2 . '" title="' . $r['old'] . '">' . $r['old'] . '</a></span>';
	
	if (!$ran1 or !$ran2) $r['sep'] = '';

	$out = $ran1 . $r['sep'] . $ran2;
	
	if ($out) echo NR . '<div class="pagination pagination2">' . $out . '</div>' . NR;
	
	return $r_orig;
}

# функция выполняется при деинсталяции плагина
function pagination2_uninstall($args = array())
{	
	mso_delete_option('pagination2', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function pagination2_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('pagination2', 'plugins', 
		array(
			'old' => array(
							'type' => 'text', 
							'name' => t('Записи старее'), 
							'description' => t('Текст ссылок для старых записей'), 
							'default' => t('Старее »»»')
						),
			'new' => array(
							'type' => 'text', 
							'name' => t('Записи новее'), 
							'description' => t('Текст ссылки на новые записи'), 
							'default' => t('««« Новее')
						), 
			'sep' => array(
							'type' => 'text', 
							'name' => t('Разделитель'), 
							'description' => t('Укажите разделитель'), 
							'default' => ' | '
						)
			),
		t('Настройки плагина пагинации'), // титул
		t('Укажите необходимые опции.')   // инфо
	);
}

# end file