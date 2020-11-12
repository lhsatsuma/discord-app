<?php
$GLOBALS['primaryCommandjokenpo']->registerSubCommand('jogar', function ($message, $params) {
	
	$jokenpo = new DiscordApp\Bean\Jokenpo();
	$jokenpo->user_id = $message->author->id;
	$options = ['pedra', 'papel', 'tesoura'];
	
	$embed = [
		'author' => [],
		'title' => 'Ops! Erro ao jogar o jokenpo!',
		'description' => "Digite o comando corretamente:\n``\$jokenpo jogar <pedra|papel|tesoura>``",
		'color' => DiscordApp\ColorsEmbed::get('RED'),
		'fields' => [],
	];
	
	$choose = $params[0];
	
	if(in_array($choose, $options)){
		
		$rand = array_rand($options);
		$bot_choose = $options[$rand];
		$result = '';
		if(
		($choose == 'papel' && $bot_choose == 'papel')
		|| ($choose == 'tesoura' && $bot_choose == 'tesoura')
		|| ($choose == 'pedra' && $bot_choose == 'pedra')
		){
			$result = 'draw';
		}elseif(
		($choose == 'papel' && $bot_choose == 'pedra')
		|| ($choose == 'tesoura' && $bot_choose == 'papel')
		|| ($choose == 'pedra' && $bot_choose == 'tesoura')
		){
			$result = 'win';
		}elseif(
		($choose == 'papel' && $bot_choose == 'tesoura')
		|| ($choose == 'tesoura' && $bot_choose == 'pedra')
		|| ($choose == 'pedra' && $bot_choose == 'papel')
		){
			$result = 'lose';
		}
			
		$embed['title'] = $message->author->username.' jogou jokenpo!';
		$embed['description'] = "Você jogou: **".strtoupper($choose)."**\n";
		$embed['description'] .= "O BOT jogou: **".strtoupper($bot_choose)."**\n";
		if($result == 'draw'){
			$jokenpo->plusDraw($choose);
			$embed['color'] = DiscordApp\ColorsEmbed::get('ORANGE');
			$embed['description'] .= "\n**DEU EMPATE!**";
		}elseif($result == 'win'){
			$jokenpo->plusWin($choose);
			$embed['color'] = DiscordApp\ColorsEmbed::get('GREEN');
			$embed['description'] .= "\n🎉🎉🎉**VOCÊ GANHOU!**🎉🎉🎉";
		}elseif($result == 'lose'){
			$jokenpo->plusLose($choose);
			$embed['description'] .= "\n**VOCÊ PERDEU! TENTE NOVAMENTE!**";
		}
	}
	
	$message->channel->sendMessage('', false, $embed);
	$message->delete();
	
}, [
	'description' => 'Jogar Pedra, Papel ou Tesoura!',
	'usage' => '$jokenpo jogar <pedra|papel|tesoura>',
]);
?>