<?php
/*
Plugin Name: Busy Server
Plugin URI: http://wordpress.org/plugins/busy-server/
Description: When the server load is higher than specified, show an error message instead of loading the page.
Version: 0.2.2
Author: Lubos Svoboda
*/
?>
<?php


//TODO: check only when page not cached
//TODO: Logged in users can always
	//if (is_user_logged_in()) ;

add_action('send_headers', 'busy_server_start');

// admin actions
if ( is_admin() ){ 
  add_action('admin_menu','busy_server_menu');
  add_action( 'admin_init', 'busy_server_mysettings' );
} 



// Add admin menu entry
function busy_server_menu(){
     add_options_page('Busy Server', 'Busy Server', 'manage_options', 'busy-server-menu', 'busy_server_options');
}

// Add admin options page
function busy_server_options(){
     include('busy-server-admin.php');
}


// Add admin options fields
function busy_server_mysettings() { // whitelist options
  register_setting( 'busy-server-group', 'busy_server_max_load' );
  register_setting( 'busy-server-group', 'busy_server_busy_message' );
}


//Calculate web server load, @return array
if (!function_exists('busy_server_load') ) {
	function busy_server_load() {
		if(function_exists('sys_getloadavg')) {
		$load = sys_getloadavg();
			// if (!empty($load))	return $load[0]." / ".$load[1]. " / ". $load[2];
			if (!empty($load))	return $load;
			else return false;
		} else if (stristr(PHP_OS, 'win')) {
			//TODO: fast faster checking or warn user
			/*
			ob_start();
			passthru('typeperf -sc 1 "\processor(_total)\% processor time"',$status);
			$content = ob_get_contents();
			ob_end_clean();
			if ($status === 0) {
				if (preg_match("/\,\"([0-9]+\.[0-9]+)\"/",$content,$load)) {
					$load['unit'] = '%';
					return $load;
				}
			}	
			 */		
        } 	else return false;
	}
}


//Display the busy error
if (!function_exists('busy_server_show')) {
	function busy_server_show() {
	
		header("HTTP/1.1 503 Service Temporarily Unavailable");
		header("Status: 503 Service Temporarily Unavailable");
		header("Retry-After: 3600");
			?><!DOCTYPE html>
				<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<title>Server Busy</title>
					<meta name="robots" content="none" />
				</head>
				<body>
					<div>
			<?php	
		
				$busy_msg = get_option('busy_server_busy_message');
				if (empty($busy_msg))
					$busy_msg = 'Server is busy. Please try reloading this page after few minutes.'; // default message
				echo $busy_msg;
				echo '</div></body>';
				exit;
			}
		}

//Calculate load for UNIX multiple core machine, @return float
if (!function_exists('busy_server_calculate_load')) {
	function busy_server_calculate_load($raw_load) {
		$cores = substr_count (file_get_contents('/proc/cpuinfo'), 'model name');
		if ($cores)
			return $raw_load / $cores;
		else
			return $raw_load;
	}
}

//Main routine
if (!function_exists('busy_server_start')) {
	function busy_server_start() {

		$load_arr = busy_server_load();
		if (!empty($load_arr)) {
			
			$cur_load = 0;
			//TODO: meta to header!!!
			//echo '<meta name="busy-server" content="' . addslashes(implode ( ' ' , $load_arr )) . '">';
			
			
			if(!empty($load_arr['unit'])) {
				//windows
				$cur_load = intval($load_arr[1]);
				$max_load_win = floatval(get_option('busy_server_max_load')); 
				if (empty($max_load_win))
					$max_load_win = 98; // default max load percentage
				if ($cur_load > $max_load_win) 
					busy_server_show();
			}
			else {
			//UNIX
				$cur_load = busy_server_calculate_load($load_arr[0]);
				$max_load_unix = floatval(get_option('busy_server_max_load')); 
				if (empty($max_load_unix))
					$max_load_unix = 2; // default load per core ( 1.0 = 100%)
				if ($cur_load > $max_load_unix) 
					busy_server_show();
			}
			
		}	
		else {
			//TODO: meta to header!!!
			//echo '<meta name="busy-server" content="Unable to detect the server load. Disable the plugin.">';
		}

	}	
}
