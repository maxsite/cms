 * MaxSite CMS
 * (c) http://maxsite.org/
 * Класс для формирования thumb-изображений
 * указывается url входящего изображения
 * на выходе url нового изображения
 
			require_once(getinfo('templates_dir') . 'default/stock/thumb/thumb.php');
			
			// адрес должен быть в uploads!!!
			$img = 'http://сайт/uploads/файл';
			
			// первый параметр - адрес сайта 
			// второй постфикс для нового файла
			$t = new Thumb($img, '-205-145');
			
			if ($t->init === true) // уже есть готовое изображение в кэше
			{
				$new_img = $t->new_img; // сразу получаем новый адрес
			}
			elseif($t->init === false) // входящий адрес ошибочен
			{
				$new_img = false; // ошибка
			}
			else
			{	
				// работаеаем с изображением
				
				# $t->resize(205); // пропорциональное изменение по ширине
				
				# $t->resize(0, 145); // пропорция по высоте
				
				# $t->resize(205, 145); // точный размер
				
				# $t->crop(205, 145); // обрезка ширина - высота - от левого верхнего угла
				
				# $t->crop(205, 145, 30, 50); // смещение по x=30 y=50
				
				# $t->crop_center(205, 145); // кроп по центру
				
				# $t->resize_crop(205, 145); // вначале уменьшение по ширине, после обрезка от верхнего угла
				
				# $t->resize_crop(205, 145, 30, 50); // со смещением
				
				# $t->resize_crop_center(205, 145); // уменьшение по ширине, после кроп по центру
				
				# $t->resize_h_crop_center(205, 145); // уменьшение по высоте, после кроп по центру
				
				# $t->resize_full_crop_center(205, 145); // оптимальное заполнение без пустот resize + кроп по центру
				
				$new_img = $t->new_img; // url-адрес готового изображения
			}

			unset($t); // удалим созданный объект
			
			if ($new_img)
			{
				// адрес нового изображения
			}
			
			
			или так, совместно с page-out.php
			
			if (
				$img = thumb_generate(
				$p->meta_val('img_page_home'), // адрес из метаполя
				100, //ширина
				100, //высота
				getinfo('template_url') . 'images/image-pending-100-100.png', // если нет
				'resize_full_crop_center', 
				// тип формирования  
				// resize_crop crop_center crop resize resize_crop_center resize_h_crop_center resize_full_crop_center
				false // заменять кэш? true - для отладки
				))
			{
				// в $img готовый адрес
				
				$img = '<img src="' . $img . '" class="left" alt="" title="' . htmlspecialchars($p->val('page_title')). '">';
				
				$p->html($img);
			}
			
			