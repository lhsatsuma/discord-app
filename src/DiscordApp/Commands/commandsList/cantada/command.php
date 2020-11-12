<?php
$discord->registerCommand('cantada', function ($message, $params) {
	
	$cantadas_prontas = get_json_file('uploads/cantadas.json');
	
	$key = array_rand($cantadas_prontas);

	$embed = [
		'author' => [],
		'title' => '',
		'description' => '',
		'color' => 16580705,
		'fields' => [],
	];
	
	$cantada = $cantadas_prontas[$key];
	
	$msg = '**Cantadas Engraçadas!**';
	
	if(!empty($params[0]) && strpos($params[0], '<@!') !== false){
		$msg .= "\n".$params[0].', <@'.$message->author->id.'> mandou uma cantada para você!';
	}
	
	if(
		strpos($cantada, 'http://') !== false
		|| strpos($cantada, 'https://') !== false
	){
		$embed['image']['url'] = $cantada;
	}else{
		$embed['title'] = $cantada;
	}
	
	$message->channel->sendMessage($msg, false, $embed);
	$message->delete();
	
}, [
  'description' => "Cantadas boas!",
  'usage' => "\$cantada <usuario>(OPCIONAL)",
  'cooldown' => 3000,
]);
?>