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
	
	echo '<h3>' . t('Текущий шаблон:') . ' <em>' . $current_template . '</em></h3>';
	
	if (file_exists($templates_dir . $current_template . '/screenshot.jpg'))
	{
		echo '<img class="template_current" src="' . $MSO->config['templates_url'] . $current_template . '/screenshot.jpg' . '" width="250" height="200" alt="" title="">';
	}	
	
	if (file_exists($templates_dir . $current_template . '/info.php'))
	{
		require($templates_dir . $current_template . '/info.php');
		echo '<p><a href="' . $info['template_url'] . '">' . $info['name'] . ' ' . $info['version'] . '</a>';
		echo '<br>' . $info['description'];
		echo '<br>' . t('Автор:') . ' <a href="' . $info['author_url'] . '">' . $info['author'] . '</a>';
		echo '</p>';
	}
		
	// все каталоги в массиве $dirs
	$dirs = directory_map($templates_dir, true);
	
	echo '<form method="post">' . mso_form_session('f_session_id');
	echo '<div class="float-parent options_templates">';
	
	foreach ($dirs as $dir)
	{
		if ($dir == $current_template) continue;
		
		// обязательный файл index.php
		$index = $templates_dir . $dir . '/index.php';
		
		
		if (file_exists($index))
		{
			$out = '<div class="template">';
			//$out .= '<h2>' . $dir . '</h2>';
			
			$screenshot = $templates_dir . $dir . '/screenshot.jpg';
			
			if (file_exists($screenshot))
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
			
			$out .= '<input type="submit" name="f_submit[' . $dir . ']" value="' . t('Выбрать этот шаблон') . '" style="margin: 10px;">';
			$out .= '</div>';

			echo $out;
		}
	}

	echo '</div>';
	echo '</form>';
	
?>