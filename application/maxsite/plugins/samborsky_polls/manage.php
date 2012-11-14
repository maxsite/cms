<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
global $MSO;
////////////////////////////////////////////
//		ФУНКЦИИ
////////////////////////////////////////////

////////////////////////////////////////////
//		добовляет пустые ответы
function add_empty_answers($data = array(), $nmb = 15)
{
	$newanswers = new stdClass;
	$newanswers->a_id = '';
	$newanswers->a_answer = '';
	$newanswers->a_votes = '';
	$newanswers->view = 0;

	if(count($data) < 2)
		$data = array_pad($data,2, clone $newanswers);

	foreach($data as &$ans)
	{
		$ans = (object)$ans;
		$ans->view = 1;
	}

	$data = array_pad($data,$nmb,$newanswers);

	return $data;
}

////////////////////////////////////////////
// 		Длительность по-умолчанию
//		возвращает время окончания без 1-ой секунды
function get_len_polls($t,$val)
{
	switch($val)
	{
		case t('1 день'): $val = mktime(0,0,0,date("m",$t),date("d",$t)+1,date("Y",$t))-1;break;
		case t('1 неделя'): $val = mktime(0,0,0,date("m",$t),date("d",$t)+7,date("Y",$t))-1;break;
		case t('2 недели'): $val = mktime(0,0,0,date("m",$t),date("d",$t)+14,date("Y",$t))-1;break;
		case t('1 месяц'): $val = mktime(0,0,0,date("m",$t)+1,date("d",$t),date("Y",$t))-1;break;
		case t('3 месяца'): $val = mktime(0,0,0,date("m",$t)+3,date("d",$t),date("Y",$t))-1;break;
		case t('6 месяцев'): $val = mktime(0,0,0,date("m",$t)+6,date("d",$t),date("Y",$t))-1;break;
		case t('Год'): $val = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t)+1)-1;break;
		case t('Бессрочное'): $val = 0;break;
		default: $val = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
	}
	
	return $val;
}

////////////////////////////////////////////
// 		Безопасность по-умолчанию
function get_protect_polls($val)
{
	switch($val)
	{
		case t('Только для зарегистрированых (users)'): $val = 2;break;
		case t('Защита по Coookie'): $val = 1;break;
		default: $val = 0;
	}
	return $val;
}

function date_to_unix($d, $h=0, $m=0, $s=0)
{
	$d = explode('/',$d);
	if(count($d) == 3) return mktime($h,$m,$s,$d[0],$d[1],$d[2]);
	else return false; 
}

////////////////////////////////////////////
//	Чутка безопасности
function add_protect($str)
{
	$CI = &get_instance();
	
	$str = $CI->security->xss_clean($str);
	$str = addslashes($str);
	return $str;
}

////////////////////////////////////////////
// 		Проверка данных из формы и формирование массива для вставки в бд
function polls_check_postData()
{
	$CI = &get_instance();
	
	$data['qu'] = array(
				'q_question' => '',
				'q_timestamp' => '',
				'q_expiry' => '',
				'q_totalvotes' => 0,
				'q_protection' => '');
				
	$data['ans'] = array();
	$data['errors'] = array();


	// вопрос
	if($_POST['qu'] != '')
		$data['qu']['q_question'] = add_protect($_POST['qu']);
	else
		$data['errors']['qu'] = t('Вопрос не может быть пустым');


	// ответы
	foreach($_POST['ans'] as $ans)
	{
		if($ans['ans'] != '')
		{
			$data['ans'][] = array(
					'a_answer' => add_protect($ans['ans']),
					'a_votes' => (int)$ans['votes'],
					'a_id' => $ans['id']);
			$data['qu']['q_totalvotes'] += $ans['votes'];
		}
	}
	unset($ans);
	
	if(count($data['ans']) >= 2 )
	{
		foreach($data['ans'] as $nmb => &$ans)
			$ans['a_order'] = $nmb+1;
	}
	else
		$data['errors']['ans'] = t('Должно быть минимум два ответа');
		
		
	// Дата начала
	if(!$data['qu']['q_timestamp'] = date_to_unix($_POST['beginDate']))
		$data['errors']['beginDate'] = '';


	// Дата окончания
	if(!isset($_POST['noExpiry']))
	{
		if(!$data['qu']['q_expiry'] = date_to_unix($_POST['expiryDate'],23,59,59))
			$data['errors']['expiryDate'] = t('Неправильно введена дата окончания');
		
		// Если дата окончания меньше даты начала
		if($data['qu']['q_expiry'] < $data['qu']['q_timestamp'])
			$data['qu']['q_expiry'] = date_to_unix($_POST['beginDate'],23,59,59);
	}
	else $data['qu']['q_expiry'] = 0;
	

	$data['qu']['q_protection'] = (int)$_POST['q_protection'] < 3 ? (int)$_POST['q_protection'] : 2 ;
	$data['id'] = $_POST['id'];
					 
	return $data;
}



