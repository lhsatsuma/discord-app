<?php
namespace DiscordApp\Bean;

use DiscordApp\Bean\Base;

class Velha extends Base{
	public $id;
	public $date_entered;
	public $date_modified;
	public $user_id;
	public $played_1 = 0;
	public $played_2 = 0;
	public $played_3 = 0;
	public $played_4 = 0;
	public $played_5 = 0;
	public $played_6 = 0;
	public $played_7 = 0;
	public $played_8 = 0;
	public $played_9 = 0;
	public $last_msg_id = '';
	public $status = '';
	public $win = 0;
	public $dbh;
	public $level = '';
	public $options = [1,2,3,4,5,6,7,8,9];
	public $levels = array(
		'facil',
		'medio',
		'dificil',
	);
	public $wins_spots = array(
		//Linhas
		array(1, 2, 3),
		array(4, 5, 6),
		array(7, 8, 9),
		//Retas
		array(1, 4, 7),
		array(2, 5, 8),
		array(3, 6, 9),
		//Diagonais
		array(1, 5, 9),
		array(3, 5, 7),
	);
	public $almost_wins_spots = array(
		//Linhas
		array(1, 2, 999 => 3),
		array(4, 5, 999 => 6),
		array(7, 8, 999 => 9),
		array(1, 999 => 2, 3),
		array(4, 999 => 5, 6),
		array(7, 999 => 8, 9),
		array(999 => 1, 2, 3),
		array(999 => 4, 5, 6),
		array(999 => 7, 8, 9),
		
		//Retas
		array(1, 4, 999 => 7),
		array(2, 5, 999 => 8),
		array(3, 6, 999 => 9),
		array(1, 999 => 4, 7),
		array(2, 999 => 5, 8),
		array(3, 999 => 6, 9),
		array(999 => 1, 4, 7),
		array(999 => 2, 5, 8),
		array(999 => 3, 6, 9),
		
		
		//Diagonais
		array(1, 5, 999 => 9),
		array(3, 5, 999 => 7),
		array(1, 999 => 5, 9),
		array(3, 999 => 5, 7),
		array(999 => 1, 5, 9),
		array(999 => 3, 5, 7),
	);
	
	public const circle = '🟤';
	public const white = '❓';
	public const ex = '❎';
	public $fields = ['id', 'date_entered', 'date_modified', 'user_id', 'last_msg_id', 'status', 'win', 'level', 'played_1', 'played_2', 'played_3', 'played_4', 'played_5', 'played_6', 'played_7', 'played_8', 'played_9'];
	public function __construct()
	{
		parent::__construct();
		$this->dbh->table = 'jdv';
	}
	
	public function markBot($spot)
	{
		$var_spot = 'played_'.$spot;
		
		if(!empty($this->$var_spot)){
			return false;
		}
		
		$this->$var_spot = 1;
		
		return true;
	}
	public function getAvailables()
	{
		$options_available = array();
		foreach($this->options as $option){
			$var_spot = 'played_'.$option;
			
			if(empty($this->$var_spot)){
				$options_available[$option] = $option;
			}
		}
		return $options_available;
	}
	public function randBot()
	{
		$options_available = $this->getAvailables();
		
		if($this->level == 'facil'){
			//random, vc só perde se for azarado
			$key_rand = array_rand($options_available);
		}elseif($this->level == 'medio'){
			//Checar se o cara está prestes a ganhar
			$key_rand = $this->checkAlmostWinSpots('2');
		}elseif($this->level == 'dificil'){
			//Checar se o BOT pode ganhar nesta rodada
			$key_rand_check = $this->checkAlmostWinSpots('1');
			if($key_rand_check !== false){
				$key_rand = $key_rand_check;
			}else{
				//Checar se o cara está prestes a ganhar
				$key_rand = $this->checkAlmostWinSpots('2');
			}
		}
		
		if(!isset($options_available[$key_rand])){
			//Se der algum erro acima, pega um spot random
			$key_rand = array_rand($options_available);
			
		}
		return $options_available[$key_rand];
	}
	
