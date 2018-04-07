<?php
namespace Application;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

include_once K_SERV_DRIV . '.php';

class Model extends Driver{

	protected function __construct(){
		parent::connection(K_SERV_HOST, K_SERV_USER, K_SERV_PASS, K_SERV_DATA, K_SERV_PORT);
	}
	
	function free(\mysqli_result $query = NULL){
		if (NULL === $query) $query = $this->result;
		$query->free();
	}
	
	protected function insert($table, array $data){
		return parent::insert($table, $this->encode($data));
	}
	
	protected function update($table, array $data, $condition = []){
		$data = $this->encode($data);
		return parent::update($table, $data, $condition);
	}

	protected function getOne($table, array $field, $condition = [], $options = []){
		$sql = $this->buildSelect($table, $field, $condition, $options);
		
		$result = new \stdClass;
		$this->query($sql);
		
		while ($row = $this->fetch()) {
			$result = $row;
			$this->free();
			break;
		}

		return $result;
	}

	protected function getMany($table, array $field, $condition = [], $options = []){
		$sql = $this->buildSelect($table, $field, $condition, $options);
		return $this->query($sql);
	}

	protected function count($table, $field, $condition = []){
		$condition = $this->encode($condition);
		$condition = parent::where($condition);
		
		$sql = "SELECT COUNT(`{$field}`) AS `numrow` FROM `{$table}` {$condition}";
		
		$this->query($sql);
		return parent::numRows();
	}

	protected function delete($table, $condition){
		return parent::delete($table, $condition);
	}

}