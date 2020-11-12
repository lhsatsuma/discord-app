<?php
namespace DiscordApp\DataBase;

class Helper{
	public $table;
	public $fields = '*';
	public $inner_join = array();
	public $where;
	public $order_by;
	public $limit;
	public $group_by;
	public $insert_tables;
	public $db;

	public function __construct($db_class){
		$this->db = $db_class;
	}

	function resetParams(){
		$this->table = '';
		$this->fields = '*';
		$this->inner_join = array();
		$this->where = '';
		$this->order_by = '';
		$this->group_by = '';
		$this->insert_tables = '';
	}

	function Select($show_sql = false){
		$SQL = "SELECT {$this->fields} FROM {$this->table}";
		foreach ($this->inner_join as $key => $args) {
			$SQL .= "\n" . $args['type'] . " " . $args['table'] . " ON " . $args['on'];
		}

		if ($this->where) {
			$SQL .= "\n WHERE " . $this->where;
		}

		if ($this->order_by) {
			$SQL .= "\n ORDER BY " . $this->order_by;
		}

		if ($this->group_by) {
			$SQL .= "\n GROUP BY " . $this->group_by;
		}

		if ($this->limit) {
			$SQL .= "\n LIMIT " . $this->limit;
		}

		$query = $this->db->Query($SQL, $show_sql);
		$retorno = array();
		if (!$query) {
			$GLOBALS['discord']->log->Fatal('Select Query Error: '.$this->db->last_error, 0, 1);
		}

		if ($this->db->numRows($query) > 0) {
			while ($result = $this->db->fetchByAssoc($query)) {
				$retorno[] = $result;
			}

			return $retorno;
		} else {
			return false;
		}
	}

	function Insert($insert_tables = array(), $show_sql = false)
	{
		foreach ($insert_tables as $table => $fields) {
			$SQL = "INSERT INTO {$table} (";
			$columns = '';
			$values = '';
			$c_fields = '';
			foreach ($fields as $field => $val) {
				$val = $this->QuoteSQL($val);
				$columns .= $c_fields."\n".$field;
				$values .= $c_fields."'".$val."'\n";
				$c_fields = ',';
			}
			$SQL .= $columns;
			$SQL .= ") VALUES (";
			$SQL .= $values;
			$SQL .= ")";
			$query = $this->db->Query($SQL, $show_sql);
			if (!$query) {
				$GLOBALS['discord']->log->Fatal('Insert Query Error: '.$this->db->last_error, 0, 1);
			}
		}
	}
	function Update($update_tables = array(), $show_sql = false){
		foreach ($update_tables as $table => $args) {
			$SQL = "UPDATE {$table} SET";
			$columns = '';
			$values = '';
			$c_fields = '';
			foreach ($args['fields'] as $field => $val) {
				$val = $this->QuoteSQL($val);
				$columns .= $c_fields."\n".$field." = '".$val."'";
				$c_fields = ',';
			}

			$SQL .= $columns;
			if ($args['where']) {
				$SQL .= "\nWHERE ".$args['where'];
			}

			$query = $this->db->Query($SQL, $show_sql);
			if (!$query) {
				$GLOBALS['discord']->log->Fatal('Update Query Error: '.$this->db->last_error, 0, 1);
			}
		}
	}

	function Delete($delete_tables = array(), $show_sql = false){
		foreach ($delete_tables as $table => $where) {
			$SQL = "DELETE FROM {$table}";
			if ($where) {
				$SQL .= "\nWHERE ".$where;
			}

			$query = $this->db->Query($SQL, $show_sql);
			if (!$query) {
				$GLOBALS['discord']->log->Fatal('Update Query Error: '.$this->db->last_error, 0, 1);
			}
		}
	}

