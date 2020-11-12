<?php
/*
SORTEIO DE NUMERO RANDOM MIN E MAX
*/
$discord->registerCommand('sorteio', function ($message, $params) {
	global $log;
	
	$msg = '**Sorteio!**';
	$embed = [
		'author' => [],
		'title' => 'Comandos de "Sorteio"',
		'color' => DiscordApp\ColorsEmbed::get('GREEN'),
		'fields' => [],
	];
	
	$embed['description'] = '**Como realizar um sorteio?**';
	$embed['description'] .= "\nDigite: ``\$sorteio <numero_min> <numero_max>``";
	$embed['description'] .= "\nExemplo: ``\$sorteio 3 90``";
	$embed['description'] .= "\n\n**Qual o número mínimo?**";
	$embed['description'] .= "\nO número mínimo é **0 (zero)**.";
	$embed['description'] .= "\n\n**Qual o número máximo?**";
	$embed['description'] .= "\nO número máximo é **9999999999**";
	
	if(isset($params[0]) && isset($params[1])){
		$min_int = (int) $params[0];
		$max_int = (int) $params[1];
		
		if($min_int < '0'){
			$embed['description'] =  'O número mínimo não pode ser menor que 0!';
		}elseif($max_int > '9999999999'){
			$embed['description'] =  'O número máximo não pode ser maior que 9999999999!';
		}elseif(strlen($params[0]) != strlen($min_int)|| strlen($params[1]) != strlen($max_int)){
			$embed['description'] = 'Digite o comando correto: ``$sorteio <numero_min> <numero_max>``';
		}elseif($max_int < $min_int){
			$embed['description'] =  'O número máximo não pode ser menor que o número mínimo!';
		}else{
			$rand = rand($min_int, $max_int);
			
			$embed['title'] = $message->author->username.' fez um sorteio de '.$min_int.' até '.$max_int.'!';
			$embed['description'] = '*O número sorteado foi*: **'.$rand.'**';
			$embed['color'] = DiscordApp\ColorsEmbed::get('GREEN');
		}
	}
	
	$message->channel->sendMessage($msg, false, $embed);
	$message->delete();
	
}, [
  'description' => 'Sorteio de um numero!',
  'usage' => "\$sorteio <numero_min> <numero_max>",
]);
?>