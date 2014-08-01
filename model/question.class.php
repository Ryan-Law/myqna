<?php
defined('ACC') || exit('Permission Denied');

class Question extends Model
{
	protected $pk = 'id';
	protected $table = 'question';

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}