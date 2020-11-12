<?php
$GLOBALS['primaryCommandvelha']->registerSubCommand('parar', function ($message, $params) {
	global $log;
	
	$velha = new DiscordApp\Bean\Velha();
	
	$msg = '**Jogo da Velha!**';
	
	$velha->user_id = $message->author->id;
	$velha->selectActive();
	if(!empty($velha->last_msg_id)){
		$message->channel->deleteMessages([$velha->last_msg_id]);
	}
	
	$embed = [
		'author' => [],
		'title' => 'Ops! Erro ao parar o jogo atual de Jogo da Velha!',
		'description' => "Digite o comando corretamente:\n``\$velha parar``",
		'thumbnail' => [
			'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f6/Tic_Tac_Toe.png/200px-Tic_Tac_Toe.png',
		],
		'color' => DiscordApp\ColorsEmbed::get('GREEN'),
		'fields' => [],
	];
	
	if(empty($velha->id)){
		$embed['title'] = "Não existe um jogo ativo no momento!";
		$embed['description'] = "Digite ``\$velha iniciar <facil|medio|dificil> <sim>(OPCIONAL)``";
	}else{
		$velha->status = 'done';
		$velha->save();
		$embed['title'] = "Jogo parado com sucesso!";
		$embed['description'] = '';
	}
	
	$message->channel->sendMessage('', false, $embed);
	$message->delete();
	
}, [
	'description' => 'Parar o jogo atual!',
	'usage' => '$jokenpo parar',
]);
?>