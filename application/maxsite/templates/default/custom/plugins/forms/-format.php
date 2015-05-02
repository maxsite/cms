<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// массив данных для формата вывода

$format['container_class'] = 'mso-forms'; // css-класс для div-контейнера формы

$format['textarea'] = '<p><label><span>[description][require_title]</span><textarea [field_param]>[value]</textarea></label>[tip]</p>';

$format['checkbox'] = '<p><label><span></span><input [field_param] value="1" type="checkbox"> [description]</label>[tip]</p>';

$format['select'] = '<p><label><span>[description][require_title]</span><select [field_param]>[option]</select></label>[tip]</p>';

$format['input'] = '<p><label><span>[description][require_title]</span><input [field_param]></label>[tip]</p>';

$format['tip'] = '<span class="mso-forms-tip">[tip]</span>';

$format['file_description'] = '<p><label><span>[file_description]</span></label></p>';
$format['file_field'] = '<p>[file_field]</p>';
$format['file_tip'] = '<p><span class="mso-forms-tip">[file_tip]</span></p>';

$format['message_ok'] = '<p class="mso-forms-ok">' . tf('Ваше сообщение отправлено') . '</p>';
$format['message_error'] = '<p class="mso-forms-error">[error]</p>';

$format['antispam'] = '<p><label><span>[antispam] [antispam_ok][require_title]</span>[input]</label></p>';

$format['buttons'] = '<p class="mso-forms-buttons">[submit] [reset]</p>';

$format['mail_field'] = '[description]: [post_value] [NR]';

# end of file