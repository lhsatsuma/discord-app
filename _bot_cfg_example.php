<?php
if(!defined('ENTRYPOINT')) { die('access denied!'); }

if(isset($bot_cfg)){
	unset($bot_cfg);
}

$GLOBALS['bot_cfg'] = [
	'admin_channel_id' => '<ADMIN_CHANNEL_ID>',
	'log_dir' => __DIR__ . '/logs/',
	'discordOptions' => [
		'name' => '<NAME OF BOT>',
		'token' => '<TOKEN>',
		'prefix' => '$',
		'description' => '<DESCRIPTION_BOT>',
		'defaultHelpCommand' => false,
	],
	'db' => [
		'host' => '<HOST>',
		'user' => '<USER>',
		'pass' => '<PASSWORD>',
		'db_name' => '<DATABASE_NAME>',
	],
	'servers_allow' => [
		710607431410909185, //DBIKE Server
	],
];
?>