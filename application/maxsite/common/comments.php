<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Основные функции MaxSite CMS
 * (c) https://max-3000.com/
 * Функции для комментариев
 */

// функция получения комментариев
function mso_get_comments($page_id = 0, $r = [])
{
    global /*$MSO,*/ $page;

    $r = mso_hook('mso_get_comments_args', $r);

    if (!isset($r['limit'])) $r['limit'] = false;
    if (!isset($r['order'])) $r['order'] = 'asc';
    if (!isset($r['tags'])) $r['tags'] = '<p><img><strong><em><i><b><u><s><pre><code><blockquote>';
    if (!isset($r['tags_users'])) $r['tags_users'] = '<a><p><img><strong><em><i><b><u><s><pre><code><blockquote>';
    if (!isset($r['tags_comusers'])) $r['tags_comusers'] = '<a><p><img><strong><em><i><b><u><s><pre><code><blockquote>';
    if (!isset($r['anonim_comments'])) $r['anonim_comments'] = [];
    if (!isset($r['anonim_title'])) $r['anonim_title'] = ''; // ' ('. t('анонимно'). ')'; // дописка к имени для анонимов
    if (!isset($r['anonim_no_name'])) $r['anonim_no_name'] = tf('Аноним'); // Если не указано имя анонима

    // если аноним указывает имя с @, то это страница в твиттере - делаем ссылку
    if (!isset($r['anonim_twitter'])) $r['anonim_twitter'] = true;

    // дописка к имени для комментаторов без ника
    if (!isset($r['add_author_name'])) $r['add_author_name'] = tf('Комментатор');

    $CI = &get_instance();

    // получим список всех комюзеров, где посдчитается количество их комментариев
    $all_comusers = mso_comuser_update_count_comment();

    $CI->db->select('page.page_id, page.page_slug, page.page_title, comments.*,
    users.users_id, 
    users.users_nik,
    users.users_count_comments,
    users.users_url,
    users.users_email,
    users.users_avatar_url,
    
    comusers.comusers_id, 
    comusers.comusers_nik,
    comusers.comusers_count_comments,
    comusers.comusers_allow_publish,
    comusers.comusers_email,
    comusers.comusers_avatar_url,
    comusers.comusers_url
    ');

    if ($page_id) $CI->db->where('page.page_id', $page_id);

    // если нет анономого коммента, то вводим условие на comments_approved=1 - только разрешенные
    if (!$r['anonim_comments']) {
        $CI->db->where('comments.comments_approved', '1');
    } else {
        // есть массив с указанными комментариям - они выводятся отдельно
        $CI->db->where('comments.comments_approved', '0');
        $CI->db->where_in('comments.comments_id', $r['anonim_comments']);
    }

    // вот эти два join жутко валят мускуль...
    // пока решение не найдено, все запросы к комментам следует кэшировать на уровне плагина
    $CI->db->join('users', 'users.users_id = comments.comments_users_id', 'left');
    $CI->db->join('comusers', 'comusers.comusers_id = comments.comments_comusers_id', 'left');

    // вручную делаем этот where, потому что CodeIgniter его неверно экранирует
    $CI->db->where($CI->db->dbprefix . 'page.page_id', $CI->db->dbprefix . 'comments.comments_page_id', false);

    // для private-записей комментарии разрешаем
    if (isset($page['page_status']) and $page['page_status'] === 'private')
        $CI->db->where('page.page_status', 'private');
    else
        $CI->db->where('page.page_status', 'publish');

    $CI->db->order_by('comments.comments_date', $r['order']);

    if ($r['limit']) $CI->db->limit($r['limit']);

    $CI->db->from('comments, page');

    $query = $CI->db->get();

    if ($query->num_rows() > 0) {
        $comments = $query->result_array();

        foreach ($comments as $key => $comment) {
            $commentator = 3; // комментатор: 1-комюзер 2-автор 3-аноним

            if ($comment['comusers_id']) {
                // это комюзер
                if ($comment['comusers_nik'])
                    $comment['comments_author_name'] = $comment['comusers_nik'];
                else
                    $comment['comments_author_name'] = $r['add_author_name'] . ' ' . $comment['comusers_id'];
                $comment['comments_url'] = '<a href="' . getinfo('siteurl') . 'users/' . $comment['comusers_id'] . '">' . $comment['comments_author_name'] . '</a>';

                // есть адрес страницы
                if ($comment['comusers_url']) {
                    // зачистка XSS
                    $comments[$key]['comusers_url'] = mso_xss_clean($comment['comusers_url'], '');
                }

                // зачистка XSS комюзер имя
                if ($comment['comusers_nik']) {
                    $comments[$key]['comusers_nik'] = mso_xss_clean($comment['comusers_nik']);
                }

                $commentator = 1;

                if (isset($all_comusers[$comment['comusers_id']]))
                    $comments[$key]['comusers_count_comments'] = $all_comusers[$comment['comusers_id']];
            } elseif ($comment['users_id']) // это автор
            {
                if ($comment['users_url'])
                    $comment['comments_url'] = '<a href="' . $comment['users_url'] . '">' . $comment['users_nik'] . '</a>';
                else
                    $comment['comments_url'] = $comment['users_nik'];

                $commentator = 2;
            } else // просто аноним
            {
                if (!$comment['comments_author_name']) $comment['comments_author_name'] = $r['anonim_no_name'];

                if ($r['anonim_twitter']) {
                    // разрешено проверять это твиттер-логин?

                    if (strpos($comment['comments_author_name'], '@') === 0) {
                        // первый символ @
                        $lt = mso_slug(substr($comment['comments_author_name'], 1)); // вычленим @

                        $lt = mso_xss_clean($lt, 'Error', $lt, true); // зачистка XSS

                        $comment['comments_url'] = '<a href="https://twitter.com/' . $lt . '" rel="nofollow">@' . $lt . '</a>';
                    } else {
                        $comment['comments_url'] = $comment['comments_author_name'] . $r['anonim_title'];
                    }
                } else {
                    $comment['comments_url'] = $comment['comments_author_name'] . $r['anonim_title'];
                }
            }

            $comments_content = $comment['comments_content'];

            if (mso_hook_present('comments_content_custom'))
                $comments_content = mso_hook('comments_content_custom', $comments_content);
            else
                $comments_content = mso_comments_autotag($comments_content, $commentator, $r);

            $comments_content = mso_hook('comments_content_out', $comments_content);

            $comments[$key]['comments_content'] = $comments_content;
            $comments[$key]['comments_url'] = $comment['comments_url'];
        }
    } else
        $comments = [];

    return $comments;
}

// парсер текста для комментариев
function mso_comments_autotag($text, $commentator = 3, $r = ['tags' => '<p><img><strong><em><i><b><u><s><pre><code><blockquote>', 'tags_comusers' => '<p><img><strong><em><i><b><u><s><pre><code><blockquote>', 'tags_users' => '<p><img><strong><em><i><b><u><s><pre><code><blockquote>'])
{
    // раньше использовался mso_auto_tag теперь свой вариант
    if ($commentator == 1)
        $text = strip_tags($text, $r['tags_comusers']);
    elseif ($commentator == 2)
        $text = strip_tags($text, $r['tags_users']);
    else
        $text = strip_tags($text, $r['tags']);

    $text = mso_comments_content($text);
    $text = mso_hook('comments_content', $text);

    return $text;
}

// обработка через парсер parser_default
function mso_comments_content($text = '')
{
    // текст комментария прогоняется через стандартный парсер
    if (!function_exists('parser_default_content')) {
        require_once getinfo('plugins_dir') . 'parser_default/index.php';
    }

    $text = str_replace("\n", "<br>", $text); // обязательная замена
    $text = parser_default_content($text);
    $text = mso_hook('comments_content_out', $text);

    return $text;
}

// функция отправляет админу уведомление о новом комментарии
// первый парметр id, второй данные текст и т.д.
function mso_email_message_new_comment($id = 0, $data = [], $page_title = '')
{
    $data['page_title'] = $page_title; // заголовок страницы
    $data['id'] = $id; // номер комментария
    $data['comments_content'] =    mso_xss_clean($data['comments_content']);

    // хук на который можно повесить подписку на новые комментарии
    mso_hook('mso_email_message_new_comment', $data);

    // рассылаем комментарий всем, кто на него подписан
    mso_email_message_new_comment_subscribe($data);

    // После рассылки смотрим, какие уведомления мы хотим получать.
    $level = mso_get_option('email_comments_level', 'general', 1);
    $return = false; //А это потому, что пых не понимает return false; внутри кейсов.
    switch ($level) {
        case 6:
            $return = true;
            break;  // Ни от кого.
        case 5:
            if ($data['comments_approved']) $return = true;
            break;  // Требующий модерации
        case 4:
            if ((array_key_exists('comments_users_id', $data) or array_key_exists('comments_comusers_id', $data))) $return = true;
            break;
        case 3:
            if (!array_key_exists('comments_comusers_id', $data)) $return = true;
            break; // От комментаторов
        case 2:
            if (array_key_exists('comments_users_id', $data)) $return = true;
            break;     // От всех кроме юзеров
        case 1:
            break;  // От всех
    }

    if ($return) return false;

    $email = mso_get_option('comments_email', 'general', false); // email куда приходят уведомления

    // если не задан, отдельный email, то берём email администратора.
    if (!$email) $email = mso_get_option('admin_email', 'general', false);

    if (!$email) return false;

    $CI = &get_instance();

    if (!$data['comments_approved']) // нужно промодерировать
        $subject = '[' . getinfo('name_site') . '] ' . '(-) ' . tf('Новый комментарий') . ' (' . $id . ') "' . $page_title . '"';
    else
        $subject = '[' . getinfo('name_site') . '] ' . tf('Новый комментарий') . ' (' . $id . ') "' . $page_title . '"';

    // не посылаем уведомление о своём комментарии если нет специальной опции
    if (!mso_get_option('subscribe_message_my_comment', 'general', true) && isset($data['comments_users_id']) && $data['comments_users_id'] == getinfo('users_id')) return false;

    // шаблон уведомления
    $def_option = 'Новый комментарий на "{{ $page_title }}"
{{ $comment_url }} 
 
{% if (!$comments_approved) : %}
Комментарий требует модерации: {{ $edit_link }} 
 
{% endif %}
Автор IP: {{ $comment_ip }} 
Referer: {{ $comment_referer }} 
Дата: {{ $comment_date }} 
 
{% if ($user) : %}
Пользователь: {{ $user_id }} 
{% endif %}
{% if ($comuser) : %}
Комюзер: id={{ $comuser_id }}, ник: {{ $comuser_nik }}, email: {{ $comuser_email }} 
Профиль: {{ $comuser_url }} 
{% endif %}
{% if ($anonim) : %}
Аноним: {{ $anonim }} 
{% endif %}
 
Текст:
{{ $comment_content }} 
 
Администрировать комментарий вы можете по ссылке:
{{ $edit_link }}
';

    $template = mso_get_option('template_email_message_new_comment', 'general', $def_option);
    $comment_url = mso_get_permalink_page($data['comments_page_id'])  . '#comment-' . $id;
    $comments_approved = $data['comments_approved'];
    $comment_ip = $data['comments_author_ip'];
    $comment_referer = mso_clean_str($_SERVER['HTTP_REFERER'], 'xss');
    $comment_date = $data['comments_date'];
    $user = $comuser = $anonim = false;

    if (isset($data['comments_users_id'])) {
        $user = true;
        $user_id = $data['comments_users_id'];
    }

    if (isset($data['comments_comusers_id'])) {
        $comuser = true;
        $comuser_id = $data['comments_comusers_id'];

        $CI->db->select('comusers_nik, comusers_email');
        $CI->db->from('comusers');
        $CI->db->where('comusers_id', $data['comments_comusers_id']);

        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $comusers = $query->row();

            $comuser_nik = $comusers->comusers_nik;
            $comuser_email = $comusers->comusers_email;
            $comuser_url = getinfo('siteurl') . 'users/' . $data['comments_comusers_id'];
        }
    }

    if (isset($data['comments_author_name'])) {
        $anonim = $data['comments_author_name'];
    }

    $comment_content = $data['comments_content'];
    $edit_link = getinfo('site_admin_url') . 'comments/edit/' . $id;
    $template = mso_tmpl_prepare($template, false);

    ob_start();
    eval($template);
    $text = ob_get_contents();
    ob_end_clean();

    // Чтобы плагин smtp_mail точно знал, что ему подсунули коммент, а не вычислял это по subject
    $data = array_merge($data, ['comment' => true]);

    // А зная о комментарии, он сможет сотворить некоторые бонусы.
    return mso_mail($email, $subject, $text, false, $data);
}

