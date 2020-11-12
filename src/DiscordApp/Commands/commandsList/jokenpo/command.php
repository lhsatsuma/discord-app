<?php
$GLOBALS['primaryCommandjokenpo'] = $discord->registerCommand('jokenpo', function ($message, $params) {
	
	$msg = '**Pedra, Papel ou Tesoura!**';
	$embed = [
		'author' => [],
		'title' => 'Comandos do "Pedra, Papel ou Tesoura"',
		'color' => DiscordApp\ColorsEmbed::get('GREEN'),
		'fields' => [],
	];
	
	$embed['description'] = '**Como jogar?**';
	$embed['description'] .= "\nDigite: ``\$jokenpo jogar <pedra|papel|tesoura>``";
	$embed['description'] .= "\nExemplo: ``\$jokenpo jogar pedra``";
	$embed['description'] .= "\n\n**Como vejo minhas pontuações?**";
	$embed['description'] .= "\nDigite: ``\$jokenpo status``";
	$embed['description'] .= "\n\n**Como reseto minhas pontuações?**";
	$embed['description'] .= "\nDigite: ``\$jokenpo reset``";
	
	$message->channel->sendMessage($msg, false, $embed);
	$message->delete();
	
}, [
  'description' => 'Pedra, Papel ou Tesoura!',
  'usage' => "\$jokenpo <jogar|status|reset>",
]);
?>