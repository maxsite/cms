<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 */

# функция автоподключения плагина
function comment_smiles_autoload($args = array())
{
	mso_hook_add( 'head', 'comment_smiles_head'); # хук на head шаблона - для JS
	mso_hook_add( 'admin_comment_edit', 'comment_smiles_head_admin_comment_edit'); # для JS админки
	mso_hook_add( 'comments_content_start', 'comment_smiles_custom', 1); # хук на форму
}

# подключаем JS в head
function comment_smiles_head($arg = array())
{
	if (!is_type('home') and !is_type('category') and !is_type('tag') and !is_type('archive') and !is_type('comments') and !is_type('contact') and !is_type('search') and !is_type('users'))
		echo '<script src="'. getinfo('plugins_url') . 'comment_smiles/comment_smiles.js"></script>' . NR;
}

function comment_smiles_head_admin_comment_edit($arg = array())
{
    echo '<script src="'. getinfo('plugins_url') . 'comment_smiles/comment_smiles.js"></script>' . NR;
}

# функции плагина
function comment_smiles_custom($arg = array())
{
	$image_url=getinfo('uploads_url').'smiles/';
	$CI = & get_instance();
	$CI->load->helper('smiley_helper');
	$smileys=_get_smiley_array();
  
	// идея Евгений - http://jenweb.info/page/hide-smileys, http://forum.max-3000.com/viewtopic.php?f=6&t=3192
	echo NR . '<div style="width: 19px; height: 19px; float: right; text-align: right; margin-top: -23px; cursor: pointer; background: url(\'' . getinfo('plugins_url') . 'comment_smiles/bg.gif\') no-repeat;" title="' . t('Показать/скрыть смайлики') . '" class="btn-smiles"></div>' . NR; 
  
	echo '<p style="padding-bottom:5px;" class="comment_smiles">';
  
	//кусок кода из smiley_helper
	$used = array();
	foreach ($smileys as $key => $val)
	{
		// Для того, чтобы для смайлов с одинаковыми картинками (например :-) и :))
		// показывалась только одна кнопка
		if (isset($used[$smileys[$key][0]])) continue;
			
		echo "<a href=\"javascript:void(0);\" onclick=\"addSmile('".$key."')\"><img src=\"".$image_url.$smileys[$key][0]."\" width=\"".$smileys[$key][1]."\" height=\"".$smileys[$key][2]."\" title=\"".$smileys[$key][3]."\" alt=\"".$smileys[$key][3]."\" style=\"border:0;\"></a> ";
		$used[$smileys[$key][0]] = TRUE;
	}
  
	echo '</p><script>$("p.comment_smiles").hide();</script>';
}

# end file
