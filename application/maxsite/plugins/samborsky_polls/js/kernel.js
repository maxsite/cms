
	/**********
	 * Отправляем запрос к php
	 * @param {Object} polls
	 * @param {Object} loader
	 * @param {Object} data
	 * @param {Object} ajax_path
	 */

	function sp_polls_send_query(polls,loader,data,ajax_path){
	
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
			        loader.show();
					polls.hide();
				},
			success:
				function(json,textStatus){
					
					// Если произошла ошибка
					if( 1 == json.error_code){
						alert(json.error_description);
					}
					else{
						loader.hide();
						polls.show();
						polls.html( json.resp );
					}
				},
			error:
				function(){
					loader.hide();
					polls.show();
					alert('Error browser.');
				}
		});
	}


	/***********
	 * Выводим результаты голосования
	 * @param {Object} q_id
	 */

	function sp_polls_results(q_id){
		
		var data = 'type=results&q_id=' + q_id;
		var polls = $('#sp_polls_' + q_id);
		var loader = $('#sp_polls_loader_' + q_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#sp_ajax_path_' + q_id).val();
		
		if( ajax_path.length ){
			sp_polls_send_query(polls,loader,data,ajax_path);
		}		
	}
	
	/*********
	 * Учитываем голос
	 * @param {Object} q_id
	 */
	
	function sp_polls_vote(q_id){
		
		var data = 'type=vote&q_id=' + q_id; 
		var polls = $('.sp_polls').filter('#sp_polls_' + q_id);
		var loader = $('.sp_polls_loader').filter('#sp_polls_loader_' + q_id);
		
		// Получаем результаты голосования
		$('.sp_question_' + q_id).each(function(i){
			
			if( true == $(this).prop('checked') ){
				
				data += '&a_id[]=' + $(this).val();
			}
		});
		
		// Отправляем POST запрос
		var ajax_path = $('#sp_ajax_path_' + q_id).val();
		
		if( ajax_path.length ){
			sp_polls_send_query(polls,loader,data,ajax_path);
		}	
	}
