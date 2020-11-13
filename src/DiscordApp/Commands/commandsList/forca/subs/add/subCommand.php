<?php
$GLOBALS['primaryCommandforca']->registerSubCommand('add', function($message, $params){
	global $discord, $bot_cfg;
	$msg = "**Adicionar palavra no Jogo da Forca!**";
	
	$forca = new DiscordApp\Bean\Forca();
	
	if($message->channel_id !== $bot_cfg['admin_channel_id']){
		$embed = [
			'title' => 'Permissão negada!',
			'description' => 'utilize este comando no canal de <#'.$bot_cfg['admin_channel_id'].'>',
			'color' => DiscordApp\ColorsEmbed::get('RED'),
		];
		$discord->log->Warning($message->author->username.' tentou utilizar o comando $reloadcommands no canal de: '.$message->channel->name, 0, 1);
	}else{
		
		
	
		$embed = [
			'author' => [
				'name' => $message->author->user->username,
				'icon_url' => $message->author->user->avatar,
			],
			'title' => 'Ops! Erro ao adicionar a palavra!',
			'description' => "Digite o comando corretamente: ``\$forca add \"<palavra>\" \"<chances>\" \"<dica_1>\"(N)``",
			'thumbnail' => [
				'url' => $forca->options['thumb'],
			],
		];
		
		
		if(
			!empty($params[0])
			&& !empty($params[1])
			&& !empty($params[2])
		){
			$palavras_saved = $forca->getPalavras();
			$exists = false;
			foreach($palavras_saved as $palavra_saved){
				if($palavra_saved['palavra'] == $params[0]){
					$exists = true;
					break;
				}
			}
			if($exists){
				$embed['description'] = "Já existe a palavra '{$params[0]}' cadastrado!\nDigite ``\$forca edit \"<palavra>\" \"<chances>\" \"<dica_1>\"(N)`` para editar";
				
			}else{
				$count_params = count($params);
				
				$dicas = [];
				
				for($i = 2;$i<$count_params;$i++){
					$dicas[] = $params[$i];
					
				}
				
				$palavras_to_save = [
					'palavra' => strtolower($params[0]),
					'chances' => $params[1],
					'dicas' => $dicas,
				];
				
				$palavras_saved[] = $palavras_to_save;
				
				$encoded = json_encode($palavras_saved, JSON_PRETTY_PRINT);
				if(file_put_contents('uploads/forca.json', $encoded)){
					$embed['title'] = 'Palavra adicionado com sucesso!';
					$embed['description'] = "Palavra: {$palavras_to_save['palavra']}";
					$embed['description'] .= "\nChances: {$palavras_to_save['chances']}";
					$embed['description'] .= "\nDicas:\n\n";
					
					for($i=0;$i<count($dicas);$i++){
						$numero_dica = $i + 1;
						$embed['description'] .= (($i > 0) ? "\n\n" : "") . "Dica número ".$numero_dica .": ";
						$embed['description'] .= $dicas[$i];
					}
				}else{
					$embed['description'] = "Não foi possível salvar o arquivo!";
				}
				
			}
		}
	}
	
	$message->channel->sendMessage($msg, false, $embed);

	
	$message->delete();
},[
	'description' => 'Adicionar uma nova palavra!',
	'usage' => "\$forca add \"<palavra>\" \"<chances>\" \"<dica_1>\"(N)",

]);
?>