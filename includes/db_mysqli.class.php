<?php
defined('ACC') || exit('Permission Denied');
/* MySQLi数据库 */
class DB_MySQLi
{
	protected static $instance = null;
	protected static $conn = null;
	protected static $cfg = null;
	public $querytime = null;
	public $querycount = null;

	protected function __construct()
	{
		self::$cfg = CFG::Ins();
		$this->conn(self::$cfg->dbhost, self::$cfg->dbuser, self::$cfg->dbpass);
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
	 * 连接数据库
	 * @param $h string 数据库主机地址
	 * @param $u string 数据库用户名
	 * @param $p string 数据库密码
	 */
	public function conn($h, $u, $p)
	{
		self::$conn = new mysqli($h, $u, $p);
		if(!self::$conn){
			throw new Exception('DB:Connection Failed!');
		}
		self::$conn->select_db(self::$cfg->dbname);
		self::$conn->set_charset(self::$cfg->dbcharset);
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
			self::$conn->query($sql);
			return $this->getAffectedRows();
		}
		$sql = 'insert into ' . $table . ' (' . implode(',', array_keys($data)) . ')';
		$sql .= ' values (\'';
		$sql .= implode("','", array_values($data));
		$sql .= '\')';
		self::$conn->query($sql);
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
		$res = self::$conn->query($sql);
		if(!$res){
			return false;
		}
		while($row = $res->fetch_assoc()){
			$list[] = $row;
		}
		return $list;
	}

	/**
	 * 查询多单行数据
	 * @param $sql SQL语句
	 * @return array / null
	 */
	public function getRow($sql)
	{
		if($res = self::$conn->query($sql)){
			return $res->fetch_array();
		}	
	}

	/**
	 * 查询单行数据
	 * @param $sql SQL语句
	 * @return array / null
	 */
	public function getOne($sql)
	{
		if($res = self::$conn->query($sql)){
			$row = $res->fetch_row();
			return $row[0];
		}
	}

	/**
	 * 查询SQL语句
	 * @param $sql SQL语句
	 * @return mixed
	 */
	public function query($sql)
	{
		$res = self::$conn->query($sql);
		$this->querycount++;
		$this->querytime = microtime(true);
		
		if(!$res){
			echo 'SQL Error: ' . self::$conn->error;
			return false;
		}
		return $res;
	}

	/**
	 * 获取执行结果受影响的行数
	 */
	public function getAffectedRows()
	{
		return self::$conn->affected_rows;
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
		return self::$conn->insert_id;
	}
}