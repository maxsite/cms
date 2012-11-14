<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function paginator_autoload($a = array())
{
	mso_hook_add('head', 'paginator_head');
	mso_hook_add('admin_head', 'paginator_head');
	mso_hook_add('pagination', 'paginator_go', 10);
	return $a;
}

function paginator_activate($args = array())
{
	if (!function_exists('paginator3000_go')) mso_delete_option('paginator3000', 'plugins' );
	return $args;
}

function paginator_head() 
{
	$url = getinfo('plugins_url').'paginator/';
	
	$options = mso_get_option('paginator', 'plugins', array() );
	if ( !isset($options['css']) ) $options['css'] = '1';
	if ( $options['css'] == '1' ) echo '<link rel="stylesheet" href="'.$url.'paginator.css">'.NR;
	
	echo '<script src="'.$url.'paginator.js"></script>'.NR;
}

function paginator_mso_options()
{
	mso_admin_plugin_options('paginator', 'plugins', 
		array(
			  'page_count' => array(
								'type' => 'text', 
								'name' => t('Количество отображаемых страниц'), 
								'description' => '', 
								'default' => '10'
								),
			  'lang_next' => array(
								'type' => 'text', 
								'name' => t('Записи новее'), 
								'description' => '',
								'default' => t('Следующая')
								),
			  'lang_prior' => array(
								'type' => 'text', 
								'name' => t('Записи старее'), 
								'description' => '',
								'default' => t('Предыдущая')
								),
			  'lang_first' => array(
								'type' => 'text', 
								'name' => t('Первая запись'), 
								'description' => '',
								'default' => t('Первая')
								),
			  'lang_last' => array(
								'type' => 'text', 
								'name' => t('Последняя запись'), 
								'description' => '',
								'default' => t('Последняя')
								),
			  'returnOrder' => array(
								'type' => 'checkbox',
								'name' => t('Выводить страницы в обратном порядке'),
								'description' => '',
								'default' => '0'
								),
			  'css' => array(
								'type' => 'checkbox', 
								'name' => t('Загружать дефолтные css-стили'), 
								'description' => t('Снимите галочку если будут использоваться собственные css-стили для пагинатора'),
								'default' => '1'
								)
			),
		t('Настройки плагина «Paginator»'),
		t('Укажите необходимые опции')
	);
}

function paginator_uninstall($args = array())
{	
	mso_delete_option('paginator', 'plugins' );
	return $args;
}

function paginator_go($r = array())
{
	global $MSO;

	$r_orig = $r;

	if (!$r) return $r;
	if ( !isset($r['maxcount']) || $r['maxcount'] == '1' ) return $r;
	if ( !isset($r['limit']) ) return $r;
	if ( !isset($r['type']) )  $r['type'] = false;
	if ( !isset($r['next_url']) ) $r['next_url'] = 'next';

	$options = mso_get_option('paginator', 'plugins', array() );

	if ( !isset($options['page_count']) ) $options['page_count'] = '10';
	if ( !isset($options['returnOrder']) ) $options['returnOrder'] = '0';
	if ( !isset($options['lang_next']) ) $options['lang_next'] = t('Следующая');
	if ( !isset($options['lang_prior']) ) $options['lang_prior'] = t('Предыдущая');
	if ( !isset($options['lang_last']) ) $options['lang_last'] = t('Последняя');
	if ( !isset($options['lang_first']) ) $options['lang_first'] = t('Первая');

	$current_paged = mso_current_paged($r['next_url']);
	if ($current_paged > $r['maxcount']) $current_paged = $r['maxcount'];

	if ($r['type'] !== false) $type = $r['type'];
		else $type = $MSO->data['type'];

	$a_cur_url = $MSO->data['uri_segment'];
	if ($type != 'page_404') $cur_url = getinfo('site_url').$type;
		else $cur_url = getinfo('site_url');

	foreach ($a_cur_url as $val)
	{
		if ($val == $r['next_url']) break;
		else
		{
			if ($val != $type) $cur_url .= '/@@' . $val;
		}
	}
	
	$cur_url = str_replace('//@@', '/', $cur_url);
	$cur_url = str_replace('@@', '', $cur_url);

	$returnOrder = ( $options['returnOrder'] == '1' ) ? 'true' : 'false';
	$id = mt_rand(1,999);

	echo '<script>$(document).ready(function() {$("#pag'.$id.'").paginator({pagesTotal:'.$r['maxcount'].', pagesSpan:'.$options['page_count'].', pageCurrent:'.$current_paged.', baseUrl:"'.$cur_url.'/next/", returnOrder:'.$returnOrder.', lang: {next:"'.$options['lang_next'].'", last:"'.$options['lang_last'].'", prior:"'.$options['lang_prior'].'", first:"'.$options['lang_first'].'", arrowRight:String.fromCharCode(8594), arrowLeft:String.fromCharCode(8592)}});})</script>'.NR.'<div id="pag'.$id.'" class="paginator"></div>'.NR;

	return $r_orig;
}

# end file