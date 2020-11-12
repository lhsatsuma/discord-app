<?php
/*
PING VERIFICAR SE O BOT TA ATIVO
*/
$discord->registerCommand('reload', function ($message) {
	global $discord, $LoadCommands, $bot_cfg;
	
	return $discord->disabledCommand($message);
	
	$embed = [
		'author' => [],
		'title' => 'Permissão negada!',
		'description' => 'utilize este comando no canal de <#'.$bot_cfg['admin_channel_id'].'>',
		'color' => DiscordApp\ColorsEmbed::get('RED'),
		'fields' => [],
	];
	
	if($message->channel_id !== $bot_cfg['admin_channel_id']){
		$discord->log->Warning($message->author->username.' tentou utilizar o comando $reloadcommands no canal de: '.$message->channel->name, 0, 1);
	}else{
		$completed = $LoadCommands->reload_commands();
		if($completed == ''){
			$embed['title'] = 'Reload completo!';
			$embed['color'] = DiscordApp\ColorsEmbed::get('BLUE');
			$embed['description'] = '';
		}else{
			$embed['title'] = 'Erro ao recarregar! Tente reiniciar manualmente.';
			$embed['description'] = $completed;
			$embed['color'] = DiscordApp\ColorsEmbed::get('RED');
		}
	}
	$message->channel->sendMessage('', false, $embed);
	$message->delete();
	
}, [
  'description' => 'Reload Commands',
  'usage' => "\$reload",
]);
?>