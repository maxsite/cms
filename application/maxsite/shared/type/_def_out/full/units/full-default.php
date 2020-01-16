<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

// стандартный вывод full-записей

$p->div_start('mso-page-content mso-type-' . getinfo('type') . '-content');

if ($f = mso_page_foreach('content')) {
	require $f;
} else {
	// есть отметка не выводить миниатюру
	if ($p->meta_val('image_for_page_out') === 'no-page' or $p->meta_val('image_for_page_out') === '') {
		// если show_thumb_type_ТИП вернул false, то картинку не ставим
		// show_thumb - если нужно отключить для всех типов
		if (
			mso_get_val('show_thumb', true)
			and mso_get_val('show_thumb_type_' . getinfo('type'), true)
		) {
			// вывод миниатюры перед записью
			if ($image_for_page = thumb_generate(
				$p->meta_val('image_for_page'),
				mso_get_option('image_for_page_width', getinfo('template'), 280),
				mso_get_option('image_for_page_height', getinfo('template'), 210),
				false,
				'resize_full_crop_center',
				false,
				'mini',
				true,
				mso_get_option('upload_resize_images_quality', 'general', 90)
			)) {
				if (mso_get_option('image_for_page_link', getinfo('template'), 1)) {
					echo $p->page_url(true) . $p->img($image_for_page, mso_get_option('image_for_page_css_class', getinfo('template'), 'image_for_page'), '', $p->val('page_title')) . '</a>';
				} else {
					echo $p->img($image_for_page, mso_get_option('image_for_page_css_class', getinfo('template'), 'image_for_page'), '', $p->val('page_title'));
				}
			}
		}
	}

	$p->content('', '');
	// $p->clearfix();
}

// для page возможен свой info-bottom
if ($f = mso_page_foreach('info-bottom-' . getinfo('type'))) require $f;
elseif ($f = mso_page_foreach('info-bottom')) require $f;

$p->html('<aside>');

mso_page_content_end();

if ($f = mso_page_foreach('full-default-content-end')) {
	require $f;
} else {
	$p->clearfix();
	$p->line('[comments]');
}

$p->html('</aside>');

$p->div_end('mso-page-content mso-type-' . getinfo('type') . '-content');

# end of file
