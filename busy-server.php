
<?php
/*
Plugin Name: Busy Server
Plugin URI: https://github.com/gibboncz/busy-server
Description: When the server load is higher than specified, show an error message instead of loading the page.
Version: 0.1
Author: Lubos Svoboda
*/
?>
<?php


//TODO: check only when page not cached




add_action('send_headers', 'busy_server_start');



//Calculate web server load, for statistical purposes, Windows and UNIX, @return string
if (!function_exists('busy_server_load1')) {
function busy_server_load1($windows = false) {
    $os=strtolower(PHP_OS);
    if(strpos($os, 'win') === false){
        if(file_exists('/proc/loadavg')) {
            $load = file_get_contents('/proc/loadavg');
            $load = explode(' ', $load, 1);
            $load = $load[0];
        }
				elseif(function_exists('shell_exec')) {
            $load = explode(' ','uptime');
            $load = $load[count($load)-1];
        }
				else {
            return false;
        }
        if(function_exists('shell_exec'))$cpu_count = shell_exec('cat /proc/cpuinfo | grep processor | wc -l');
        return array('Load'=>$load,'CPU count'=>$cpu_count);
    }
		elseif($windows){
        if(class_exists('COM')) {
            $wmi = new COM("Winmgmts://");
            //
						$server = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");
            $load=0;
            $cpu_count=0;
             
             foreach($server as $cpu){
                 $cpu_count++;
                 $load += $cpu->loadpercentage;
             }
             
             $load = round($load/$cpu_count);
            
            return array('Load'=>$load,'CPU count'=>$cpu_count);
        }
        return false;
    }
    return false;
}
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
		echo 'Server is busy. Please try reloading this page after few minutes.';
		exit;
	}
}

//Calculate load for UNIX multiple core machine, @return float
if (!function_exists('busy_server_calculate_load')) {
	function busy_server_calculate_load($raw_load) {
		$cores = substr_count (file_get_contents('/proc/cpuinfo'), 'model name');
		return $raw_load / $cores;
	}
}

//Main routine
if (!function_exists('busy_server_start')) {
	function busy_server_start() {

		//default maximum load values:
		$max_load_win = 98; // percentage
		$max_load_unix = 2; //load per core ( 1.0 = 100%)
	

		$load_arr = busy_server_load();
		if (!empty($load_arr)) {
			
			$cur_load = 0;
			//TODO: meta to header!!!
			//echo '<meta name="busy-server" content="' . addslashes(implode ( ' ' , $load_arr )) . '">';
			
			
			if(!empty($load_arr['unit'])) {
				//windows
				$cur_load = intval($load_arr[1]);
				if ($cur_load > $max_load_win) 
					busy_server_show();
			}
			else {
			//UNIX
				$cur_load = busy_server_calculate_load($load_arr[0]);
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
