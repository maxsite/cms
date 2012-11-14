<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * Widget Top commentators
 * (c) http://maxsitecms.ru/
 */
 

# функция автоподключения плагина
function top_commentators_autoload($args = array())
{
  # регистрируем виджет
  mso_register_widget('top_commentators_widget', t('Активные комментаторы'));
}

# функция выполняется при деинсталяции плагина
function top_commentators_uninstall($args = array())
{  
  mso_delete_option_mask('top_commentators_widget_', 'plugins' ); // удалим созданные опции
  return $args;
}


# функция, которая берет настройки из опций виджетов
function top_commentators_widget($num = 1)
{
  $widget = 'top_commentators_widget_' . $num; // имя для опций = виджет + номер
  $options = mso_get_option($widget, 'plugins', array() ); // получаем опции

  // заменим заголовок, чтобы был в  h2 class="box"
  if ( isset($options['header']) and $options['header'] ) $options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
    else $options['header'] = '';
  
  return top_commentators_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function top_commentators_widget_form($num = 1)
{

  $widget = 'top_commentators_widget_' . $num; // имя для формы и опций = виджет + номер
  
  // получаем опции 
  $options = mso_get_option($widget, 'plugins', array());
  
  if ( !isset($options['header']) ) $options['header'] = '';
  if ( !isset($options['format']) ) $options['format'] = '[LINK_URL][NAME][/LINK]<sup>[COUNT]</sup>';
  if ( !isset($options['commentators_cnt']) ) $options['commentators_cnt'] = 10;
  if ( !isset($options['days']) ) $options['days'] = 30 ;

  
  // вывод самой формы
  $CI = & get_instance();
  $CI->load->helper('form');
  
  $form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
  
  $form .= mso_widget_create_form(t('Формат'), form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ), t('Возможные подстановки: [LINK_URL]ссылка[/LINK] [LINK_PAGE]ссылка[/LINK] [NAME] [COUNT]'));
  
  $form .= mso_widget_create_form('Количество комментаторов', form_input( array( 'name'=>$widget . 'commentators_cnt', 'value'=>$options['commentators_cnt'] ) ), '');
  
  $form .= mso_widget_create_form(t('За сколько дней учитывать комментарии'), form_input( array( 'name'=>$widget . 'days', 'value'=>$options['days'])), '');


  return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function top_commentators_widget_update($num = 1)
{

  $widget = 'top_commentators_widget_' . $num; // имя для опций = виджет + номер
  
  // получаем опции
  $options = $newoptions = mso_get_option($widget, 'plugins', array());
  
  # обрабатываем POST
  $newoptions['header'] = mso_widget_get_post($widget . 'header');
  $newoptions['format'] = mso_widget_get_post($widget . 'format');
  $newoptions['commentators_cnt'] = mso_widget_get_post($widget . 'commentators_cnt');
  $newoptions['days'] = mso_widget_get_post($widget . 'days');

  if ( $options != $newoptions ) 
    mso_add_option($widget, $newoptions, 'plugins' );
}


function top_commentators_widget_custom($options = array(), $num = 1)
{
  if ( !isset($options['format']) ) $options['format'] = '[LINK_URL][NAME][/LINK]<sup>[COUNT]</sup>';
  if ( !isset($options['commentators_cnt']) ) $options['commentators_cnt'] = 10;
  if ( !isset($options['days']) ) $options['days'] = 30 ;

  $cache_key = 'top_commentators_widget' . serialize($options) . $num;
  
  $k = mso_get_cache($cache_key);
  if ($k)
  {
    $out=$k;
  }
  else 
  {
    $CI = & get_instance();
    $CI->db->select('comusers_id,comusers_nik,comusers_url,count(comments_id) as com_cnt');
    $CI->db->from('comments');
    $CI->db->join('comusers', 'comusers.comusers_id = comments.comments_comusers_id');
    $CI->db->limit($options['commentators_cnt']);
    
    
    $CI->db->where('comments_date >', date('Y-m-d H:i:s', time()-$options['days']*24*60*60));
    
    
    $CI->db->where('comments_approved', '1');
    $CI->db->where('comusers_activate_string', 'comusers_activate_key',false);
    $CI->db->group_by('comusers_nik');
    $CI->db->order_by('com_cnt','DESC');
    $query = $CI->db->get();
    
    $out = '';
    if ($query->num_rows() > 0)
    {
      $users = $query->result_array();


      foreach ($users as $user)
      {
        if ( $user['comusers_nik'] == '' )
          $user['comusers_nik'] = t('Комментатор') . ' ' . $user['comusers_id'] ;

        if ( $user['comusers_url'] == '' )
          $user['comusers_url'] = getinfo('siteurl') . 'users/' . $user['comusers_id'];

        $out .= '<li>' . str_replace( array('[NAME]', '[COUNT]','[/LINK]','[LINK_URL]','[LINK_PAGE]'),
                                      array($user['comusers_nik'], $user['com_cnt'],'</a>','<a href="' . $user['comusers_url'] . '">','<a href="' . getinfo('siteurl') . 'users/' . $user['comusers_id'] . '">'),
                                      $options['format'] )
                . '</a></li>';
      }

      if ($out) $out = $options['header'] . '<ul class="is_link top_commentators">' . $out . '</ul>' . NR;
    }

    mso_add_cache($cache_key, $out); // сразу в кэш добавим
  }
  return $out;
}

# end file