<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$return = array(
	'error_code' => 1,
	'error_description' => t('Не указан ID голосования'),
	'resp' => ''
);

if( isset($_POST['q_id']) && is_numeric($_POST['q_id']) && $_POST['type'] ){

	// Учесть голос		
	if( 'vote' == $_POST['type'] ){
	
		$question = new sp_question($_POST['q_id']);
		
		// Получим данные о голосовании
		if( $question->get() ){
			
			// Проверим, можно ли голосовать
			if( $question->check_allow() ){
				
				if( isset($_POST['a_id']) ){
				
					// Ставим кукис
					if( 1 == $question->data->q_protection ){
						set_cookie(array(
							'name' => 'sp_' . $question->id,
							'value' => TRUE,
							// На 3 месяца
							'expire' => 3600*24*30*3
						));
					}
					
					// Учитываем голоса
					foreach( $_POST['a_id'] as $a_id ){
						$answer = new sp_answer($a_id);
						$answer->inc();
					}
					
					// Запишем логи
					sp_write_logs();
					
					$question->update(array(
						// +1 проголосовавший 
						'q_totalvoters' => $question->data->q_totalvoters + 1,
						// + добавляем голоса 
						'q_totalvotes' => $question->data->q_totalvotes + count($_POST['a_id'])
					));
					
					if( $question->get() ){
						$return['error_code'] = 0;
						$return['error_description'] = '';
						$return['resp'] = $question->results();
					}
					else{
						$return['error_description'] = t('Проблема с загрузкой результатов голосования');
					}
				}
				else{
					$return['error_description'] = t('Не указан вариант ответа');
				}
			}
			else{
				$return['error_description'] = $question->last_error;
			}
		}
		else{
			$return['error_description'] = t('Голосования не существует');
		}
	}
	
	// Показать результаты
	else if( 'results' == $_POST['type'] ){
		
		$question = new sp_question($_POST['q_id']);
		
		// Получим данные о голосовании
		if( $question->get() ){
			
			$return['error_code'] = 0;
			$return['error_description'] = '';
			$return['resp'] = $question->results();
		}
		else{
			$return['error_description'] = t('Голосования не существует');
		}
	}
	else{
		$return['error_description'] = t('Не известный метод');
	}
}

echo json_encode($return);	

function sp_write_logs(){
	global $MSO;
	
	$CI = &get_instance();
	
	$host = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
	if( empty($host) ) $host = $_SERVER['REMOTE_ADDR'];
	
	$ip = ip2long($_SERVER['REMOTE_ADDR']);
	
	foreach( $_POST['a_id'] as $a_id ){
		
		$CI->db->insert('sp_logs',array(
			'l_qid' 		=> $_POST['q_id'],
			'l_aid' 		=> $a_id,
			'l_ip'			=> $ip,
			'l_host'		=> $host,
			'l_timestamp'	=> mktime(),
			'l_userid'		=> is_login() ? $MSO->data['session']['users_id'] : 0,
			'l_user'		=> is_login() ? $MSO->data['session']['users_login'] : '-'
		));
	}
}	

?>
