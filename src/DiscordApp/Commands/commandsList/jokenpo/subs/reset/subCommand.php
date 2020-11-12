<?php
$GLOBALS['primaryCommandjokenpo']->registerSubCommand('reset', function ($message) {
	
	$jokenpo = new DiscordApp\Bean\Jokenpo();
	$jokenpo->user_id = $message->author->id;
	
	$jokenpo->resetScore();
	
	$embed = [
		'author' => [],
		'title' => 'Ops! Erro ao jogar o jokenpo!',
		'description' => "Digite o comando corretamente:\n``\$jokenpo reset``",
		'color' => DiscordApp\ColorsEmbed::get('RED'),
		'fields' => [],
	];
	$embed['title'] = 'Pontuação no Jokenpo de '.$message->author->username;
	$embed['description'] = '**Pontos resetados com sucesso!**';
	$embed['color']	= DiscordApp\ColorsEmbed::get('GREEN');
	
	$message->channel->sendMessage('', false, $embed);
	$message->delete();
	
}, [
	'description' => 'Resetar Pontuações!',
	'usage' => '$jokenpo reset',
]);
?>