// функция отправляет новому комюзеру уведомление о новой регистрации
// первый параметр id, второй данные
// третий - если это автоматическая активация и подтверждение не требуется
function mso_email_message_new_comuser($comusers_id = 0, $ins_data = [], $comusers_activate_auto = false)
{
    $email = $ins_data['comusers_email']; // email куда приходят уведомления

    if (!$email) return false;

    // comusers_password
    // comusers_activate_key

    $subject = tf('Регистрация на ') . getinfo('title');

    // шаблон уведомления
    $def_option = '{% if ($activate_auto) : %}
Спасибо за регистрацию на сайте "{{ $name_site }}" — {{ $site_url }} 
 
Ваша страница:
{{ $comuser_url }} 
 
Ваш код активации:
{{ $activate_key }} 
 
Сохраните это письмо, поскольку код активации может понадобиться для смены пароля.
{% else: %}
Вы или кто-то еще зарегистрировал ваш адрес на сайте "{{ $name_site }}" — {{ $site_url }} 
 
Если это действительно сделали вы, то вам нужно подтвердить эту регистрацию. Для этого следует пройти по ссылке:
{{ $comuser_url }} 
 
И ввести следующий код для активации:
{{ $activate_key }} 
 
(Сохраните это письмо, поскольку код активации может понадобиться для смены пароля.)
 
Если же регистрацию выполнили не вы, то просто удалите это письмо.
{% endif %}
';

    $template = mso_get_option('template_email_message_new_comuser', 'general', $def_option);

    $activate_auto = $comusers_activate_auto;
    $name_site = getinfo('name_site');
    $site_url = getinfo('siteurl');
    $comuser_url = getinfo('siteurl') . 'users/' . $comusers_id;
    $activate_key = $ins_data['comusers_activate_key'];
    $template = mso_tmpl_prepare($template, false);

    ob_start();
    eval($template);
    $text = ob_get_contents();
    ob_end_clean();

    return mso_mail($email, $subject, $text, $email); // поскольку это регистрация, то отправитель - тот же email
}

