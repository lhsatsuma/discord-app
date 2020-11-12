<?php
/*
PING VERIFICAR SE O BOT TA ATIVO
*/
$discord->registerCommand('ping', function ($message) {
	global $log;
	
	$embed = [
		'author' => [],
		'title' => 'Ping recebido!',
		'description' => 'Data: '.date('d/m/Y H:i:s'),
		'color' => DiscordApp\ColorsEmbed::get('BLUE'),
		'fields' => [],
	];
	$message->channel->sendMessage('', false, $embed);
	
}, [
  'description' => 'pong!',
  'usage' => "\$ping",
]);
?>