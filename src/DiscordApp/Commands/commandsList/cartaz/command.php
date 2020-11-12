<?php
/*
PING VERIFICAR SE O BOT TA ATIVO
*/
$discord->registerCommand('cartaz', function ($message, $params) {
	global $discord, $bot_cfg;
	$msg = '';
	$validated = false;
	$embed = [
		'author' => [],
		'title' => 'Permissão negada!',
		'description' => 'utilize este comando no canal de <#'.$bot_cfg['admin_channel_id'].'>',
		'color' => DiscordApp\ColorsEmbed::get('RED'),
		'fields' => [],
	];
	
	if($message->channel_id !== $bot_cfg['admin_channel_id']){
		$discord->log->Warning($message->author->username.' tentou utilizar o comando $reloadcommands no canal de: '.$message->channel->name, 0, 1);
	}else{
		$embed['title'] = 'Comando inválido!';
		$embed['description'] = 'Utilize o comando corretamente: $cartaz "<data>" "<link_youtube>" "<data>"';
		
		if(!empty($params[0])){
			
			$data_yt = [];
			
			$doc = new \DOMDocument();
			libxml_use_internal_errors(true);
			
			$doc_string = file_get_contents($params[1]);
			
			$doc->loadHTML($doc_string);
			
			$nodes = $doc->getElementsByTagName('meta');
			$nodeListLength = $nodes->length; // this value will also change
			for ($i = 0; $i < $nodeListLength; $i ++)
			{
				$node = $nodes->item($i);
				if($node->getAttribute('name') == 'title'){
					$data_yt['title'] = clear_string(utf8_decode($node->getAttribute('content')));
				}
			}
			$nodes = $doc->getElementsByTagName('link');
			$nodeListLength = $nodes->length; // this value will also change
			for ($i = 0; $i < $nodeListLength; $i ++)
			{
				$node = $nodes->item($i);
				if($node->getAttribute('rel') == 'canonical'){
					$data_yt['link'] = clear_string(utf8_decode($node->getAttribute('href')));
				}elseif($node->getAttribute('rel') == 'image_src'){
					$data_yt['image'] = clear_string(utf8_decode($node->getAttribute('href')));
				}
			}
			
			if(
				!empty($data_yt['title'])
				&& !empty($data_yt['link'])
				&& !empty($data_yt['image'])
				&& strpos($data_yt['link'], 'youtube') !== false
			){
				
				$embed['title'] = 'Novo filme em Cartaz'.((!empty($params[2])) ? ' - '.$params[2] : '');
				$embed['image']['url'] = $data_yt['image'];
			
				$embed['description'] = "**TÍTULO: {$params[0]}**";
				$embed['color'] = DiscordApp\ColorsEmbed::get('BLUE');
				$validated = true;
				$channelCartaz = $discord->getChannel(774122287389343774);
				
				$channelCartaz->sendMessage('', false, $embed)->then(function($message){
					$message->react('🍿');
				});
			}
		}
	}
	if(!$validated){
		$message->channel->sendMessage('', false, $embed);
	}else{
		$embed = [
			'author' => [],
			'title' => 'Filme adicionado em cartaz com sucesso!',
			'description' => '',
			'color' => DiscordApp\ColorsEmbed::get('GREEN'),
			'fields' => [],
		];
		$message->channel->sendMessage('', false, $embed);
	}
	$message->delete();
	
}, [
  'description' => 'Coloca filmes em cartaz',
  'usage' => '$cartaz "<nome>" "<link_youtube>" "<data>"',
]);
?>