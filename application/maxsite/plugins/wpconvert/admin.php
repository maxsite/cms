<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	global $MSO;
	$CI = & get_instance();
	
	if (!defined('MAGPIE_CACHE_AGE'))	define('MAGPIE_CACHE_AGE', 1000); // время кэширования MAGPIE
	require_once(getinfo('common_dir') . 'magpierss/rss_fetch.inc');
	
	
	
	// проверка 
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_chek', 'f_xml_file')) )
	{
		mso_checkreferer();
		
		if (!isset($post['f_yes']))
		{
			echo '<div class="error">' . t('Угу, зщас... У тебя сайт накроется, кто отвечать будет?! В ЛЕС!!!') . '</div>';
		}
		else
		{
			$f_xml_file = $post['f_xml_file'];
			
			$fn = getinfo('uploads_dir') . $f_xml_file;
			$url = getinfo('uploads_url') . $f_xml_file;
			
			if (file_exists($fn))
			{
				//echo '<div class="update">Пошел процесс... Ждите...</div>';


				$all = fetch_rss($url);
				
				// pr($all);
				
				if ($all)
				{
					$categorys0 = unserialize($all->channel['categorys']);
					$categorys = array();
					$categorys_out = '';
					
					foreach ($categorys0 as $key=>$val)
					{
						# $categorys[$val->term_id] = array('id'=>$val->term_id, 'name'=>$val->name, 'slug'=>$val->slug ); // все рубрики
						$categorys_out .= $val->name . ' ('. $val->slug . ') | ';
					}
					
					/*
					$tags0 = unserialize($all->channel['tags']);
					$tags = array();
					$tags_out = '';
					
					foreach ($tags0 as $key=>$val)
					{
						$tags_out .= $val->name . ' | ';
					}
					*/
					
					$out = '<h2>' . t('Файл:') . ' ' . $f_xml_file . '</h2>';
					$out .= '<ul>';
						$out .= '<li><b>' . t('Сайт:') . '</b> ' . $all->channel['title'];
						$out .= '<br><b>' . t('Ссылка:') . '</b> ' . $all->channel['link'];					
						$out .= '<br><b>' . t('Все рубрики:') . '</b> ' . $categorys_out;					
						// $out .= '<br><b>Все метки:</b> ' . $tags_out;					
						$out .= '<br><b>' . t('Всего записей:') . '</b> ' . count($all->items);					
						$out .= '<br><br></li>';
					$out .= '</ul><br><hr><br>';
					
					
					$sql_count = count($categorys0) * 2; // расчет количества запросов к БД
					$slug_rename = 0; // переименованных slug
					$pages_count = 0; // всего записей
					$comments_count = 0; // всего клмментариев
					
					$out .= '<ul>';
					foreach($all->items as $item)
					{
						$status = $item['wp']['status'];
						
						if ($status == 'publish' or $status == 'static' or $status == 'draft')
						{
							// pr($item);
							if ($status == 'static')
							{
								$status = 'publish';
								$page_type_id = 'static'; // static
							}
							else
							{
								$page_type_id = 'blog'; // blog
							}
							
							$cat_tag = unserialize($item['category']);
							if (isset($cat_tag['category'])) $category = implode(' | ', $cat_tag['category']);
								else $category = '-';
							
							if (isset($cat_tag['tag'])) $tag = implode(' | ', $cat_tag['tag']);
								else $tag = '-';
							
							// pr($category);
							
							// $category = implode(' | ', unserialize($item['category']));
							
							if ($status == 'publish') $status = '<span style="color:green"><b>' . $status . '</b></span>';
							elseif ($status == 'draft') $status = '<span style="color:orange"><b>' . $status . '</b></span>';
							
							if ($page_type_id == 'static') $page_type_id = '<span style="color:orange"><b>' . $page_type_id . '</b></span>';
							else $page_type_id = '<span style="color:blue"><b>' . $page_type_id . '</b></span>';
							
							$post_date = $item['wp']['post_date'];
							if ($post_date == '0000-00-00 00:00:00') $post_date = date('Y-m-d H:i:s');
							
							if (!isset($item['title'])) $item['title'] = 'no-title';
							
							$out .= '<li><h2>' . $item['title'] . '</h2>';
							$out .= '<b>' . t('Статус:') . '</b> ' . $status;
							$out .= '<br><b>' . t('Тип страницы:') . '</b> ' . $page_type_id;
							$out .= '<br><b>' . t('Рубрики:') . '</b> ' . $category;
							$out .= '<br><b>' . t('Метки:') . '</b> ' . $tag;
							$out .= '<br><b>' . t('Дата:') . '</b> ' . $post_date ;
							$out .= '<br><b>' . t('Комментарии:') . '</b> ' . $item['wp']['comment_status'];
							
							if (!isset($item['wp']['post_name'])) $slug = mso_slug($item['title']);
								else $slug = urldecode($item['wp']['post_name']);
							
							$slug_new = mso_slug($slug);
							$out .= '<br><b>Slug:</b> ' . $slug;
							// если $slug = числу, то нужно его заменить на заголовок
							$i = (int) $slug;
							if ( (string) $slug != (string) $i ) $i = false; // slug не число
							if ($i) $slug_new = mso_slug($item['title']);
							if ($slug != $slug_new)
							{
								$out .= '<br><b>' . t('Новый slug:') . '</b> <span style="color: red"> ' . $slug_new . '</span>';
								$slug_rename++;
							}
							
							if (isset($item['content'])) $text = $item['content'];
							else $text = '';
							$out .= '<br><b>' . t('Текст:') . '</b> ' . htmlspecialchars(mso_str_word($text, 80)) . '&lt;...&gt;';
							
							$comments = array();
							if (isset($item['comments'])) $comments = @unserialize($item['comments']);
							
							
							$comments = count($comments);
							$out .= '<br><b>' . t('Комментарии:') . '</b> ' . $comments;
							
							
							$out .= '<br><br></li>';
							
							$sql_count = $sql_count + 8 + $comments;
							$comments_count = $comments_count + $comments;
							$pages_count++;
						}
					}

					$out .= '</ul>';
					
					echo $out . '<div class="update">' 
								. t('Готово! Проверка выполнена!<br>Предположительно запросов к БД будет:') 
								. ' ' . $sql_count 
								. '<br>' . t('Измененных slug (url):') . ' ' . $slug_rename 
								. '<br>' . t('Всего записей:') . ' ' . $pages_count 
								. '<br>' . t('Всего комментариев:') . ' ' . $comments_count 
								. '</div>';
				}
				else
				{
					echo '<div class="error">' . t('Что за ерунду ты мне подсовываешь? Файл-то пустой!') . '</div>';
				}
			}
			else // нет файла
			{
				echo '<div class="error">' . t('Файл') . ' <b>' . f_xml_file. '</b> ' . t('не найден! Загрузите его в каталог /uploads/ Можно через Загрузку.') . '</div>';
			}
		}
	}
	
	
	////////////////////////////////////////////////////////////////////////////
	// конвертер 
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_go', 'f_xml_file')) )
	{
		mso_checkreferer();
		
		if (!isset($post['f_yes']))
		{
			echo '<div class="error">' . t('Угу, сщас... У тебя сайт накроется, кто отвечать будет?! В ЛЕС!!!') . '</div>';
		}
		else
		{
			$f_xml_file = $post['f_xml_file'];
			
			$fn = getinfo('uploads_dir') . $f_xml_file;
			$url = getinfo('uploads_url') . $f_xml_file;
			
			if (file_exists($fn))
			{
			
				// попытаемся установить большое время выполнения скрипта
				@set_time_limit(0);
				@ini_set('max_execution_time', 0);
		
				// echo '<div class="update">Пошел процесс... Ждите...</div>';
				
				require_once( getinfo('common_dir') . 'category.php' );
				require_once( getinfo('common_dir') . 'functions-edit.php' );


				$all = fetch_rss($url);
				$out = '';
				if ($all)
				{
					
					$all_link_redirect = ''; // список всех адресов   старый | новый | 301
					$out = '<h2>' . t('Файл:') . ' ' . $f_xml_file . '</h2>';
					
					/*
						вначале получаем список своих рубрик.
						сравниваем их с входным
						меняем у них ключ slug array(slug_new и id)
						
						потом в процессе добавления записей можно будет ссылаться на slug 
						и получать сразу уже новые slug_new и id
						
						если таких рубрик нет, то добавляем их
					
					*/
					
					$categorys0 = unserialize($all->channel['categorys']);
					$categorys = array();
					
					$mycategorys = mso_cat_array_single('page', 'category_name', 'ASC', 'blog', false); // существующие рубрики
					
					$mycategorys0 = array();
					// обработаем массив для удобства
					foreach ($mycategorys as $key=>$val) $mycategorys0[$val['category_name']] = $val;
					$mycategorys = $mycategorys0;
					
					$new_categorys = array(); // массив в котором записываем только новые рубрики
					foreach ($categorys0 as $key=>$val)
					{
						// $categorys[$val->slug] = 
							// array('id_old'=>$val->term_id, 'name_old'=>$val->name, 'slug_old'=>$val->slug ); // все рубрики
						
						if ( !isset($mycategorys[$val->name]) ) // нет такой рубрики
							$new_categorys[$val->name] = array('name'=>$val->name, 'slug'=>mso_slug($val->slug));
					}
					
					// pr($new_categorys);
					
					if ($new_categorys)
					{
						$out .= '<h2>' . t('Добавленные рубрики') . '</h2>';
						foreach ($new_categorys as $val)
						{
							$result = mso_new_category( array( 'category_name'=>$val['name'], 'category_slug'=>$val['slug'] ) );
							
							if ($result['result']) 
							{
								$out .= '<span style="color:green">+ ' . $val['name'] . ' : ' . $result['description'] . '</span><br>';
							}
							else // какая-то ошибка
							{
								$out .= '<span style="color:red">- ' . $val['name'] . ' : ' . $result['description'] . '</span><br>';
							}
						}
						$out .= '<br><hr><br>';
					}
					
					mso_flush_cache(); // сбросим кэш
					$mycategorys = mso_cat_array_single('page', 'category_name', 'ASC', 'blog', false); // существующие рубрики
					$mycategorys0 = array();
					// обработаем массив для удобства
					foreach ($mycategorys as $key=>$val) $mycategorys0[$val['category_name']] = $val;
					
					$mycategorys = $mycategorys0; // рубрики готовы
								
					// pr($mycategorys);
					// pr($categorys);
					
					
					/*
						далее нужно добавить все записи и спазу же их комментарии
					*/
					
					$out .= '<h2>' . t('Добавленные страницы') . '</h2>';

					foreach($all->items as $item)
					{
						$status = $item['wp']['status'];
						
						if ($status == 'publish' or $status == 'static' or $status == 'draft')
						{
							
							if ($status == 'static')
							{
								$status = 'publish';
								$page_type_id = 2; // static
							}
							else
							{
								$page_type_id = 1; // blog
							}
							
							$cat_tag = unserialize($item['category']);
							
							if (isset($cat_tag['tag'])) $tag = implode(',', $cat_tag['tag']);
								else $tag = '';
							
							
							if (isset($cat_tag['category'])) $category = $cat_tag['category'];
								else $category = array();
							
							//pr($category);
							
							$cat1 = array();
							foreach($category as $key=>$cat)
							{
								$cat1[] = $mycategorys[$cat]['category_id'];
							}
							
							if (!isset($item['title'])) $item['title'] = 'no-title';
							
							if (isset($item['link'])) $old_link = $item['link'];
								else $old_link = '';
							
							if (!isset($item['wp']['post_name'])) $slug = mso_slug($item['title']);
								else $slug = urldecode($item['wp']['post_name']);

							$slug_new = mso_slug($slug);
							// если $slug = числу, то нужно его заменить на заголовок
							$i = (int) $slug;
							if ( (string) $slug != (string) $i ) $i = false; // slug не число
							if ($i) $slug_new = mso_slug($item['title']);
							
							$comment_allow = ($item['wp']['comment_status'] == 'open') ? '1' : '0';
							
							if (!isset($item['content'])) $content = '';
								else $content = $item['content'];
							
							$content = str_replace(chr(10), "<br>", $content);
							$content = str_replace(chr(13), "", $content);
							$content = str_replace('<!--more-->', '[cut]', $content);
							
							$post_date = $item['wp']['post_date'];
							if ($post_date == '0000-00-00 00:00:00') $post_date = date('Y-m-d H:i:s');
							
							$data = array(
								'page_id_autor' => $MSO->data['session']['users_id'],
								'page_title' => $item['title'],
								'page_content' => $content,
								'page_status' => $status,
								'page_type_id'=>$page_type_id,
								'page_slug' => $slug_new,
								'page_comment_allow' => $comment_allow,
								'page_tags' => $tag,
								'page_date_publish' => $post_date,
								'page_id_cat' => implode(',', $cat1),
								);
								
							
							$result = mso_new_page($data);
							
							if ($result['result']) 
							{
								$page_id = $result['result'][0];
								$out .= '<span style="color:green">+ ' . $item['title'] . ' : ' . $result['description'] 
										. ' (' . $page_id . ')</span><br>';
										
										
								$all_link_redirect .=  $old_link . ' | [URL-NEW]' . $result['result'][1] . ' | 301' . NR;
								
							}
							else // какая-то ошибка
							{
								$page_id = 0;
								$out .= '<span style="color:red">- ' . $item['title'] . ' : ' . $result['description'] . '</span><br>';
							}
							
							
							/*
							теперь под эту запись нужно создать комментарии
							*/
							
							$comments = array();
							if (isset($item['comments'])) $comments = @unserialize($item['comments']);

							if ($page_id and $comments) // есть комментарии
							{
							
								// pr($comments);
								
								foreach($comments as $comment)
								{
								
									if ($comment->comment_approved)
									{
										$ins_data = array (
											'comments_page_id' => $page_id,
											'comments_author_name' => $comment->comment_author,
											'comments_author_ip' => $comment->comment_author_IP,
											'comments_date' => $comment->comment_date,
											'comments_content' => $comment->comment_content,
											'comments_approved' => '1'
											);

											$res = ($CI->db->insert('comments', $ins_data)) ? '1' : '0';
									}
								}
							}
								
							
							
						}
					}

					echo $out . '<br><div class="update">' . t('Готово! Конвертирование выполнено!') . '</div>';
					
					echo '<p><b>Готовые редиректы</b></p><br><textarea rows="10" class="w100">' . $all_link_redirect . '</textarea><br><br>';
					
				}
				else
				{
					echo '<div class="error">' . t('Что за ерунду ты мне подсовываешь? Файл-то пустой!') . '</div>';
				}
			}
			else // нет файла
			{
				echo '<div class="error">' . t('Файл') . ' <b>' . f_xml_file. '</b> ' . t('не найден! Загрузите его в каталог /uploads/ Можно через Загрузку.') . '</div>';
			}
		}
	}	
	
	
	
