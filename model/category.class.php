<?php
defined('ACC') || exit('Permission Denied');

class Category extends Model
{
	protected $pk = 'cat_id';
	protected $table = 'category';

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function getCountByCategory($cat_id = null)
	{
		$sql = 'select count(*) from ' . $this->prefix . 'question where 1';
		$sql .= isset($cat_id) ? ' and cat_id = ' . $cat_id : '';
		return $this->findOne($sql);
	}

	public function getArticleByCategory($cat_id = null, $page, $pagesize)
	{
		$sql = 'select id,q.cat_id,question,pubtime,cat_name from ' . $this->prefix . 'question q,' . $this->prefix .
				 'category c';
		$sql .= ' where q.cat_id=c.cat_id and 1';
		$sql .= isset($cat_id) ? ' and q.cat_id=' . $cat_id : '';
		$sql .= ' order by id desc';
		$sql .= ' limit ' . $pagesize * ($page - 1) . ',' . $pagesize;
		return $this->findAll($sql);
	}
}
?>