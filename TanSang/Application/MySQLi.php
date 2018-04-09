<?php
namespace Application;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

class Driver{

    private $driver;

    protected function connection($host, $user, $pass, $data, $port = 3306){
        if (!isset($this->driver)){
            $this->driver = new \MySQLi($host, $user, $pass);

            if ($this->driver->connect_errno)
			throw new ApplicationException("Cannot connect to database.", 0, 0, __FILE__, __LINE__);

            if (!@$this->driver->select_db($data))
            throw new ApplicationException("Cannot found database.", 0, 0, __FILE__, __LINE__);
        
            $this->driver->set_charset(K_SERV_CHAR);
        }
    }

    protected function query($sql){
        if (!($query = @$this->driver->query($sql)))
			throw new ApplicationException("Query clause invalid.", 0, 0, __FILE__, __LINE__);
		$this->result = $query;
		return $query;
    }

    protected function numRows(\mysqli_result $query = NULL){
    	if (NULL === $query && isset($this->result)) $query = $this->result;
    	return ($query instanceof \mysqli_result) ? $query->num_rows : 0;
    }
    
    protected function newID(){
		return isset($this->driver) ? $this->driver->insert_id : 0;
	}

	protected function affectRows(){
		return isset($this->driver) ? $this->driver->affected_rows : 0;
	}

	public function fetch(\mysqli_result $query = NULL, $array = 0){
		if (NULL === $query && isset($this->result)) $query = $this->result;
		$row = NULL;

		if ($query instanceof \mysqli_result){
			$row = ($array === 1) ? $query->fetch_assoc() : $query->fetch_object();
		}

		return $row;
	}

	protected function free(\mysqli_result $query){
		$query->free();
	}

    protected function insert($table, array $data){
        $field = '`' . join('`,`', array_keys($data)) . '`';
        $value = "'" . join("','", array_values($data)) . "'";
        $sql = "INSERT INTO `{$table}` ({$field}) VALUES ($value)";
        $this->query($sql);
    }

    protected function update($table, array $data, $condition = []){
		if (!empty($data)){
			$sql = "UPDATE `{$table}` SET ";
			foreach($data as $field => $value) $sql .= "`{$field}`='{$value}',";
			$sql = substr($sql, 0, -1);
			$sql .= " " . $this->where($condition);
			return $this->query($sql);
		}
		return false;
	}

	protected function delete($table, $condition){
		$sql = "DELETE FROM `{$table}` " . $this->where($condition);
		return $this->query($sql);
	}

	protected function where($condition, $type = "WHERE"){
		$cond = "";
		if (is_string($condition) && strlen($condition) > 0) $cond = "{$type} " . $condition;
		else if (is_array($condition) && !empty($condition)){
			$condition = $this->encode($condition);
			$cond = "{$type} 1";
			foreach ($condition as $key => $value){
				if (is_numeric($value)) $cond .= " AND `{$key}` = " . $value;
				else if (is_string($value)) $cond .= " AND `{$key}` = '{$value}'";
				else if (is_null($value)) $cond .= " AND `{$key}` IS NULL";
				else if (is_bool($value) && !$value) $cond .= " AND `{$key}` IS NOT NULL";
				else if (is_array($value)){
					$index = (count($value) == 3) ? 1 : 0;
					$begin = isset($value[$index]) ? $value[$index] : null;
					$index++;
					$end = isset($value[$index]) ? $value[$index] : null;
					if (!!$begin && !!$end){
						if ($begin > $end){
							$temp = $end;
							$end = $begin;
							$begin = $temp;
						}
						$not = ($index == 2) ? " NOT " : "";
						$cond .= " AND {$not} BETWEEN '{$begin}' AND '{$end}'";
					}
				}
			}
		}
		return $cond;
	}

	protected function group($option){
		$str = "";
		if (isset($option['group'])){
			$group = $option['group'];
			if (is_string($group) && strlen($group) > 0) $str = "GROUP BY " . $group;
			if (is_array($group) && !empty($group)) $str = "GROUP BY `" . join("`, `", $group) . "`"; 
		}
		return $str;
	}

	protected function limit($option){
		$str = "";
		if (isset($option['start']) || isset($option['limit'])){
			$start = isset($option['start']) ? intval($option['start']) : 0;
			if ($start < 0) $start = 0;
			$str = "LIMIT " . $start;
			if (isset($option['limit']) && intval($option['limit']) > 0) $str .= ", " . intval($option['limit']);
		}
		return $str;
	}

	protected function order($option = array()){
		$str = "";
		if (isset($option['sort']) && is_array($option['sort']) && !empty($option['sort'])){
			$sort = $option['sort'];
			foreach ($sort as $key => $value){
				if (is_string($value)){
					$type = (strtolower($value) == 'desc') ? 'DESC' : 'ASC';
					$str .= " `{$key}` {$type},";
				}
			}
			$str = trim($str, ',');
			if ($str != '') $str = "ORDER BY" . $str;
		}
		return $str;
	}

	function buildSelect($table, array $field, $condition = [], $option = []){
		$condition = $this->where($condition);
		$group = $this->group($option);
		$having = isset($option['having']) ? $this->where($option['having'], "HAVING") : "";
		$order = $this->order($option);
		$limit = $this->limit($option);
		if (is_array($field)){
			if (empty($field)) $field = '*';
			else $field = "`" . implode('`, `', $field) . "`";
		}
		$sql = "SELECT {$field} FROM `{$table}` {$condition} {$group} {$having} {$order} {$limit}";
		return $sql;
	}

	function encode($param){
		if (is_string($param)) $param = addslashes($param);
		if (is_array($param)) foreach($param as $key => $value) $param[$key] = $this->encode($value);
		if (is_object($param)) foreach($param as $key => $value) $param->{$key} = $this->encode($value);
		return $param;
	}

	function decode($param){
		if (is_string($param)) $param = stripslashes($param);
		if (is_array($param)) foreach($param as $key => $value) $param[$key] = $this->decode($value);
		if (is_object($param)) foreach($param as $key => $value) $param->{$key} = $this->decode($value);
		return $param;
	}

    function close(){
		@$this->driver->close();
	}

	function __destruct(){
		if ($this->driver) $this->close();
	}


}
