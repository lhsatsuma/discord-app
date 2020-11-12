<?php
$GLOBALS['primaryCommandvelha']->registerSubCommand('iniciar', function ($message, $params) {
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
	if(empty($params[0]) || !in_array(trim($params[0]), $velha->levels)){
		$embed['description'] = "Digite o comando corretamente:\n``\$velha iniciar <".implode("|", $velha->levels)."> <sim>(OPCIONAL)``";
	}else{
		if(!empty($velha->id)){
			$embed['title'] = "Parece que você já começou um jogo!";
			$embed['description'] = "Digite ``\$velha parar`` para cancelar o jogo anterior ou continue a jogar o anterior.";
		}else{
			$velha->level = trim($params[0]);
			$velha->status = 'new';
			
			if($params[1] == 'sim'){
				$rand_bot = $velha->randBot();
				$velha->markBot($rand_bot);
			}
			
			$velha->save();
			
			$embed['title'] = "Sua vez de jogar!";
			$embed['description'] = $velha->mountSpots();
		}
	}
	
	$message->channel->sendMessage('', false, $embed)->then(function ($message) use ($velha) {
		if(!empty($velha->id)){
			$velha->last_msg_id = $message->id;
			$velha->save();
		}
	});
	$message->delete();
	
}, [
	'description' => 'Iniciar um novo jogo!',
	'usage' => '$velha iniciar <facil|medio|dificil> <sim>(OPCIONAL)',
]);
?>