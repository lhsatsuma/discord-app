<?php
$GLOBALS['primaryCommandforca']->registerSubCommand('dica', function ($message, $params) {
	
	$forca = new DiscordApp\Bean\Forca();
	
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
			'url' => $forca->options['thumb'],
		],
		'fields' => [],
	];
	$forca->user_id = $message->author->id;
	$forca->selectActive();
	
	if(!empty($forca->last_msg_id)){
		$message->channel->deleteMessages([$forca->last_msg_id]);
	}
	
	if(!empty($forca->id)){
		$gived = $forca->giveHint();
		
		$gived_msg = '';
		
		if(!$gived){
			$gived_msg = "\n\n**Não existem mais dicas disponíveis!**";
		}else{
			$forca->save();
		}
		
		$embed['description'] = $forca->mountSpots();
	
		$embed['description'] .= $gived_msg;
		$embed['description'] .= "\n\nTentativas restantes: **{$forca->chances_left}**";
		$embed['description'] .= "\n\nQuantidade de letras: **{$forca->palavraInfo['count']}**";
		$embed['description'] .= "\nTentativas: **".((count($forca->letras) > 0) ? implode(',', $forca->letras) : ' ')."**";
		$embed['description'] .= "\n\n".$forca->mountHints();
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
	'description' => 'Dá uma dica do jogo atual!',
	'usage' => '$forca dica',
]);
?>