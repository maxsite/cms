<?php

// компиляция shared/css-less/style-all-mini.css
// из shared/css-less/default/style-all-mini.less

// Это технический файл, не используйте его
// http://localhost/codeigniter/require-maxsite/c2hhcmVkL2Nzcy1sZXNzL2RlZmF1bHQvY29tcGlsZS1zdHlsZS1hbGwtbWluaS1jc3MtcmVxdWlyZS1tYXhzaXRlLnBocA==

echo mso_lessc(
	getinfo('shared_dir') . 'css-less/default/style-all-mini.less',
	getinfo('shared_dir') . 'css-less/style-all-mini.css',
	'',
	false,
	true,
	true
);

# end file