	function InsertEmail($module = NULL, $bean_id = NULL, $email_imp = NULL, $show_sql = false){
		if (empty($module) || empty($bean_id) || empty($email_imp)) {
			return false;
		}

		$GLOBALS['discord']->log->Debug("Inserting Emails on {$module} {$bean_id}");
		$email_expl = explode(';', $email_imp);
		$count = 0;
		foreach ($email_expl as $email) {
			if ($count > 0) {
				$primary = 0;
			} else {
				$primary = 1;
			}

			$email = str_replace("'", "", $email);
			$email 		= strtolower($email);
			$email_caps = $this->ConverteMaiusculo($email);
			$id_email = create_guid();
			$dataAtual = gmdate("Y-m-d H:i:s");
			$insert_email = "INSERT INTO email_addresses (
				id,
				email_address,
				email_address_caps,
				invalid_email,
				opt_out,
				date_created,
				date_modified,
				deleted
			) VALUES (
				'{$id_email}',
				'{$email}',
				'{$email_caps}',
				0,
				0,
				'{$dataAtual}',
				'{$dataAtual}',
				'0'
			)";
			$query = $this->db->Query($insert_email, $show_sql);
			if (!$query) {
				$GLOBALS['discord']->log->Fatal('Insert Email Addresses Query Error: '.$this->db->last_error, 0, 1);
			}

			$id_rel_email = create_guid();
			$insert_rel_email = "INSERT INTO email_addr_bean_rel (
				id,
				email_address_id,
				bean_id,
				bean_module,
				primary_address,
				reply_to_address,
				date_created,
				date_modified,
				deleted
			) VALUES (
				'{$id_rel_email}',
				'{$id_email}',
				'{$bean_id}',
				'{$module}',
				'{$primary}',
				1,
				'{$dataAtual}',
				'{$dataAtual}',
				'0'
			)";

			$query = $this->db->Query($insert_rel_email, $show_sql);
			if (!$query) {
				$GLOBALS['discord']->log->Fatal('Insert Email Addresses Rel Query Error: '.$this->db->last_error, 0, 1);
			}

			$count++;
		}

		return true;
	}

	function DeleteAndInsertEmail($module = NULL, $bean_id = NULL, $email_imp = NULL, $show_sql = false){
		if (empty($module) || empty($bean_id) || empty($email_imp)) {
			return false;
		}

		$this->DeleteEmail($module, $bean_id, $show_sql);
		$this->InsertEmail($module, $bean_id, $email_imp, $show_sql);
		return true;
	}

	function DeleteEmail($module = NULL, $bean_id = NULL, $show_sql = false){
		if (empty($module) || empty($bean_id)) {
			return false;
		}

		$GLOBALS['discord']->log->Debug("Deleting Emails on {$module} {$bean_id}");
		$where = "bean_module = '{$module}' AND bean_id = '{$bean_id}'";
		$SQL = "DELETE FROM email_addresses WHERE id IN (SELECT email_address_id FROM email_addr_bean_rel WHERE {$where})";
		$query = $this->db->Query($SQL, $show_sql);
		if (!$query) {
			$GLOBALS['discord']->log->Fatal($this->db->last_error, 1, 0);
		}

		$SQL = "DELETE FROM email_addr_bean_rel WHERE {$where}";
		$query = $this->db->Query($SQL, $show_sql);
		if (!$query) {
			$GLOBALS['discord']->log->Fatal($this->db->last_error, 1, 0);
		}

		return true;
	}

	function ConverteMaiusculo($stri){
		$array1 = array("á", "à", "ä", "â", "ã", "é", "è", "ë", "ê", "í", "ì", "ï", "î", "ó", "ò", "ö", "ô", "õ", "ú", "ù", "ü", "û", "ç", "ñ");
		$array2 = array("Á", "À", "Ä", "Â", "Ã", "É", "È", "Ë", "Ê", "Í", "Ì", "Ï", "Î", "Ó", "Ó", "Ö", "Ô", "Õ", "Ú", "Ù", "Ü", "Û", "Ç", "Ñ");
		return strtoupper(str_replace($array1, $array2, $stri));
	}

	function QuoteSQL($stri){
		return str_replace("'", "''", $stri);
	}

	function SearchAssigned($id_protheus){
		if (empty($id_protheus)) {
			return '1';
		}

		$SQL = "SELECT * FROM users LEFT JOIN users_cstm ON users.id = users_cstm.id_c WHERE users.deleted = 0 AND users.status = 'Active' AND users_cstm.id_protheus_c = '{$id_protheus}'";
		$query_users = $this->db->Query($SQL);
		if (!$query_users) {
			$GLOBALS['discord']->log->Fatal($this->db->last_error, 1, 0);
		}

		$return_id_protheus = '1';
		while ($result = $this->db->fetchByAssoc($query_users)) {
			$return_id_protheus = $result['id'];
		}

		return $return_id_protheus;
	}
}
?>