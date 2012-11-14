<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function editor_markitup_autoload($args = array())
{
	mso_hook_add('editor_custom', 'editor_markitup'); # хук на подключение своего редактора
	//mso_hook_add('admin_page_form_q_files', 'editor_markitup_q_files'); # форма быстрой загрузки файлов
}

# функция выполняется при деинсталяции плагина
function editor_markitup_uninstall($args = array())
{	
	mso_delete_option('editor_markitup', 'plugins' ); // удалим созданные опции
	return $args;
}

function editor_markitup($args = array()) 
{
	$options = mso_get_option('editor_markitup', 'plugins', array() ); // получаем опции
	
	if (!isset($options['preview'])) $options['preview'] = 'aftertext';
	if (!isset($options['previewautorefresh'])) $options['previewautorefresh'] = 'no';
	if (!isset($options['previewPosition'])) $options['previewPosition'] = 'after';
	
	if ($options['preview'] == 'aftertext') $editor_config['preview'] = 'previewInWindow: "",';
		else $editor_config['preview'] = 'previewInWindow: "width=960, height=800, resizable=yes, scrollbars=yes",';
	
	if ($options['previewautorefresh'] == 'no') $editor_config['previewautorefresh'] = 'previewAutoRefresh: false,';
	else $editor_config['previewautorefresh'] = 'previewAutoRefresh: true,';
	
	if ($options['previewPosition'] == 'before') 
		$editor_config['previewPosition'] = 'previewPosition: "before",';
	else 
		$editor_config['previewPosition'] = 'previewPosition: "after",';
	
	
	$editor_config['url'] = getinfo('plugins_url') . 'editor_markitup/';
	$editor_config['dir'] = getinfo('plugins_dir') . 'editor_markitup/';

	if (isset($args['content'])) $editor_config['content'] = $args['content'];
	else $editor_config['content'] = '';
		
	if (isset($args['do'])) $editor_config['do'] = $args['do'];
		else $editor_config['do'] = '';
		
	if (isset($args['posle'])) $editor_config['posle'] = $args['posle'];
		else $editor_config['posle'] = '';	
		
	if (isset($args['action'])) $editor_config['action'] = ' action="' . $args['action'] . '"';
		else $editor_config['action'] = '';
	
	if (isset($args['height'])) $editor_config['height'] = (int) $args['height'];
	else 
	{
		$editor_config['height'] = (int) mso_get_option('editor_height', 'general', 400);
		if ($editor_config['height'] < 100) $editor_config['height'] = 400;
	}

	# Приведение строк с <br> в первозданный вид
	$editor_config['content'] = preg_replace('"&lt;br\s?/?&gt;"i', "\n", $editor_config['content']);
	$editor_config['content'] = preg_replace('"&lt;br&gt;"i', "\n", $editor_config['content']);


	// смайлы - код из comment_smiles
	$image_url=getinfo('uploads_url').'smiles/';
	$CI = & get_instance();
	$CI->load->helper('smiley_helper');
	$smileys=_get_smiley_array();
	$used = array();
	$smiles = '';
	foreach ($smileys as $key => $val)
	{
		// Для того, чтобы для смайлов с одинаковыми картинками (например :-) и :))
		// показывалась только одна кнопка
		if (isset($used[$smileys[$key][0]]))
		{
		  continue;
		}
		
		$im = "<img src='" . $image_url.$smileys[$key][0] . "' title='" . $key . "'>";
		$smiles .= '{name:"' .  addcslashes($im, '"') . '", notitle: "1", replaceWith:"' . $key . '", className:"col1-0" },' . NR;
		
		$used[$smileys[$key][0]] = TRUE;
	}
	if ($smiles)
	{
		$smiles = NR . "{name:'" . t('Смайлы') . "', openWith:':-)', closeWith:'', className:'smiles', dropMenu: [" 
				. $smiles
				. ']},';
	}
	
	require($editor_config['dir'] . 'editor-bb.php');
}

# опции редактора
function editor_markitup_mso_options() 
{
	mso_admin_plugin_options('editor_markitup', 'plugins', 
		array(
			/*
			'editor' => array(
							'type' => 'radio', 
							'name' => 'Редактор', 
							'description' => 'Выберите тип редактора. Для отображения BB-code на сайте требуется включить плагин <strong>BBCode</strong>',
							'values' => 'HTML # BB-CODE', 
							'default' => 'BB-CODE',
							'delimer' => '&nbsp;&nbsp;&nbsp;&nbsp;',
						),	
			)
			*/
			'preview' => array(
							'type' => 'radio', 
							'name' => t('Режим предпросмотра текста'), 
							'description' => '',
							'values' => t('aftertext||На странице редактора # win||В отдельном окне'), 
							'default' => 'aftertext',
							'delimer' => '&nbsp;&nbsp;&nbsp;&nbsp;',
						),
						
			'previewPosition' => array(
							'type' => 'radio', 
							'name' => t('Окно предпросмотра до или после текста'), 
							'description' => t('Будет работать только если используется предпросмотр в окне редактора'),
							'values' => t('after||Под текстовым редактором # before||Перед текстовым редактором'), 
							'default' => 'after',
							'delimer' => '&nbsp;&nbsp;&nbsp;&nbsp;',
						),
						
			'previewautorefresh' => array(
							'type' => 'radio', 
							'name' => t('Обновление предпросмотра'), 
							'values' => t('no||Обновлять предпросмотр вручную # yes||Использовать автообновление предпросмотра'), 
							'default' => 'no',
							'delimer' => '&nbsp;&nbsp;&nbsp;&nbsp;',
							'description' => '',
						),	
			
			),
			t('Настройки текстового редактора markItUp'), // титул
			t('Укажите необходимые опции.')   // инфо
	);

}

// форма быстрой загрузки файлов
function editor_markitup_q_files($out = '')
{
	$out = '';
	
	
	
	return $out;
}

# end file