// функция добавляет новый коммент и выводит сообщение о результате
function mso_get_new_comment($args = [])
{
    global $MSO;

    $args = mso_hook('mso_get_new_comment_args', $args);

    if ($post = mso_check_post(array('comments_session', 'comments_submit', 'comments_page_id', 'comments_content'))) {
        // mso_checkreferer(); // если нужно проверять на реферер
        $CI = &get_instance();

        // заголовок страницы
        if (!isset($args['page_title'])) $args['page_title'] = '';

        // стили
        if (!isset($args['css_ok'])) $args['css_ok'] = 'comment-ok';
        if (!isset($args['css_error'])) $args['css_error'] = 'comment-error';

        // разрешенные тэги
        if (!isset($args['tags'])) $args['tags'] = '<p><blockquote><br><span><strong><strong><em><i><b><u><s><pre><code>';

        // обрабатывать текст на xss-атаку
        if (!isset($args['xss_clean'])) $args['xss_clean'] = true;

        // если найдена xss-атака, то не публиковать комментарий
        if (!isset($args['xss_clean_die'])) $args['xss_clean_die'] = false;

        // запрещенные слова как имя автора
        if (!isset($args['noword']))
            $args['noword'] = [
                '.com', '.ru', '.net', '.org', '.info', '.ua',
                '.su', '.name', '/', 'www.', 'http', ':', '-', '"',
                '«', '»', '%', '<', '>', '&', '*', '+', '\''
            ];

        mso_hook('add_new_comment');

        if (!mso_checksession($post['comments_session']))
            return '<div class="' . $args['css_error'] . '">' . tf('Ошибка сессии! Обновите страницу') . '</div>';

        if (!$post['comments_page_id'])
            return '<div class="' . $args['css_error'] . '">' . tf('Ошибка!') . '</div>';

        $comments_page_id = $post['comments_page_id'];
        $id = (int) $comments_page_id;

        if ((string) $comments_page_id != (string) $id) $id = false; // $comments_page_id не число
        if (!$id) return '<div class="' . $args['css_error'] . '">' . tf('Ошибка!') . '</div>';

        // капчу проверим
        // если этот хук возвращает false, значит капча неверная
        if (!mso_hook('comments_new_captcha', true)) {
            // если определен хук на неверную капчу, отдаем его
            if (mso_hook_present('comments_new_captcha_error')) {
                return mso_hook('comments_new_captcha_error');
            } else {
                return '<div class="' . $args['css_error'] . '">' . tf('Ошибка! Неверно введены нижние символы!') . '</div>';
            }
        }

        // вычищаем от запрещенных тэгов
        if ($args['tags']) {
            $t = $post['comments_content'];
            $t = strip_tags($t, $args['tags']); // оставим только разрешенные тэги
            $post['comments_content'] = $t; // сохраним как текст комментария
        }

        // если указано рубить коммент при обнаруженной xss-атаке 
        if ($args['xss_clean_die'] and mso_xss_clean($post['comments_content'], true, false) === true) {
            return '<div class="' . $args['css_error'] . '">' . tf('Обнаружена XSS-атака!') . '</div>';
        }

        if (!trim($post['comments_content']))
            return '<div class="' . $args['css_error'] . '">' . tf('Ошибка, нет текста!') . '</div>';

        // возможно есть текст, но только из одних html - не пускаем
        if (!trim(strip_tags(trim($post['comments_content']))))
            return '<div class="' . $args['css_error'] . '">' . tf('Ошибка, нет полезного текста!') . '</div>';

        // вычищаем текст от xss
        if ($args['xss_clean']) {
            $post['comments_content'] =  mso_xss_clean($post['comments_content']);
            // проставим pre исправление ошибки CodeIgniter
            $post['comments_content'] = str_replace('&lt;/pre>', '</pre>', $post['comments_content']);
        }

        $comments_author_ip = $_SERVER['REMOTE_ADDR'];
        $comments_date = date('Y-m-d H:i:s');
        $comments_content = mso_hook('new_comments_content', $post['comments_content']);

        // есть дли родитель у комментария
        $comments_parent_id = $post['comments_parent_id'] ?? '0';

        // провека на спам - проверим через хук new_comments_check_spam
        $comments_check_spam = mso_hook(
            'new_comments_check_spam',
            [
                'comments_content' => $comments_content,
                'comments_date' => $comments_date,
                'comments_author_ip' => $comments_author_ip,
                'comments_page_id' => $comments_page_id,
                'comments_server' => $_SERVER,
                'comments_parent_id' => $comments_parent_id,
                'comments_author' => $post['comments_author'] ?? false,
                'comments_email' => $post['comments_email'] ?? false,
                'comusers_email' => $post['comusers_email'] ?? false,
                'comments_user_id' => $post['comments_user_id'] ?? false,
                'comments_comusers_nik' => $post['comments_comusers_nik'] ?? false,
                'comments_comusers_url' => $post['comments_comusers_url'] ?? false,
            ],
            false
        );

        // если есть спам, то возвращается что-то отличное от comments_content
        // если спама нет, то должно вернуться false
        // если есть подозрения, то возвращается массив с moderation (comments_approved)
        // если есть параметр check_spam=true, значит определен спам и он вообще не пускается
        // сообщение для вывода в парметре 'message'

        // разрешение антиспама moderation
        // -1 - не определено, 0 - можно разрешить, 1 - отдать на модерацию
        $moderation = -1;

        if ($comments_check_spam) {
            if (isset($comments_check_spam['check_spam']) and $comments_check_spam['check_spam'] == true) {
                if (isset($comments_check_spam['message']) and $comments_check_spam['message'])
                    return '<div class="' . $args['css_error'] . '">' . $comments_check_spam['message'] . '</div>';
                else
                    return '<div class="' . $args['css_error'] . '">' . tf('Ваш комментарий определен как спам и удален.') . '</div>';
            } else {
                // спам не определен, но возможно стоит moderation - принудительная модерация
                if (isset($comments_check_spam['moderation'])) $moderation = $comments_check_spam['moderation'];
            }
        }

        // проверим есть ли уже такой комментарий
        // проверка по ip и тексту
        $CI->db->select('comments_id');
        $CI->db->where([
            'comments_page_id' => $comments_page_id,
            'comments_author_ip' => $comments_author_ip,
            'comments_content' => $comments_content,
        ]);

        $query = $CI->db->get('comments');

        if ($query->num_rows()) {
            return '<div class="' . $args['css_error'] . '">' . tf('Похоже, вы уже отправили этот комментарий...') . '</div>';
        }

        if (is_login()) {
            // коммент от автора
            $comments_users_id = $MSO->data['session']['users_id'];

            $ins_data = array(
                'comments_users_id' => $comments_users_id,
                'comments_page_id' => $comments_page_id,
                'comments_author_ip' => $comments_author_ip,
                'comments_date' => $comments_date,
                'comments_content' => $comments_content,
                'comments_parent_id' => $comments_parent_id,
                'comments_approved' => 1, // авторы могут сразу публиковать комменты без модерации
                'comments_author_name' => '',
            );

            $res = ($CI->db->insert('comments', $ins_data)) ? '1' : '0';

            if ($res) {
                $id_comment_new = $CI->db->insert_id();

                mso_email_message_new_comment($id_comment_new, $ins_data, $args['page_title']);
                mso_flush_cache();
                $CI->db->cache_delete_all();
                mso_hook('new_comment');
                mso_redirect(mso_current_url() . '#comment-' . $id_comment_new);
            } else {
                return '<div class="' . $args['css_error'] . '">' . tf('Ошибка добавления комментария') . '</div>';
            }
        } else {
            if (isset($post['comments_reg'])) {
                // комюзер или аноном
                if ($post['comments_reg'] == 'reg') {
                    // нужно зарегистрировать или уже есть регистрация
                    // проверим есть ли разршение на комментарии от комюзеров
                    // для случаев подделки post-запроса
                    if (!mso_get_option('allow_comment_comusers', 'general', '1'))
                        return '<div class="' . $args['css_error'] . '">' . tf('Error allow_comment_comusers') . '</div>';

                    if (!isset($post['comments_email']) or !$post['comments_email'])
                        return '<div class="' . $args['css_error'] . '">' . tf('Нужно указать Email') . '</div>';

                    if (!isset($post['comments_password']) or !$post['comments_password'])
                        return '<div class="' . $args['css_error'] . '">' . tf('Нужно указать пароль') . '</div>';

                    $comments_email = mso_strip($post['comments_email']);
                    $comments_password = mso_strip($post['comments_password']);

                    if (!mso_valid_email($comments_email))
                        return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный Email') . '</div>';

                    // проверим время последнего комментария чтобы не очень часто
                    if (!mso_last_activity_comment())
                        return '<div class="' . $args['css_error'] . '">' . tf('Слишком частые комментарии. Попробуйте позже.') . '</div>';

                    // вначале нужно зарегистрировать comюзера - получить его id и только после этого добавить сам коммент
                    // но вначале есть смысл проверить есть ли такой ком-пользователь

                    $comusers_id = false;

                    $CI->db->select('comusers_id, comusers_password');
                    $CI->db->where('comusers_email', $comments_email);
                    $query = $CI->db->get('comusers');

                    if ($query->num_rows()) {
                        // есть такой комюзер
                        $row = $query->row_array(1);

                        // пароль не нужно шифровать mso_md5
                        if (isset($post['comments_password_md']) and $post['comments_password_md']) {
                            if ($row['comusers_password'] != $comments_password) // пароль неверный
                                return '<div class="' . $args['css_error'] . '">' . tf('Неверный пароль') . '</div>';
                        } else {
                            if ($row['comusers_password'] != mso_md5($comments_password)) // пароль неверный
                                return '<div class="' . $args['css_error'] . '">' . tf('Неверный пароль') . '</div>';
                        }

                        $comusers_id = $row['comusers_id']; // получаем номер комюзера
                    } else {
                        // такого комюзера нет
                        $ins_data = array(
                            'comusers_email' => $comments_email,
                            'comusers_password' => mso_md5($comments_password)
                        );

                        // генерируем случайный ключ активации
                        $ins_data['comusers_activate_key'] = mso_md5(rand());
                        $ins_data['comusers_date_registr'] = date('Y-m-d H:i:s');
                        $ins_data['comusers_last_visit'] = date('Y-m-d H:i:s');
                        $ins_data['comusers_ip_register'] = $_SERVER['REMOTE_ADDR'];
                        $ins_data['comusers_notify'] = '1'; // сразу включаем подписку на уведомления

                        // если сразу отправлен адрес ссайта
                        if (isset($post['comments_comusers_url']) and $post['comments_comusers_url']) {
                            $comusers_url = htmlspecialchars(mso_xss_clean(strip_tags($post['comments_comusers_url'])));
                            // if (strpos($comusers_url, 'http://') === false) $comusers_url = 'http://' . $comusers_url;

                            if ($comusers_url) $ins_data['comusers_url'] = $comusers_url;
                        }

                        // если сразу отправлен ник
                        if (isset($post['comments_comusers_nik']) and $post['comments_comusers_nik']) {
                            $ins_data['comusers_nik'] = htmlspecialchars(mso_xss_clean(strip_tags($post['comments_comusers_nik'])));
                        }

                        // Автоматическая активация новых комюзеров
                        // если активация стоит автоматом, то сразу её и прописываем
                        if (mso_get_option('comusers_activate_auto', 'general', '0'))
                            $ins_data['comusers_activate_string'] = $ins_data['comusers_activate_key'];

                        $res = ($CI->db->insert('comusers', $ins_data)) ? '1' : '0';

                        if ($res) {
                            // сохраним в сессии время отправления комментария - используется в mso_last_activity_comment
                            $CI->session->set_userdata('last_activity_comment', time());

                            $comusers_id = $CI->db->insert_id(); // номер добавленной записи

                            // нужно добавить опцию в мета «новые комментарии, где я участвую» subscribe_my_comments
                            // вначале грохаем если есть такой ключ
                            $CI->db->where('meta_table', 'comusers');
                            $CI->db->where('meta_id_obj', $comusers_id);
                            $CI->db->where('meta_key', 'subscribe_my_comments');
                            $CI->db->delete('meta');

                            // теперь добавляем как новый
                            $ins_data2 = array(
                                'meta_table' => 'comusers',
                                'meta_id_obj' => $comusers_id,
                                'meta_key' => 'subscribe_my_comments',
                                'meta_value' => '1'
                            );

                            $CI->db->insert('meta', $ins_data2);

                            // почему CodeIgniter не может так?
                            // INSERT INTO table SET column = 1, id=1 ON DUPLICATE KEY UPDATE column = 2

                            // отправляем ему уведомление с кодом активации
                            mso_email_message_new_comuser($comusers_id, $ins_data, mso_get_option('comusers_activate_auto', 'general', '0'));

                            mso_flush_cache();
                            $CI->db->cache_delete_all();
                        } else {
                            return '<div class="' . $args['css_error'] . '">' . tf('Ошибка регистрации') . '</div>';
                        }
                    }

                    if ($comusers_id) {
                        // Модерация комюзеров 1 - модерировать
                        $comments_com_approved = mso_get_option('new_comment_comuser_moderate', 'general', 1);

                        // если включена модерация комюзеров
                        // и включена опция только первого комментария
                        // то получаем кол-во комментариев комюзера
                        if ($comments_com_approved and mso_get_option('new_comment_comuser_moderate_first_comment', 'general', 0)) {
                            $all_comusers = mso_comuser_update_count_comment(); // список комюзер => колво комментов

                            // есть такой комюзер и у него более 1 комментария
                            if (isset($all_comusers[$comusers_id]) and $all_comusers[$comusers_id] > 0)
                                $comments_com_approved = 0; // разрешаем публикацию
                        }

                        // но у нас в базе хранится значение наоборот - 1 разрешить 0 - запретить
                        $comments_com_approved = !$comments_com_approved;

                        if ($moderation == 1) $comments_com_approved = 0; // антиспам определил, что нужно премодерировать

                        if ($comments_com_approved == 1) {
                            // если разрешено
                            $comments_com_approved = mso_hook(
                                'new_comments_check_spam_comusers',
                                [
                                    'comments_page_id' => $comments_page_id,
                                    'comments_comusers_id' => $comusers_id,
                                    'comments_com_approved' => $comments_com_approved,
                                ],
                                1
                            );
                        }

                        // комюзер добавлен или есть
                        // теперь сам коммент
                        $ins_data = [
                            'comments_page_id' => $comments_page_id,
                            'comments_comusers_id' => $comusers_id,
                            'comments_author_ip' => $comments_author_ip,
                            'comments_date' => $comments_date,
                            'comments_content' => $comments_content,
                            'comments_approved' => $comments_com_approved,
                            'comments_parent_id' => $comments_parent_id,
                            'comments_author_name' => '',
                        ];

                        // проверим время последнего комментария чтобы не очень часто
                        if (!mso_last_activity_comment())
                            return '<div class="' . $args['css_error'] . '">' . tf('Слишком частые комментарии. Попробуйте позже.') . '</div>';

                        $res = ($CI->db->insert('comments', $ins_data)) ? '1' : '0';

                        if ($res) {
                            // сохраним в сессии время отправления комментария - используется в mso_last_activity_comment
                            $CI->session->set_userdata('last_activity_comment', time());

                            $id_comment_new = $CI->db->insert_id();

                            // посколько у нас идет редирект, то данные об отправленном комменте
                            // сохраняем в сессии номер комментария
                            if (isset($MSO->data['session'])) {
                                $CI->session->set_userdata(array('comments' =>
                                array(
                                    // $CI->db->insert_id()=>$comments_page_id
                                    $id_comment_new
                                )));
                            }

                            mso_email_message_new_comment($id_comment_new, $ins_data, $args['page_title']);
                            mso_flush_cache();
                            $CI->db->cache_delete_all();
                            mso_hook('new_comment');

                            // если комюзер не залогинен, то сразу логиним его

                            $CI->db->select('comusers_id, comusers_password, comusers_email, 
                                    comusers_nik, comusers_url, comusers_avatar_url, comusers_last_visit');
                            $CI->db->where('comusers_email', $comments_email);
                            $CI->db->where('comusers_password', mso_md5($comments_password));
                            $query = $CI->db->get('comusers');

                            if ($query->num_rows()) {
                                // есть такой комюзер
                                $comuser_info = $query->row_array(1); // вся инфа о комюзере

                                // сразу же обновим поле последнего входа
                                $CI->db->where('comusers_id', $comuser_info['comusers_id']);
                                $CI->db->update('comusers', array('comusers_last_visit' => date('Y-m-d H:i:s')));

                                $expire  = time() + 60 * 60 * 24 * 30; // 30 дней = 2592000 секунд

                                $name_cookies = 'maxsite_comuser';
                                $value = serialize($comuser_info);

                                // ставим куку и редиректимся автоматом
                                mso_add_to_cookie(
                                    $name_cookies,
                                    $value,
                                    $expire,
                                    mso_current_url(true) . '#comment-' . $id_comment_new
                                );
                                exit;
                            }

                            mso_redirect(mso_current_url() . '#comment-' . $id_comment_new);
                        } else {
                            return '<div class="' . $args['css_error'] . '">' . tf('Ошибка добавления комментария') . '</div>';
                        }
                    }
                } elseif ($post['comments_reg'] == 'noreg') {
                    // комментарий от анонима

                    // проверим есть ли разрешение на комментарии от анонимов
                    // для случаев подделки post-запроса
                    if (!mso_get_option('allow_comment_anonim', 'general', '1'))
                        return '<div class="' . $args['css_error'] . '">' . tf('Error allow_comment_anonim') . '</div>';

                    // проверим время последнего комментария чтобы не очень часто
                    if (!mso_last_activity_comment())
                        return '<div class="' . $args['css_error'] . '">' . tf('Слишком частые комментарии. Попробуйте позже.') . '</div>';

                    if (isset($post['comments_author'])) {
                        $comments_author_name = mso_strip($post['comments_author']);
                        $comments_author_name = str_replace($args['noword'], '', $comments_author_name);
                        $comments_author_name = htmlspecialchars(trim($comments_author_name));
                        if (!$comments_author_name) $comments_author_name = tf('Аноним');
                    } else {
                        $comments_author_name = 'Аноним';
                    }

                    // можно ли публиковать без модерации?
                    $comments_approved = mso_get_option('new_comment_anonim_moderate', 'general', 1);

                    // но у нас в базе хранится значение наоборот - 1 разрешить 0 - запретить
                    $comments_approved = !$comments_approved;

                    if ($moderation == 1) $comments_approved = 0; // антиспам определил, что нужно премодерировать

                    $ins_data = array(
                        'comments_page_id' => $comments_page_id,
                        'comments_author_name' => $comments_author_name,
                        'comments_author_ip' => $comments_author_ip,
                        'comments_date' => $comments_date,
                        'comments_content' => $comments_content,
                        'comments_approved' => $comments_approved,
                        'comments_parent_id' => $comments_parent_id,
                    );

                    $res = ($CI->db->insert('comments', $ins_data)) ? '1' : '0';

                    if ($res) {
                        $id_comment_new = $CI->db->insert_id();

                        // сохраним в сессии время отправления комментария - используется в mso_last_activity_comment
                        $CI->session->set_userdata('last_activity_comment', time());

                        // посколько у нас идет редирект, то данные об отправленном комменте
                        // сохраняем в сессии номер комментария
                        if (isset($MSO->data['session'])) {
                            $CI->session->set_userdata(array('comments' =>
                            array(
                                // $CI->db->insert_id()=>$comments_page_id
                                $id_comment_new
                            )));
                        }
                        mso_email_message_new_comment($id_comment_new, $ins_data, $args['page_title']);
                        mso_flush_cache();
                        $CI->db->cache_delete_all();
                        mso_hook('new_comment');
                        mso_redirect(mso_current_url() . '#comment-' . $id_comment_new);
                    } else {
                        return '<div class="' . $args['css_error'] . '">' . tf('Ошибка добавления комментария') . '</div>';
                    }
                }
            }
        }
    }
}

// получаем данные комюзера.
// если id = 0, то номер получаем из сессии или текущего сегмента (2)
function mso_get_comuser($id = 0, $args = [])
{
    global $MSO;

    if (!$id) {
        // не указан id, получаем его из сессии
        if (isset($MSO->data['session']['comuser']) and $MSO->data['session']['comuser'])
            $id = $MSO->data['session']['comuser']['comusers_id'];
        else
            $id = mso_segment(2); // или сегмент в url
    }

    if (!$id) return []; // нет номера, выходим

    if (!is_numeric($id)) return []; // если id указан не номером, выходим

    if (!isset($args['limit'])) $args['limit'] = 20;
    if (!isset($args['tags'])) $args['tags'] = '<p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
    if (!isset($args['order'])) $args['order'] = 'comments_date';
    if (!isset($args['asc'])) $args['asc'] = 'desc';

    $CI = &get_instance();
    $CI->db->select('comusers.*, COUNT(comments_comusers_id) as comusers_count_comment_real');
    $CI->db->from('comusers');
    $CI->db->where('comusers_id', $id);
    $CI->db->limit(1);

    // отдавать все комменты, включая и неотмодерированные
    //$CI->db->where('comments.comments_approved', '1');

    $CI->db->join('comments', 'comusers.comusers_id = comments.comments_comusers_id', 'left');
    $CI->db->group_by('comments_comusers_id');

    $query = $CI->db->get();

    if ($query->num_rows() > 0) {
        $comuser = $query->result_array(); // данные комюзера
        $comuser_count_comment_first = $comuser[0]['comusers_count_comments']; // первоначальное значание колво комментариев

        // подсоединим к нему [comments] - все его комментарии
        $CI->db->select('comments.*, page.page_id, page.page_title, page.page_slug');
        $CI->db->from('comments');
        $CI->db->where('comments_comusers_id', $id);
        // $CI->db->where('page.page_status', 'publish');
        // $CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));

        $CI->db->where('comments.comments_approved', '1');
        $CI->db->join('page', 'page.page_id = comments.comments_page_id');
        $CI->db->order_by('comments_date', $args['asc']);

        if ($args['limit']) $CI->db->limit($args['limit']);

        $query = $CI->db->get();
        $comments = []; // все комменты

        if ($query->num_rows() > 0) {
            // нужно обработать тексты комментариев на предмет всяких хуков и лишних тэгов
            $comments = $query->result_array();

            foreach ($comments as $key => $comment) {
                $comments[$key]['comments_content'] = mso_comments_content($comment['comments_content']);
            }

            $comuser[0]['comments'] = $comments;
            // $comuser[0]['comments'] = $query->result_array();

            $comuser[0]['comusers_count_comments'] = count($comments);
        } else {
            $comuser[0]['comments'] = [];
        }

        // колво комментариев не совпадает с реальным - нужно обновить
        if ($comuser_count_comment_first != count($comments)) {
            mso_comuser_set_count_comment($id, count($comments));
        }

        // в секцию meta добавим все метаполя данного юзера
        $CI->db->select('meta_key, meta_value');
        $CI->db->from('meta');
        $CI->db->where('meta_table', 'comusers');
        $CI->db->where('meta_id_obj', $id);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            // переделаем полученный массив в key = value
            foreach ($query->result_array() as $val) {
                $comuser[0]['comusers_meta'][$val['meta_key']] = $val['meta_value'];
            }
        } else {
            $comuser[0]['comusers_meta'] = [];
        }

        // от вских гадостей
        $comuser[0]['comusers_url'] =  mso_xss_clean($comuser[0]['comusers_url']);

        // if ($comuser[0]['comusers_url'] and strpos($comuser[0]['comusers_url'], 'http://') === false)
        //    $comuser[0]['comusers_url'] = 'http://' . $comuser[0]['comusers_url'];

        $comuser[0]['comusers_msn'] =  mso_xss_clean($comuser[0]['comusers_msn']); // twitter
        $comuser[0]['comusers_msn'] = mso_slug(str_replace('@', '', $comuser[0]['comusers_msn']));

        // подчистка 
        $comuser[0] = mso_clean_post([
            'comusers_nik' => 'base',
            'comusers_icq' => 'base',
            'comusers_jaber' => 'base',
            'comusers_skype' => 'base',
            'comusers_description' => 'base',
            'comusers_msn' => 'base',
            'comusers_url' => 'base',
        ], $comuser[0]);

        // pr($comuser);

        return $comuser;
    } else {
        return [];
    }
}

