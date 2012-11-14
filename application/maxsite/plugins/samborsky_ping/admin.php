<?php 

	if( !is_array($list = mso_get_option('samborsky_ping_list','plugins')) ){
		$list = array();
	}
	
	if(isset($_POST['save_submit'])){
		
		$list = explode("\n",$_POST['samborsky_ping_list']);
		
		foreach( $list as $key => $value ){
			$list[$key] = trim($value);
		}

		mso_add_option('samborsky_ping_list',array_unique($list),'plugins');
	}
	
	$string_lists = '';
	foreach( $list as $key => $value ){
		if( !empty($value) ){
			$string_lists .= $value . "\n";
		}
	}

?>

<h1>Пинги</h1>

Список пинг сервисов (дубли удалятся)
<br>
<form action="" method="post">
	<textarea name="samborsky_ping_list" style="width: 80%; height: 200px;"><?= $string_lists ?></textarea>
	<p><input type="submit" value="Сохранить" name="save_submit"></p>
</form>


<br><br>
Ручной запуск пингов
<form action="" method="post">
	<p><input type="submit" value="Запустить" name="submit_ping_start"></p>
</form>

<?

	if( isset($_POST['submit_ping_start']) ){
		
		if( !is_array($list = mso_get_option('samborsky_ping_list','plugins')) ){
			$list = array();
		}
		
		$CI = &get_instance();
		$CI->load->library('table');
		$CI->table->add_row('<strong>№</strong>','<strong>Сервис</strong>','<strong>Результат</strong>');
		$CI->table->set_template(array(
			'table_open' => '<table width="90%" border="0" cellpadding="0" cellspacing="6">'
		));

		$CI->load->library('xmlrpc');
		
		$CI->xmlrpc->method('weblogUpdates.ping');
		$CI->xmlrpc->request(array(
			mso_get_option('name_site'),
			getinfo('site_url'),
			getinfo('site_url') . 'feed'
		));
		
		$i = 0;
		foreach( $list as $key => $value ){
			
			if( !empty($value) ){
				
				$CI->xmlrpc->server($value,80);
				
				if( $CI->xmlrpc->send_request() ){
					
					$resp = $CI->xmlrpc->display_response();
					$CI->table->add_row(++$i,$value,$resp['flerror'] == '1' ? "<font color='red'>$resp[message]</font>" : $resp['message'] );
				}
				else{
					
					$CI->table->add_row(++$i,$value,$CI->xmlrpc->display_error());
				}
			}
		}
		
		echo $CI->table->generate(); 	
	}

?>

<br>
Идея и разработка плагина - <a href="http://www.samborsky.com/">Евгений Самборский</a>.