?>
<h1>WordPress convert</h1>
<p class="info"><?= t('С помощью данной страницы вы можете конвертировать WordPress-данные для MaxSite CMS. Для начала вам нужно выполнить экспорт из WordPress. Для выполнения экспорта вам нужно скопировать файл export-max.php в каталог wp-admin. После этого наберите адрес http://сайт/wp-admin/export-max.php Выполните экспорт. Обратите внимание, что я проверял на WordPress 2.3.3. На других версиях ничего не гарантирую. Из-за некоторых ошибок и особенности формата, собственный wp-экспорт не подойдет для наших целей.') ?></p>

<p class="info"><?= t('Экспорт я рекомендую сделать частями так, чтобы размер одного файла не превышал 300-400Кб. При конвертировании это позволит уменьшить нагрузку на сервер, а также позволит обойти ограничения хостинга на время выполнения скриптов и максимальный размер файла. В итоге у вас получится несколько xml-файлов.') ?></p>

<p class="info"><?= t('Перед конвертацией вам следует открыть каждый xml-файл в FireFox. Если браузер ругается на какие-то ошибки, то вам следует их исправить прямо в файле. К сожалению WordPress может неверно формировать xml-файл, но я постарался исправить ошибки в своем export-max.php.') ?></p>

<p class="info"><?= t('Лишь только после того, как FireFox отобразит дерево элементов без ошибок, вы можете загрузить файл в каталог /uploads/. Можно через Загрузки.') ?></p>