// устанавливаем колво комментариев у указаного комюзера
function mso_comuser_set_count_comment($id = 0, $count = -1)
{
    if (!$id) return;

    $CI = &get_instance();

    // не указано количество - нужно его получить
    if ($count == -1) {
        $CI->db->select('COUNT(comments_comusers_id) as comusers_count_comment_real', false);
        $CI->db->from('comusers');
        $CI->db->where('comusers_id', $id);
        $CI->db->where('comments.comments_approved', '1');
        $CI->db->join('comments', 'comusers.comusers_id = comments.comments_comusers_id', 'left');
        $CI->db->group_by('comments_comusers_id');
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $comuser = $query->result_array(); // данные комюзера
            $count = $comuser[0]['comusers_count_comment_real'];
        } else {
            $count = 0;
        }
    }

    $CI->db->where('comusers_id', $id);
    $CI->db->update('comusers', array('comusers_count_comments' => $count));
    $CI->db->cache_delete_all();
}

// функция проверяет в цикле количество реальных комментариев
// с тем, что указано в поле comusers_count_comments
// если данные не совпадают, то выполняется обновление 
// с помощью mso_comuser_set_count_comment()
// функция возвращает $all_comusers - массив всех комюзеров
function mso_comuser_update_count_comment()
{
    $cache_key = 'all_comusers';
    $k = mso_get_cache($cache_key);

    if (!$k) {
        // нет в кэше
        $CI = &get_instance();

        $CI->db->select('comusers_id, comusers_count_comments, COUNT(comments_comusers_id) as comusers_count_comment_real');
        $CI->db->from('comusers');
        $CI->db->where('comments.comments_approved', '1');
        $CI->db->join('comments', 'comusers.comusers_id = comments.comments_comusers_id', 'left');
        $CI->db->group_by('comments_comusers_id');
        $query = $CI->db->get();

        $all_comusers = [];

        if ($query->num_rows() > 0) {
            $comusers = $query->result_array();
            foreach ($comusers as $comuser) {
                $all_comusers[$comuser['comusers_id']] = $comuser['comusers_count_comment_real'];

                // сразу сверим количество кмментариев
                if ($comuser['comusers_count_comments'] != $comuser['comusers_count_comment_real']) // не равно
                    mso_comuser_set_count_comment($comuser['comusers_id'], $comuser['comusers_count_comment_real']);
            }
        }

        mso_add_cache($cache_key, $all_comusers); // в кэш
    } else {
        $all_comusers = $k;
    }

    return $all_comusers;
}

