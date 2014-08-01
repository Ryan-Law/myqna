<?php
defined ( 'ACC' ) || exit ( 'Permission Denied' );
/* 配置类 */
class CFG {
	protected static $instance = NULL;
	protected $date = array ();
	final protected function __construct() {
		$this->date = require ROOTPATH . 'includes/cfg_setting.php';
	}
	final protected function __clone() {
		return self::$instance;
	}
	public static function Ins() {
		if (!(self::$instance instanceof self)) {
			self::$instance = new self;
		}	
		return self::$instance;
	}
	
	public function __get($key) {
		if (array_key_exists ( $key, $this->date )) {
			return $this->date [$key];
		} else {
			return NULL;
		}
	}
	public function __set($key, $value) {
		$this->date [$key] = $value;
	}
}

?>