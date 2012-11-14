<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * For MaxSite CMS
 * Spoiler Plugin
 * Author: (c) Tux
 * Plugin URL: http://6log.ru/spoiler 
 */

# функция автоподключения плагина
function spoiler_autoload($args = array())
{
	mso_hook_add( 'head', 'spoiler_head');
	mso_hook_add( 'content', 'spoiler_custom'); # хук на вывод контента

	$options_key = 'plugin_spoiler';
	$options = mso_get_option($options_key, 'plugins', array());
	if ( isset($options['comments']) and ( $options['comments'] == 1) )
	{
		mso_hook_add( 'comments_content_out', 'spoiler_custom');
	}
}

# функция выполняется при деинсталяции плагина
function spoiler_uninstall($args = array())
{
	// константа
	$options_key = 'plugin_spoiler';

	mso_delete_option($options_key,'plugins');
	return $args;
}

# функции плагина
function spoiler_custom($text)
{
	// константа
	$options_key = 'plugin_spoiler';

	/* Настройки*/
	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['hide']) ) $options['hide'] = t('Скрыть');
	if ( !isset($options['show']) ) $options['show'] = t('Показать...');
	if ( !isset($options['comments']) ) $options['comments'] = 0;

	$showtext = $options['show'];
	$hidetext = $options['hide'];
   
	// dont edit!
	//$pattern = '@(\[spoiler\](.*?)\[/spoiler\])@is';
	$pattern = "@\[spoiler(=)?(.*?)\](.*?)\[\/spoiler\]@is";

	// замена  [spoiler]...[/spoiler] тегов
	if (preg_match_all($pattern, $text, $matches))
	{
		for ($i = 0; $i < count($matches[0]); $i++)
		{
			//$id   = 'id'.rand();
			$id = 'id' . rand(100,999);
			$html = '';
			
			if ($matches[1][$i] == '=')
			{
				if ( strpos($matches[2][$i], "/") !== false )
				{
					$matches[2][$i] = str_replace("'", "\'", $matches[2][$i]);
					$matches[2][$i] = str_replace("\"", "&quot;", $matches[2][$i]);	
					
					$tm = explode("/", $matches[2][$i]);
					if ( strpos($matches[2][$i], "/") === 0 )
					{
						$hidetext = $tm[1];
						$showtext = $options['show'];
					}
					else
					{
						$hidetext = $tm[1];
						$showtext = $tm[0];
					}
				} 
				else
				{
					$showtext = $matches[2][$i];
					$hidetext = $options['hide'];
				}
			}
			else
			{
				$showtext = $options['show'];
				$hidetext = $options['hide'];			
			}
			  
			$html .= '<p class="spoiler"><a class="spoiler_link_show" href="javascript:void(0)" onclick="SpoilerToggle(\'' . $id . '\', this, \'' . $showtext.'\', \'' . $hidetext . '\')">' . $showtext . '</a></p>';

			$html .= '<div class="spoiler_div" id="' . $id . '" style="display:none"><p>' . $matches[3][$i] . '</p></div>';

			//$text = str_replace($matches[0][$i], $html, $text);
			
			$text = preg_replace($pattern, $html, $text, 1);
		}
    }

    return $text;
}

# JavaScript & css text добавляем в head
function spoiler_head($args = array())
{
	$options_key = 'plugin_spoiler';
	$options = mso_get_option($options_key, 'plugins', array());
	
	if ( !isset($options['style'])  ) $options['style'] = '';
	if ($options['style'] != '')
	{
		echo '
		<link rel="stylesheet" href="' . getinfo('plugins_url') . 'spoiler/style/'.$options['style']. '">';
	}	
	
	echo '	
	<script>
	
	function SpoilerToggle(id, link, showtext, hidetext)
	{
		var spoiler = document.getElementById(id);
    	if (spoiler.style.display != "none")
		{
           	spoiler.style.display = "none";
            link.innerHTML = showtext;
            link.className = "spoiler_link_show";
        }
		else
		{
	       	spoiler.style.display = "block";
            link.innerHTML = hidetext;
            link.className = "spoiler_link_hide";
        }
    }
	</script>
	';
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function spoiler_mso_options() 
{

	//// Взято из wp-converter
	$CI = & get_instance();
	// найдем все файлы по маске *.css
	$CI->load->helper('directory');
//	$dir = directory_map(getinfo('uploads_dir'), true); // только в текущем каталоге
	$path = getinfo('plugins_dir').'spoiler/style/';
	$dir = directory_map($path, true);
	
	if (!$dir) $dir = array();
	natsort($dir);
	$option_select = '';
	$option_select .= '||' . t('без стилей');

	foreach ($dir as $file)
	{
		if (@is_dir(getinfo('plugins_url').'spoiler/style/' . $file)) continue; // это каталог
		if (preg_match('|(.*?)\.css|', $file)) 
		{
			$option_select .= '#'. $file . '||' . $file;
		}
	}
////	
    # ключ, тип, ключи массива
    mso_admin_plugin_options('plugin_spoiler', 'plugins', 
        array(
            'hide' => array(
                            'type' => 'text', 
                            'name' => t('Спрятать:'), 
                            'description' => t('Можно настроить какой текст появится в раскрытом виде'), 
                            'default' => t('Скрыть')
                        ),
            'show' => array(
                            'type' => 'text', 
                            'name' => t('Показать:'), 
                            'description' => t('Можно настроить какой текст появится в скрытом виде'), 
                            'default' => t('Показать...')
                        ), 
            'style' => array(
                            'type' => 'select', 
                            'name' => t('Файл стилей:'), 
                            'description' => t('Стили лежат в следеющей папке: (.../plugins/spoiler/style/)'),
							'values' => $option_select,
                            'default' => ''
                        ),
            'comments' => array(
                            'type' => 'checkbox', 
                            'name' => t('Использовать спойлеры в комментариях'), 
                            'description' => t(' '), 
                            'default' => 0
                        ), 
            ),
		t('Настройки плагина Spoiler'), // титул
		t('<p>С помощью этого плагина вы можете скрывать текст под спойлер.<br>Для использования плагина обрамите нужный текст в код [spoiler]ваш текст[/spoiler]</p><p class="info">Также возможны такие варианты: <br>[spoiler=показать]ваш текст[/spoiler], [spoiler=показать/спрятать]ваш текст[/spoiler], [spoiler=/спрятать]ваш текст[/spoiler]</p>')  // инфа
    );
}

# end file