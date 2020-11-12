<?php
$GLOBALS['primaryCommandvelha'] = $discord->registerCommand('velha', function ($message, $params) {
	global $log, $bot_cfg;
	
	$velha = new DiscordApp\Bean\Velha();
	$options = [1,2,3,4,5,6,7,8,9];
	
	$msg = '**Jogo da Velha!**';
	
	$embed = [
		'author' => [
			'name' => $message->author->username,
			'icon_url' => $message->author->avatar
		],
		'title' => 'Comandos do "Jogo da Velha"',
		'thumbnail' => [
			'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f6/Tic_Tac_Toe.png/200px-Tic_Tac_Toe.png',
		],
		'description' => "",
		'color' => DiscordApp\ColorsEmbed::get('GREEN'),
		'fields' => [],
	];
	$embed['description'] = '**GABARITO DA GRADE:**';
	$embed['description'] .= "\n**1** | **2** | **3**";
	$embed['description'] .= "\n**4** | **5** | **6**";
	$embed['description'] .= "\n**7** | **8** | **9**";
	$embed['description'] .= "\n\n**Como iniciar um jogo?**";
	$embed['description'] .= "\nDigite: ``\$velha iniciar <facil|medio|dificil> <sim>(OPCIONAL)``";
	$embed['description'] .= "\nExemplo: ``\$velha iniciar facil``";
	$embed['description'] .= "\n\n**Obs.**: Para iniciar um jogo com o BOT iniciando, basta passar o parâmetro de <sim>";
	$embed['description'] .= "\nExemplo: ``\$velha iniciar facil sim``";
	$embed['description'] .= "\n\n**Como marcar um ponto?**";
	$embed['description'] .= "\nDigite: ``\$velha marcar <1|2|3|4|5|6|7|8|9>``";
	$embed['description'] .= "\nExemplo: ``\$velha marcar 3``";
	$embed['description'] .= "\n\n**Como eu paro o jogo atual?**";
	$embed['description'] .= "\nDigite: ``\$velha parar``";
	$embed['description'] .= "\n\n**Como eu vejo a grade atual?**";
	$embed['description'] .= "\nDigite: ``\$velha mostrar``";
	
	$message->channel->sendMessage($msg, false, $embed);
	$message->delete();
	
},[
	'description' => 'Jogo da Velha',
	'usage' => "\$velha <iniciar,parar,marcar,mostrar>",
	'cooldown' => 10000,
]);

?>