// обработка POST из формы комюзера
function mso_comuser_edit($args = [])
{
    global $MSO;

    if (!isset($args['css_ok'])) $args['css_ok'] = 'comment-ok';
    if (!isset($args['css_error'])) $args['css_error'] = 'comment-error';

    // id комюзера, который в сессии
    if (isset($MSO->data['session']['comuser']) and $MSO->data['session']['comuser'])
        $id_session = $MSO->data['session']['comuser']['comusers_id'];
    else
        $id_session = false;

    if ($post = mso_check_post(['f_session_id', 'f_submit', 'f_comusers_activate_key'])) {
        // это активация
        // защита рефера
        mso_checkreferer();

        // защита сессии - если не нужно закомментировать строчку!
        if ($MSO->data['session']['session_id'] != $post['f_session_id']) mso_redirect();

        // получаем номер юзера id из f_submit[]
        $id = (int) mso_array_get_key($post['f_submit']);

        if (!$id) return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный номер пользователя') . '</div>';

        // проверяем id в сессии с сабмитом 
        // if ($id != $id_session) 
        //	return '<div class="' . $args['css_error']. '">'. t('Ошибочный номер пользователя'). '</div>';

        $f_comusers_activate_key = trim($post['f_comusers_activate_key']);

        if (!$f_comusers_activate_key) return '<div class="' . $args['css_error'] . '">' . tf('Неверный (пустой) ключ') . '</div>';

        // нужно проверить если у указанного комюзера не равные ключи
        // если они равны, то ничего не делаем
        $CI = &get_instance();
        $CI->db->select('comusers_activate_string, comusers_activate_key');
        $CI->db->from('comusers');
        $CI->db->where('comusers_id', $id);
        $CI->db->limit(1);

        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $comuser = $query->result_array(); // данные комюзера

            if ($comuser[0]['comusers_activate_string'] == $comuser[0]['comusers_activate_key']) {
                // уже равны, активация не требуется
                return '<div class="' . $args['css_ok'] . '">' . tf('Активация уже выполнена') . '</div>';
            } else {
                // ключи в базе не равны
                // сверяем с переданным ключом из формы
                if ($f_comusers_activate_key == $comuser[0]['comusers_activate_key']) {
                    // верный ключ - обновляем в базе

                    $CI->db->where('comusers_id', $id);
                    $res = ($CI->db->update(
                        'comusers',
                        array('comusers_activate_string' => $f_comusers_activate_key)
                    )) ? '1' : '0';

                    $CI->db->cache_delete_all();

                    if ($res)
                        return '<div class="' . $args['css_ok'] . '">' . tf('Активация выполнена!') . '</div>';
                    else
                        return '<div class="' . $args['css_error'] . '">' . tf('Ошибка БД при добавления ключа активации') . '</div>';
                } else {
                    return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный ключ активации') . '</div>';
                }
            }
        } else {
            // вообще нет такого комюзера
            return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный номер пользователя') . '</div>';
        }
    } elseif ($post = mso_check_post(['flogin_session_id', 'flogin_submit', 'flogin_user', 'flogin_password', 'flogin_redirect'])) {
        // логинимся через стандартную _mso_login()
        _mso_login();

        return;
    }

    // это форма?
    elseif ($post = mso_check_post(['f_session_id', 'f_submit', 'f_comusers_email', 'f_comusers_password', 'f_comusers_nik', 'f_comusers_url', 'f_comusers_icq', 'f_comusers_msn', 'f_comusers_jaber',       'f_comusers_date_birth', 'f_comusers_description', 'f_comusers_avatar_url'])) {
        // это обновление формы
        if (!is_login_comuser())
            return '<div class="' . $args['css_error'] . '">' . tf('Ошибочные данные пользователя') . '</div>';

        // защита рефера
        mso_checkreferer();

        // защита сессии - если не нужно закомментировать строчку!
        if ($MSO->data['session']['session_id'] != $post['f_session_id']) mso_redirect();

        // получаем номер юзера id из f_submit[]
        $id = (int) mso_array_get_key($post['f_submit']);

        if (!$id) return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный номер пользователя') . '</div>';

        // проверяем id в сессии с сабмитом 
        if ($id != $id_session)
            return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный номер пользователя') . '</div>';

        $f_comusers_email = trim($post['f_comusers_email']);
        $f_comusers_password = trim($post['f_comusers_password']);

        if (!$f_comusers_email or !$f_comusers_password)
            return '<div class="' . $args['css_error'] . '">' . tf('Необходимо указать email и пароль') . '</div>';

        // проверим есть ли такой комюзер
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('comusers');

        // CodeIgniter экранирует where, даже когда только условия в полях
        $CI->db->where('comusers_activate_string=comusers_activate_key', '', false); // активация должна уже быть

        $CI->db->where([
            'comusers_id' => $id,
            'comusers_email' => $f_comusers_email,
            'comusers_password' => $f_comusers_password
        ]);
        
        $CI->db->limit(1);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            // все ок - логин пароль верные
            $comuser = $query->result_array(); // данные комюзера

            $f_comusers_avatar_url = mso_strip(
                $post['f_comusers_avatar_url'],
                false,
                ['\\', '|', '?', '%', '*', '`']
            );

            $allowed_ext = array('gif', 'jpg', 'jpeg', 'png'); // разрешенные типы
            $ext = strtolower(str_replace('.', '', strrchr($f_comusers_avatar_url, '.'))); // расширение файла

            if (!in_array($ext, $allowed_ext)) $f_comusers_avatar_url = ''; // запрещенный тип файла
            if (!isset($post['f_comusers_notify'])) $post['f_comusers_notify'] = '0';
            if (!isset($post['f_comusers_skype'])) $post['f_comusers_skype'] = ''; // скайп

            $post = mso_clean_post([
                'f_comusers_nik' => 'base',
                'f_comusers_url' => 'base',
                'f_comusers_icq' => 'base',
                'f_comusers_msn' => 'base',
                'f_comusers_jaber' => 'base',
                'f_comusers_skype' => 'base',
                'f_comusers_date_birth' => 'base',
                'f_comusers_description' => 'base',
                'f_comusers_notify' => 'int',
            ], $post);

            $upd_date = [
                'comusers_nik' => strip_tags($post['f_comusers_nik']),
                'comusers_url' => strip_tags($post['f_comusers_url']),
                'comusers_icq' => strip_tags($post['f_comusers_icq']),
                'comusers_msn' => strip_tags($post['f_comusers_msn']),
                'comusers_jaber' => strip_tags($post['f_comusers_jaber']),
                'comusers_skype' => strip_tags($post['f_comusers_skype']),
                'comusers_date_birth' => strip_tags($post['f_comusers_date_birth']),
                'comusers_description' => strip_tags($post['f_comusers_description']),
                'comusers_avatar_url' => $f_comusers_avatar_url,
                'comusers_notify' => $post['f_comusers_notify'],
            ];

            // pr($upd_date );

            $CI->db->where('comusers_id', $id);
            $res = ($CI->db->update('comusers', $upd_date)) ? '1' : '0';

            // если переданы метаполя, то обновляем и их
            if (isset($post['f_comusers_meta']) and $post['f_comusers_meta']) {
                //pr($post);

                foreach ($post['f_comusers_meta'] as $key => $val) {
                    // вначале грохаем если есть такой ключ
                    $CI->db->where('meta_table', 'comusers');
                    $CI->db->where('meta_id_obj', $id);
                    $CI->db->where('meta_key', $key);
                    $CI->db->delete('meta');

                    // теперь добавляем как новый
                    $ins_data = [
                        'meta_table' => 'comusers',
                        'meta_id_obj' => $id,
                        'meta_key' => $key,
                        'meta_value' => $val
                    ];

                    $CI->db->insert('meta', $ins_data);
                }
            }

            $CI->db->cache_delete_all();
            // mso_flush_cache(); // сбросим кэш

            if ($res)
                return '<div class="' . $args['css_ok'] . '">' . tf('Обновление выполнено!') . '</div>';
            else
                return '<div class="' . $args['css_error'] . '">' . tf('Ошибка БД при обновлении') . '</div>';
        } else {
            return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный email и пароль') . '</div>';
        }
    } // обновление формы
}

