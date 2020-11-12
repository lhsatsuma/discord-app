<?php
namespace DiscordApp\Bean;

use DiscordApp\DataBase\Helper;

class Base
{
	public $fields = [];
	public function __construct()
	{
		$this->dbh = new Helper($GLOBALS['db']);
	}
	
	public function mountFieldsObj($vals)
	{
		foreach($vals as $field => $val){
			if(gettype($this->$field) == 'array'){
				$this->$field = unserialize($val);
			}else{
				$this->$field = $val;
			}
		}
	}
	
	public function select($where = null)
	{
		if(!$where){
			$this->dbh->where = "user_id = '{$this->user_id}'";
		}else{
			$this->dbh->where = $where;
		}
		$selected = $this->dbh->Select();
		if(!empty($selected[0]['id'])){
			$this->mountFieldsObj($selected[0]);
		}
	}
	
	public function selectActive()
	{
		$this->select("user_id = '{$this->user_id}' AND status <> 'done'");
	}
	
	public function save()
	{
		$data_override = [];
		
		foreach($this->fields as $field){
			if(gettype($this->$field) == 'array'){
				$data_override[$field] = serialize($this->$field);
			}
		}
		if(empty($this->id)){
			$this->insert($data_override);
		}else{
			$this->update($data_override);
		}
	}
	
	public function insert($data_override)
	{
		$insert = [];
		
		$this->id = create_guid();
		$this->date_entered = date("Y-m-d H:i:s");
		$this->date_modified = date("Y-m-d H:i:s");
		$data = $this->getArrFields($data_override);
		
		$insert[$this->dbh->table] = $data;
		
		$this->dbh->Insert($insert);
	}
	
	public function update($data_override)
	{
		$this->date_modified = date("Y-m-d H:i:s");
		
		$data = $this->getArrFields($data_override);
		
		$update = [];
		$update[$this->dbh->table] = [
			'fields' => $data,
			'where' => "id = '{$this->id}'",
		];
		$this->dbh->Update($update);
	}
	
	public function getArrFields($data_override)
	{		
		$data = array();
		
		foreach($this->fields as $field){
			if($data_override[$field]){
				$data[$field] = $data_override[$field];
			}else{
				$data[$field] = $this->$field;
			}
		}
		return $data;
	}
}
?>