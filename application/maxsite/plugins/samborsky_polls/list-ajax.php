<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if(isset($_POST['act']) and $_POST['act'] == 'close')
{
	$CI = &get_instance();
	
	$CI->db->update('sp_questions',array('q_active' => 0),array('q_id' => $_POST['p_id']));
	$CI->db->insert('sp_logs',array('l_qid'=>$_POST['p_id'],'l_host'=>'Закрыто','l_timestamp'=>gmmktime()));
	echo 1;
}

elseif(isset($_POST['act']) and $_POST['act'] == 'open')
{
	$CI = &get_instance();
	
	$CI->db->update('sp_questions',array('q_active' => 1),array('q_id' => $_POST['p_id']));
	$CI->db->insert('sp_logs',array('l_qid'=>$_POST['p_id'],'l_host'=>'Открыто','l_timestamp'=>gmmktime()));
	echo 1;
}

?>
