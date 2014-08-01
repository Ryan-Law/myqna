<?php
define('ACC', TRUE);
require 'includes/init.php';

$action = isset($_GET['act']) ? trim($_GET['act']) : 'list';
$smarty->assign('category', Category::model()->findAll());

if($action == 'list'){
	!isset($_SESSION['userinfo']) || header("location:./index.php\n");
	$smarty->display('user.html');
}

elseif($action == 'login'){
	!isset($_SESSION['userinfo']) || header("location:./index.php\n");
	$params = User::model()->fillter($_POST);
	$res = User::model()->validateUser($params['username'],$params['password']);
	if(isset($res)){
		$_SESSION['sid'] = session_id();
		$_SESSION['userinfo'] = $res;
			User::model()->updateLastLogin($res['uid']);
		echo '1'; // 登陆成功
	}else{
		echo '0'; // 用户名或密码错误
	}
}

elseif($action == 'logout'){
	if(isset($_SESSION['userinfo'])){
		unset($_SESSION['userinfo']);
		echo '1'; 
	}
}

elseif($action == 'regis'){
	$params = User::model()->fillter($_POST);
	// 用户名和密码验证
	if($params['username']==""||$params['password']==""){ echo '-1'; return; }
	// 验证码验证
	if(strtolower($_POST['captcha'])!= strtolower($_SESSION['captcha'])){ echo "-2"; return; }
	// 用户名是否存在验证
	if(User::model()->checkUserExist(strtolower($params['username']))){ echo "-3"; return; }
	$data = array(
				'username'=>$params['username'],
				'password'=>md5($params['password']),
				'email'=>$params['email'],
				'registime'=>time()
			);
	$res = User::model()->add($data);
	if(!empty($res)){
		$userInfo = User::model()->validateUser($params['username'],$params['password']);
		if(isset($userInfo)){
			$_SESSION['userinfo'] = $userInfo;
			User::model()->updateLastLogin($userInfo['uid']);
		}
		echo '1';
	}else{
		echo '0';
	}
}

elseif($action == "captcha"){
	Image::captcha();
	$_SESSION['captcha'] = Image::$captcha;
}

?>
