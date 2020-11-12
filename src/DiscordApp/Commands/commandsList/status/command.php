<?php
/*
PING VERIFICAR SE O BOT TA ATIVO
*/
$discord->registerCommand('status', function ($message) {
	global $discord, $bot_cfg;
	
	$embed = [
		'author' => [
			'name' => $message->author->username,
			'icon_url' => $message->author->avatar,
		],
		'thumbnail' => [
			'url' => 'https://cdn.discordapp.com/icons/710607431410909185/e1e9494ab23f245cf12619a65518738c.jpg?size=1024',
		],
		'title' => 'Permissão negada!',
		'description' => 'Utilize este comando no canal de <#'.$bot_cfg['admin_channel_id'].'>',
		'color' => DiscordApp\ColorsEmbed::get('RED'),
		'fields' => [],
	];
	
	if($message->channel_id !== $bot_cfg['admin_channel_id']){
		$discord->log->Info($message->author->username.' tentou utilizar o comando $status no canal de: '.$message->channel->name);
	}else{
		$ProcessRunning = new DiscordApp\Process\ProcessRunning();
		$embed['title'] = 'Status DBIKE BOT!';
		$embed['description'] = $ProcessRunning->mount_str_check_run();
		$embed['color'] = DiscordApp\ColorsEmbed::get('BLUE');
	}
	$message->channel->sendMessage('', false, $embed);
	$message->delete();
	
}, [
  'description' => 'Status of System BOT',
  'usage' => "\$status",
  'cooldown' => 10,
]);
?>