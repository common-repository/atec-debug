<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_wpc_tools
{
	public function enabled($enabled): void { echo '<span style="color:', ($enabled?'green':'red'), '" title="', ($enabled?esc_attr__('Enabled','atec-debug'):esc_attr__('Disabled','atec-debug')), '" class="dashicons dashicons-', ($enabled?'yes-alt':'warning'), '"></span>'; }
	public function error($cache,$txt): void { echo '<p class="atec-red">', esc_html($cache), $cache!==''?' ':'', esc_html($txt),'.</p>'; }
	public function success($cache,$txt): void { echo '<p>', esc_html($cache), $cache!==''?' ':'', esc_html($txt), '.&nbsp;<span class="dashicons dashicons-yes-alt"></span></p>'; }
	public function p($txt): void { echo '<p>', esc_html($txt), '.</p>'; }
}
?>