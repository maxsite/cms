$(document).ready(function(){

/*	Пока не работает. тз:
 * 	после изменения в бд (закрыть-открыть)
 *	нужно заменить: значок замка, класс ссылки (open/close_poll), 
 * 	title ссылки (открыть/закрыть голосование),
 * 	в предыдущей ячейке таблицы - закрыто/бессрочное/оталось ...дней

	// Список. Закрыть голосование
	$("a.close_poll").click(function(){
		$.ajax({
			type: "POST",
			url: list_ajax,
			data: "act=close&p_id=" + $(this).attr("p_id"),
			success: function(ans){
				if(ans == 1)
				{
					
				}		
			},
		});
		return false;
	});

	
	// Список. Открыть голосование
	$("a.open_poll").click(function(){
		$.ajax({
			type: "POST",
			url: list_ajax,
			data: "act=open&p_id=" + $(this).attr("p_id"),
			success: function(ans){
				alert(ans);
			},
		});
		return false;
	});
*/	
	
	// Управление. Добавить ответ
	$("a.add_ans").click(function(){
		if($('#sortable_polls li:hidden:first').index() == -1){
			alert(text[0]);
		}
		else{
			$('#sortable_polls li:hidden:first').slideDown(100);
		}
		return false;
	});
	
	
	// Управление. Удалить ответ
	$("a.del_ans").click(function(){
		if (confirm(text[1])) {
			$(this).parent('li').slideUp(100);
			$(this).parent('li').children('input').attr('value','');
			$(this).parent('li').children('input:hidden').val('')
			$('#sortable_polls li:last').after($(this).parent('li'));
		};

		return false;
	});
	
});
