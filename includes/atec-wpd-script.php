<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_wpd_script { function __construct($status,$url,$nonce) {		

$value='SCRIPT_DEBUG';

echo '
<div class="atec-btn-div">
	<div class="tablenav">';
		atec_checkbox_button_div(esc_attr($value),esc_attr($value),false,$status[$value],$url,'&action='.esc_attr($value).'&nav=Script',$nonce);
		echo '<div style="padding-top: 2px;">'; atec_info('SCRIPT_DEBUG forces WordPress to use the “dev” versions of core CSS and JavaScript files'); echo '</div>
	</div>
</div>';

}}
?>