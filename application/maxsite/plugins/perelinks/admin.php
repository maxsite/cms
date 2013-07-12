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

	$chk = $options['allowlate'] ? ' checked="checked"  ' : '';
	
	$form .= '<p class="hr"><label><input name="f_allowlate" type="checkbox" ' . $chk . '> ' . t('Ссылаться ли на более поздние записи') . '</label></p>';
	
	$form .= '<p class="fhint">' . t('Если отмечено, ссылаемся на любые записи кроме как из будущего. Иначе только на записи с более ранней датой, чем текущая запись.'). '</p>';

	$form .= '<p class="hr"><label class="fheader" for="f_linkcount">' . t('Количество внутренних ссылок') . '</label></p>
			<p><span><input name="f_linkcount" id="f_linkcount" type="text" value="' . $options['linkcount'] . '"></span></p>';
	
	$form .= '<p class="fhint">' . t('Количество внутренних ссылок на одной странице (ссылаться не больше чем на X страниц. 0 — без ограничений).'). '</p>';

	$form .= '<p class="hr"><label class="fheader" for="f_wordcount">' . t('Ограничение вхождений слов') . '</label></p>
		<p><span><input name="f_wordcount" id="f_wordcount" type="text" value="' . $options['wordcount'] . '"></span></p>';
	
	$form .= '<p class="fhint">' . t('0 — без ограничений. 1 — только первое одинаковое слово делать ссылкой. Дальнейшее не реализовано.'). '</p>';

	$form .= '<p class="hr header"><span>' . t('Стоп-слова') . '</span></p>';
	$form .= '<p><textarea name="f_stopwords" rows="10">' . htmlspecialchars($options['stopwords']) . '</textarea></p>';
	$form .= '<p class="fhint">' . t('Список слов через пробел, которые не будут становиться ссылками.') . '</p>';

	echo '<form class="fform" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<br><button type="submit" name="f_submit" class="i save">' . t('Сохранить изменения') . '</button>';
	echo '</form>';

# end file