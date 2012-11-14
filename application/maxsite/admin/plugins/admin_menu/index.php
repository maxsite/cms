<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function admin_menu_autoload($args = array())
{	
	mso_hook_add( 'admin_menu', 'admin_menu_menu');
}


# выводит меню в админке
function admin_menu_menu($args = array()) 
{
	global $admin_menu, $MSO;
	
	$admin_url = getinfo('site_admin_url');
		
	$nr = "\n";
	$out = '';
	
	if ( count($MSO->data['uri_segment']) > 1 )
	{
		$cur_url2 = $MSO->data['uri_segment'][2]; // второй сегмент
		
		# текущий урл строится из сегментов от второго до последнего
		$cur_url = $MSO->data['uri_segment'];
		$cur_url = array_slice($cur_url, 1);
		$cur_url = implode('/', $cur_url);
	
		if (!$cur_url) $cur_url = 'home';
	}
	else  
	{
		$cur_url = 'home';
		$cur_url2 = 'home';
	}
	
	// если меню не содержит подменю, то не выводим его
	$admin_menu1 = $admin_menu; 
	
	foreach ($admin_menu1 as $key => $value)
		if (count($admin_menu1[$key])<2) unset($admin_menu1[$key]);
	
	// pr($admin_menu1);
	
	foreach ($admin_menu1 as $key => $value)
	{
		$out .= $nr . '<ul class="admin-menu admin-menu-' . ($key ? $key : 'beginning') . '">';
		$out .= $nr . '<li class="admin-menu-top"><a href="#" class="admin-menu-section">' . _mso_del_menu_pod($value['']) . '</a>';

		if (count($value)>1 )
		{
			$out .= $nr . '    <ul class="admin-submenu">';
			foreach ($value as $url => $name)
			{
				if ( $value[''] == $name ) continue;
				
				if ($url == $cur_url or $url == $cur_url2) 
				{
					$selected = ' class="admin-menu-selected admin-menu-' . mso_slug($url) . '"';
				}
				else 
				{
					$selected = ' class="admin-menu-' . mso_slug($url) . '"';
				}
				
				$out .= $nr . '      <li' . $selected . ' title="' . _mso_del_menu_pod($name) . '"><a href="' . $admin_url . $url . '">' . _mso_del_menu_pod($name) . '</a></li>';
			}
			$out .= $nr . '    </ul>';
		}
		$out .= $nr . '  </li>' . $nr . '</ul>' . $nr;
	}

	return $out;
}


# end file