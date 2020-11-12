<?php
$GLOBALS['primaryCommandforca']->registerSubCommand('chutar', function ($message, $params) {
	$msg = '**Jogo da Forca!**';
	
	$embed = [
		'author' => [
			'name' => $message->author->username,
			'icon_url' => $message->author->avatar
		],
		'title' => 'Jogo da Forca!',
		'description' => "",
		'color' => DiscordApp\ColorsEmbed::get('GREEN'),
		'thumbnail' => [
			'url' => 'http://www.cjdinfo.com.br/images/diversao/forca/vazia.png',
		],
		'fields' => [],
	];
	
	$chute = implode(' ',$params);
	$forca = new DiscordApp\Bean\Forca();
	$forca->user_id = $message->author->id;
	$forca->selectActive();
	
	if(!empty($forca->last_msg_id)){
		$message->channel->deleteMessages([$forca->last_msg_id]);
	}
	
	if(!empty($forca->id)){
		if(empty($chute)){
			$embed['title'] = "Digite o comando corretamente";
			$embed['description'] .= "\$forca chutar <palavra>";
			
		}else{
			if(in_array($chute, $forca->guessed)){
				$embed['title'] = "Você já chutou ".strtoupper($chute)." anteriormente!";
				$embed['description'] = "``\$forca chutar <palavra>``";
			}else{
				$guess_right = $forca->tryGuess($chute);
				
				$check_over = $forca->checkWinLose();
				
				
				if($check_over == 'win'){
					$forca->status = 'done';
					$embed['description'] = $forca->mountSpots(true);
					$embed['description'] .= "\n\n:tada:**Você GANHOU O JOGO! PARABÉNS!!**:tada:";
					$embed['description'] .= "\n\n**Estatísticas:**";
					$embed['description'] .= "\n**Dicas usadas: ".count($forca->dicas)."**";
					$embed['description'] .= "\n**Tentativas: ".$forca->countTrys()."**";
				}elseif($check_over == 'lose'){
					$forca->status = 'done';
					$embed['description'] = $forca->mountSpots(true);
					$embed['description'] .= "\n\n**VOCÊ PERDEU O JOGO!**";
					$embed['description'] .= "\n\n**Estatísticas:**";
					$embed['description'] .= "\n**Dicas usadas: ".count($forca->dicas)."**";
					$embed['description'] .= "\n**Tentativas: ".$forca->countTrys()."**";
				}else{
					$embed['description'] = $forca->mountSpots();
				
					if(!$guess_right){
						$embed['description'] .= "\n\nSeu chute estava errado: **".strtoupper($chute)."**";
					}
					
					$embed['description'] .= "\n\nTentativas restantes: **{$forca->chances_left}**";
					$embed['description'] .= "\n\nQuantidade de letras: **{$forca->palavraInfo['count']}**";
					$embed['description'] .= "\nTentativas: **".((count($forca->letras) > 0) ? implode(', ', $forca->letras) : ' ') . ((count($forca->guessed) > 0) ? implode(', ', $forca->guessed) : ' ')."**";
					$embed['description'] .= "\n\n".$forca->mountHints();
				}
				$forca->save();
			}
		}
	}else{
		$embed['title'] = "Não existe um jogo ativo no momento!";
		$embed['description'] = "Digite ``\$forca iniciar`` para começar um novo jogo.";
	}
	
	$message->channel->sendMessage($msg, false, $embed)->then(function($message) use($forca) {
		$forca->last_msg_id = $message->id;
		$forca->save();
	});
	$message->delete();
	
},[
	'description' => 'Chutar a palavra!',
	'usage' => '$forca chutar <palavra>',
]);
?>