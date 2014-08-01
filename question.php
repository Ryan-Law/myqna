<?php
define ( 'ACC', TRUE );
require 'includes/init.php';

$id = isset ( $_GET ['id'] ) ? intval ( $_GET ['id'] ) : 1;

// 分类导航栏
$smarty->assign('category',Category::model()->findAll());
// 获取文章
$smarty->assign('qlist',Question::model()->findOneRowByPK($id));
$smarty->display('question.html',$id);
?>
