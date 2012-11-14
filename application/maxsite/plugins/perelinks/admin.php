<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();

	$options_key = 'perelinks';

	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		$options = array();
		$options['linkcount'] = isset( $post['f_linkcount']) ? $post['f_linkcount'] : 0;
		$options['wordcount'] = isset( $post['f_wordcount']) ? $post['f_wordcount'] : 0;
		$options['allowlate'] = isset( $post['f_allowlate']) ? 1 : 0;
		$options['stopwords'] = isset( $post['f_stopwords']) ? $post['f_stopwords'] : 'будет нужно';

		mso_add_option($options_key, $options, 'plugins' );

		echo '<div class="update">' . t('Обновлено!') . '</div>';
	}


	echo '<h1>'. t('Плагин perelinks'). '</h1><p class="info">'. t('С помощью этого плагина вы можете сделать настраиваемую внутреннюю перелинковку.'). '</p>';

	$options = mso_get_option($options_key, 'plugins', array());
	$options['linkcount'] = isset($options['linkcount']) ? (int)$options['linkcount'] : 0;
	$options['wordcount'] = isset($options['wordcount']) ? (int)$options['wordcount'] : 0;
	$options['allowlate'] = isset($options['allowlate']) ? (int)$options['allowlate'] : 1;
	$options['stopwords'] = isset($options['stopwords']) ? $options['stopwords'] : 'будет нужно';

	$form = '';

	$form .= '<h2>' . t('Настройки') . '</h2>';

	$chk = $options['allowlate'] ? ' checked="checked"  ' : '';
	$form .= '<p><label><input name="f_allowlate" type="checkbox" ' . $chk . '> <strong>' . t('Ссылаться ли на более поздние записи') . '</strong></label><br>';
	$form .= t('Если отмечено, ссылаемся на любые записи кроме как из будущего. Иначе только на записи с более ранней датой, чем текущая запись.'). '</p>';

	$form .= '<p>&nbsp;</p><p><label><input name="f_linkcount" type="text" value="' . $options['linkcount'] . '"> <strong>' . t('Количество внутренних ссылок') . '</strong></label><br>';
	$form .= t('Количество внутренних ссылок на одной странице (ссылаться не больше чем на х страниц. 0 — без ограничений).'). '</p>';

	$form .= '<p>&nbsp;</p><p><label><input name="f_wordcount" type="text" value="' . $options['wordcount'] . '"> <strong>' . t('Ограничение вхождений слов') . '</strong></label><br>';
	$form .= t('0 — без ограничений. 1 — только первое одинаковое слово делать ссылкой. Дальнейшее не реализовано.'). '</p>';

	$form .= '<br><br><h2>' . t('Стоп-слова') . '</h2>';
	$form .= '<p>' . t('Список слов через пробел, которые не будут становиться ссылками.') . '</p>';
	$form .= '<textarea name="f_stopwords" rows="7" style="width: 99%;">';
	$form .= htmlspecialchars($options['stopwords']);
	$form .= '</textarea>';

	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<br><input type="submit" name="f_submit" value="' . t('Сохранить изменения') . '" style="margin: 25px 0 5px 0;">';
	echo '</form>';

?>