<?php
define('ENTRYPOINT', true);
ini_set('error_log', 'php_errors.log');
ini_set('display_errors', 1);

require('_bot_cfg.php');

include __DIR__ . '/vendor/autoload.php';

use DiscordApp\InitApp;
use DiscordApp\Commands\LoadCommands;

$GLOBALS['discord'] = new InitApp();

$GLOBALS['LoadCommands'] = new LoadCommands();
global $LoadCommands;

$LoadCommands->initial_load();

global $discord;
$discord->on('ready', function($discord) {
	global $bot_cfg;
	
	return;
	//Send ready message into channel of #server-commands
	$channel = $discord->getChannel((int)$bot_cfg['admin_channel_id']);
	$guild = $discord->guilds->offsetGet($channel->guild_id);
	
	if($guild){
		$embed = [
			'author' => [],
			'title' => $bot_cfg['discordOptions']['name'].' ESTÁ ONLINE!',
			'description' => 'Data: '.date("d/m/Y H:i:s")."\nPID: ".getmypid(),
			'color' => DiscordApp\ColorsEmbed::get('GREEN'),
			'thumbnail' => [
				'url' => $guild->icon,
			],
			'fields' => [],
		];
		$channel->sendMessage('', false, $embed);
	}
});
$discord->run();
?>