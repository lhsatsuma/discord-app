<?php
$GLOBALS['primaryCommandvelha']->registerSubCommand('mostrar', function ($message, $params) {
	global $log;
	
	$velha = new DiscordApp\Bean\Velha();
	
	$options = [1,2,3,4,5,6,7,8,9];
	
	$velha->user_id = $message->author->id;
	$velha->selectActive();
	if(!empty($velha->last_msg_id)){
		$message->channel->deleteMessages([$velha->last_msg_id]);
	}
	
	$embed = [
		'author' => [],
		'title' => 'Ops! Erro ao iniciar um Jogo da Velha!',
		'description' => "Digite o comando corretamente:\n``\$velha jogar <".implode("|", $velha->levels)."> <sim>(OPCIONAL)``",
		'thumbnail' => [
			'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f6/Tic_Tac_Toe.png/200px-Tic_Tac_Toe.png',
		],
		'color' => DiscordApp\ColorsEmbed::get('RED'),
		'fields' => [],
	];
	
	if(empty($velha->id)){
		$embed['title'] = "Não existe um jogo ativo no momento!";
		$embed['description'] = "Digite ``\$velha j`` para começar um novo jogo";
	}else{
		$embed['title'] = "Sua vez de jogar!";
		$embed['description'] = $velha->mountSpots();
	}
	
	$message->channel->sendMessage('', false, $embed)->then(function ($message) use ($velha) {
		if(!empty($velha->id)){
			$velha->last_msg_id = $message->id;
			$velha->save();
		}
	});
	$message->delete();
	
}, [
	'description' => 'Jogar Pedra, Papel ou Tesoura!',
	'usage' => '$jokenpo jogar <pedra|papel|tesoura>',
]);
?>