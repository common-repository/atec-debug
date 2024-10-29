<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_wpd_updates { function __construct($status,$url,$nonce) {		

$value = 'WP_AUTO_UPDATE_CORE';

echo '
<div class="atec-btn-div">
  <div class="tablenav">
	  	<div>'; atec_checkbox_button_div(esc_attr($value),esc_attr($value),false,$status[$value],$url,'&nav=Updates&action='.esc_attr($value),$nonce); echo '</div>
		<div>'; 
			atec_help('updates','Update settings'); 
			echo '
			<div id="updates_help" class="atec-help atec-dn">
				<ul class="atec-m-0">
					<li>FALSE: Prevents any automated updates to WordPress core.</li>
					<li>TRUE: Allows WordPress core to automatically update anytime there is a new version, whether it be a development, minor or major release.</li>
				</ul>
			</div>
	</div>
</div>';

}}

?>