<p class="info"><?= t('Перед началом конвертации нужно выполнить проверку. Для этого нажмите кнопку «Проверить файл». В результате вы увидите отчет о проверке. И лишь в случае отсутствия ошибок, можно запустить конвертацию.') ?></p>

<p class="info"><?= t('<b>Правила конвертирования.</b> Копируются все тексты, включая обычные записи и постоянные страницы. В записях сохраняется slug (короткая ссылка) при условии, что в системе еще нет такой. Если есть, то добавляется префикс 1, 2 и т.д. В комментариях копируется только текст и имя. Остальные данные не используются. Рубрики создаются по их названию. Если такое название уже есть, то используется существующая рубрика. Иерархия конвертируемых рубрик полностью теряется. Записи конвертируются только со статусом publish, static и draft.') ?></p>

<p class="info"><?= t('Обратите внимание, что процесс конвертирования очень ресурсоемкий. Прежде всего он потребует много php-памяти, а также множество SQL-запросов к БД. При конвертировании система попробует установить большее время выполнения php-скриптов, чтобы сервер принудительно не сбросил соединение. Однако не на всех хостингах такая возможность может сработать. Если сервар слабый, то он может не успеть обработать все SQL-запросы. В этом случае вам придется уменьшить размер xml-файла и попытаться выполнить конвертирование заново по частям.') ?></p>

