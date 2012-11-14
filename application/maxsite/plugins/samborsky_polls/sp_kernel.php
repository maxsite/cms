<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	class sp_question{
		
		var $id = 0; 
		var $data = array();
		var $last_error = '';
		var $options = array();
		
		function __construct($id = 0){
			$this->id = $id;
			
			$CI = &get_instance();
			$CI->load->helper('cookie');
			
			$default = array(
					'archive_url' => 'polls-archive',
					'show_archives_link' => 1,
					'show_results_link' => 1,
					'close_after_hour' => 0,
					'admin_number_records' => 10,
					'len_polls' => t('1 неделя'),
					'secur_polls' => t('Защита по Coookie')
			);
		
			$this->options = mso_get_option('plugin_samborsky_polls', 'plugins', $default);
		}
		
		/***
		 * Возвращает html код активного голосования.
		 * Если юзер голосовал в нем, выводятся результаты.
		 * @return 
		 */
		
		function get_active_code(){
			
			$CI = &get_instance();
			
			// Получаем активное голосование
			$CI->db->select('*')->limit(1)->order_by('q_id','random');
			
			if( !$this->id ){
				
				// Где голосование активно, и входит во временные рамки
				$CI->db->where(array(
					'q_active' => true 
				));
			}
			else{
				
				// С указанным ID
				$CI->db->where('q_id',$this->id);
			}
			
			$questions = $CI->db->get('sp_questions');
			if( $questions->num_rows() > 0 ){
				
				$this->data = $questions->row();
				$this->id = $this->data->q_id;
				
				// если подошла дата закрытия, и голосование не бессрочное - закрываем
				if($this->data->q_expiry and $this->data->q_active)
				{
					$close_time = $this->data->q_expiry;
					$close_time += $this->options['close_after_hour']*60*60;
					if($close_time < mktime())
					{
						$this->data->q_active = 0;
						$this->update();
						$CI->db->insert('sp_logs',array('l_qid'=>$this->id,'l_host'=>t('Закрыто'),'l_timestamp'=>mktime()));
					}
				}
				
				// Если голосование запрещено, т.е. по сути пользователь уже голосовал
				// Показываем только результаты
				if(!$this->check_allow())
					return $this->results();

				// Если голосование закрыто - показываем только результаты
				elseif(!$this->data->q_active)
					return $this->results();
					
				else{
					return $this->form();
				}
			}
			
			return '';
		}
		
		function results(){
			
			if( !$this->id ) return '';
			
			$CI = &get_instance();
			$CI->load->library('table');
			$CI->table->clear();
			
			$CI->table->set_template(array( 
				'table_open'  => '<table class="sp_results">' 
			));
			$CI->table->set_caption("<strong>{$this->data->q_question}</strong>");
			
			// Находим все варианты ответов
			$answers = new sp_answer();
			$answers_array = $answers->get_array($this->id);
				
			foreach( $answers_array as $a ){
				$line = getinfo('plugins_url') . 'samborsky_polls/img/1.gif';
				
				$percent = $this->data->q_totalvotes ? ($a->a_votes/$this->data->q_totalvotes)*100 : 0.00;
				$percent_width = ceil($percent);
				$percent = round($percent,2);
				
				$CI->table->add_row("{$a->a_answer} ({$a->a_votes}) {$percent}%");
				$CI->table->add_row("<img src=\"$line\" style=\"border-left: 1px solid #7cbeeb;\" width=\"{$percent_width}%\" height=\"10\">");
			}
			
			$total = number_format($this->data->q_totalvoters,0,' ',' ');

			$CI->table->add_row("<strong>".t('Всего проголосовало:')."</strong> {$total} ".t('чел'));
			
			if( $this->options['show_archives_link'] )
				$CI->table->add_row('<div align="center"><a href="'.getinfo('siteurl').$this->options['archive_url'].'">'.t('Архивы голосований').'</a></div>');
		
			$out = $CI->table->generate();
			
			return $out;
		}
		
		function form(){
			
			$CI = &get_instance();
			$CI->load->library('table');
			$CI->table->clear();
			
			$CI->table->set_template(array(
				'table_open'  => '<table class="sp_polls" id="sp_polls_'.$this->id.'">'
			));
			$CI->table->set_caption("<strong>{$this->data->q_question}</strong>");
			
			$answers = new sp_answer();
			$answers_array = $answers->get_array($this->id);
				
			foreach( $answers_array as $a )
			{
				$CI->table->add_row(
				
					"<label><input type=\"radio\" id=\"sp_answer_{$a->a_id}\" class=\"sp_question_{$this->id}\" name=\"sp_question_{$this->id}\" value=\"{$a->a_id}\"> {$a->a_answer}</label>");
				/*
				$CI->table->add_row(
					"<input type=\"radio\" id=\"sp_answer_{$a->a_id}\" class=\"sp_question_{$this->id}\" name=\"sp_question_{$this->id}\" value=\"{$a->a_id}\">",
					"<label for=\"sp_answer_{$a->a_id}\">{$a->a_answer}</label>"
				);
				*/
			}
			
			// Куда отправлять POST
			$ajax_path = getinfo('ajax') . base64_encode('plugins/samborsky_polls/ajax-ajax.php');
			
			$results_link = $this->options['show_results_link'] ? '&nbsp;&nbsp; <a href="javascript: void(0);" onclick="javascript:sp_polls_results('.$this->id.');" class="sp_polls_ajax_link">'.t('Результаты').'</a>' : '';
			
			$CI->table->add_row(
				'<input type="hidden" id="sp_ajax_path_'.$this->id.'" value="'.$ajax_path.'">'
				. '<input type="button" value="'.t('Проголосовать').'" onclick="javascript:sp_polls_vote('.$this->id.');">' . $results_link
			);
			
			if( $this->options['show_archives_link'] )
				$CI->table->add_row('<a href="'.getinfo('siteurl').$this->options['archive_url'].'">'.t('Архивы голосований').'</a>');

			// Генерируем таблицу и форму загрузки
			$out = $CI->table->generate() . 
			"<div class=\"sp_polls_loader\" id=\"sp_polls_loader_{$this->id}\">
				<img src=\"". getinfo('plugins_url') . 'samborsky_polls/ajax-loader.gif' ."\" alt=\"".t('Идет загрузка…')."\">
				<p>".t('Идет загрузка…')."</p>
			</div>";
			
			return $out;
		}
		
		function get(){
			
			if( !$this->id ) return false;
			
			$CI = &get_instance();
			$CI->db->select('*');
			$CI->db->limit(1);
			$CI->db->where('q_id',$this->id);
	
			$query = $CI->db->get('sp_questions');
	
			if( $query->num_rows() ){
				
				$this->data = $query->row();
				return true;
			}
			
			return false;
		}
		
		function insert($data = array()){
			
			if( !empty($data) ){
				$this->data = $data;
			}
			else{
				return false;
			}
			
			$CI = &get_instance();
			
			if( $ret = $CI->db->insert('sp_questions',$this->data) ){
				
				$this->id = $CI->db->insert_id();
				$CI->db->cache_delete_all();
				return true;
			}
			
			return false;
		}
		
		function update($data = array()){
			
			if( !$this->id ) return false;
			
			if( !empty($data) ){
				$this->data = $data;
			}
			
			$CI = &get_instance();
			$CI->db->where('q_id',$this->id);
			
			
			$r = $CI->db->update('sp_questions',$this->data);
			
			$CI->db->cache_delete_all();
			
			return $r;
		}
		
		function check_allow(){
			global $MSO;
			
			if( empty($this->data) ){
				return false;
			}
			
			// Если на голосовании нет защиты
			if( 0 == $this->data->q_protection ){
				return true;
			}
			
			// Если стоит защита по Cookie;
			if( 1 == $this->data->q_protection ){
				$cookie = get_cookie('sp_' . $this->id);
				
				if( !$cookie ){
					return TRUE;
				}
				else{
					$this->last_error = t('Вы уже голосовали');
				}
			}
			
			// Если стоит защита по юзеру;
			if( 2 == $this->data->q_protection ){
				
				if( is_login() ){
					
					// Проверим голосовал ли чел
					$CI = &get_instance();
					$CI->db->select('l_id')->limit(1)->where(array(
						'l_qid' => $this->id,
						'l_userid' => $MSO->data['session']['users_id']
					));
					
					$query = $CI->db->get('sp_logs');
			
					if( $query->num_rows() ){
						
						$this->last_error = t('Вы уже голосовали');
						return FALSE;
					}
					
					return TRUE;
				}
				else{
					$this->last_error = t('Голосовать могут только зарегистрированые пользователи. Пройдите регистрацию.');
				}
			}

			// Если голосовать можно только зарегистрированным (тем кто в mso_users, не путать с комюзерами)
			
			return false;
		}
		
		function close(){
			
			if( !$this->id ) return false;
			
			$CI = &get_instance();
			return $CI->db->where('q_id',$this->id)->limit(1)->update('sp_questions',array('q_active' => false));
		}
		
		public function open(){
			
			if( !$this->id ) return false;
			
			$CI = &get_instance();
			return $CI->db->where('q_id',$this->id)->limit(1)->update('sp_questions',array('q_active' => true));
		}
	}
	
	class sp_answer{
		
		var $a_id = 0;
		var $data = array();
		
		function __construct($a_id = 0){
			$this->a_id = $a_id;
		}
		
		/***
		 * Получает массив вариантов ответов
		 * @return 
		 */
		function get($a_id = 0){
			
			$this->data = array();
			if( $a_id ) $this->a_id = $a_id;
			
			$CI = &get_instance();
	
			$CI->db->select('*');
			$CI->db->order_by('a_order');
			$CI->db->limit(1);
			$CI->db->where('a_id',$this->a_id);
	
			$query = $CI->db->get('sp_answers');
	
			if( $query->num_rows() ){

				$this->data = $query->row();			
			}
			
			return !empty($this->data);
		}
		
		/***
		 * Обновляет вариант ответа
		 * @return 
		 * @param object $data
		 */
		function update($data = array(),$a_id = 0){
			
			if( !empty($data) ){
				$this->data = $data;
			}

			if( $a_id ) $this->a_id = $a_id;

			$CI = &get_instance();
			$CI->db->where('a_id',$this->a_id);
			$CI->db->update('sp_answers',$this->data);
			$CI->db->cache_delete_all();
		}
		
		/***
		 * Создает вариант ответа
		 * @return 
		 * @param object $data[optional]
		 */
		function insert($data = array()){
			
			if( !empty($data) ){
				$this->data = $data;
			}
			else{
				return false;
			}
			
			$CI = &get_instance();
			
			if( $ret = $CI->db->insert('sp_answers',$this->data) ){
				
				$this->id = $CI->db->insert_id();
				$CI->db->cache_delete_all();
				return true;
			}
			
			return false;
		}
		
		/***
		 * Увеличивает счетчик для заданного варианта ответа
		 * @return 
		 * @param object $a_id[optional]
		 */
		
		function inc($a_id = 0){
			
			if( $a_id ) $this->a_id = $a_id;
			
			if( $this->get() ){
				$this->update(array(
					'a_votes' => $this->data->a_votes + 1
				));	
			}
		}
		
		/***
		 * Получает массив вариантов ответов по ID голосования
		 * @return 
		 * @param object $q_id
		 */
		
		function get_array($q_id){
			
			if( !$q_id ) return array();
			
			$CI = &get_instance();
			
			// Получаем варианты ответов
			$CI->db->select('*');
			$CI->db->where('a_qid',$q_id);
			$CI->db->order_by('a_order');
			$answers = $CI->db->get('sp_answers');
			
			if( $answers->num_rows() ){
				
				return $answers->result();
			}
			
			return array();
		}
		
	}
	
	class sp_archive{
		
		function single($id){
			
			$question = new sp_question($id);
			return $question->get_active_code();
		}
		
		function archive(){
			
			$CI = &get_instance();
			$CI->db->select('*');
			$CI->db->order_by('q_id','desc');
			
			$archive_url = mso_get_option('plugin_samborsky_polls', 'plugins', array('archive_url'=>'polls-archive'));
			$archive_url = $archive_url['archive_url'];
			
			$query = $CI->db->get('sp_questions');
			
			if( $query->num_rows() ){
				
				$CI->load->library('table');
				$CI->table->clear();
	
				$CI->table->set_template(array(
					'table_open'  => '<table border="0" width="100%" class="samborspy_polls_archive">'
				));
				$CI->table->set_heading(
					t('Название голосования'),
					'<div align="right">'.t('Сумма голосов').'</div>',
					'<div align="right">'.t('Проголосовало чел.').'</div>',
					t('Статус'));
	
					
				foreach( $query->result() as $row )
				{
					$CI->table->add_row(
						($row->q_active) ? '<a href="'. getinfo('siteurl') .$archive_url .'/'. $row->q_id .'">' . stripslashes($row->q_question) . '</a>' : stripslashes($row->q_question),
						'<div align="right">' . number_format($row->q_totalvotes,0,' ',' ') . '</div>',
						'<div align="right">' . number_format($row->q_totalvoters,0,' ',' ') . '</div>',
						$row->q_active ? t('Активно') : t('Закрыто')
					);
				}

				return $CI->table->generate(); 
			}
			
			return '';
		}

		function get(){
			
			$seg = mso_segment(2);
			
			// Пустой параметр, выводим архив
			if( empty($seg) ){
				return $this->archive();
			}
			// Чистовой параметр, значит ID
			else if( is_numeric($seg) ){
				return $this->single($seg);
			}

			return '';
		}
	}

# End of file
