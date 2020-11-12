<?php
namespace DiscordApp\Commands;

use Discord\CommandClient\Command;

class CommandApp extends Command
{
    public function handle($message, array $args)
    {
		global $discord, $bot_cfg;
		
		$invalid_request = '';
		if(in_array($message->channel->guild_id, $bot_cfg['servers_allow'])){
			$subCommand = array_shift($args);

			if (array_key_exists($subCommand, $this->subCommands)) {
				$invalid_request = $this->subCommands[$subCommand]->handle($message, $args);
			} elseif (array_key_exists($subCommand, $this->subCommandAliases)) {
				$invalid_request = $this->subCommands[$this->subCommandAliases[$subCommand]]->handle($message, $args);
			}else{

				if (! is_null($subCommand)) {
					array_unshift($args, $subCommand);
				}

				$currentTime = round(microtime(true) * 1000);
				if (isset($this->cooldowns[$message->author->id])) {
					if ($this->cooldowns[$message->author->id] < $currentTime) {
						$this->cooldowns[$message->author->id] = $currentTime + $this->cooldown;
					} else {
						$invalid_request = sprintf($this->cooldownMessage, (($this->cooldowns[$message->author->id] - $currentTime) / 1000));
					}
				} else {
					$this->cooldowns[$message->author->id] = $currentTime + $this->cooldown;
				}
				if(!$invalid_request){
					$discord->log->Debug('Received command: '.$message->content);
					$return = call_user_func_array($this->callable, [$message, $args]);
					$discord->log->Debug('Received command: '.$message->content.' DONE!');
				}
			}
		}else{
			$invalid_request = "**Meus comandos só podem ser utilizados no servidor DBIKE Server**";
		}
		
		if ($invalid_request) {
			$embed = [
				'author' => [],
				'title' => "",
				'description' => "",
				'fields' => [],
			];
			if(strlen($return) > 255 || strpos($return, "\n") !== false){
				$embed['description'] = $invalid_request;
			}else{
				$embed['title'] = $invalid_request;
			}
			$message->channel->sendMessage('', false, $embed);
			$message->delete();
		}
		
		return null;
    }
}
?>