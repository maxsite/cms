<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function sp_install_table($table,$fields,$primary){
	
	$CI = &get_instance();
	
	// Удаляем таблицу
	$CI->dbforge->drop_table($table);
	
	// Добавляем поля
	$CI->dbforge->add_field($fields);
	
	// Указываем PRIMARY KEY
	$CI->dbforge->add_key($primary,TRUE);
	
	// Создаем таблицу
	return $CI->dbforge->create_table($table);
}

function sp_install(){

	$CI = &get_instance();
	$CI->load->dbforge();
	
	// Таблица "Вопросы"

	if(!$CI->db->table_exists("sp_questions"))
	{
		sp_install_table('sp_questions',array(
		
			'q_id' => array(
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
				
			'q_question' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'NULL' => FALSE
			),
				
			'q_timestamp' => array(
				'type' => 'INT',
				'constraint' => '10',
				'NULL' => FALSE,
			),

			'q_totalvotes' => array(
				'type' => 'INT',
				'NULL' => FALSE,
				'default' => '0'
			),
					
			'q_active' => array(
				'type' => 'TINYINT',
				'NULL' => FALSE,
				'default' => '1'
			),
			
			'q_expiry' => array(
				'type' => 'INT',
				'constraint' => '10',
				'NULL' => FALSE,
				'default' => '0'
			),

			'q_multiple' => array(
				'type' => 'TINYINT',
				'NULL' => FALSE,
				'default' => '0',
			),
				
			'q_totalvoters' => array(
				'type' => 'INT',
				'NULL' => FALSE,
				'default' => '0'
			),
			
			'q_protection' => array(
				'type' => 'TINYINT',
				'NULL' => FALSE,
				'default' => '1'
			)
			
		),'q_id');
	};
	
	// Таблица "Ответы"
	if(!$CI->db->table_exists("sp_answers"))
	{
		sp_install_table('sp_answers',array(

			'a_id' => array(
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			
			'a_qid' => array(
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'default' => '0',
				'NULL' => FALSE
			),
			
			'a_answer' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'NULL' => FALSE
			),
			
			'a_votes' => array(
				'type' => 'INT',
				'NULL' => FALSE,
				'default' => '0'
			),
			
			'a_order' => array(
				'type' => 'TINYINT',
				'NULL' => FALSE,
				'default' => '0'
			)
			
		),'a_id');
	};
		
	// Таблица "Логи"
	if(!$CI->db->table_exists("sp_logs"))
	{
		sp_install_table('sp_logs',array(

			'l_id' => array(
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			
			'l_qid' => array(
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'default' => '0',
				'NULL' => FALSE
			),		
			
			'l_aid' => array(
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'default' => '0',
				'NULL' => FALSE
			),
			
			'l_ip' => array(
				'type' => 'INT',
				'NULL' => FALSE
			),
			
			'l_host' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'NULL' => FALSE
			),
			
			'l_timestamp' => array(
				'type' => 'INT',
				'NULL' => FALSE,
				'default' => '0'
			),
			
			'l_user' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'NULL' => FALSE,
				'default' => '0'
			),
			
			'l_userid' => array(
				'type' => 'INT',
				'constraint' => '10',
				'NULL' => FALSE,
				'default' => '0'
			)
			
		),'l_id');
	};
	
}

function sp_add_options()
{
	$options = array(
		'archive_url' => 'polls-archive',
		'show_archives_link' => 1,
		'show_results_link' => 1,
		'close_after_hour' => 0,
		'admin_number_records' => 10,
		'len_polls' => t('1 неделя'),
		'secur_polls' => t('Защита по Coookie')
	);
	
	mso_add_option('plugin_samborsky_polls',  $options, 'plugins' );
}

?>
