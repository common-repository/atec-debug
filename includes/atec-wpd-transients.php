<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_wpd_transients { function __construct($url, $nonce, $action) {		

global $wpdb; 

function delete_trans($id)
{
	if (str_starts_with($id,'_site_')) delete_site_transient(str_replace(['_site_transient_timeout_','_site_transient_'],'',$id));
	else delete_transient(str_replace(['_transient_timeout_','_transient_'],'',$id));
}

if ($action==='delete') { if (($id=atec_clean_request('id'))!='') delete_trans($id); }
elseif ($action==='delete_all')
{
	// @codingStandardsIgnoreStart
	$transients = $wpdb->get_results($wpdb->prepare('SELECT option_name FROM %1s WHERE option_name LIKE "%\_transient\_timeout\_%"', $wpdb->options));
	// @codingStandardsIgnoreEnd
	foreach ( $transients as $trans) { delete_trans($trans->option_name); }
}

echo '
<div class="atec-btn-div">
	  <div class="tablenav">';
		atec_nav_button_confirm($url,$nonce,'delete_all','Transients','Delete all timed out');
echo '</div>
</div>';

atec_little_block('Transients');

echo '<table class="atec-table atec-table-tiny fixed">
<thead><tr><th>#</th><th>ID</th><th>Name</th><th>Value</th><th></th></tr></thead>
<tbody>';

$c=0;
// @codingStandardsIgnoreStart
$transients = $wpdb->get_results($wpdb->prepare('SELECT option_id, option_name, option_value FROM %1s WHERE option_name LIKE "%\_transient\_%" ORDER BY `option_name`', $wpdb->options)); 
// @codingStandardsIgnoreEnd

foreach ( $transients as $trans) 
{
	$c++;
	$short=atec_short_string($trans->option_value);
	echo '
	<tr>',
		'<td>', esc_attr($c), '</td>',
		'<td>', esc_attr($trans->option_id), '</td>',
		'<td class="atec-anywrap ', (str_contains($trans->option_name,'_timeout')?'atec-red':''), '">', esc_attr($trans->option_name), '</td>',
		'<td class="atec-anywrap">', esc_attr($short), '</td>';
		if (str_starts_with($trans->option_name,'_site_') || str_starts_with($trans->option_name,'_transient_')) atec_create_button('delete&nav=Transients','trash',true,$url,esc_attr($trans->option_name),$nonce);
		else echo '<td></td>';
	echo '
	</tr>';
}
echo '</tbody></table>';

}}
?>