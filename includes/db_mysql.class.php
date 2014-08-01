<?php
defined('ACC') || exit('Permission Denied');
/* MySQL数据库 */
class DB_MySQL
{
	protected static $instance = null;
	protected static $cfg = null;
	public $conn = null;
	public $querytime = null;
	public $querycount = null;

	protected function __construct()
	{
		self::$cfg = CFG::Ins();
		$this->conn = mysql_connect(self::$cfg->dbhost, self::$cfg->dbuser, self::$cfg->dbpass);
		if(!$this->conn){
			throw new Exception('DB:Connection Failed!');
		}
		mysql_select_db(self::$cfg->dbname, $this->conn);
		mysql_set_charset(self::$cfg->dbcharset, $this->conn);
	}

	public function __clone()
	{
		return self::$instance;
	}

	public static function Ins()
	{
		if(!(self::$instance instanceof self)){
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * 自动执行insert / update 语句
	 * @param $table string 表名
	 * @param $data array 数据
	 * @param $mode string 执行操作 insert / update
	 * @param $where string 条件
	 * @return array / boolean
	 */
	public function autoExecute($table, $data, $mode = 'insert', $where = '')
	{
		if(!is_array($data)){
			return false;
		}
		if($mode == 'update'){
			$sql = 'update ' . $table . ' set ';
			foreach($data as $k => $v){
				$sql .= $k . " = '" . $v . "', ";
			}
			$sql = substr($sql, 0, -2);
			$sql .= ' ' . $where . ' ';
			if(empty($where)){
				return false;
			}
			mysql_query($sql, $this->conn);
			return $this->getAffectedRows();
		}
		$sql = 'insert into ' . $table . ' (' . implode(',', array_keys($data)) . ')';
		$sql .= ' values (\'';
		$sql .= implode("','", array_values($data));
		$sql .= '\')';
		mysql_query($sql, $this->conn);
		return $this->getAffectedRows();
	}

	/**
	 * 查询多行数据
	 * @param $sql SQL语句
	 * @return array / boolean
	 */
	public function getAll($sql)
	{
		$list = array();
		$res = mysql_query($sql, $this->conn);
		if(!$res){
			return false;
		}
		while($row = mysql_fetch_assoc($res)){
			$list[] = $row;
		}
		return $list;
	}

	/**
	 * 查询多单行数据
	 * @param $sql SQL语句
	 * @return array / boolean
	 */
	public function getRow($sql)
	{
		$res = mysql_query($sql, $this->conn);
		if(!$res){
			return false;
		}
		return mysql_fetch_array($res);
	}

	/**
	 * 查询多单个数据
	 * @param $sql SQL语句
	 * @return array / boolean
	 */
	public function getOne($sql)
	{
		$res = mysql_query($sql, $this->conn);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_row($res);
		return $row[0];
	}

	/**
	 * 查询SQL语句
	 * @param $sql SQL语句
	 * @return mixed
	 */
	public function query($sql)
	{
		Log::log_save($sql);
		$res = mysql_query($sql, $this->conn);
		$this->querycount ++;
		$this->querytime = microtime(true);
		if(!$res){
			echo 'SQL Error: ' . mysql_error($this->conn);
			return false;
		}
		return $res;
	}

	/**
	 * 获取执行结果受影响的行数
	 */
	public function getAffectedRows()
	{
		return mysql_affected_rows($this->conn);
	}

	/**
	 * 获取数据表结构
	 */
	public function getAllFields($tablename)
	{
		return $this->getAll('desc ' . $tablename);
	}

	public function getLastInsertID()
	{
		return $this->conn->insert_id;
	}
}