<?php
if (!defined( 'ABSPATH' )) { exit; }

if (!defined('ATEC_INIT_INC')) require_once('atec-init.php');
add_action('admin_menu', function() 
{ 
	$str=WP_DEBUG?'WP_DEBUG':''; 
	$str.=(defined('SAVEQUERIES') && SAVEQUERIES)?(($str!==''?'/':'SAVEQUERIES').''):'';
	atec_wp_menu(__DIR__,'atec_wpd',$str!==''?'Debug':'<span title="'.$str.'">Debug</span>❗');
});

function atec_wpd_admin_bar($wp_admin_bar): void
{
	$newDebug='Debug'; $optionName='atec_wpd_new_error';
	if (get_option($optionName,false)) $newDebug='Errors';
	$link=get_admin_url().'admin.php?page=atec_wpd';
	$args = ['id' => 'atec_wpd_admin_bar', 
				  'title' => '<span style="font-size:12px;">
				  		<img title="WP_DEBUG is enabled" src="'.esc_url( plugins_url( '/assets/img/atec_wpd_icon_admin.svg', __DIR__ ) ) .'" style="vertical-align: bottom; height:14px; margin:9px 4px 9px 0;"><span style="color:'.($newDebug==='Debug'?'lightcoral':'red').';">'.esc_attr($newDebug).'</span>
					</span>',
				  'href' => $link];
	$wp_admin_bar->add_node($args);
	if (defined('SAVEQUERIES') && SAVEQUERIES)
	{
		$nonce = wp_create_nonce('atec_wpd_nonce');
		$args = ['id' => 'atec_wpd_admin_bar_sq', 
				  	'title' => '
					  	<span style="font-size:12px;">
						  	<img title="SAVEQUERIES is enabled" src="'.esc_url( plugins_url( '/assets/img/atec_wpd_icon_admin.svg', __DIR__ ) ) .'" style="vertical-align: bottom; height:14px; margin:9px 4px 9px 0;"><span style="color:red;">QUERIES</span>
						</span>',
				  	'href' => $link.'&nav=Queries&_wpnonce='.esc_attr($nonce)];
		$wp_admin_bar->add_node($args);		
	}
}

if (WP_DEBUG && WP_DEBUG_LOG) add_action('wp_error_added', function() { update_option('atec_wpd_new_error',true,'on'); } );

add_action( 'init', function() 
{ 
	if (!class_exists('ATEC_wp_memory')) require_once('atec-wp-memory.php');
	add_action('admin_bar_menu', 'atec_wp_memory_admin_bar', PHP_INT_MAX);
	if (WP_DEBUG) add_action('admin_bar_menu', 'atec_wpd_admin_bar', PHP_INT_MAX);
	
	if (in_array($slug=atec_get_slug(), ['atec_group','atec_wpd']))
	{
		if (!defined('ATEC_TOOLS_INC')) require_once('atec-tools.php');	
		add_action( 'admin_enqueue_scripts', function() { atec_reg_style('atec',__DIR__,'atec-style.min.css','1.0.002'); });
		
		if ($slug!=='atec_group')
		{
			function atec_wpd(): void { require_once('atec-wpd-dashboard.php'); }
			add_action( 'admin_enqueue_scripts', function()
			{
				atec_reg_style('atec_check',__DIR__,'atec-check.min.css','1.0.001');
				atec_reg_script('atec_check',__DIR__,'atec-check.min.js','1.0.001');
				atec_reg_script('atec_debug',__DIR__,'atec-debug.min.js','1.0.0');
			});
		}
	}	
});
?>
