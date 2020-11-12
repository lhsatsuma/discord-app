<?php
$discord->registerCommand('clearlogs', function ($message) {
	global $bot_cfg;
	
	$embed = [
		'author' => [
			'name' => $message->author->username,
			'icon_url' => $message->author->avatar,
		],
		'title' => 'Permissão negada!',
		'description' => 'utilize este comando no canal de <#'.$bot_cfg['admin_channel_id'].'>',
		'color' => DiscordApp\ColorsEmbed::get('RED'),
		'fields' => [],
	];
	
	if($message->channel_id !== $bot_cfg['admin_channel_id']){
		$discord->log->Info($message->author->username.' tentou utilizar o comando $status no canal de: '.$message->channel->name);
	}else{
		
		$discord->logs = scan_dir('logs/');

		$leave = array(
			'cron.log',
		);
		$count = [
			'deletados' => 0,
			'limpos' => 0,
		];
		foreach($discord->logs as $discord->log_file){
			if(!in_array($discord->log_file, $leave)){
				unlink('logs/'.$discord->log_file);
				$count['deletados']++;
			}else{
				$str_log = $discord->log->Info('Cleared Logs!', 0, 1);
				file_put_contents('logs/'.$discord->log_file, $str_log);
				$count['limpos']++;
			}
		}
		$embed['title'] = 'Logs limpos com sucesso!';
		$embed['description'] = "Arquivos Deletados: **{$count['deletados']}**\n";
		$embed['description'] .= "Arquivos Limpos: **{$count['limpos']}**";
		$embed['color'] = DiscordApp\ColorsEmbed::get('BLUE');
	}
	$message->channel->sendMessage('', false, $embed);
	$message->delete();
	
}, [
  'description' => 'Clear sys logs',
  'usage' => "\$clearlogs",
  'cooldown' => 60000,
]);
?>