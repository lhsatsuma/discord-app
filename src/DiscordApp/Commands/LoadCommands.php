<?php
namespace DiscordApp\Commands;

class LoadCommands
{
	public function __construct()
	{
		global $discord;
		$this->discord = $discord;
	}
	public function initial_load()
	{
		/*
		INCLUDING ALL COMMANDS CLIENTS
		*/
		$base_path = __DIR__ . '/commandsList/';
		$scan = scan_dir($base_path);
		$return = '';
		foreach($scan as $commandName){
			$commandPath = $base_path.$commandName.'/';
			
			$classFile = $commandPath.$commandName.'.php';
			$commandFile = $commandPath.'command.php';
			
			if(file_exists($commandFile)){
				if(file_exists($classFile)){
					$required_class = $this->try_require($classFile);
					if(empty($required_class)){
						$required_command = $this->load_command($commandName, $commandPath);
					}else{
						$this->discord->log->Fatal('Unable to load class '.$classFile.':'.$required_class, 0, 1);
					}
				}else{
					$required_command = $this->load_command($commandName, $commandPath);
				}
			}
		}
		$return .= $this->load_lookup();
		return $return;
	}
	
	public function load_lookup()
	{
		return;
		$scan = scan_dir('AppFiles/lookUp/');
		$return = '';
		foreach($scan as $lookUpName){
			
			$lookUpPath = 'AppFiles/lookUp/'.$lookUpName;
			if(file_exists($lookUpPath)){
				$required_lookup = $this->try_require($lookUpPath);
				if(!empty($required_lookup)){
					$return .= $required_lookup;
					$this->discord->log->Fatal('Unable to load LookUp '.$lookUpName.':'.$required_lookup, 0, 1);
				}else{
					$this->discord->log->Debug('Loaded LookUp: '.$lookUpName);
				}
			}
		}
		return $return;
		
	}
	public function load_command($commandName, $commandPath, $load_subs = true)
	{
		global $discord;
		$return = '';
		
		$commandFile = $commandPath.'command.php';
		if($discord->getCommand($commandName)){
			$discord->unregisterCommand($commandName);
		}
		$required_command = $this->try_require($commandFile);
		
		if(empty($required_command)){
			$this->discord->log->Debug('Loaded command name: '.$commandName);
			if($load_subs){
				$subCommandRootPath = $commandPath.'subs/';
				if(is_dir($subCommandRootPath)){
					$varToCheck = 'primaryCommand'.$commandName;
					if(isset($GLOBALS[$varToCheck])){
					
						$this->discord->log->Debug('Loading sub commands for: '.$commandName);
						
						$scanSub = scan_dir($subCommandRootPath);
						
						foreach($scanSub as $subCommandName){
							$subCommandPath = $subCommandRootPath.$subCommandName.'/';
							$subCommandFile = $subCommandPath.'subCommand.php';
							if(file_exists($subCommandFile)){
								$required_subCommand = $this->load_sub_command($subCommandName, $subCommandPath);
								if($required_subCommand){
									$return = $required_subCommand;
								}
							}
						}
						
						$this->discord->log->Debug('Loaded sub commands for: '.$commandName);
					}else{
						$return = 'Failed to load command name: '.$commandFile.'! No $primaryCommand was defined!';
					}
				}
			}
		}else{
			$return = 'Failed to load command name: '.$commandFile.'! '.$required_command;
		}
		
		if(!empty($return)){
			$this->discord->log->Debug($return);
		}
		return $return;
	}
	
	public function load_sub_command($subCommandName, $subCommandPath)
	{
		$return = '';
		
		$subCommandFile = $subCommandPath.'subCommand.php';
		
		$required_subCommand = $this->try_require($subCommandFile);
		
		if(empty($required_subCommand)){
			$this->discord->log->Debug('Loaded subCommand name: '.$subCommandName);
		}else{
			$return = 'Failed to load subCommand name: '.$subCommandFile.'! '.$required_subCommand;
		}
		
		return $return;
	}
	
	private function try_require($fileName)
	{
		$log_str = '';
		try{
			$discord = $this->discord;
			include($fileName);
		}catch(ParseError $e){
			var_dump($e);
			$log_str = $e->getMessage().' on line '.$e->getline();
		}
		return $log_str;
	}
	
	public function reload_commands()
	{
		$base_path = __DIR__ . '/commandsList/';
		$scan = scan_dir($base_path);
		$return = '';
		foreach($scan as $commandName){
			$commandPath = $base_path.$commandName.'/';
			
			$commandFile = $commandPath.'command.php';
			$required_command = $this->load_command($commandName, $commandPath);
			if($required_command){
				$return .= $required_command."\n";
			}
		}
		return $return;
	}
}
?>