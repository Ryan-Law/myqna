<?php
define ( 'ACC', TRUE );
require './includes/init.php';

$cid = isset ( $_GET ['cid'] ) + 0 ? intval ( $_GET ['cid'] ) : null;
$page = isset ( $_GET ['page'] ) + 0 ? intval ( $_GET ['page'] ) : 1;
$pagesize  = isset ( $_GET ['pagesize'] ) + 0 ? intval ( $_GET ['pagesize'] ) : 25;

// 分类导航栏
$smarty->assign('category',Category::model()->findAll());
// 分类文章数目
$totals = Category::model()->getCountByCategory($cid);
$smarty->assign ( 'totals', $totals );
// 分类文章
$smarty->assign ( 'artlist', Category::model()->getArticleByCategory($cid,$page,$pagesize) );
// 分类文章分页
$smarty->assign('pagelist',Paging::page($totals, $page, $pagesize,'index.php',array('cid'=>$cid)));
$smarty->display ( 'index.html',$cid,$page );

?>