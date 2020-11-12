<?php
namespace DiscordApp\Process;

class ProcessRunning
{
	//ALL FUNCTIONS OF PROCESS RUNNING
	function check_running()
	{
		$running = array();
		
		$exec = exec(' ps -eo pid,etime,args | grep php', $output);
		foreach($output as $line){
			if(strpos($line, '/opt/bitnami/php/bin/php.bin run.php') !== false){
				$line = str_replace(' /opt/bitnami/php/bin/php.bin run.php', '', $line);
				$pid_info = explode('    ', $line);
				$running[] = array('pid' => $pid_info[0], 'time' => $pid_info[1]);
			}
		}
		return $running;
	}
	
	function last_log_run()
	{
		global $bot_cfg;
		$logcron = '';
		$total_lines = 5;
		exec('tail -'.$total_lines.' logs/cron.log', $logcron_ar);
		foreach($logcron_ar as $log){
			if(
			strpos($log, $bot_cfg['discordOptions']['token']) !== false
			|| strpos($log, 'session_id') !== false
			){
				$log = '|||||||||||||OMMITED LOG LINE FOR SECURITY REASONS!|||||||||||||';
			}
			$logcron .= $log."\n";
		}
		return $logcron;
	}
	
	function server_info()
	{
		exec('httpd -v', $apache);
		exec('uptime -p', $uptime);
		return [
			'apache' => str_replace('Server version: ', '', $apache[0]),
			'php' => phpversion(),
			'os_uptime' => str_replace('up ', '', $uptime[0]),
		];
	}

	function mount_str_check_run()
	{
		$running = $this->check_running();
		$log_client = $this->last_log_run('client');
		$log_bot = $this->last_log_run('bot');
		
		$return = '**GERADO EM: '.date("d/m/Y H:i:s").'**';
		
		if (substr(php_sapi_name(), 0, 3) == 'cli') {
			$server_info = $this->server_info();
			$return .= "\n\n**===>SERVER INFO<=== **\n";
			$return .= "PHP VERSION: **".$server_info['php']."**\n";
			$return .= "APACHE VERSION: **".$server_info['apache']."**\n";
			$return .= "OS UPTIME: **".$server_info['os_uptime']."**\n";
		}
		$return .= "\n\n**===>DBIKE BOT:<===**\n";
		$return .= "STATUS: **".((count($running)) ? 'RUNNING ON PID: '.$running[0]['pid'] : 'NOT RUNNING')."**\n";
		$return .= "UPTIME: **".((trim($running[0]['time'])) ? trim($running[0]['time']) : '00:00:00')."**\n";
		$return .= "\n\n**===>TAIL CRON LOG:<=== **\n";
		$return .= print_r($log_bot, true);
		return $return;
	}
}
?>