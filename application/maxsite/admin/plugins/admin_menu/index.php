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
	global $admin_menu, $admin_menu_bread, $MSO, $admin_menu_select_option;
	
	$admin_url = getinfo('site_admin_url');
	
	$admin_menu_bread = array(); // хлебные крошки
	
	$nr = "\n";
	$out = '';
	
	$admin_menu_select_option = ''; // <option> для <select>
	
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
		
		// обрамляющий ul.admin-menu — выводим в конце цикла, чтобы задать css-классы
		$out_block_css_class = 'admin-menu admin-menu-' . ($key ? $key : 'beginning');
		
		$out1 = $nr . '<li class="admin-menu-top"><a href="#" class="admin-menu-section admin-menu-section-' . ($key ? $key : 'beginning') . '">' . _mso_del_menu_pod($value['']) . '</a>';
		
		$admin_menu_select_option .= '<optgroup label="' . htmlspecialchars(_mso_del_menu_pod($value[''])) . '">'. NR;
		
		if (count($value)>1 )
		{
			
			$out1 .= $nr . '<ul class="admin-submenu">';
			
			foreach ($value as $url => $name)
			{
				if ( $value[''] == $name ) continue;

				$opt_selected = '';
				
				if ($url == $cur_url or $url == $cur_url2) 
				{
					$selected = ' class="admin-menu-selected admin-menu-' . mso_slug($url) . '"';
					
					$opt_selected = ' selected';
					
					$out_block_css_class .= ' admin-menu-top-selected';
					
					if (!$admin_menu_bread) // хлебные крошки
					{
						$admin_menu_bread[] = _mso_del_menu_pod($value['']);
						$admin_menu_bread[] = _mso_del_menu_pod($name);
					}
				}
				elseif ($key == 'page' and $cur_url2 == 'page_edit') // редактирование записи
				{
					$out_block_css_class .= ' admin-menu-top-selected';
					$selected = ' class="admin-menu-' . mso_slug($url) . '"';
				}
				else 
				{
					$selected = ' class="admin-menu-' . mso_slug($url) . '"';
				}
				
				$out1 .= $nr . '      <li' . $selected . ' title="' . _mso_del_menu_pod($name) . '"><a href="' . $admin_url . $url . '">' . _mso_del_menu_pod($name) . '</a></li>';
				
				$admin_menu_select_option .= '<option value="' . $admin_url . $url . '"' . $opt_selected . '>' ._mso_del_menu_pod($name) . '</option>'. NR;
			}
			$out1 .= $nr . '</ul>';
		}
		
		$out1 .= $nr . '  </li>';
		
		$out .= $nr . '<ul class="' . $out_block_css_class . '">' . $out1 . '</ul>' . $nr;
	}
	
	
	// пункты в настройки админ-панели в отдельную опцию
	// добавляется в самый верх в Избранное
	$admin_menu_favorites = trim(mso_get_option('admin_menu_favorites', 'general'));

	if ($admin_menu_favorites)
	{
		$menu = str_replace("\r", "", $admin_menu_favorites); // если это windows
		$menu = str_replace("_NR_", "\n", $menu);
		$menu = str_replace(" ~ ", "\n", $menu);
		$menu = str_replace("\n\n\n", "\n", $menu);
		$menu = str_replace("\n\n", "\n", $menu);

		$menu = explode("\n", trim($menu));
		
		$out_my = '<ul class="admin-menu admin-menu-favorites"><li class="admin-menu-top"><a href="#" class="admin-menu-section admin-menu-section-favorites">' . t('Избранное') . '</a><ul class="admin-submenu">';
			
		$my_admin_menu_select_option = '<optgroup label="' . htmlspecialchars(t('Избранное')) . '">';
			
		foreach ($menu as $elem)
		{
			# разобъем строчку по адрес | название
			$elem = explode('|', trim($elem));
			$elem = array_map('trim', $elem);
			$elem = array_unique($elem); 
			
			$url = $name = $attr = '';
			
			if (isset($elem[0])) $url = $elem[0];  // адрес
			if (isset($elem[1])) $name = $elem[1];  // название
			if (isset($elem[2]) and $elem[2]) $attr = ' ' . $elem[2];  // атрибуты
			
			$out_my .= '<li title="' . $name .'"><a href="' . $url . '"' . $attr . '><span>' . $name .'</span></a></li>';
			
			$my_admin_menu_select_option .= '<option value="' . $url . '"' . '>' . $name . '</option>';
		}
		
		$out_my .= '</ul></li></ul>';
			
		$admin_menu_select_option = $my_admin_menu_select_option . $admin_menu_select_option;
			
		$out = $out_my . $out;
	}
	
	return $out;
}


# end of file