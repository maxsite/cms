<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// компоненты подвала
// см. _header_components.php
if ($fn = get_component_fn('default_footer_component1', 'footer-copyright.php')) require($fn);
if ($fn = get_component_fn('default_footer_component2', 'footer-statistic.php')) require($fn);
