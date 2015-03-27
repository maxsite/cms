<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

# буферизация вывода

$OUTPUT = ob_get_contents();

ob_end_clean();

$OUTPUT = my_compress_text($OUTPUT);

echo $OUTPUT;

# end of file