# Если вы хотите использовать свое оформление гостевой, то скопируйте файл guestbook.css в каталог своего шаблона.



# Данный плагин использует подключение к шаблону с помощью хука «custom_page_404», который появился в MaxSite CMS 0.32. Если у вас старая версия, то вам достаточно заменить в index.php своего шаблона строчку:

	else require($type_dir . 'page_404.php');
	
на

	else
	{
		if ( !mso_hook_present('custom_page_404') or !mso_hook('custom_page_404')) 
			require($type_dir . 'page_404.php');
	}
	

#