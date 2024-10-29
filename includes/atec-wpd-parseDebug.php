<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_wpd_parseDebug { function __construct($customLog, $lastMod, $debugPath, $debugFileSize, $url, $nonce) {		

global $wp_filesystem;
WP_Filesystem();

atec_little_block_with_info('Log file',[],'',array('update','delete'),$url,$nonce);

if ($customLog!='') echo '<p style="margin-top: -10px;">Custom log file: ',esc_html($debugPath),'</p>';
if ($lastMod)
{
	$date = new DateTime(); $date->setTimestamp($lastMod);
	echo '<h4>Last modified: ',esc_attr($date->format('Y-m-d h:m:s')),'</h4>';
}

$debug='';
$wpMaxMemLimit = wp_convert_hr_to_bytes(WP_MAX_MEMORY_LIMIT);
if ($wp_filesystem->exists($debugPath)) 
{
	if ($wp_filesystem->size($debugPath)>$wpMaxMemLimit/2) atec_error_msg('The debug.log file exceeds the memory limit');
	else
	{ 
		$debug=$wp_filesystem->get_contents($debugPath);
		// $debug	= preg_replace('/\[+[\d\-\w]+\s+[\d:]+\s+[A-Z]+\]+\s+/', '', $debug);
		$debug	= strtr($debug,['PHP Parse error:  '=>'', 'PHP Fatal error:  '=>'', 'PHP Warning:  '=>'Error: ']);
		$home	= get_home_path();
		$home	= preg_replace('/\//','\/',$home);
		$debug	= preg_replace('/'.$home.'/', './', $debug);
		
		echo '<div class="atec-code" id="debug" style="display:none; font-size: 1em; line-height: 1.6em;">',esc_html($debug),'</div>';
		atec_reg_inline_script('wpd_parseDebug', 'parseDebug();', true);
	}
}
else atec_info('No debug file');

}}
?>