<?php
$GLOBALS['primaryCommandforca'] = $discord->registerCommand('forca', function ($message) {
	$msg = '**Jogo da Forca!**';
	
	$embed = [
		'author' => [
			'name' => $message->author->username,
			'icon_url' => $message->author->avatar
		],
		'title' => 'Comandos do "Jogo da Forca"',
		'thumbnail' => [
			'url' => 'http://www.cjdinfo.com.br/images/diversao/forca/vazia.png',
		],
		'description' => "",
		'color' => DiscordApp\ColorsEmbed::get('GREEN'),
		'fields' => [],
	];
	
	$embed['description'] .= "**Como iniciar um jogo?**";
	$embed['description'] .= "\nDigite: ``\$forca iniciar``";
	$embed['description'] .= "\n\n**Como dar dica da palavra?**";
	$embed['description'] .= "\nDigite: ``\$forca dica``";
	$embed['description'] .= "\n\n**Posso chutar o que é a palavra?**";
	$embed['description'] .= "\nSim, basta digitar: ``\$forca chutar <palavra>``";
	$embed['description'] .= "\n\n**Como eu paro o jogo atual?**";
	$embed['description'] .= "\nDigite: ``\$forca parar``";
	$embed['description'] .= "\n\n**Como eu vejo a palavra atual?**";
	$embed['description'] .= "\nDigite: ``\$forca mostrar``";
	
	$message->channel->sendMessage($msg, false, $embed);
	$message->delete();
	
},[
	'description' => 'Jogo da Forca!',
	'usage' => '$forca <iniciar|mostrar|dica|parar|letra>',
]);
?>