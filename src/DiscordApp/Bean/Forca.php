<?php
namespace DiscordApp\Bean;

use DiscordApp\Bean\Base;

class Forca extends Base{
	public $id;
	public $date_entered;
	public $date_modified;
	public $user_id;
	public $last_msg_id = '';
	public $status = '';
	public $palavra = '';
	public $win = 0;
	public $chances_left = 6;
	public $letras = [];
	public $dicas = [];
	public $guessed = [];
	public $palavraInfo = [];
	public $fields = ['id', 'date_entered', 'date_modified', 'user_id', 'last_msg_id', 'status', 'palavra', 'win', 'letras', 'guessed', 'dicas'];
	public $options = [
		'thumb' => 'http://www.cjdinfo.com.br/images/diversao/forca/vazia.png',	
	];
	protected $dbh;
	
	public const white_space = '🔳';
	public function __construct()
	{
		parent::__construct();
		$this->dbh->table = 'forca';
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
		if(!empty($this->palavra)){
			$this->setPalavraInfo($this->palavra);
		}
		$this->countChances();
	}
	
	public function countChances()
	{
		$this->chances_left = $this->palavraInfo['file']['chances'];
		if(!empty($this->letras)){
			foreach($this->letras as $letter){
				if(strpos($this->palavra, $letter) === false){
					$this->chances_left--;
				}
			}
		}
		if($this->chances_left){
			if(!empty($this->guessed)){
				foreach($this->guessed as $guessed){
					if($this->palavra !== $guessed){
						$this->chances_left--;
					}
				}
			}
		}
	}
	
	public function countTrys()
	{
		$count = 0;
		$count += count($this->letras);
		$count += count($this->guessed);
		return $count;
	}
	
	public function checkWinLose()
	{
		if($this->chances_left <= 0){
			$this->win = 0;
			$this->status = 'done';
			return 'lose';
		}
		$count_left = 0;
		for($i=0;$i<$this->palavraInfo['count'];$i++){
			$letra_atual = substr($this->palavraInfo['file']['palavra'], $i, 1);
			if(!in_array($letra_atual, $this->letras)){
				$count_left++;
			}
		}
		if($count_left <= 0){
			$this->win = 1;
			$this->status = 'done';
			return 'win';
		}
		if(!empty($this->guessed)){
			foreach($this->guessed as $guessed){
				if($this->palavra == $guessed){
					$this->win = 1;
					$this->status = 'done';
					return 'win';
				}
			}
		}
		
		return '';
	}
	
	public function setPalavraInfo($palavra)
	{
		$arr = $this->getPalavras();
		foreach($arr as $palavra_key => $info){
			if($info['palavra'] == $palavra){
				$this->palavra = $palavra;
				$this->palavraInfo['file'] = $arr[$palavra_key];
				$this->palavraInfo['count'] = strlen(str_replace([" ","-"], "", $this->palavraInfo['file']['palavra']));
				$this->palavraInfo['count_dicas'] = count($this->palavraInfo['file']['dicas']);
				$this->chances_left = $this->palavraInfo['file']['chances'];
				break;
			}
		}
	}
	
	public function getPalavras()
	{
		return get_json_file('uploads/forca.json');
	}
	
	public function tryLetter($letter)
	{
		$this->letras[] = $letter;
		if(strpos($this->palavra, $letter) !== false){
			return true;
		}else{
			$this->countChances();
			return false;
		}
	}
	
	public function tryGuess($guess)
	{
		$this->guessed[] = $guess;
		if($this->palavra == $guess){
			return true;
		}else{
			$this->countChances();
			return false;
		}
	}
	
	public function mountSpots($force = false)
	{
		$str = "";
		for($i=0;$i<$this->palavraInfo['count'];$i++){
			$letra_atual = substr($this->palavraInfo['file']['palavra'], $i, 1);
			if($letra_atual == ' '){
				$str .= ':blue_square: ';
			}elseif($letra_atual == '-'){
				$str .= ' **-** ';
			}elseif($force || in_array($letra_atual, $this->letras)){
				$str .= ':regional_indicator_'.strtolower($letra_atual).': ';
			}else{
				$str .= self::white_space.' ';
			}
		}
		return $str;
	}
	
	public function giveHint()
	{
		$return = false;
		foreach($this->palavraInfo['file']['dicas'] as $key => $dica){
			if(!in_array($key, $this->dicas)){
				$this->dicas[] = $key;
				$return = true;
				break;
			}
		}
		return $return;
	}
	
	public function mountHints()
	{
		$str = "";
		for($i=0;$i<$this->palavraInfo['count_dicas'];$i++){
			$numero_dica = $i + 1;
			$str .= (($i > 0) ? "\n\n" : "") . "Dica número ".$numero_dica .": ";
			if(array_key_exists($i, $this->dicas)){
				$str .= $this->palavraInfo['file']['dicas'][$i];
			}else{
				$str .= ":grey_question: :grey_question: :grey_question:";
			}
		}
		return $str;
	}
}
?>