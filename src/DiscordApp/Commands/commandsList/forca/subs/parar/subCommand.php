<?php
$GLOBALS['primaryCommandforca']->registerSubCommand('parar', function ($message, $params) {
	
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
		$forca->status = 'done';
		$forca->save();
		$embed['title'] = "Jogo parado com sucesso!";
		$embed['description'] = '';
	}else{
		$embed['title'] = "Não existe um jogo ativo no momento!";
		$embed['description'] = "Digite ``\$forca iniciar`` para começar um novo jogo.";
	}
	
	$message->channel->sendMessage($msg, false, $embed);
	$message->delete();
	
},[
	'description' => 'Para o jogo atual!',
	'usage' => '$forca parar',
]);
?>