// восстановление пароля комюзера
function mso_comuser_lost($args = [])
{
    global $MSO;

    if (!isset($args['css_ok'])) $args['css_ok'] = 'comment-ok';
    if (!isset($args['css_error'])) $args['css_error'] = 'comment-error';

    // если нет опции password_recovery, значит восстанавливаем с учетом номера комюзера во втором сегмента адреса
    // если опция есть, значит восстанавливаем без учета id комюзера
    if (!isset($args['password_recovery']))
        $password_recovery = false;
    else
        $password_recovery = true;


    // id комюзера, который в сессии - какой комюзер
    // если комюзер залогинен, то будет $id_session
    // если нет, то залогиненности нет
    if (isset($MSO->data['session']['comuser']) and $MSO->data['session']['comuser'])
        $id_session = $MSO->data['session']['comuser']['comusers_id'];
    else
        $id_session = false;

    if ($post = mso_check_post(['f_session_id', 'f_submit', 'f_comusers_email'])) {
        // это активация

        mso_checkreferer(); # защита рефера

        // защита сессии - если не нужно закомментировать строчку!
        if ($MSO->data['session']['session_id'] != $post['f_session_id']) mso_redirect();

        if (!$password_recovery) {
            // номер пользователя указан в f_submit - вычленяем его

            // получаем номер юзера id из f_submit[]
            $id = (int) mso_array_get_key($post['f_submit']);

            if (!$id) return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный номер пользователя') . '!</div>';

            // проверяем id в сессии с сабмитом 
            if ($id_session and $id != $id_session)
                return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный номер пользователя2') . '</div>';
        }

        $comusers_email = trim($post['f_comusers_email']);

        if (!$comusers_email) return '<div class="' . $args['css_error'] . '">' . tf('Нужно указать email') . '</div>';

        if (!mso_valid_email($comusers_email)) return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный email') . '</div>';

        $CI = &get_instance();

        // проверим есть ли вообще такой юзер
        $CI->db->select('comusers_id');

        if (!$password_recovery) $CI->db->where('comusers_id', $id); // если явно указан id, то ищем по нему

        $CI->db->where('comusers_email', $comusers_email);

        $query = $CI->db->get('comusers');

        if ($query->num_rows() == 0) // нет такого комментатора
            return '<div class="' . $args['css_error'] . '">' . tf('Неверный email или номер пользователя') . '!</div>';

        if ($password_recovery) {
            // получаем id из последнего запроса
            // получим id этого комюзера
            $res = $query->result_array();
            $id = $res[0]['comusers_id'];
        }

        $comusers_new_password = trim($post['f_comusers_password']);
        $comusers_activate_key = trim($post['f_comusers_activate_key']);

        if ($comusers_email and !$comusers_activate_key and !$comusers_new_password) {
            // указан email без остального

            // проверим есть ли активация
            $CI->db->select('comusers_id, comusers_activate_key');
            $CI->db->where('comusers_id', $id);
            $CI->db->where('comusers_activate_string=comusers_activate_key', '', false);
            $CI->db->where('comusers_email', $comusers_email);
            $CI->db->limit(1);
            $query = $CI->db->get('comusers');

            if ($query->num_rows() > 0) {
                // все верно, можно установить новый пароль
                $comuser = $query->result_array(); // данные комюзера

                mso_email_message_new_comuser(
                    $id,
                    array('comusers_email' => $comusers_email, 'comusers_activate_key' => $comuser[0]['comusers_activate_key'])
                );

                return '<div class="' . $args['css_ok'] . '">' . tf('Код активации отправлен на ваш email') . '!</div>';
            } else {
                return '<div class="' . $args['css_error'] . '">' . tf('Данный email не зарегистрирован или не активирован') . '</div>';
            }
        } elseif ($comusers_email and $comusers_new_password and !$comusers_activate_key)
        // указан email, новый пароля, но не указан код активации
        {
            return '<div class="' . $args['css_error'] . '">' . tf('Для установки нового пароля нужно заполнить все поля!') . '</div>';
        } elseif ($comusers_email and !$comusers_new_password and $comusers_activate_key) {
            // указан email и код активации, но не указан новый пароль

            return '<div class="' . $args['css_error'] . '">' . tf('Для установки нового пароля нужно заполнить все поля!') . '</div>';
        }

        // если указано поле активации и новый пароль, то сверяем код активации с базой + email + id и если все верно,
        // то обновляем пароль
        // если же поле активации не указано, то высылаем его на указанный email

        $CI->db->select('comusers_id');
        $CI->db->where('comusers_id', $id);
        $CI->db->where('comusers_activate_key', $comusers_activate_key);
        $CI->db->where('comusers_activate_string', $comusers_activate_key);
        $CI->db->where('comusers_email', $comusers_email);
        $CI->db->limit(1);

        $query = $CI->db->get('comusers');

        if ($query->num_rows() > 0) {
            // все верно, можно установить новый пароль
            $CI->db->where('comusers_id', $id);
            $CI->db->where('comusers_email', $comusers_email);
            $res = ($CI->db->update('comusers', array('comusers_password' => mso_md5($comusers_new_password)))) ? '1' : '0';

            $CI->db->cache_delete_all();

            if ($res) {
                // все ок
                // сразу логиним и редиректим на страницу комюзера
                $data = array(
                    'email' => $comusers_email,
                    'password' => $comusers_new_password,
                    'redirect' => getinfo('siteurl') . 'users/' . $id,
                    'allow_create_new_comuser' => false
                );

                mso_comuser_auth($data);
                exit;
                // return '<div class="' . $args['css_ok']. '">'. t('Новый пароль установлен!'). '</div>';
            } else
                return '<div class="' . $args['css_error'] . '">' . tf('Ошибка БД при смене пароля...') . '</div>';
        } else {
            return '<div class="' . $args['css_error'] . '">' . tf('Данные указаны неверно!') . '</div>';
        }
    }
}

