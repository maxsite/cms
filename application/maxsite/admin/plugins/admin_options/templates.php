<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1><?= t('Шаблоны для сайта') ?></h1>
<p class="info"><?= t('Выберите нужный шаблон. Все шаблоны хранятся в каталоге <strong>«maxsite/templates»</strong>. Название шаблона совпадает с названием его каталога.') ?></p>

<?php 
	$CI = & get_instance();
	
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		$f_template = mso_array_get_key($post['f_submit']); 
		
		# еще раз проверим есть ли шаблон
		$index = $MSO->config['templates_dir'] . $f_template . '/index.php';
		
		if (file_exists($index))
		{
			mso_add_option('template', $f_template, 'general');
			$MSO->config['template'] = $f_template;
			echo '<div class="update">' . t('Обновлено!') . '</div>';
		}
		else
		{
			echo '<div class="error">' . t('Ошибка обновления') . '</div>';
		}
	}
	
	
	// получаем список каталогов 
	$CI->load->helper('directory');
	
	$templates_dir = $MSO->config['templates_dir'];
	
	$current_template = $MSO->config['template'];
	
	echo '<div class="mar30-b flex flex-wrap bg-gray100 pad20">
		<h4 class="w100 mar0-t">' . t('Текущий шаблон:') . ' ' . $current_template . '</h4>
		<div class="mar10-b">
		';
		
	if (file_exists($templates_dir . $current_template . '/screenshot.png'))
	{
		echo '<img src="' . $MSO->config['templates_url'] . $current_template . '/screenshot.png' . '" width="250" height="200">';
	}
	elseif (file_exists($templates_dir . $current_template . '/screenshot.jpg'))
	{
		echo '<img src="' . $MSO->config['templates_url'] . $current_template . '/screenshot.jpg' . '" width="250" height="200">';
	}
		
		echo '
		
		</div>
		<div class="flex-grow1 pad20-l">
		';
		
		
	if (file_exists($templates_dir . $current_template . '/info.php'))
	{
		require($templates_dir . $current_template . '/info.php');
		echo '<p><a href="' . $info['template_url'] . '">' . $info['name'] . ' ' . $info['version'] . '</a>';
		echo '<br>' . $info['description'];
		echo '<br>' . t('Автор:') . ' <a href="' . $info['author_url'] . '">' . $info['author'] . '</a>';
		
		if (isset($info['maxsite-min-version'])) echo '<br>' . t('Необходимая версия MaxSite CMS:') . ' ' . $info['maxsite-min-version'];
		
		echo '</p>';
	}
	
	echo '</div></div>';
	
	
	
	// все каталоги в массиве $dirs
	$dirs = directory_map($templates_dir, true);
	
	echo '<form method="post">' . mso_form_session('f_session_id');
	echo '<div class="options-templates flex flex-wrap">';
	
	foreach ($dirs as $dir)
	{
		if ($dir == $current_template) continue;
		
		// обязательный файл index.php
		$index = $templates_dir . $dir . '/index.php';
		
		
		if (file_exists($index))
		{
			$out = '<div class="mar30-b t-center pad20 shadow bg-gray100">';
			
			if (file_exists($templates_dir . $dir . '/screenshot.png'))
			{
				$screenshot = $MSO->config['templates_url'] . $dir . '/screenshot.png';
				$out .= '<img src="' . $screenshot . '" width="250" height="200" alt="' . $dir . '" title="' . $dir . '">';
			}
			elseif (file_exists($templates_dir . $dir . '/screenshot.jpg'))
			{
				$screenshot = $MSO->config['templates_url'] . $dir . '/screenshot.jpg';
				$out .= '<img src="' . $screenshot . '" width="250" height="200" alt="' . $dir . '" title="' . $dir . '">';
			}
			else
			{
				$out .= '<div class="template_noimage">' . t('Нет изображения') . '</div>';
			}
			
			$info_f = $templates_dir . $dir . '/info.php';
			if (file_exists($info_f))
			{
				require($info_f);
				$out .= '<p><a href="' . $info['template_url'] . '">' . $info['name'] . ' ' . $info['version'] . '</a>';
				$out .= '<br>' . $info['description'];
				$out .= '<br>' . t('Автор:') . ' <a href="' . $info['author_url'] . '">' . $info['author'] . '</a>';
				$out .= '</p>';
			}
			
			$out .= '<button type="submit" name="f_submit[' . $dir . ']" class="button i-check">' . t('Выбрать этот шаблон') . '</button>';
			
			$out .= '</div>';

			echo $out;
		}
	}

	echo '</div></form>';
	
?>