<?php
/*
PING VERIFICAR SE O BOT TA ATIVO
*/
$discord->registerCommand('pidkill', function ($message) {
	global $discord, $bot_cfg;
	
	$alright_to_kill = false;
	
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
		$alright_to_kill = true;
		$embed = [
			'author' => [],
			'title' => 'Foi bom enquanto durou! R.I.P BOT '.date("d/m/Y H:i:s"),
			'description' => "Foi bom enquanto durou, vou sentir sua falta!\n Nos vemos mais tarde... :wave: :wave:",
			'color' => DiscordApp\ColorsEmbed::get('RED'),
			'fields' => [],
		];
	}
	$message->delete();
	$message->channel->sendMessage('', false, $embed)->done(function() use($alright_to_kill) {
		if($alright_to_kill){
			exit;
		}
		
	});
}, [
  'description' => 'pidkill!',
  'usage' => "\$pidkill",
]);
?>