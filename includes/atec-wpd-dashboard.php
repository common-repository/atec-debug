<?php
if (!defined( 'ABSPATH' )) { exit; }
if (!class_exists('ATEC_wp_memory')) require_once('atec-wp-memory.php');

class ATEC_wpd_dashboard { function __construct() {		
	
$mem_tools=new ATEC_wp_memory();

echo '
<div class="atec-page">';
	$mem_tools->memory_usage();
	atec_header(__DIR__,'wpd','Debug');	
	
	echo '
	<div class="atec-main">';

		global $wp_filesystem;
		WP_Filesystem();
		
		$url		= atec_get_url();
		$nonce = wp_create_nonce(atec_nonce());
		$action = atec_clean_request('action');
		$nav 	= atec_clean_request('nav');
		if ($nav==='') $nav='Debug';
		
		$memlimit=atec_clean_request('memlimit');
		$configPath=get_home_path().'/wp-config.php';
		$config=$wp_filesystem->get_contents($configPath);
		
		$arr		=['WP_DEBUG','WP_DEBUG_DISPLAY','WP_DEBUG_LOG','SCRIPT_DEBUG','WP_ALLOW_REPAIR','SAVEQUERIES'];
		$status	=[]; 

		if ($nav=='Info') { require_once('atec-info.php'); new ATEC_info(__DIR__); }
		elseif ($nav=='Debug')
		{    
				$status=['WP_DEBUG'=>WP_DEBUG, 'WP_DEBUG_DISPLAY'=>WP_DEBUG_DISPLAY, 'WP_DEBUG_LOG'=>WP_DEBUG_LOG];
				$default=['WP_DEBUG'=>false, 'WP_DEBUG_DISPLAY'=>true, 'WP_DEBUG_LOG'=>false];
				if (gettype(WP_DEBUG_LOG)!=='boolean') { $customLog=true; $debugPath=WP_DEBUG_LOG; }
				else { $customLog=false; $debugPath=WP_CONTENT_DIR.'/debug.log'; }
				$lastMod=($wp_filesystem->exists($debugPath))?$wp_filesystem->mtime($debugPath):false;
		}
		elseif ($nav=='Updates') $status['WP_AUTO_UPDATE_CORE'] = defined('WP_AUTO_UPDATE_CORE') && WP_AUTO_UPDATE_CORE;
		elseif ($nav=='Queries') $status['SAVEQUERIES'] = defined('SAVEQUERIES') && SAVEQUERIES;
		elseif ($nav=='Repair') $status['WP_ALLOW_REPAIR'] = defined('WP_ALLOW_REPAIR') && WP_ALLOW_REPAIR;
		elseif ($nav=='Script') $status['SCRIPT_DEBUG'] = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;
		function replaceConfig($config,$key,$reg,$subst): string
        {
			$new=preg_replace($reg, $subst, $config, 1);
			if ($config!=$new) atec_admin_notice('update','Updated wp-config.php ('.$key.')');
			return $new;
		}

		if (in_array($nav,['Debug','Memory','Queries','Repair','Updates','Script']))
		{
			
			if ($action=='defaultMemLimit') { $action='memlimit'; $memlimit='40M'; }
			if ($action=='delete') $wp_filesystem->delete($debugPath);
			else 
			if (in_array($action, $arr) || $action=='memlimit' || $action=='default' || $action=='WP_AUTO_UPDATE_CORE')
			{
					$backupPath=str_replace('.php','.php.atec-debug-bck.txt',$configPath);
					$wp_filesystem->put_contents($backupPath,$config);
					if ($action=='memlimit') 
					{ 
						if ($memlimit=='') { $memlimit='40M'; atec_admin_notice('update','WP_MEMORY_LIMIT set to default value: 40M'); }
						$key='WP_MEMORY_LIMIT'; $subst="define( 'WP_MEMORY_LIMIT', '".$memlimit."' );"; 
					}
					else if ($action!='default') 
					{ 
						$key=$action; $status[$action]=!$status[$action]; $subst="define( '".$action."', ".($status[$action]?"true":"false")." );"; 
					}
					
					function regExp($config,$key,$subst): array
                    {
					if (!str_contains($config, '\''.$key.'\'')) 
					{ 
						$reg = '/\<\?php\n{0,0}/'; 
						$subst="<?php\n/* Added by atec-debug */\n".$subst; 
					}
						else { $reg = '/define\(\s?\''.$key.'\',\s?[\']?([\w\d]*)[\']?\s?\);/'; }
						return ['reg'=>$reg, 'subst'=>$subst];
					}
					
					if ($action=='default')
					{
						foreach ($default as $key => $value)
						if ($key!='WP_DEBUG_LOG' || !$customLog)
						{ 
  							$subst="define( '".$key."', ".($value?"true":"false")." );"; $status[$key]=$value;
  							$regArr=regExp($config,$key,$subst); $config=replaceConfig($config,$key,$regArr['reg'],$regArr['subst']); 
						}
					}
					else 
					{ 
						$regArr=regExp($config,$key,$subst); 
						$config=replaceConfig($config,$key,$regArr['reg'],$regArr['subst']);
					}
					$wp_filesystem->put_contents($configPath,$config);
			}
		}
					
