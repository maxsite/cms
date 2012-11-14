<?php

// компиляция shared/css-less/var_style.css
// из shared/css-less/default/var_style.less

// Это технический файл, не используйте его
// http://localhost/codeigniter/require-maxsite/c2hhcmVkL2Nzcy1sZXNzL2RlZmF1bHQvY29tcGlsZS12YXJfc3R5bGUtY3NzLXJlcXVpcmUtbWF4c2l0ZS5waHA=

echo mso_lessc(
	getinfo('shared_dir') . 'css-less/default/var_style.less',
	getinfo('shared_dir') . 'css-less/var_style.css',
	'',
	false,
	true,
	true
);

# end file