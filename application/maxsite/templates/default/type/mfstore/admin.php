<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

$dirModule = getinfo('template_dir') . 'modules/' . $bdir;

if (!file_exists($dirModule)) {

    $ajaxM =  getinfo('ajax') . base64_encode(str_replace('.php', '-ajax.php', str_replace(str_replace('\\', '/', getinfo('base_dir')), '', str_replace('\\', '/', __FILE__))));

    $session = getinfo('session_id');

    echo '<div class=" mar30-tb t-center"><button class="button button1" onClick="sendM(\'' . mso_segment(2) . '\')">Скопировать этот модуль в modules</button><div class="mar10-tb t-green" id="resultM"></div></div>';

    echo <<<EOF
<script>
function sendM(m) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        document.getElementById("resultM").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("POST", "{$ajaxM}", true);
    xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlhttp.send("m=" + m + "&session={$session}");
}
</script>
EOF;
} else {
    echo '<div class="mar30-tb t-center">Модуль уже установлен</div>';
}

# end of file
