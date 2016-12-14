<?php
class CApp {
	static public function init(){
		// 定义当前请求的系统常量
		define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
		define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
		define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
		define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
		define('IS_PUT',        REQUEST_METHOD =='PUT' ? true : false);
		define('IS_DELETE',     REQUEST_METHOD =='DELETE' ? true : false);
		define('IS_AJAX',       ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])) ? true : false);
		
		// 当前文件名
		if(!defined('_PHP_FILE_')) {
			if(IS_CGI) {
				//CGI/FASTCGI模式下
				$_temp  = explode('.php',$_SERVER['PHP_SELF']);
				define('_PHP_FILE_',    rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_temp[0].'.php'),'/'));
			}else {
				define('_PHP_FILE_',    rtrim($_SERVER['SCRIPT_NAME'],'/'));
			}
		}
		if(!defined('__ROOT__')) {
			// 网站URL根目录
			if( strtoupper(APP_NAME) == strtoupper(basename(dirname(_PHP_FILE_))) ) {
				$_root = dirname(dirname(_PHP_FILE_));
			}else {
				$_root = dirname(_PHP_FILE_);
			}
			define('__ROOT__',   (($_root=='/' || $_root=='\\')?'':$_root));
		}
		// URL常量
		define('__SELF__',strip_tags($_SERVER['REQUEST_URI']));
		defined('__THEME__') or define('__THEME__', __ROOT__.'/Theme/');//公用文件目录
		// 系统变量安全过滤
// 		if(C('VAR_FILTERS')) {
// 			$filters    =   explode(',',C('VAR_FILTERS'));
// 			foreach($filters as $filter){
// 				// 全局参数过滤
// 				array_walk_recursive($_POST,$filter);
// 				array_walk_recursive($_GET,$filter);
// 			}
// 		}
	}
	
	static public function run(){
		CApp::init();
		// Session初始化
// 		if(C('SESSION_AUTO_START')){
// 			session_start();
// 		}
		include MODE_PATH."function.php";
		CApp::exec();
		return ;
	}
	
	static public function exec(){
		//分析URL参数
		$action = C('DEFAULT_ACTION');
		$method = C('DEFAULT_METHOD');
		if(isset($_GET['s'])) {
			$s_arr = explode('/',trim($_GET['s'],'/'));//s参数
			if(isset($s_arr[0])) {
				$action = $s_arr[0];
			}
			if(isset($s_arr[1])) {
				$method = $s_arr[1];
			}
		}
		define('ACTION',$action);
		define('METHOD',$method);
		//执行action
		if(file_exists(APP_ACTION_PATH.$action."Action.class.php")) {
			require_cache(APP_ACTION_PATH.$action."Action.class.php");
			$ActionClassName = $action."Action";
			$ActionObj = new $ActionClassName;
			
			if(method_exists($ActionObj, '_init')) {
				$return = $ActionObj->_init();//先执行init方法
			}
			if(method_exists($ActionObj, $method)) {
				$action_res = call_user_func(array($ActionObj, $method));
			} else {
				die($action.' 操作 '.$method.' 方法不存在');
				return false;
			}
		} else {
			die('请求的操作不存在');
			return false;
		}
		
	}
}