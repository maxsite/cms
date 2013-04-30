<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_set_val('home_list_header', '<div class="home_header">Последние записи</div>');

// используется только если выбран main-шаблон для двух сайдбаров
// mso_register_sidebar('2', t('2-й сайдбар'));

// сайдбары в подвале
mso_register_sidebar('3', t('Подвал: 1-й сайдбар'));
mso_register_sidebar('4', t('Подвал: 2-й сайдбар'));
mso_register_sidebar('5', t('Подвал: 3-й сайдбар'));
mso_register_sidebar('6', t('Подвал: 4-й сайдбар'));
mso_register_sidebar('7', t('Подвал: 5-й сайдбар'));
