<?php

// Это технический файл, не используйте его
// require-maxsite/YWRtaW4vdGVtcGxhdGUvZGVmYXVsdC9fY29tcGlsZS9sb2dpbmZvcm0tcmVxdWlyZS1tYXhzaXRlLnBocA==
// admin/template/default/_compile/loginform-require-maxsite.php
// http://www.kruglov.ru/useful/base64/

echo mso_lessc(
	getinfo('admin_dir') . 'template/default/_compile/loginform.less',
	getinfo('admin_dir') . 'template/default/loginform.css',
	'',
	false,
	true,
	true
);

# end file