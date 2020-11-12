<?php
$discord->registerCommand('ajuda', function ($message, $args) {
	global $discord;
	if(!$discord->checkGuildPermission($message)){
		return;
	}
	$message->delete();
	$prefix = str_replace((string) $discord->user, '@'.$discord->username, $discord->commandClientOptions['prefix']);

	if (count($args) > 0) {
		$commandString = implode(' ', $args);
		$command = $discord->getCommand($commandString);

		if (is_null($command)) {
			return "O comando {$commandString} não existe.";
		}

		$ajuda = $command->getHelp($prefix);

		/**
		 * @todo Use internal Embed::class
		 */
		$embed = [
			'author' => [
				'name' => $discord->commandClientOptions['name'],
				'icon_url' => $discord->client->avatar,
			],
			'title' => 'Comandos disponíveis para o DBIKE BOT!',
			'description' => ! empty($ajuda['longDescription']) ? $ajuda['longDescription'] : $ajuda['description'],
			'fields' => [],
			'footer' => [
				'text' => $discord->commandClientOptions['name'],
			],
		];

		if (! empty($ajuda['usage'])) {
			$embed['fields'][] = [
				'name' => 'Usage',
				'value' => '``'.$ajuda['usage'].'``',
				'inline' => true,
			];
		}

		if (! empty($discord->aliases)) {
			$aliasesString = '';
			foreach ($discord->aliases as $alias => $command) {
				if ($command != $commandString) {
					continue;
				}

				$aliasesString .= "{$alias}\r\n";
			}
			$embed['fields'][] = [
				'name' => 'Aliases',
				'value' => $aliasesString,
				'inline' => true,
			];
		}

		if (! empty($ajuda['subCommandsHelp'])) {
			foreach ($ajuda['subCommandsHelp'] as $subCommandHelp) {
				$embed['fields'][] = [
					'name' => $subCommandHelp['command'],
					'value' => $subCommandHelp['description'],
					'inline' => true,
				];
			}
		}

		$message->channel->sendMessage('', false, $embed);
		return;
	}

	/**
	 * @todo Use internal Embed::class
	 */
	$embed = [
		'author' => [
			'name' => $discord->commandClientOptions['name'],
			'icon_url' => $discord->client->avatar,
		],
		'title' => 'Comandos disponíveis para o DBIKE BOT!',
		'description' => $discord->commandClientOptions['description']."\n\nDigite `{$prefix}ajuda` no chat para maiores informações de um comando específico.\n----------------------------",
		'fields' => [],
		'footer' => [
			'text' => $discord->commandClientOptions['name'],
		],
	];

	// Fallback in case commands count reaches the fields limit
	if (count($discord->commands) > 20) {
		foreach ($discord->commands as $command) {
			$ajuda = $command->getHelp($prefix);
			$embed['description'] .= "\n\n`".$ajuda['command']."`\n".$ajuda['description'];
		}
	} else {
		foreach ($discord->commands as $command) {
			$ajuda = $command->getHelp($prefix);
			$embed['fields'][] = [
				'name' => $ajuda['command'],
				'value' => $ajuda['description'],
				'inline' => true,
			];
		}
	}

	$message->channel->sendMessage('', false, $embed);
}, [
	'description' => 'Retorna uma lista de comandos disponíveis.',
	'usage' => '$ajuda',
	'cooldown' => 5000,
]);
?>