	private function checkWinSpots($bot_player)
	{
		$winned = false;
		foreach($this->wins_spots as $spots){
			$count = 0;
			$count_to_win = count($spots);
			foreach($spots as $spot){
				$spot_var = 'played_'.$spot;
				if($this->$spot_var == $bot_player){
					$count++;
				}
			}
			if($count == $count_to_win){
				$winned = true;
				break;
			}
		}
		return $winned;
	}
	
	private function checkAlmostWinSpots($bot_player)
	{
		global $discord;
		$winned = false;
		foreach($this->almost_wins_spots as $almost_key => $spots){
			$count = 0;
			$count_to_win = count($spots);
			foreach($spots as $spot_key => $spot){
				if($spot_key == 999){
					$spot_var = 'played_'.$spot;
					if($this->$spot_var){
						if($this->$spot_var !== $bot_player){
						}else{
							$count++;
						}
					}else{
						$count++;
					}
				}else{
					$spot_var = 'played_'.$spot;
					if($this->$spot_var){
						if($this->$spot_var == $bot_player){
							$count++;
						}
					}
				}
			}
			if($count == $count_to_win){
				$winned = $spots[999];
				break;
			}
		}
		$discord->log->Debug('Checked Almost Win for: '.$bot_player. 'DONE! Winned: '.$winned);
		return $winned;
	}
	
	private function checkDrawSpots()
	{
		if(
			$this->played_1
			&& $this->played_2
			&& $this->played_3
			&& $this->played_4
			&& $this->played_5
			&& $this->played_6
			&& $this->played_7
			&& $this->played_8
			&& $this->played_9
		){
			return true;
		}else{
			return false;
		}
		
	}
	
	public function checkWin()
	{
		
		if($this->checkWinSpots('1')){
			return 'bot';
		}elseif($this->checkWinSpots('2')){
			return 'player';
		}elseif($this->checkDrawSpots()){
			return 'draw';
		}
		
		//No one wins yet
		return false;
	}
	
	public function markPlayer($spot)
	{
		$var_spot = 'played_'.$spot;
		
		if(!empty($this->$var_spot)){
			return false;
		}
		
		$this->$var_spot = 2;
		
		return true;
	}
	
	public function mountSpots()
	{
		
		foreach($this->options as $spot){
			$played = 'played_'.$spot;
			$played = $this->$played;
			if($played == '1'){
				$spots[$spot] = self::circle;
			}elseif($played == '2'){
				$spots[$spot] = self::ex;
			}else{
				$spots[$spot] = self::white;
			}
		}
		
		$str = "";
		$str .= $spots[1].'  |  '.$spots[2].'  |  '.$spots[3];
		$str .= "\n\n".$spots[4].'  |  '.$spots[5].'  |  '.$spots[6];
		$str .= "\n\n".$spots[7].'  |  '.$spots[8].'  |  '.$spots[9];
		
		return $str;
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
			'jdv' => array(
				'id' => $this->id,
				'date_entered' => date("Y-m-d H:i:s"),
				'date_modified' => date("Y-m-d H:i:s"),
				'user_id' => $this->user_id,
				'last_msg_id' => $this->last_msg_id,
				'status' => $this->status,
				'win' => $this->win,
				'level' => $this->level,
				'played_1' => $this->played_1,
				'played_2' => $this->played_2,
				'played_3' => $this->played_3,
				'played_4' => $this->played_4,
				'played_5' => $this->played_5,
				'played_6' => $this->played_6,
				'played_7' => $this->played_7,
				'played_8' => $this->played_8,
				'played_9' => $this->played_9,
			),
		);
		$this->dbh->Insert($insert);
	}
	
	public function update()
	{
		$update = array(
			'jdv' => array(
				'fields' => array(
					'date_modified' => date("Y-m-d H:i:s"),
					'last_msg_id' => $this->last_msg_id,
					'status' => $this->status,
					'win' => $this->win,
					'level' => $this->level,
					'played_1' => $this->played_1,
					'played_2' => $this->played_2,
					'played_3' => $this->played_3,
					'played_4' => $this->played_4,
					'played_5' => $this->played_5,
					'played_6' => $this->played_6,
					'played_7' => $this->played_7,
					'played_8' => $this->played_8,
					'played_9' => $this->played_9,
				),
				'where' => "id = '{$this->id}'",
			),
		);
		$this->dbh->Update($update);
	}
}
?>