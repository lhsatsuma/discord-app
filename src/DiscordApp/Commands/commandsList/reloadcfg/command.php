<?php
/*
PING VERIFICAR SE O BOT TA ATIVO
*/
$discord->registerCommand('reloadcfg', function ($message) {
	global $discord, $bot_cfg;
	
	return $discord->disabledCommand($message);
	
	$embed = [
		'author' => [],
		'title' => 'Permissão negada!',
		'description' => 'utilize este comando no canal de <#'.$bot_cfg['channel_admin_cmd'].'>',
		'color' => DiscordApp\ColorsEmbed::get('RED'),
		'fields' => [],
	];
	if($message->channel_id !== $bot_cfg['channel_admin_cmd']){
		$discord->log->Warning($message->author->username.' tentou utilizar o comando $reloadcfg no canal de: '.$message->channel->name);
	}else{
		try{
			require('_bot_cfg.php');
			$embed['title'] = 'Reload cfg completo!';
			$embed['color'] = DiscordApp\ColorsEmbed::get('BLUE');
			$embed['description'] = '';
			$discord->log->Info($message->author->username.' requested updated cfg');
		}catch(Throwable $e){
			$msg_error = 'ERROR RELOADING CFG FILE: '.$e->getMessage().' on line '.$e->getline();
			$discord->log->Fatal($msg_error, 0, 1);
			$embed['title'] = 'Erro ao recarregar o cfg! Tente reiniciar manualmente.';
			$embed['description'] = $msg_error;
			$embed['color'] = DiscordApp\ColorsEmbed::get('RED');
		}
	}
	$message->channel->sendMessage('', false, $embed);
	$message->delete();
	
}, [
  'description' => 'Reload cfg',
  'usage' => "\$reloadcfg",
]);
?>