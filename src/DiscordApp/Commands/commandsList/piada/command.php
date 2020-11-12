<?php
/*
PIADAS COM TROCADILHOS
*/
$discord->registerCommand('piada', function ($message) {
	global $log;
	
	$msg = '**Piadas Engraçadas!**';
	
	$piadas_prontas = get_json_file('uploads/trocadilhos.json');
	
	$key = array_rand($piadas_prontas);
	
	$message->delete();
	
	$embed = [
		'author' => [],
		'title' => $piadas_prontas[$key]['pergunta'],
		'description' => $piadas_prontas[$key]['resposta'],
		'color' => DiscordApp\ColorsEmbed::get_rand(),
		'image' => [
			'url' => $cantadas_prontas[$key],
		],
		'fields' => [],
	];
	
	$message->channel->sendMessage($msg, false, $embed);
	$message->delete();
	
}, [
  'description' => 'trocadilhos idiotas!',
  'usage' => '$piada',
  'cooldown' => 3000,
]);
?>