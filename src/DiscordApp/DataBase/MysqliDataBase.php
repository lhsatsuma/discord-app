<?php
namespace DiscordApp\DataBase;

class MysqliDataBase extends DataBase{
	public function __construct($Origem, $params){
		$this->convert = true;
		parent::__construct($Origem, $params);
	}
	function Connect(){
		$this->connection = mysqli_connect($this->host, $this->host_user, $this->host_pwd, $this->database);
		if($this->connection){
			if($this->convert){
				mysqli_set_charset($this->connection,"utf8");
			}
			return true;
		}else{
			$this->last_error = 'Connection Error '.$this->origem.': '.mysqli_connect_error();
			return false;
		}
	}
	function CloseConn(){
		return mysqli_close($this->connection);
	}
	function ErrorQuery($sql){
		$this->last_error = 'SQL Failed: '.$sql.' : '.mysqli_error($this->connection);
		$this->CloseConn();
	}
	function Query($sql, $show_sql = false){
		$this->Connect();
		if($this->connection){
			if($show_sql){
				echo $sql."\n";
			}
			$query = mysqli_query($this->connection, $sql) or $this->ErrorQuery($sql);
			$this->CloseConn();
			return $query;
		}else{
			$this->CloseConn();
			return false;
		}
	}
	function fetchByAssoc($query){
		$result = mysqli_fetch_assoc($query);
		return $result;
	}
	function fetchFields($query){
		$fields = array();
		while($field = mysqli_fetch_field($query)){
			$fields[] = $field->name;
		}
		return $fields;
	}
	function numRows($query){
		return mysqli_num_rows($query);
	}
}
?>