////////////////////////////////////////////
//		ОБРАБОТКА И ПОДГОТОВКА ДАННЫХ
////////////////////////////////////////////
$default = array(
		'archive_url' => 'polls-archive',
		'show_archives_link' => 1,
		'show_results_link' => 1,
		'close_after_hour' => 0,
		'admin_number_records' => 10,
		'len_polls' => t('1 неделя'),
		'secur_polls' => t('Защита по Coookie')
);

$options = mso_get_option('plugin_samborsky_polls', 'plugins', $default);

// обработка POST
if($post = mso_check_post(array('f_session_id', 'f_submit')))
{
	$CI = &get_instance();
	$data = polls_check_postData();
	if(count($data['errors']) == 0)
	{
		if($_POST['act'] == 'edit')
		{
			$id = $_POST['id'];
			$CI->db->update('sp_questions',$data['qu'],array('q_id' => $id));
			$CI->db->delete('sp_answers',array('a_qid'=>$id));
		}
		else
		{
			$CI->db->insert('sp_questions',$data['qu']);
			$id = mysql_insert_id();
		}
		
		foreach($data['ans'] as $ans)
		{
			$ans['a_qid'] = $id;
			if($ans['a_id'] == '')
				unset($ans['a_id']);
				
			$CI->db->insert('sp_answers',$ans);
		}
		unset($ans);
		
		if($_POST['act'] == 'edit')
		{
			$CI->db->insert('sp_logs',array('l_qid'=>$id,'l_host'=>t('Отредактировано'),'l_timestamp'=>mktime(),'l_user'=>is_login()?$MSO->data['session']['users_login']:'-'));
			header("Location: " .getinfo('site_url') ."admin/samborsky_polls/list/edit_ok");
		}
		else
		{
			$CI->db->insert('sp_logs',array('l_qid'=>$id,'l_host'=>t('Создано'),'l_timestamp'=>mktime(),'l_user'=>is_login()?$MSO->data['session']['users_login']:'-'));
			header("Location: " .getinfo('site_url') ."admin/samborsky_polls/list/add_ok");
			
		}
	}
	else
	{
		echo '<div class="error">' .t(implode('<br />', $data['errors']), 'admin') .'</div>';
	}
}

// данные для редактирования голосования
if(is_numeric(mso_segment(4)) and !isset($data['errors']))
{
	$qu = new sp_question(mso_segment(4));
	$qu->get();

	$answers = new sp_answer();
	$answers_array = $answers->get_array(mso_segment(4));
	$answers_array = add_empty_answers($answers_array);
	
	$act = 'edit';
	$no_expiry = '';
	if($qu->data->q_expiry == 0)
	{
		$no_expiry = 'checked="checked"';
		$qu->data->q_expiry = $qu->data->q_timestamp;
	}
	//pr($qu);
}

// данные для нового голосования
elseif(!isset($data['errors']))
{
	$act = 'new';
	$no_expiry = '';
	$date = mktime(0,0,0,date("m"),date("d"),date("Y"));
	
	if(!$exp = get_len_polls($date, $options['len_polls']))
	{
		$exp = $date;
		$no_expiry = 'checked="checked"';
	}

	$protect = get_protect_polls($options['secur_polls']);

	$qu = new stdClass();
	$qu->data->q_id = '';
	$qu->data->q_question = '';
	$qu->data->q_timestamp = $date;
	$qu->data->q_expiry = $exp;
	$qu->data->q_protection = $protect;
	
	$answers_array = add_empty_answers();
}

