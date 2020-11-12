<?php
namespace DiscordApp;

use Discord\DiscordCommandClient;
use Symfony\Component\OptionsResolver\OptionsResolver;

use DiscordApp\DataBase\DataBase;
use DiscordApp\DataBase\MysqliDataBase;
use DiscordApp\Commands\CommandApp;
use DiscordApp\Process\ProcessRunning;

class InitApp extends DiscordCommandClient
{
	public $log;
	
	public function __construct()
	{
		global $bot_cfg;
		parent::__construct($bot_cfg['discordOptions']);
		
		$this->log = new LogControl($bot_cfg['log_dir'], 'bot.log', array(1,2,3,4), array('level' => 1, 'die' => 0, 'echo' => 1));
		$process = new ProcessRunning();

		if(count($process->check_running()) > 1){
			$this->log->Fatal("Aborting execute: already running!", 1, 1);
		}
		$GLOBALS['db'] = new MysqliDataBase('app', $bot_cfg['db']);
		if(!$GLOBALS['db']->TestConnection()){
			$log->Fatal($GLOBALS['db']->last_error, 1, 1);
		}
	
	}
	
	public function checkGuildPermission($message)
	{
		global $bot_cfg;
		if(
			empty($bot_cfg['servers_allow'])
			|| in_array($message->channel->guild_id, $bot_cfg['servers_allow'])
		){
			return true;
		}
		$message->channel->sendMessage("**Meus comandos só podem ser utilizados no servidor DBIKE Server**", false);
		return false;
	}
	
    public function buildCommand(string $command, $callable, array $options = []): array
    {
        if (is_string($callable)) {
            $callable = function ($message) use ($callable) {
                return $callable;
            };
        } elseif (is_array($callable) && ! is_callable($callable)) {
            $callable = function ($message) use ($callable) {
                return $callable[array_rand($callable)];
            };
        }

        if (! is_callable($callable)) {
            throw new \Exception('The callable parameter must be a string, array or callable.');
        }

        $options = $this->resolveCommandOptions($options);

        $commandInstance = new CommandApp(
            $this, $command, $callable,
            $options['description'], $options['longDescription'], $options['usage'], $options['cooldown'], $options['cooldownMessage']);

        return [$commandInstance, $options];
    }
	
	protected function resolveCommandOptions(array $options): array
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setDefined([
                'description',
                'longDescription',
                'usage',
                'aliases',
                'cooldown',
                'cooldownMessage',
            ])
            ->setDefaults([
                'description' => 'Nenhuma descrição disponível.',
                'longDescription' => '',
                'usage' => '',
                'aliases' => [],
                'cooldown' => 0,
                'cooldownMessage' => 'aguarde %d segundo(s) para usar este comando novamente.',
            ]);

        $options = $resolver->resolve($options);

        if (! empty($options['usage'])) {
            $options['usage'] .= ' ';
        }

        return $options;
    }
	public function DisabledCommand($message)
	{
		$embed = [
			'author' => [],
			'title' => 'Comando desativado!',
			'description' => "Infelizmente esse comando não funciona mais! :cry: :cry: :cry:\nEstamos trabalhando para fazer esse comando voltar com força total.",
			'color' => ColorsEmbed::get('RED'),
			'fields' => [],
		];
		$message->channel->sendMessage('', false, $embed);
		return;
	}
}