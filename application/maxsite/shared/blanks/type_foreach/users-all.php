<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		if (!$comuser['comusers_nik']) $comuser['comusers_nik'] = tf('Комментатор'). ' ' . $comuser['comusers_id'];
		echo '<li><a href="' . getinfo('siteurl') . 'users/' . $comuser['comusers_id'] . '">' . $comuser['comusers_nik'] . '</a></li>';
	
?>