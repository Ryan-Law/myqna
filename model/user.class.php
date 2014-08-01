<?php
defined('ACC') || exit('Permission Denied');

class User extends Model
{
	protected $pk = 'uid';
	protected $table = 'user';
	

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
	
	
	/**
	 * 验证用户名和密码
	 * @param $username string 用户名
	 * @param $password string 用户密码
	 * @return boolean 验证成功 true / 验证失败 false
	 * */
	public function validateUser($username,$password){
		$res = $this->findOneRow('select uid,username,lastlogin,permission from '.$this->table().' where username = \''
				.$username.'\' and password = \''.md5($password).'\'');
		switch ($res['permission']){
			case '7':
				$res['pmname'] = '管理员'; break;
			case '3':
				$res['pmname'] = '高级用户'; break;
			case '1':
				$res['pmname'] = '普通用户'; break;
		}
		return $res;
	}
	
	public function checkUserExist($username){
		return (bool)$this->findOne('select count(*) from '.$this->table().' where username = \''.$username.'\'');
	}
	
	public function createUser($data){
		return $this->add($data);
	}
	
	public function updateLastLogin($uid){
		return $this->update(array('lastlogin'=>time()), $uid);
	}
}