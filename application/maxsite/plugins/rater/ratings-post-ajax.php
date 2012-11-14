<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	global $_COOKIE;

	// if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) die('AJAX Error');

	mso_checkreferer(); // защищаем реферер

	if ( $post = mso_check_post(array('rating', 'slug')) )
	{
		// данные хранятся в куках посетителя - алгоримт тотже, что и в mso_page_view_count_first
		$name_cookies = 'maxsite_rating';
		$expire = 60 * 60 * 24 * 30; // 30 дней = 2592000 секунд

		if (isset($_COOKIE[$name_cookies]))	$all_slug = $_COOKIE[$name_cookies]; // значения текущего кука
			else $all_slug = ''; // нет такой куки вообще

		$slug = $post['slug']; // слаг страницы откуда пришел запрос

		$all_slug = explode(' ', $all_slug); // разделим в массив

		if ( in_array($slug, $all_slug) ) // уже есть текущий урл - не увеличиваем счетчик
		{
			echo '<span>' . t('Вы уже голосовали!') . '</span>';
			return;
		}

		$rating = (int) $post['rating']; // выставленная оценка

		if ($rating) // есть присланная оценка
		{
			// нужно обновить рейтинг
			$all_slug[] = $slug; // добавляем текущий id
			$all_slug = array_unique($all_slug); // удалим дубли на всякий пожарный
			$all_slug = implode(' ', $all_slug); // соединяем обратно в строку
			$expire = time() + $expire;
			@setcookie($name_cookies, $all_slug, $expire); // записали в куку

			// запишем значение в БД - нужно взять текущее
			// и вычислить среднюю на основе page_rating и page_rating_count

			$CI = & get_instance();

			$CI->db->select('page_rating, page_rating_count, page_slug');
			$CI->db->where('page_id', $slug);
			$CI->db->limit(1);

			$query = $CI->db->get('page');

			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				$page_rating = $row->page_rating; // текущая оценка
				$page_rating_count = $row->page_rating_count; // колличество проголосовавших

				// средняя оценка вычисляется как $page_rating деленное на $page_rating_count
				// но в $page_rating хранится сумма всех оценок!
				$page_rating = $page_rating + $rating;
				$page_rating_count++;


				$CI->db->where('page_id', $slug);
				$CI->db->update('page', array('page_rating'=>$page_rating, 'page_rating_count' => $page_rating_count) );
				
				# $CI->db->cache_delete_all();
				$CI->db->cache_delete('page', $row->page_slug);
				$CI->db->cache_delete('ajax', base64_encode('plugins/rater/ratings-post.php'));
				
				// обнуление рейтинга всех записей
				// $CI->db->update('page', array('page_rating'=>0, 'page_rating_count'=>0) );

				$sredn = round($page_rating / $page_rating_count);

				echo '<span>' . t('Ваша оценка:') . '</span> ' . $rating . '<br><span>' 
							. t('Средняя оценка') . '</span>: ' . $sredn 
							. ' ' . t('из') . ' ' . $page_rating_count . ' ' 
							. t('проголосовавших');
				
				mso_hook('global_cache_all_flush'); // сбрасываем весь html-кэш
			
			}
		}
	}
?>