		$actions=['#bug Debug','#memory Memory','#toolbox Repair','#bug Script','#update Updates'];
		$actions=array_merge($actions,['#database Queries','#hourglass-start Cron','#scroll Includes','#sliders wp-config']);
		if (extension_loaded('xdebug')) $actions[] = '#bug Xdebug';
		$licenseOk=atec_check_license();
		atec_nav_tab($url, $nonce, $nav, $actions, $licenseOk?999:5, !$licenseOk);

		echo '
		<div class="atec-g atec-border">';

		atec_progress();
		if (in_array($nav, ['Debug','Queries','Repair','Script','Updates'])) require_once('atec-check.php');

		if ($nav=='Cron') 
		{ 
			if (atec_pro_feature('`Cron´ lists all cron jobs with the option to selective delete')) 
			{ 
				@include_once('atec-wpd-parseCron-pro.php'); 
				if (class_exists('ATEC_wpd_parseCron')) new ATEC_wpd_parseCron($url, $nonce, $action); 
				else atec_missing_class_check();
			} 
		}
		elseif ($nav=='Includes') 
		{ 
			if (atec_pro_feature('`Included´ lists all php scripts included on current page'))
			{ @include_once('atec-wpd-parseIncludes-pro.php'); atec_missing_class_check('ATEC_wpd_included'); } 
		}
		elseif ($nav=='Queries') 
		{ 
			if (atec_pro_feature('`Queries´ enables SAVEQUERIES to capture and display all database queries on the last page called')) 
			{ 
				@include_once('atec-wpd-parseQueries-pro.php'); 
				if (class_exists('ATEC_wpd_parseQueries')) new ATEC_wpd_parseQueries($status, $url, $nonce); 
				else atec_missing_class_check();
			} 
		}
		elseif ($nav=='Xdebug') 
		{ 
			if (atec_pro_feature('`Xdebug´ shows all information about the Xdebug extension')) 
			{ @include_once('atec-wpd-Xdebug-pro.php'); atec_missing_class_check('ATEC_wpd_Xdebug'); } 
		}
		elseif ($nav=='wp_config') 
		{ 
			if (atec_pro_feature('`WP-Config´ shows the content of the wordpress wp-config.php file')) 
			{ @include_once('atec-parseWPconfig-pro.php'); atec_missing_class_check('ATEC_parseWPconfig'); } 
		}
		elseif ($nav=='Memory') { require_once('atec-wpd-memory.php'); new ATEC_wpd_memory($memlimit, $url, $nonce); }
		elseif ($nav=='Repair') { require_once('atec-wpd-repair.php'); new ATEC_wpd_repair($status, $url, $nonce); }
		elseif ($nav=='Script') { require_once('atec-wpd-script.php'); new ATEC_wpd_script($status, $url, $nonce); }
		elseif ($nav=='Updates') { require_once('atec-wpd-updates.php'); new ATEC_wpd_updates($status, $url, $nonce); }
		elseif ($nav=='Debug')
		{
			delete_option('atec_wpd_new_error'); 
			$debugFileSize=wp_filesize($debugPath);
			wp_cache_set('atec_wpd_debug_size',$debugFileSize);				
			
			$excludeArr=['WP_ALLOW_REPAIR','SAVEQUERIES','SCRIPT_DEBUG'];
			echo '
			<div class="atec-btn-div">
  				<div class="tablenav">
					<div class="atec-btn-chk-div">';
  							foreach ($arr as $value) 
						  	{
    							if (!in_array($value,$excludeArr))
    							{
									$disabled=$value=='WP_DEBUG_LOG' && $customLog;
									atec_checkbox_button_div($value,str_replace('WP_','',$value),$disabled,$status[$value],$url,'&action='.$value,$nonce);
    							}
  							}
							echo '<div class="atec-mt-2">'; atec_nav_button($url,$nonce,'default','','Reset to default'); echo '</div>';
			  				atec_help('debug','Options');	
							echo '
							<div id="debug_help" class="atec-help atec-dn">
								All of these options are constants defined in the wp-config.php file.
								<ul>';
	  							$desc	=[ 'WP_DEBUG'=>'WP_DEBUG triggers the “debug” mode. Will show errors, notices, and warnings.',
												'WP_DEBUG_DISPLAY'=>'WP_DEBUG_DISPLAY controls whether debug messages are shown inside the HTML of pages.',
												'WP_DEBUG_LOG'=>'WP_DEBUG_LOG causes all errors to also be saved to a debug.log.'];
								foreach ($arr as $value)
										if (!in_array($value,$excludeArr)) echo '<li class="small" style="margin: 0;">',esc_html($desc[$value]),'</li>';
							echo '
								</ul>
								Make sure to prevent file access to debug.log by adding this to your .htaccess file:<br>
								&lt;FilesMatch "debug.log"&gt;Require all denied&lt;/FilesMatch&gt;
							</div>
					</div>
				</div>
			</div>';

			require_once('atec-wpd-parseDebug.php');
			new ATEC_wpd_parseDebug($customLog, $lastMod, $debugPath, $debugFileSize, $url, $nonce);
			}	
		
		echo '
		</div>
	</div>
</div>';  

if (!class_exists('ATEC_footer')) require_once('atec-footer.php');

}}

new ATEC_wpd_dashboard();
?>