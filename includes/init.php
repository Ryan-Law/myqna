<?php
defined('ACC') || exit('Permission Denied');
define('ROOTPATH', dirname(dirname(str_replace('\\', '/', __FILE__))) . '/');

// 自动加载类
function __autoLoad($class)
{
	$path = array(
		'includes/smarty/' => '.class.php',
		'includes/smarty/plugins/' => '.php',
		'includes/smarty/sysplugins/' => '.php',
		'includes/' => '.class.php',
		'model/' => '.class.php'
	);
	foreach($path as $k => $v){
		$newpath = ROOTPATH . $k . $class . $v;
		if(file_exists($newpath)){
			require $newpath;
		}
	}
}

// 自动转义参数
function _addslashes($arr)
{
	foreach($arr as $k => $v){
		if(is_string($v)){
			$arr[$k] = addslashes($v);
		}
		if(is_array($v)){
			$arr[$k] = _addslashes($v);
		}
	}
	return $arr;
}
$_GET = _addslashes($_GET);
$_POST = _addslashes($_POST);
$_COOKIE = _addslashes($_COOKIE);

// 错误报告
// 开发模式：显示全部信息 生产环境：关闭所有信息
define('DEBUG', (bool)CFG::Ins()->DEBUG);
defined('DEBUG') ? error_reporting(E_ALL ^ E_NOTICE) : error_reporting(0);

// 时区设定
date_default_timezone_set(CFG::Ins()->timezone);

// 初始化数据库
if(function_exists('mysqli_connect')){
	$db = DB_MySQLi::Ins();
}else{
	$db = DB_MySQL::Ins();
}

// 开启sesstion
session_start();

// 读取用户权限表
if(!isset($_SESSION['rolelist'])){
	$_SESSION['rolelist'] = Role::model()->getRoleList();
}
foreach($_SESSION['rolelist'] as $v){
	defined($v['rname']) || define($v['rname'], $v['rvalue'] + 0);
}

// 初始化Smarty模板
$smarty = new Smarty();
$smarty->left_delimiter = '<{';
$smarty->right_delimiter = '}>';
$smarty->template_dir = ROOTPATH . 'template/';
$smarty->compile_dir = ROOTPATH . 'temp/compiled/';
$smarty->cache_dir = ROOTPATH . 'temp/cache/';
$smarty->assign('appname', CFG::Ins()->appname);
$smarty->assign('copyright', CFG::Ins()->copyright);
$smarty->assign('appurl', CFG::Ins()->appurl);
$smarty->assign('companyname', CFG::Ins()->companyname);
// 指定后台Smarty模板
if(ACC === 'admin'){
	$smarty->template_dir = ROOTPATH . 'admin/theme/';
	$smarty->compile_dir = ROOTPATH . 'temp/compiled/admin/';
}
// 开发环境：强制编译，关闭缓存
// 生产环境：关闭编译检测，开启缓存，缓存时间默认2分钟，可适当调整
if(defined('DEBUG')){
	$smarty->force_compile = true;
}else{
	$smarty->caching = true;
	$smarty->cache_lifetime = 3600;
	$smarty->compile_check = false;
}

?>