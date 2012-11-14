<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	

	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	$CI->load->library('table');
	$CI->load->helper('directory');
	$CI->load->helper('form');
	
	
	$path = $MSO->config['uploads_dir'];
	$current_dir = '';
	
	// разрешенные типы файлов
	$allowed_types = 'gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|flv|swf|mp3|wav|xls|7z';
	
	
	/*	
	// по сегменту определяем текущий каталог в uploads
	// если каталога нет, скидываем на дефолтный ''
	
	$current_dir = $current_dir_h2 = mso_segment(3);
	if ($current_dir) $current_dir .= '/';
	
	$path = $MSO->config['uploads_dir'] . $current_dir;
	if ( ! is_dir($path) ) // нет каталога
	{
		$path = $MSO->config['uploads_dir'];
		$current_dir = $current_dir_h2 = '';
	}
	else
	{
		if ($current_dir_h2) $current_dir_h2 = '/' . $current_dir_h2;
	}
	
	echo '<h2>Текущий каталог: uploads' . $current_dir_h2 . '</h2>';
	
	// нужно вывести навигацию по каталогам в uploads
	$all_dirs = directory_map($MSO->config['uploads_dir'], true); // только в uploads
	$out = '';
	foreach ($all_dirs as $d)
	{
		// это каталог
		if (is_dir( getinfo('uploads_dir') . $d) and $d != '_mso_float' and $d != 'mini' and $d != '_mso_i' and $d != 'smiles') 
		{
			$out .= '<a href="'. $MSO->config['site_admin_url'] . 'files/' . $d . '">' . $d . '</a>   ';
		}
	}
	if ($out) 
	{
		$out = '<a href="'. $MSO->config['site_admin_url'] . 'files">uploads</a>   ' . $out;
		$out = str_replace('   ', ' | ', trim($out));
		$out = '<p>Навигация: ' . $out . '</p>';
		echo $out;
	}
	*/
	
	// описания файлов хранятся в виде серилизованного массива в
	// uploads/_mso_i/_mso_descritions.dat
	$fn_mso_descritions = $path . '_mso_i/_mso_descriptions.dat';
	
	if (!file_exists( $fn_mso_descritions )) // файла нет, нужно его создать
		write_file($fn_mso_descritions, serialize(array())); // записываем в него пустой массив
	
	if (file_exists( $fn_mso_descritions )) // файла нет, нужно его создать
	{
		// массив данных: fn => описание )
		$mso_descritions = unserialize( read_file($fn_mso_descritions) ); // получим из файла все описания
	}
	else $mso_descritions = array();
	

	$tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="100%"><colgroup width="100">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );

	$CI->table->set_template($tmpl); // шаблон таблицы
	
	// заголовки
	// $CI->table->set_heading('', 'Коды для вставки');
	
	// проходимся по каталогу аплоада и выводим их списком
	
	$uploads_dir = $MSO->config['uploads_dir'] . $current_dir;
	$uploads_url = $MSO->config['uploads_url'] . $current_dir;
	
	// все файлы в массиве $dirs
	$dirs = directory_map($uploads_dir, true); // только в текущем каталоге
	
	if (!$dirs) $dirs = array();
	
	sort($dirs);

	$allowed_ext = explode('|', $allowed_types);
	
	foreach ($dirs as $file)
	{
		if (@is_dir($uploads_dir . $file)) continue; // это каталог
		
		$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		if ( !in_array($ext, $allowed_ext) ) continue; // запрещенный тип файла
		
		$cod = '';
		
		if (isset($mso_descritions[$file])) $title = $mso_descritions[$file];
			else $title = '';
		
		$sel =  form_checkbox('f_check_files[]', $file, false, 'class="check_files" title="' . $title . '"') . ' <b>' . $file . '</b>';
		
		$cod1 = stripslashes(htmlspecialchars( $uploads_url . $file ) );
		
		if ($title) $cod .= '<p><input type="text" style="wi1dth: 99%;" value="' . $title . '">';
		
		$cod .= '<p><input type="text" style="w1idth: 99%;" value="' . $cod1 . '">';
		
		if ($title) $cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '">' . $title . '</a>') );
			else $cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '">' . $file . '</a>') );
		
		$cod .= '<p><input type="text" style="wid1th: 99%;" value="' . $cod2 . '">';
		
		if ( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png'  )
		{
			if (file_exists( $uploads_dir . '_mso_i/' . $file  )) $_f = '_mso_i/' . $file;
			else $_f = $file;
			
			if (file_exists( $uploads_dir . 'mini/' . $file  ))
				$file_mini = '=' . $uploads_url . 'mini/' . $file;
			else $file_mini = '=' . $uploads_url . $file;
			
			// $cod3 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '"><img src="' . $uploads_url . $file . '"></a>') );
			//$cod .= '<p><input type="text" style="width: 99%;" value="' . $cod3 . '">';
			
			if ($title)
				$cod3 = stripslashes(htmlspecialchars( '[image' . $file_mini . ' ' . $title . ']' . $uploads_url . $file . '[/image]') );
			else
				$cod3 = stripslashes(htmlspecialchars( '[image' . $file_mini . ']' . $uploads_url . $file . '[/image]') );
			
			$cod .= '<p><input type="text" style="wi1dth: 99%;" value="' . $cod3 . '">';
			
			$predpr = '<a class="lightbox" href="' . $uploads_url . $file . '" target="_blank" title="' . $title . ' ('. $file . ')' . '"><img style="max-width: 100px; margin: 0 auto; display: block;" src="' . $uploads_url . $_f . '"></a>';
			
		}
		else
		{
			$predpr = '<a href="' . $uploads_url . $file . '" target="_blank" title="' . $title . ' ('. $file . ')' . '"><img style="max-width: 100px; margin: 0 auto; display: block;" src="' . getinfo('admin_url') . 'plugins/admin_files/document_plain.png"></a>';
			
		}
		
		$CI->table->add_row($predpr, $sel . $cod);
	}
	
	if (count($CI->table->rows) > 0)
	{
	
		// добавляем форму, а также текущую сессию
		//echo '<form method="post">' . mso_form_session('f_session_id');
		echo $CI->table->generate(); // вывод подготовленной таблицы
		//echo '</form>';
		
		$n = '\n';
		$up = $uploads_url;
		$mess = t('Предварительно нужно выделить файлы для галереи');
		
		echo <<<EOF
		<script>
			$(function()
			{
				$('#gallerycodeclick').click(function()
				{ 
					$('#gallerycode').html('');
					
					codegal = '';
					$("input[name='f_check_files[]']").each( function(i)
					{ 
						if (this.checked)
						{
							t = this.title;
							if (!t) { t = this.value; }
							codegal = codegal + '[gal={$up}mini/' + this.value + ' ' + t + ']{$up}'+ this.value +'[\/gal]{$n}';
						}
					});
					
					if ( codegal ) 
					{
						n = $('#gallerycodename').val();
						if (n) { n = '[galname]' + n + '[/galname]';}
						else { n = ''; }
						
						codegal = '[gallery]' + n + '{$n}'+ codegal + '[/gallery]';
						$('#gallerycode').html(codegal);
						$('#gallerycode').css({ background: '#F0F0F0', width: '95%', height: '150px',
												border: '1px solid gray', margin: '20px 0', 
												'font-family': 'Courier New',
												'font-size': '9pt'});
						$('#gallerycode').fadeIn('slow');
						$('#gallerycode').select();
					}
					else
					{
						$('#gallerycode').hide();
						alert('{$mess}');
					}
				});
			});
		</script>
		<br><hr>
EOF;
		echo '
		<p>' . t('Выделите нужные файлы. (У вас должен быть активирован плагин <strong>LightBox</strong>)') . '</p>
		<p><input type="button" id="gallerycodeclick" value="' . t('Генерировать код галереи') . '">
		' . t('Название:') . ' <input type="text" id="gallerycodename" style="width: 200px" value=""> ' . t('(если нужно)') . '</p>
		<p><textarea id="gallerycode" style="display: none"></textarea>
		';
	}
	else
	{
		echo '<p>' . t('Нет файлов для отображения') . '</p>';
	}
	
?>