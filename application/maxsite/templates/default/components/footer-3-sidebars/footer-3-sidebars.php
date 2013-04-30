<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

echo '<div class="footer-3-sidebars"><div class="wrap">';
	
	echo NR . '<div class="footer-sidebar1 w33 left"><div class="wrap">';
	mso_show_sidebar('3');
	echo '</div></div>' . NR;

	echo NR . '<div class="footer-sidebar2 w33 left"><div class="wrap">';
	mso_show_sidebar('4');
	echo '</div></div>' . NR;
	
	echo NR . '<div class="footer-sidebar3 w33 left"><div class="wrap">';
	mso_show_sidebar('5');
	echo '</div></div>' . NR;
	
echo '<div class="clearfix"></div></div></div><!-- /div.footer-3-sidebars -->';
	