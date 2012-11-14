<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Плагин голосования от Евгения Самборского
 * Работы по разработке начаты 4 апреля 2009 года
 * sp = {Samborsky Polls}
 */

# функция автоподключения плагина
function samborsky_polls_autoload($args = array())
{

	if( is_type('admin') )
	{
		// хук на админку
		mso_hook_add('admin_init','samborsky_polls_init');
	}
	
	// Ядро
	require(getinfo('plugins_dir') . 'samborsky_polls/sp_kernel.php');
	
	// Хук в <head></head>
	mso_hook_add('head', 'samborsky_polls_head');
	mso_hook_add('custom_page_404', 'samborsky_polls_archive_404'); # По какому адресу будем показывать архив
	mso_register_widget('samborsky_polls_widget', t('Голосования')); # регистрируем виджет
}

function samborsky_polls_head($args = array()){
	
	mso_load_jquery();
	
	$path = getinfo('plugins_url') . 'samborsky_polls/';
	
	echo <<<EOFS
		
	<script src="{$path}js/kernel.js"></script>
	<link rel="stylesheet" href="{$path}css/style.css">
	
EOFS;
}

# функция выполняется при активации (вкл) плагина
function samborsky_polls_activate($args = array()){
	mso_create_allow('samborsky_polls_edit','Админ-доступ к samborsky_polls','plugins');
	
	require(getinfo('plugins_dir') . 'samborsky_polls/install.php');
	
	sp_install();
	sp_add_options();
	
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function samborsky_polls_deactivate($args = array()){
	// ничего не трогаем при деактивации
	return $args;
}

# функция выполняется при деинстяляции плагина
function samborsky_polls_uninstall($args = array()){
	
	$CI = &get_instance();
	$CI->load->dbforge();
	
	// Удаляем таблицы
	$CI->dbforge->drop_table('sp_questions');
	$CI->dbforge->drop_table('sp_answers');
	$CI->dbforge->drop_table('sp_logs');

	return $args;
}

# функция выполняется при указаном хуке admin_init
function samborsky_polls_init($args = array()){
	
	if( !mso_check_allow('samborsky_polls_edit') ){
		return $args;
	}
	
	$this_plugin_url = 'samborsky_polls';

	mso_admin_menu_add('plugins',$this_plugin_url,t('Голосования'));
	mso_admin_url_hook($this_plugin_url, 'samborsky_polls_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function samborsky_polls_admin_page($args = array()){
	# выносим админские функции отдельно в файл	
	if( !mso_check_allow('samborsky_polls_edit') ){
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic('mso_admin_header',' return $args . "' . t('Голосования') . '"; ' );
	mso_hook_add_dinamic('admin_title',' return "' . t('Голосования') . ' - " . $args; ' );
	
	require(getinfo('plugins_dir') . 'samborsky_polls/admin.php');
}

/***
 * Выводит голосование либо результаты голосования (если определено что юзер голосовал)
 * @return 
 * @param object $id[optional] - необязательный параметр, который выводит голосование с нужным ID
 */
function samborsky_polls($id = 0){
	$question = new sp_question($id);
	return $question->get_active_code();
}

/***
 * Выводит архив голосований
 * @return 
 */
function samborsky_polls_archive(){
	$archive = new sp_archive;
	return $archive->get();
}


function samborsky_polls_archive_404($args = array())
{
	$archive_url = mso_get_option('plugin_samborsky_polls', 'plugins', array('archive_url'=>'polls-archive'));
	if (mso_segment(1) == $archive_url['archive_url'])
	{
		if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);
		echo samborsky_polls_archive();
		if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
		
		# по хуку custom_page_404 нужно возвращать true, если не требуется обработка по page_404
		return true; 
	}
	
	return $args; 
}


/*  добавил виджет MAX   */

# функция, которая берет настройки из опций виджетов
function samborsky_polls_widget($num = 1) 
{
	$widget = 'samborsky_polls_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	
	return samborsky_polls_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function samborsky_polls_widget_form($num = 1) 
{
	$widget = 'samborsky_polls_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['polls_id']) ) $options['polls_id'] = '';
	if ( !isset($options['text_posle']) ) $options['text_posle'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Номер голосования'), form_input( array( 'name'=>$widget . 'polls_id', 'value'=>$options['polls_id'] ) ), '');
	
	$form .= mso_widget_create_form(t('Текст после'),form_textarea( array( 'name'=>$widget . 'text_posle', 'value'=>$options['text_posle'], 'style'=>'height: 100px;' ) ) , '');
			
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function samborsky_polls_widget_update($num = 1) 
{
	$widget = 'samborsky_polls_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['polls_id'] = (int) mso_widget_get_post($widget . 'polls_id');
	$newoptions['text_posle'] = mso_widget_get_post($widget . 'text_posle');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function samborsky_polls_widget_custom($options = array(), $num = 1)
{
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['polls_id']) ) $options['polls_id'] = 0;
	if ( !isset($options['text_posle']) ) $options['text_posle'] = '';

	$out = samborsky_polls((int) $options['polls_id']);
	
	if($out and $options['header']) $out = $options['header'] . $out;
	if ($out) $out .= $options['text_posle'];
	
	return $out;
}


# End of file