// данные для формы если есть ошибки при сохранении
elseif(isset($data['errors']))
{
	$act = $_POST['act'];
	$no_expiry = isset( $_POST['noExpiry']) ? 'checked="checked"' : '' ;
	
	$qu = new stdClass();
	$qu->data->q_id = $_POST['id'];
	$qu->data->q_question = $_POST['qu'];
	$qu->data->q_timestamp = date_to_unix($_POST['beginDate']);
	$qu->data->q_expiry = date_to_unix($_POST['expiryDate'],23,59,59);
	$qu->data->q_protection = $_POST['q_protection'];
	
	$answers_array = array();
	
	foreach($_POST['ans'] as $ans)
	{
		if($ans['ans'] != '')
		{
			$answer = new stdClass();
			$answer->a_id = $ans['id'];
			$answer->a_answer = $ans['ans'];
			$answer->a_votes = $ans['votes'];
			$answers_array[] = $answer;
		}
	}
	
	$answers_array = add_empty_answers($answers_array);
}

$plug_path = getinfo('plugins_url') .'samborsky_polls/';


////////////////////////////////////////////
//		ВЫВОД ФОРМЫ
////////////////////////////////////////////
?>

<h1><?= t('Добавление/Изменение голосования')?></h1>
<div class="polls_addEdit_form">
	<form method="post">
		<?= mso_form_session('f_session_id')?>
		<input type="hidden" name="act" value="<?= $act?>" />
		<input type="hidden" name="id" value="<?= $qu->data->q_id ?>" />


		<h2><?= t('Вопрос:')?></h2>
		<textarea name="qu" rows="2"><?= $qu->data->q_question ?></textarea><br /><br />


		<h2><?= t('Ответы:')?></h2>
		<div class="polls_manage_ans">
			<ul id="sortable_polls">

				<?php foreach($answers_array as $nmb => $ans):
					$st_not_vis = $ans->view ? '' : ' style="display:none;"';
				?>

				<li class="ui-state-default"<?= $st_not_vis ?>>
					<input type="text" name="ans[<?= $nmb ?>][ans]" value="<?= $ans->a_answer ?>" class="ans_text" />
					<input type="text" name="ans[<?= $nmb ?>][votes]" value="<?= $ans->a_votes ?>" class="ans_votes" />
					<input type="hidden" name="ans[<?= $nmb ?>][id]" value="<?= $ans->a_id ?>" />
					<a href="" class="del_ans"><img src="<?= $plug_path ?>img/del_ans.png" title="<?= t('удалить ответ')?>" /></a>
				</li>

				<?php endforeach ?>

			</ul>
			<a href="" class="add_ans"><p><?= t('Добавить ответ')?></p></a>
		</div><br /><br />


		<h2><?= t('Дата начала/окончания голосования:')?></h2>
		<div class="polls_manage_date">
			<?= t('Начало (М/Д/Г):')?>
			<input type="text" id="beginDate" name="beginDate" value="<?= date("m/d/Y",$qu->data->q_timestamp) ?>">
			&nbsp;&nbsp;&nbsp;
			<?= t('Окончание (М/Д/Г) (включительно):')?>
			<input type="text" id="expiryDate" name="expiryDate" value="<?= date("m/d/Y",$qu->data->q_expiry) ?>">
			<p><input type="checkbox" name="noExpiry" id="noExpiry" <?= $no_expiry ?>>&nbsp;<?= t('Бессрочное голосование')?></p>
		</div><br /><br />


		<h2><?= t('Защита от накрутки:')?></h2>
		<div class="polls_manage_protect">
			<select name="q_protection">
				<option value="2" <?php if($qu->data->q_protection==2) echo 'selected="selected"'?>><?= t('Только для зарегистрированых (users)')?></option>
				<option value="1" <?php if($qu->data->q_protection==1) echo 'selected="selected"'?>><?= t('Защита по Coookie')?></option>
				<option value="0" <?php if($qu->data->q_protection==0) echo 'selected="selected"'?>><?= t('Без защиты, один пользователь может голосовать много раз')?></option>
			</select>
		</div><br /><br />
		
		
		<p class="br"><input type="submit" name="f_submit" value="<?= t('Сохранить')?>" /></p>
	</form>
</div>
