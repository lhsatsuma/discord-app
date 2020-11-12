<?php
namespace DiscordApp\Bean;

use DiscordApp\Bean\Base;

class Jokenpo extends Base{
	public $id;
	public $date_entered;
	public $date_modified;
	public $user_id;
	public $played_pedra = 0;
	public $played_tesoura = 0;
	public $played_papel = 0;
	public $win_pedra = 0;
	public $win_tesoura = 0;
	public $win_papel = 0;
	public $draw_pedra = 0;
	public $draw_tesoura = 0;
	public $draw_papel = 0;
	public $lose_pedra = 0;
	public $lose_tesoura = 0;
	public $lose_papel = 0;
	public $total_played = 0;
	public $stats = array(
		'win' => array(
			'total' => 0,
			'perc' => 0,
		),
		'lose' => array(
			'total' => 0,
			'perc' => 0,
		),
		'draw' => array(
			'total' => 0,
			'perc' => 0,
		),
	);
	public $stats_play = array(
		'pedra' => array(
			'perc' => array(
				'total' => 0,
				'win' => 0,
				'lose' => 0,
				'draw' => 0,
			),
		),
		'papel' => array(
			'perc' => array(
				'total' => 0,
				'win' => 0,
				'lose' => 0,
				'draw' =>0,
			),
		),
		'tesoura' => array(
			'perc' => array(
				'total' => 0,
				'win' => 0,
				'lose' => 0,
				'draw' =>0,
			),
		),
	);
	public $dbh;
	public $fields = ['id', 'date_entered', 'date_modified', 'user_id', 'played_pedra', 'played_tesoura', 'played_papel', 'win_pedra', 'win_tesoura', 'win_papel', 'lose_pedra', 'lose_tesoura', 'lose_papel', 'draw_pedra', 'draw_tesoura', 'draw_papel'];
	public function __construct()
	{
		parent::__construct();
		$this->dbh->table = 'jokenpo';
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
			$this->total_played();
		}
	}
	
	public function total_played()
	{
		$this->total_played = 0;
		$options = ['pedra', 'papel', 'tesoura'];
		foreach($options as $option){
			$played_card = 'played_'.$option;
			$this->total_played += $this->$played_card;
		}
	}
	
	public function plusWin($card)
	{
		$this->select();
		$played_card = 'played_'.$card;
		$win_card = 'win_'.$card;
		$this->$played_card++;
		$this->$win_card++;
		$this->save();
	}
	
	public function plusLose($card)
	{
		$this->select();
		$played_card = 'played_'.$card;
		$lose_card = 'lose_'.$card;
		$this->$played_card++;
		$this->$lose_card++;
		$this->save();
	}
	
	public function plusDraw($card)
	{
		$this->select();
		$played_card = 'played_'.$card;
		$draw_card = 'draw_'.$card;
		$this->$played_card++;
		$this->$draw_card++;
		$this->save();
	}
	
	public function resetScore()
	{
		$this->select();
		$this->played_pedra = 0;
		$this->played_papel = 0;
		$this->played_tesoura = 0;
		$this->win_pedra = 0;
		$this->win_papel = 0;
		$this->win_tesoura = 0;
		$this->lose_pedra = 0;
		$this->lose_papel = 0;
		$this->lose_tesoura = 0;
		$this->draw_pedra = 0;
		$this->draw_papel = 0;
		$this->draw_tesoura = 0;
	
		$this->save();
	}
	
	public function getStats()
	{
		$this->total_played();
		foreach($this->stats as $type => $val){
			$this->stats[$type]['total'] = 0;
			$this->stats[$type]['perc'] = 0;
			
			$papel = $type.'_papel';
			$pedra = $type.'_pedra';
			$tesoura = $type.'_tesoura';
			$this->stats[$type]['total'] += $this->$papel;
			$this->stats[$type]['total'] += $this->$pedra;
			$this->stats[$type]['total'] += $this->$tesoura;
			
			if($this->stats[$type]['total'] > '0'){
				$this->stats[$type]['perc'] = (int) $this->stats[$type]['total'] * 100;
				$this->stats[$type]['perc'] = number_format($this->stats[$type]['perc'] / $this->total_played, 2, ',', '.');
			}
		}
	}
	
	public function getStatsPlay()
	{
		$this->total_played();
		
		
		foreach($this->stats_play as $type => $val){
			$this->stats_play[$type]['perc']['total'] = 0;
			$this->stats_play[$type]['perc']['win'] = 0;
			$this->stats_play[$type]['perc']['lose'] = 0;
			$this->stats_play[$type]['perc']['draw'] = 0;
			
			$win = 'win_'.$type;
			$lose = 'lose_'.$type;
			$draw = 'draw_'.$type;
			$played_card = 'played_'.$type;
			
			if($this->$played_card > 0){
				$this->stats_play[$type]['perc']['total'] = (int) $this->$played_card * 100;
				$this->stats_play[$type]['perc']['total'] = number_format($this->stats_play[$type]['perc']['total'] / $this->total_played, 2, ',', '.');
				$this->stats_play[$type]['perc']['win'] = (int) $this->$win * 100;
				$this->stats_play[$type]['perc']['win'] = number_format($this->stats_play[$type]['perc']['win'] / $this->$played_card, 2, ',', '.');
				$this->stats_play[$type]['perc']['lose'] = (int) $this->$lose * 100;
				$this->stats_play[$type]['perc']['lose'] = number_format($this->stats_play[$type]['perc']['lose'] / $this->$played_card, 2, ',', '.');
				$this->stats_play[$type]['perc']['draw'] = (int) $this->$draw * 100;
				$this->stats_play[$type]['perc']['draw'] = number_format($this->stats_play[$type]['perc']['draw'] / $this->$played_card, 2, ',', '.');
			}else{
				$this->stats_play[$type]['perc']['total'] = 0;
			}
		}
	}
	
	public function save()
	{
		if(empty($this->id)){
			$this->insert();
		}else{
			$this->update();
		}
	}
	
	public function insert()
	{
		$this->id = create_guid();
		$insert = array(
			'jokenpo' => array(
				'id' => $this->id,
				'date_entered' => date("Y-m-d H:i:s"),
				'date_modified' => date("Y-m-d H:i:s"),
				'user_id' => $this->user_id,
				'played_pedra' => $this->played_pedra,
				'played_tesoura' => $this->played_tesoura,
				'played_papel' => $this->played_papel,
				'win_pedra' => $this->win_pedra,
				'win_tesoura' => $this->win_tesoura,
				'win_papel' => $this->win_papel,
				'draw_pedra' => $this->draw_pedra,
				'draw_tesoura' => $this->draw_tesoura,
				'draw_papel' => $this->draw_papel,
				'lose_pedra' => $this->lose_pedra,
				'lose_tesoura' => $this->lose_tesoura,
				'lose_papel' => $this->lose_papel,
			),
		);
		$this->dbh->Insert($insert);
	}
	
	public function update()
	{
		$update = array(
			'jokenpo' => array(
				'fields' => array(
					'date_modified' => date("Y-m-d H:i:s"),
					'played_pedra' => $this->played_pedra,
					'played_tesoura' => $this->played_tesoura,
					'played_papel' => $this->played_papel,
					'win_pedra' => $this->win_pedra,
					'win_tesoura' => $this->win_tesoura,
					'win_papel' => $this->win_papel,
					'draw_pedra' => $this->draw_pedra,
					'draw_tesoura' => $this->draw_tesoura,
					'draw_papel' => $this->draw_papel,
					'lose_pedra' => $this->lose_pedra,
					'lose_tesoura' => $this->lose_tesoura,
					'lose_papel' => $this->lose_papel,
				),
				'where' => "id = '{$this->id}'",
			),
		);
		$this->dbh->Update($update);
	}
}
?>