// список всех комюзеров
function mso_get_comusers_all()
{
    $cache_key = mso_md5('mso_get_comusers_all');
    $k = mso_get_cache($cache_key);

    if ($k) return $k; // да есть в кэше

    $comusers = [];
    $CI = &get_instance();
    $CI->db->select('*');
    $CI->db->from('comusers');
    $query = $CI->db->get();

    if ($query->num_rows() > 0) {
        $comusers = $query->result_array();
        mso_add_cache($cache_key, $comusers);
    }

    // получим все мета одним запросом
    $CI->db->select('meta_id_obj, meta_key, meta_value');
    $CI->db->where('meta_table', 'comusers');
    $CI->db->order_by('meta_id_obj');

    $query = $CI->db->get('meta');

    if ($query->num_rows() > 0)
        $all_meta = $query->result_array();
    else
        $all_meta = [];

    // переделываем формат массива, чтобы индекс был равен номеру комюзера
    $r_array = [];

    foreach ($all_meta as $val) {
        $r_array[$val['meta_id_obj']][$val['meta_key']] = $val['meta_value'];
    }

    $all_meta = $r_array;

    // получить все номера страниц, где оставил комментарий комюзер
    $CI->db->select('comments_id, comments_page_id, comments_comusers_id');
    $CI->db->where('comments_comusers_id >', '0');
    $CI->db->order_by('comments_comusers_id, comments_page_id');
    $query = $CI->db->get('comments');

    if ($query->num_rows() > 0)
        $all_comments = $query->result_array();
    else
        $all_comments = [];

    // переделываем массив под удобный формат
    $r_array = [];
    $all_comments_page_id = []; // тут массив номеров страниц, где участвовал комюзер

    foreach ($all_comments as $val) {
        $r_array[$val['comments_comusers_id']][$val['comments_id']] = $val['comments_page_id'];

        $all_comments_page_id[$val['comments_comusers_id']][$val['comments_page_id']] = $val['comments_page_id'];
    }

    $all_comments = $r_array;

    // добавляем в каждого комюзера элемент массива meta, comments и comments_pages_id
    $r_array = [];

    foreach ($comusers as $key => $val) {
        $r_array[$key] = $val;

        if (isset($all_meta[$val['comusers_id']]))
            $r_array[$key]['meta'] = $all_meta[$val['comusers_id']];
        else
            $r_array[$key]['meta'] = [];

        if (isset($all_comments[$val['comusers_id']]))
            $r_array[$key]['comments'] = $all_comments[$val['comusers_id']];
        else
            $r_array[$key]['comments'] = [];

        if (isset($all_comments_page_id[$val['comusers_id']]))
            $r_array[$key]['comments_pages_id'] = $all_comments_page_id[$val['comusers_id']];
        else
            $r_array[$key]['comments_pages_id'] = [];
    }

    $comusers = $r_array;

    mso_add_cache($cache_key, $comusers);

    return $comusers;
}

// рассылаем по email уведомление о новом комментарии
function mso_email_message_new_comment_subscribe($data)
{
    /*
    Array
    (
        [comments_page_id] => 153 - id страницы
        [comments_content] => sdafsadfsdaf - текст комментария
        [comments_approved] =>  - если 0, то отправки нет
        [page_title] => тест - заголовок страницы
        [id] => 607 - id комментария
        -- [comments_comusers_id] => 1 - номер комюзера - пока не используется
        -- [comments_date] => 2009-12-10 20:45:39 - дата - пока не используется
        -- [comments_author_ip] => 127.0.0.1 - ip - пока не используется
    )
    */

    // Опция не рассылать подписку.
    if (!mso_get_option('allow_comments_subscribe', 'general', 1)) return;

    // комментарий не одобрен, не отсылаем
    if ($data['comments_approved'] == 0) return;

    // разослать нужно всем комюзерам у которых стоит получение уведомления о новом комментарии
    // $CI = &get_instance();

    $comusers_all = mso_get_comusers_all(); // все комюзеры

    $from = mso_get_option('admin_email_server', 'general', '');

    $subject = '[' . getinfo('name_site') . '] ' . tf('Новый комментарий к') . ' "' . $data['page_title'] . '"';

    $message = tf('Новый комментарий к') . ' "' . $data['page_title'] . '"' . NR . NR;
    $message .= tf('Текст:') . NR . mso_xss_clean($data['comments_content']);
    $message .= NR . NR . tf('Перейти к комментарию на сайте:') . NR .  mso_get_permalink_page($data['comments_page_id'])  . '#comment-' . $data['id'] . NR;

    foreach ($comusers_all as $comuser) {
        // отправлять на все комментарии сайта
        $subscribe_other_comments = (isset($comuser['meta']['subscribe_other_comments'])
            and $comuser['meta']['subscribe_other_comments']) ? true : false;

        // отключить subscribe_other_comments, если включена subscribe_other_comments_ban
        if (
            isset($comuser['meta']['subscribe_other_comments_ban'])
            and $comuser['meta']['subscribe_other_comments_ban']
        )
            $subscribe_other_comments = false;

        //  только на свой			
        $subscribe_my_comments = (isset($comuser['meta']['subscribe_my_comments'])
            and $comuser['meta']['subscribe_my_comments']) ? true : false;

        if (
            $subscribe_other_comments // на любой коммент
            or ($subscribe_my_comments // только свой
                and
                isset($comuser['comments_pages_id'][$data['comments_page_id']]))
        ) {
            // можно отправлять
            if (mso_valid_email($comuser['comusers_email'])) {
                $data = array_merge($data, array('subscription' => true));  //А здесь для smtp_mail важно знать, чтобы запретить сохранять мыло в файл.
                $res = mso_mail($comuser['comusers_email'], $subject, $message, $from, $data);

                if (!$res) break; // ошибка отправки почты - рубим цикл
            }
        }
    }
}

