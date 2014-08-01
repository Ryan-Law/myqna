<?php
defined('ACC') || exit('Permission Denied');

class Role extends Model
{
	protected $pk = 'roleid';
	protected $table = 'role';

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function getRoleList()
	{
		return $this->findAll('select rname,rvalue from ' . $this->table());
	}
}