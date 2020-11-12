<?php
$GLOBALS['primaryCommandvelha']->registerSubCommand('marcar', function ($message, $params) {
	global $log;
	
	$velha = new DiscordApp\Bean\Velha();
	$velha->user_id = $message->author->id;
	$velha->selectActive();
	
	if(!empty($velha->last_msg_id)){
		$message->channel->deleteMessages([$velha->last_msg_id]);
	}
	
	$embed = [
		'author' => [],
		'title' => 'Ops! Erro ao iniciar um Jogo da Velha!',
		'description' => "Digite o comando corretamente:\n``\$velha marcar <".implode("|", $velha->options).">``",
		'thumbnail' => [
			'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f6/Tic_Tac_Toe.png/200px-Tic_Tac_Toe.png',
		],
		'color' => DiscordApp\ColorsEmbed::get('RED'),
		'fields' => [],
	];
	
	if(empty($velha->id)){
		$embed['title'] = "Não existe um jogo ativo no momento!";
		$embed['description'] = "Digite ``\$velha iniciar <".implode(',', $velha->levels)."> <sim>(OPCIONAL)`` para começar um novo jogo";
	}elseif(!in_array($params[0], $velha->options)){
		$embed['title'] = 'Marcação inválida!';
		$embed['description'] = "Digite o comando corretamente:\n``\$velha marcar <".implode(',', $velha->options).">``";
	}elseif(!in_array($params[0], $velha->getAvailables())){
		$embed['title'] = 'Marcação inválida!';
		$embed['description'] = "Marcações disponíveis:\n``\$velha marcar <".implode(',', $velha->getAvailables()).">``";
	}else{
		$velha->markPlayer($params[0]);
		$win = $velha->checkWin();
		if($win == 'draw'){
			$velha->status = 'done';
			$velha->win = 3;
		}elseif($win){
			$velha->status = 'done';
			$velha->win = ($win == 'bot') ? 1 : 2;
		}else{
			$rand_bot = $velha->randBot();
			$velha->markBot($rand_bot);
			$win = $velha->checkWin();
			if($win == 'draw'){
				$velha->status = 'done';
				$velha->win = 3;
			}elseif($win){
				$velha->status = 'done';
				$velha->win = ($win == 'bot') ? 1 : 2;
			}
		}
		$velha->save();
		if(!$win){
			$embed['title'] = "Sua vez de jogar!";
			$embed['description'] = $velha->mountSpots();
		}else{
			if($win == 'bot'){
				$embed['title'] = "Jogo finalizado! Ganhador: BOT";
			}elseif($win == 'player'){
				$embed['title'] = "Jogo finalizado! Ganhador: {$message->author->username}";
			}else{
				$embed['title'] = "Jogo finalizado! Ganhador: EMPATE";
			}
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
	'description' => 'Marcar no Jogo da Velha!',
	'usage' => '$velha marcar <1|2|3|4|5|6|7|8|9>',
]);
?>