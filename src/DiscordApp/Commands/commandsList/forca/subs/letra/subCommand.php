<?php
$GLOBALS['primaryCommandforca']->registerSubCommand('letra', function ($message, $params) {
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
	
	if(
		$params[0]
		&& strlen(preg_replace("/[^A-Za-z0-9?! ]/","", $params[0])) == 1
	){
		$forca = new DiscordApp\Bean\Forca();
		$forca->user_id = $message->author->id;
		$forca->selectActive();
		
		if(!empty($forca->last_msg_id)){
			$message->channel->deleteMessages([$forca->last_msg_id]);
		}
		
		if(!empty($forca->id)){
			if(in_array($params[0], $forca->letras)){
				$embed['title'] = "Você já marcou a letra ".strtoupper($params[0])." anteriormente!";
				$embed['description'] = "``\$forca letra <A-Z>``";
			}else{
				$letter_right = $forca->tryLetter($params[0]);
				
				$check_over = $forca->checkWinLose();
				
				
				if($check_over == 'win'){
					$forca->status = 'done';
					$embed['description'] = $forca->mountSpots(true);
					$embed['description'] .= "\n\n:tada:**Você GANHOU O JOGO! PARABÉNS!!**:tada:";
					$embed['description'] .= "\n\n**Estatísticas:**";
					$embed['description'] .= "\n**Dicas usadas: ".count($forca->dicas)."**";
					$embed['description'] .= "\n**Tentativas: ".count($forca->letras) + count($forca->guessed)."**";
				}elseif($check_over == 'lose'){
					$forca->status = 'done';
					$embed['description'] = $forca->mountSpots(true);
					$embed['description'] .= "\n\n**VOCÊ PERDEU O JOGO!**";
					$embed['description'] .= "\n\n**Estatísticas:**";
					$embed['description'] .= "\n**Dicas usadas: ".count($forca->dicas)."**";
					$embed['description'] .= "\n**Tentativas: ".$forca->countTrys()."**";
				}else{
					$embed['description'] = $forca->mountSpots();
				
					if($letter_right){
						$embed['description'] .= "\n\nVocê ACERTOU a letra: **".strtoupper($params[0])."**";
					}else{
						$embed['description'] .= "\n\nVocê ERROU a letra: **".strtoupper($params[0])."**";
					}
					
					$embed['description'] .= "\n\nTentativas restantes: **{$forca->chances_left}**";
					$embed['description'] .= "\n\nQuantidade de letras: **{$forca->palavraInfo['count']}**";
				$embed['description'] .= "\nTentativas: **".((count($forca->letras) > 0) ? implode(', ', $forca->letras) : ' ') . ((count($forca->guessed) > 0) ? implode(', ', $forca->guessed) : ' ')."**";
					$embed['description'] .= "\n\n".$forca->mountHints();
				}
				$forca->save();
			}
		}else{
			$embed['title'] = "Não existe um jogo ativo no momento!";
			$embed['description'] = "Digite ``\$forca iniciar`` para começar um novo jogo.";
		}
	}else{
		$embed['title'] = "Digite o comando corretamente!";
		$embed['description'] = "``\$forca letra <A-Z>``";
	}
	
	$message->channel->sendMessage($msg, false, $embed)->then(function($message) use($forca) {
		$forca->last_msg_id = $message->id;
		$forca->save();
	});
	$message->delete();
	
},[
	'description' => 'Marcar uma letra no Jogo da Forca!',
	'usage' => '$forca letra <A-Z>',
]);
?>