<p class="info"><?= t('При конвертировании система автоматически проверяет уже существующие рубрики и записи. Если таковые уже есть, то они не добавляются. Это позволяет избежать дублирования. Ну и кроме того, вы можете не опасаться, что при повторной конвертации данные снова добавятся.') ?></p>

<p class="info"><?= t('После конвертирования можно деактивировать этот плагин, а также удалить xml-файлы. Также рекомендую очистить кэш: удалить файлы в <u>system/cache/rss/</u>') ?></p>

<p class="info"><?= t('После конвертирования старые адреса вида <u>http://site/slug</u> сохранятся. Но следует иметь ввиду, что на MaxSite CMS принята немного другая структура ссылок: <u>http://site/page/slug</u> (т.н. синонимы ссылок). Поэтому переживать, что ссылки на других ресурсах потеряются, не следует. При условии, конечно, то новый slug совпадает со старым (во время проверки файла это видно).') ?></p>

<p class="info"><?= t('<u>ВАЖНО!</u> Настоятельно рекомедую перед началом конвертирования <u>сделать дамп текущей базы данных</u>! В случае ошибок, вы быстро сможете восстановить прежнее состояние своего сайта. Не игнорируйте это замечание!') ?></p>




<?php
	
	if (!isset($f_xml_file)) $f_xml_file = '';
	
	// найдем все файлы по маске wp*.xml
	$CI->load->helper('directory');
	$dir = directory_map(getinfo('uploads_dir'), true); // только в текущем каталоге
	if (!$dir) $dir = array();
	natsort($dir);
	$option_files = '';
	foreach ($dir as $file)
	{
		if (@is_dir(getinfo('uploads_dir') . $file)) continue; // это каталог
		if (preg_match('|wp(.*?)\.xml|', $file)) 
		{
			if ($file == $f_xml_file) $option_files .= '<option selected value="' . $file . '"/>' . $file . '</option>';
			else $option_files .= '<option value="' . $file . '"/>' . $file . '</option>';
		}
	}
	
	
	echo '<br><form method="post">' . mso_form_session('f_session_id');
	echo '<label><input type="checkbox" name="f_yes" nochecked> ' . t('Я понял и согласен взять на себя всю ответственность за использование данного конвертера! Дамп также сделал и умею с ним работать') . '</label><br>';
	
	echo '<br>' . t('Выберите файл:') . ' <select style="width: 300px" name="f_xml_file">' . $option_files . '</select>';
	
	echo '<br><input type="submit" name="f_submit_chek" value="' . t('Проверить файл') . '" style="margin: 25px 0 5px 0;">';
	echo '<input type="submit" name="f_submit_go" value="' . t('Запустить конвертацию') . '" style="margin: 25px 0 5px 0;">';
	
	echo '</form>';

?>