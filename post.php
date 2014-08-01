<?php
define ( 'ACC', TRUE );
require './includes/init.php';

$action = isset($_GET['act']) ? trim($_GET['act']) : 'list';

// 分类导航栏
$smarty->assign('category',Category::model()->findAll());

// 跳转添加新问题页面
if($action == 'list'){
	$smarty->display('post.html');
}
// 添加新问题ajax验证
else if($action == 'add'){
	$params = Question::model()->fillter($_POST);
	if(empty($params['question']) || empty($params['cat_id'])){ echo 0; return; } //问题不能为空	
	$data = array(
				'cat_id'=>$params['cat_id'],
				'question'=>$params['question'],
				'point'=>$params['point'],
				'answer'=>$params['answer'],
				'pubtime'=>time ()
			);
	if (!Question::model()->add ( $data )) {
	    echo '-2'; return; //更新失败
	} else {
		$newid = Question::model()->lastInsertID ();
		$smarty->clearCache('index.html');
		echo json_encode(array('status'=>'1','id'=>Question::model()->lastInsertID ())); //更新成功
	};
}
// 跳转修改问题页面
else if ($action == 'edit') {
	$params = Question::model()->fillter($_GET);
	if(empty($params['id'])){
		frontend_msg('id不能未空');
		return;
	}
	$smarty->assign('qlist',Question::model()->findOneRowByPK($params['id']));
	$smarty->display('postedit.html',$params['id']);
}
// 修改问题ajax验证
else if($action == 'editproc'){
	$params = Question::model()->fillter($_POST);
	if(empty($params['id'])){ echo '0'; return; } //ID不能为空
 	if($_POST['authcode']!=CFG::Ins()->authcode){ echo '-1'; return; } //授权码错误
	$data = array(
			'cat_id'=>$params['cat_id'],
			'question'=>$params['question'],
			'point'=>$params['point'],
			'answer'=>$params['answer'],
	);
	$id = $params['id'];
	if (Question::model()->update($data, $id)) {
	    $smarty->clearCache('postedit.html',$id);
	    $smarty->clearCache('question.html',$id);
	    echo '1'; return;  //更新成功
	} else {
		echo '-2'; //更新失败
	};
}
?>