// авторизация/регистрация комюзеров
// обязательно указывается email
// если не указывать password, то проверка на пароль не осуществляется
function mso_comuser_auth($data)
{
    if (!isset($data['email']))
        return false;
    else
        $email = $data['email'];

    $pass = $data['password'] ?? false;
    $comusers_nik = $data['comusers_nik'] ?? '';
    $redirect = $data['redirect'] ?? true;

    // если $die = true, то всё рубим через die
    // иначе возвращаем результат по return
    $die = $data['die'] ?? true;

    // разрешить создавать через эту функцию новых комюзеров (если такого email нет в базе)
    $allow_create_new_comuser = $data['allow_create_new_comuser'] ?? true;

    $CI = &get_instance();

    // если указанный email зарегистрирован на user, то отказываем в регистрации
    $CI->db->select('users_id');
    $CI->db->where('users_email', $email);
    $query = $CI->db->get('users');

    if ($query->num_rows() > 0) {
        if ($die)
            die(tf('Данный email уже используется на сайте админом или автором.'));
        else
            return tf('Данный email уже используется на сайте админом или автором.');
    }

    // имя email и пароль нужно проверить, чтобы такие были в базе
    // вначале нужно проверить наличие такого email
    // если есть, то сверяем и пароль
    $CI->db->select('comusers_id, comusers_password, comusers_email, comusers_nik, comusers_url, comusers_avatar_url, comusers_last_visit');
    $CI->db->where('comusers_email', $email);
    $query = $CI->db->get('comusers');

    if ($query->num_rows()) {
        // есть такой комюзер
        $comuser_info = $query->row_array(1); // вся инфа о комюзере

        if ($pass !== false) {
            // пароль указан
            // сверим пароль
            if ($comuser_info['comusers_password'] == mso_md5($pass)) {
                // пароли равны, можно логинить

                // сразу же обновим поле последнего входа
                $CI->db->where('comusers_id', $comuser_info['comusers_id']);
                $CI->db->update('comusers', array('comusers_last_visit' => date('Y-m-d H:i:s')));

                $expire  = time() + 60 * 60 * 24 * 365; // 365 дней

                $name_cookies = 'maxsite_comuser';
                $value = serialize($comuser_info);

                mso_add_to_cookie($name_cookies, $value, $expire, $redirect); // в куку для всего сайта
            } else {
                // email есть но пароль ошибочный
                if ($die)
                    die('Данный email уже зарегистрирован <a href="' . getinfo('siteurl') . '">на сайте</a>. Для входа нужно указать верный пароль. Вы можете <a href="' . getinfo('siteurl') . 'password-recovery">восстановить пароль</a>.');
                else
                    return 'Данный email уже зарегистрирован <a href="' . getinfo('siteurl') . '">на сайте</a>. Для входа нужно указать верный пароль. Вы можете <a href="' . getinfo('siteurl') . 'password-recovery">восстановить пароль</a>.';
            }
        } else {
            // пароль сверять не нужно

            // сразу же обновим поле последнего входа
            $CI->db->where('comusers_id', $comuser_info['comusers_id']);
            $CI->db->update('comusers', array('comusers_last_visit' => date('Y-m-d H:i:s')));

            $expire  = time() + 60 * 60 * 24 * 365; // 365 дней
            $name_cookies = 'maxsite_comuser';
            $value = serialize($comuser_info);

            mso_add_to_cookie($name_cookies, $value, $expire, $redirect); // в куку для всего сайта
        }
    } elseif ($allow_create_new_comuser) {
        // только если разрешено создавать новых комюзеров
        // нет такого email, нужно регистрировать комюзера

        // но если запрещены регистрации, то все рубим
        if (!mso_get_option('allow_comment_comusers', 'general', '1')) {
            if ($die)
                die(t('На сайте запрещена регистрация.'));
            else
                return t('На сайте запрещена регистрация.');
        }

        // если пароль не указан, то генерируем его случайным образом
        if ($pass === false) $pass = substr(mso_md5($email), 1, 9);

        $ins_data = [
            'comusers_email' => $email,
            'comusers_password' => mso_md5($pass)
        ];

        // генерируем случайный ключ активации
        $ins_data['comusers_activate_key'] = mso_md5(rand());
        $ins_data['comusers_date_registr'] = date('Y-m-d H:i:s');
        $ins_data['comusers_last_visit'] = date('Y-m-d H:i:s');
        $ins_data['comusers_ip_register'] = $_SERVER['REMOTE_ADDR'];
        $ins_data['comusers_notify'] = '1'; // сразу включаем подписку на уведомления

        if (isset($data['comusers_url'])) {
            // если указан сайт
            if ($comusers_url = mso_clean_str($data['comusers_url']))
                $ins_data['comusers_url'] = $comusers_url;
        } else {
            $comusers_url = '';
        }

        if ($comusers_nik = mso_clean_str($comusers_nik, 'base|not_url')) {
            $ins_data['comusers_nik'] = $comusers_nik;
        }

        // Автоматическая активация новых комюзеров
        // если активация стоит автоматом, то сразу её и прописываем
        if (mso_get_option('comusers_activate_auto', 'general', '0'))
            $ins_data['comusers_activate_string'] = $ins_data['comusers_activate_key'];

        $res = ($CI->db->insert('comusers', $ins_data)) ? '1' : '0';

        if ($res) {
            $comusers_id = $CI->db->insert_id(); // номер добавленной записи

            // нужно добавить опцию в мета «новые комментарии, где я участвую» subscribe_my_comments
            // вначале грохаем если есть такой ключ
            $CI->db->where('meta_table', 'comusers');
            $CI->db->where('meta_id_obj', $comusers_id);
            $CI->db->where('meta_key', 'subscribe_my_comments');
            $CI->db->delete('meta');

            // теперь добавляем как новый
            $ins_data2 = [
                'meta_table' => 'comusers',
                'meta_id_obj' => $comusers_id,
                'meta_key' => 'subscribe_my_comments',
                'meta_value' => '1'
            ];

            $CI->db->insert('meta', $ins_data2);

            // отправляем ему уведомление с кодом активации
            mso_email_message_new_comuser($comusers_id, $ins_data, mso_get_option('comusers_activate_auto', 'general', '0'));

            // после отправки можно сразу залогинить
            $comuser_info = [
                'comusers_id' => $comusers_id,
                'comusers_password' => mso_md5($pass),
                'comusers_email' => $email,
                'comusers_nik' => $comusers_nik,
                'comusers_url' => $comusers_url,
                'comusers_avatar_url' => '',
                'comusers_last_visit' => '',
            ];

            $value = serialize($comuser_info);
            $expire  = time() + 60 * 60 * 24 * 365; // 365 дней
            $name_cookies = 'maxsite_comuser';

            mso_add_to_cookie($name_cookies, $value, $expire, $redirect); // в куку для всего сайта

        } else {
            if ($die)
                die(t('Произошла ошибка регистрации'));
            else
                return t('Произошла ошибка регистрации');
        }
    }

    return false;
}

// определение времени последнего комментария и сравнение с текущим
// нужно чтобы было не более 15 секунд - защита от частых комментариев
function mso_last_activity_comment()
{
    global $MSO;

    /*
        last_activity - время текущей сессии
        last_activity_prev - время предыдущей сесиии
        last_activity_comment - время предыдущего успешного комментария
    */

    // предыдущего комментария не было - это первый
    if (!isset($MSO->data['session']['last_activity_comment'])) return true;

    // время в секундах между последним комментарием и текущим в секундах
    $delta = time() - $MSO->data['session']['last_activity_comment'];

    return ($delta < 15) ? false : true;
}

// вывод аватарки комментатора
// на входе массив комментария из page-comments.php
function mso_avatar($comment, $img_add = 'class="mso-gravatar"', $echo = false, $size = false, $url_only = false)
{
    extract($comment);

    $avatar_url = '';

    // если отмечана опция «Только граватарки», то обнуляем указанный адрес комюзера
    if (mso_get_option('gravatar_only', 'templates', 0)) $comusers_avatar_url = '';

    if ($comusers_avatar_url)  $avatar_url = $comusers_avatar_url;
    elseif ($users_avatar_url) $avatar_url = $users_avatar_url;

    if ($size === false)
        $avatar_size = (int) mso_get_option('gravatar_size', 'templates', 80);
    else
        $avatar_size = $size;

    if ($avatar_size < 1 or $avatar_size > 512) $avatar_size = 80;

    if (!$avatar_url) {
        // аватарки нет, попробуем получить из gravatara
        if ($users_email) $grav_email = $users_email;
        elseif ($comusers_email) $grav_email = $comusers_email;
        else $grav_email = $comments_author_name ?? ''; // имя комментатора


        if ($gravatar_type = mso_get_option('gravatar_type', 'templates', '')) {
            $d = '&amp;d=' . urlencode($gravatar_type);
        } else {
            $d = '';

            $def = getinfo('template_dir') . 'assets/images/gravatar-default.png';

            if (file_exists($def)) {
                $d = '&amp;d=' . urlencode(getinfo('template_url') . 'assets/images/gravatar-default.png');
            } else {
                $def = getinfo('uploads_dir') . 'gravatar-default.png';

                if (file_exists($def))
                    $d = '&amp;d=' . urlencode(getinfo('uploads_url') . 'gravatar-default.png');
            }
        }

        $avatar_url = "https://www.gravatar.com/avatar/?gravatar_id="
            . md5($grav_email)
            . "&amp;s=" . $avatar_size
            . $d;

        // http://ru.gravatar.com/site/implement/images/php/
        // $grav_url = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;

        /*
        if (!empty($_SERVER['HTTPS'])) 
        {
            $avatar_url = "https://secure.gravatar.com/avatar.php?gravatar_id="
                . md5($grav_email)
                . "&amp;size=" . $avatar_size
                . $d;
        } 
        else 
        {
            $avatar_url = "http://www.gravatar.com/avatar.php?gravatar_id="
                . md5($grav_email)
                . "&amp;size=" . $avatar_size
                . $d;
        }
        */
    }

    if (!$url_only) {
        if ($avatar_url) $avatar_url =  '<img src="' . $avatar_url . '" width="' . $avatar_size . '" height="' . $avatar_size . '" alt="" title="" ' . $img_add . '>';
    }

    if ($echo)
        echo $avatar_url;
    else
        return $avatar_url;
}

# end of file
