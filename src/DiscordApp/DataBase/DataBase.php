<?php
namespace DiscordApp\DataBase;
/*
DB Class v1.1
31/07/2018
Por: Luis Henrique
Autor: CBR Consultoria
*/
class DataBase{
	public $origem;
	public $connection;
	public $last_error;
	public $host;
	public $host_user;
	public $host_pwd;
	public $database;
	public $convert = false;
	function __construct($origem = NULL, $params = array()){
		global $sugar_config;
		if(is_null($origem)){
			echo 'Origem nao identificado!';exit;
		}
		$this->host = $params['host'];
		$this->host_user = $params['user'];
		$this->host_pwd = $params['pass'];
		$this->database = $params['db_name'];
		$this->origem = $origem;
	}
	function TestConnection(){
		$this->Connect();
		if($this->connection){
			$this->CloseConn();
			return true;
		}else{
			$this->CloseConn();
			return false;
		}
	}
	function GetInfoCon(){
		return array(
			'host' => $this->host,
			'host_user' => $this->host_user,
			'host_pwd' => $this->host_pwd,
			'database' => $this->database,
		);
	}
}
?>