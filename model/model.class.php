<?php
defined('ACC') || exit('Permission Denied');
class Model {
	protected $pk = 'id';
	protected $table = '';
	protected $db = null;
	protected $cols = array();
	protected $fillrule = array();
	protected $prefix = '';
	public $cache_field = true;
	private static $_models = null;

	public function __construct() {
		$this->prefix = CFG::Ins()->dbprefix;
		if(function_exists('mysqli_connect')){
			$this->db = DB_MySQLi::Ins();
		}else{
			$this->db = DB_MySQL::Ins();
		}
		if($this->cache_field){
			$this->cols = $this->cacheField();
		}else{
			$this->cols = $this->getTableCols();
		}
	}

	public function table() {
		return $this->prefix . $this->table;
	}

	public static function model($className = __CLASS__) {
		if(isset(self::$_models[$className])){
			return self::$_models[$className];
		}else{
			$model = self::$_models[$className] = new $className(null);
			return $model;
		}
	}

	public function getTableCols() {
		$arr = $this->db->getAllFields($this->table());
		foreach($arr as $v){
			$this->cols[] = $v['Field'];
		}
		return $this->cols;
	}

	public function fillter(array $data) {
		$newArr = array();
		foreach($data as $k => $v){
			if(in_array($k, $this->cols)){ // 判断$k是否是表的字段
				$newArr[$k] = $v;
			}
		}
		return $this->autofill($newArr);
	}

	public function autofill(array $data) {
		foreach($this->fillrule as $k => $v){
			if(!array_key_exists($v[0], $data)){
				switch($v[1]){
					case 'value':
						$data[$v[0]] = $v[2];
						break;
					case 'function':
						$data[$v[0]] = call_user_func($v[2]);
						break;
				}
			}
		}
		return $data;
	}

	public function findAll($sql = '') {
		if(empty($sql)){
			$sql = 'select * from ' . $this->table();
		}
		return $this->db->getAll($sql);
	}

	public function findOne($sql) {
		return $this->db->getOne($sql);
	}

	public function findOneRow($sql) {
		return $this->db->getRow($sql);
	}

	public function findOneRowByPK($pk) {
		return $this->db->getRow('select * from ' . $this->table() . ' where ' . $this->pk . ' = ' . $pk);
	}

	public function delOneRowByPK($id) {
		$this->db->query('delete from ' . $this->table() . ' where ' . $this->pk . '=' . $id);
		return $this->db->getAffectedRows();
	}

	public function add(array $data) {
		return $this->db->autoExecute($this->table(), $data);
	}

	public function update(array $data, $id) {
		return $this->db->autoExecute($this->table(), $data, 'update', 'where ' . $this->pk . '=' . $id);
	}

	public function lastInsertID() {
		return $this->db->getLastInsertID();
	}

	private function cacheField() {
		$filename = ROOTPATH . 'temp/data/' . $this->table() . '.cache';
		if(!is_dir(dirname($filename))) mkdir(dirname($filename), 0777, true);
		if(file_exists($filename) && time() > (filemtime($filename) + 3600)) unlink($filename);
		if(!file_exists($filename)){
			$cache_cols = $this->getTableCols();
			file_put_contents($filename, serialize($cache_cols));
		}
		return unserialize(file_get_contents($filename));
	}
}