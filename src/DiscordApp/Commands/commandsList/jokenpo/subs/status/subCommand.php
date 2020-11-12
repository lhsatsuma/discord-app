<?php
$GLOBALS['primaryCommandjokenpo']->registerSubCommand('status', function ($message, $params) {
	
	$jokenpo = new DiscordApp\Bean\Jokenpo();
	$jokenpo->user_id = $message->author->id;
	$options = ['pedra', 'papel', 'tesoura'];
	
	$embed = [
		'author' => [],
		'title' => 'Ops! Erro ao buscar a pontuação do jogador!',
		'description' => "Digite o comando corretamente:\n``\$jokenpo status``",
		'color' => DiscordApp\ColorsEmbed::get('RED'),
		'fields' => [],
	];
	
	$choose = $params[0];
	
	$jokenpo->select();
			
	$embed['title'] = 'Pontuação no Jokenpo de '.$message->author->username;
	
	if(empty($jokenpo->id)){
		
		$embed['description'] = "**COMO VOCÊ QUER VER O SEU STATUS SE VOCÊ NUNCA JOGOU?**";
		$embed['color']	= DiscordApp\ColorsEmbed::get('ORANGE');
		
	}else{
		$jokenpo->getStats();
		
		$jokenpo->getStatsPlay();
		
		$embed['description'] = "**STATUS GERAL:**";
		$embed['description'] .= "\nJOGADOS: **{$jokenpo->total_played}**";
		$embed['description'] .= "\nGANHAS: **{$jokenpo->stats['win']['total']} ({$jokenpo->stats['win']['perc']}%)**";
		$embed['description'] .= "\nPERDIDAS: **{$jokenpo->stats['lose']['total']} ({$jokenpo->stats['lose']['perc']}%)**";
		$embed['description'] .= "\nEMPATADOS: **{$jokenpo->stats['draw']['total']} ({$jokenpo->stats['draw']['perc']}%)**";
		$embed['description'] .= "\n\n**STATUS PEDRA:**";
		$embed['description'] .= "\nJOGADOS: **{$jokenpo->played_pedra} ({$jokenpo->stats_play['pedra']['perc']['total']}%)**";
		$embed['description'] .= "\nGANHOS: **{$jokenpo->win_pedra} ({$jokenpo->stats_play['pedra']['perc']['win']}%)**";
		$embed['description'] .= "\nPERDIDOS: **{$jokenpo->lose_pedra} ({$jokenpo->stats_play['pedra']['perc']['lose']}%)**";
		$embed['description'] .= "\nEMPATADOS: **{$jokenpo->draw_pedra} ({$jokenpo->stats_play['pedra']['perc']['draw']}%)**";
		$embed['description'] .= "\n\n**STATUS TESOURA:**";
		$embed['description'] .= "\nJOGADOS: **{$jokenpo->played_tesoura} ({$jokenpo->stats_play['tesoura']['perc']['total']}%)**";
		$embed['description'] .= "\nGANHOS: **{$jokenpo->win_tesoura} ({$jokenpo->stats_play['tesoura']['perc']['win']}%)**";
		$embed['description'] .= "\nPERDIDOS: **{$jokenpo->lose_tesoura} ({$jokenpo->stats_play['tesoura']['perc']['lose']}%)**";
		$embed['description'] .= "\nEMPATADOS: **{$jokenpo->draw_tesoura} ({$jokenpo->stats_play['tesoura']['perc']['draw']}%)**";
		$embed['description'] .= "\n\n**STATUS PAPEL:**";
		$embed['description'] .= "\nJOGADOS: **{$jokenpo->played_papel} ({$jokenpo->stats_play['papel']['perc']['total']}%)**";
		$embed['description'] .= "\nGANHOS: **{$jokenpo->win_papel} ({$jokenpo->stats_play['papel']['perc']['win']}%)**";
		$embed['description'] .= "\nPERDIDOS: **{$jokenpo->lose_papel} ({$jokenpo->stats_play['papel']['perc']['lose']}%)**";
		$embed['description'] .= "\nEMPATADOS: **{$jokenpo->draw_papel} ({$jokenpo->stats_play['papel']['perc']['draw']}%)**";
		$embed['color']	= DiscordApp\ColorsEmbed::get('GREEN');
	}
	
	$message->channel->sendMessage('', false, $embed);
	$message->delete();
	
}, [
	'description' => 'Ver pontuações do jogador!',
	'usage' => '$jokenpo status',
]);
?>