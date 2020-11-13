<?php
$GLOBALS['primaryCommandforca']->registerSubCommand('iniciar', function ($message) {
	
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
	
	if(empty($forca->id)){
		$arr = $forca->getPalavras();
		$key_rand = array_rand($arr);
		$forca->setPalavraInfo($arr[$key_rand]['palavra']);
		$forca->status = 'new';
		$forca->save();
		
		$embed['description'] = $forca->mountSpots();
		$embed['description'] .= "\n\nTentativas restantes: **{$forca->chances_left}**";
		$embed['description'] .= "\n\nQuantidade de letras: **{$forca->palavraInfo['count']}**";
		$embed['description'] .= "\nTentativas: **".((count($forca->letras) > 0) ? implode(',', $forca->letras) : ' ')."**";
		$embed['description'] .= "\n\n".$forca->mountHints();
	}else{
		$embed['title'] = "Parece que você já começou um jogo!";
		$embed['description'] = "Digite ``\$forca parar`` para cancelar o jogo anterior ou continue a jogar o anterior.";
	}
	
	$message->channel->sendMessage($msg, false, $embed)->then(function($message) use($forca) {
		$forca->last_msg_id = $message->id;
		$forca->save();
	});
	$message->delete();
	
},[
	'description' => 'Iniciar um Jogo da Forca!',
	'usage' => '$forca iniciar',
]);
?>