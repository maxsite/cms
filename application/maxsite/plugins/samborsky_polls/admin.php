<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	$CI = & get_instance();
	$plugin_url = getinfo('site_url') . 'admin/samborsky_polls/';

?>

<div class="admin-h-menu">
	<a href="<?= $plugin_url ?>list" class="select"><?= t('Управление голосованиями')?></a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>manage" class="select"><?= t('Добавить новое')?></a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>settings" class="select"><?= t('Настройки')?></a>
</div>


	<?php

	// подключение стилей и скриптов
	function polls_admin_head(){
		$path = getinfo('plugins_url') .'samborsky_polls/';
		echo <<<EOFA

		<!-- admin styles  -->
		<link rel="stylesheet" href="${path}css/style_admin.css">

EOFA;


	}

	function polls_admin_head_list(){
		$path = getinfo('plugins_url') .'samborsky_polls/';
		$nmb_rec = mso_get_option('plugin_samborsky_polls', 'plugins', array('admin_number_records'=>10));
		$nmb_rec = $nmb_rec['admin_number_records'];
		$list_ajax = getinfo('ajax') .base64_encode('plugins/samborsky_polls/' .'/list-ajax.php');


		echo <<<EOFL

			<!-- admin JS -->
		<script src="${path}js/admin.js"></script>
			<!-- jQuery TableSorter + Pagination -->
		<script>var nmb_rec = {$nmb_rec};</script>
		<script src="${path}js/jTPS.js"></script>
		<script>
			var list_ajax = "{$list_ajax}";
			$(document).ready(function(){
				$('.samborsky_polls_table').jTPS({perPages:[nmb_rec]});
			});
		</script>

EOFL;
	}

	function polls_admin_head_manage(){
		$path = getinfo('plugins_url') .'samborsky_polls/';
		$text = array(t('Ограничено 15 ответами.','plugins'),t('Удаляем ответ. Вы уверены?','plugins'));
		echo <<<EOFM

			<!-- admin JS -->
		<script src="${path}js/admin.js"></script>
			<!-- jQuery UI (DatePicker) -->
		<script src="${path}js/jquery-ui-1.8.16.custom.min.js"></script>
		<link rel="stylesheet" href="${path}css/jquery-ui-1.8.16.custom.css">
		<script>
			var text = ["{$text[0]}", "{$text[1]}"];
			$(function() {
				$( "#sortable_polls" ).sortable();
				$( "#sortable123_polls" ).disableSelection();
				$( "#beginDate, #expiryDate" ).datepicker();
			});
		</script>

EOFM;

	}

	$seg = mso_segment(3);

	if( empty($seg) ){
		require(getinfo('plugins_dir') . 'samborsky_polls/list.php');
		mso_hook_add('admin_head', 'polls_admin_head_list');
		mso_hook_add('admin_head', 'polls_admin_head');
	}
	else if( $seg == 'manage' ){
		require(getinfo('plugins_dir') . 'samborsky_polls/manage.php');
		mso_hook_add('admin_head', 'polls_admin_head_manage');
		mso_hook_add('admin_head', 'polls_admin_head');
	}
	else if( $seg == 'list' ){
		require(getinfo('plugins_dir') . 'samborsky_polls/list.php');
		mso_hook_add('admin_head', 'polls_admin_head_list');
		mso_hook_add('admin_head', 'polls_admin_head');
	}
	else if( $seg == 'logs' ){
		require(getinfo('plugins_dir') . 'samborsky_polls/logs.php');
		mso_hook_add('admin_head', 'polls_admin_head');
	}
	else if( $seg == 'settings' ){
		require(getinfo('plugins_dir') . 'samborsky_polls/settings.php');
	}


?>
