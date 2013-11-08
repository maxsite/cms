<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

 /*
* произвольный html/текст

[unit]
file = html.php
text = _START_ текст с html _END_
[/unit]

или

[unit]
file = html.php
text = _START_
текст с html в несколько строк
_END_
[/unit]

*/

if (isset($UNIT['text'])) echo $UNIT['text'];

# end file