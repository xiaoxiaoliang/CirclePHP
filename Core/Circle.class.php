<?php 
class Circle {
	static public $config = array();
	static public $re = array("status"=>0,"respond"=>array());
	
	static public function autoload($name) {
		if(substr($name,-6) == 'Action' && file_exists(APP_ACTION_PATH.$name.".class.php")) {
			require_cache(APP_ACTION_PATH.$name.".class.php");
		} else if(substr($name,-5) == 'Trait' && file_exists(APP_TRAIT_PATH.$name.".class.php")) {
			require_cache(APP_TRAIT_PATH.$name.".class.php");
		}else if('Model' == $name){
			require_cache(CORE_PATH.$name.".class.php");
		} else if('Model' == substr($name,-5)) {
			require_cache(APP_MODEL_PATH.$name.".class.php");
		} else if(file_exists(CORE_PATH.$name.".class.php")) {
			require_cache(CORE_PATH.$name.".class.php");
		} else if(file_exists(LIB_PATH.$name.".class.php")){
			require_cache(LIB_PATH.$name.".class.php");
		} else if(file_exists(APP_CLASS_PATH.$name.".php")) {
			require_cache(APP_CLASS_PATH.$name.".php");
		} else {
			halt("需要找类:".$name);
		}
	}
	static public function load_config() {
		//加载配置
		$init_conf = include(CONF_PATH.'/init.php');
		//加载项目配置
		$app_conf = include(APP_CONF_PATH.'/conf.php');
		//合并配置
		Circle::$config = array_merge($init_conf, (array)$app_conf);
		if(APP_DEBUG){
			$debug_conf = include(CONF_PATH.'/debug.php');
			Circle::$config = array_merge(Circle::$config, (array)$debug_conf);
		}	
	}
	static public function is_cli() {
   		return (php_sapi_name() === 'cli') ? true : false;
	}
	static public function session_handler() {
		if(C('SESSION_MODE') == 'mongo') {
			session_set_save_handler(
			array('MongoSession', 'open'),
			array('MongoSession', 'close'),
			array('MongoSession', 'read'),
			array('MongoSession', 'write'),
			array('MongoSession', 'destroy'),
			array('MongoSession', 'gc')
			);
			register_shutdown_function('session_write_close');
		}
		if(C('SESSION_AUTO_START')){
			$sess_trans = C('SESSION_USE_TRANS');
			if ($sess_trans && C('SESSION_USE_TRANS_NAME')) {
				$session_id = '';
				switch ($sess_trans) {
					case 'post': {
						if (isset($_POST[C('SESSION_USE_TRANS_NAME')])) {
							$session_id = $_POST[C('SESSION_USE_TRANS_NAME')];
						}
						break;
					}
					case 'get': {
						if (isset($_POST[C('SESSION_USE_TRANS_NAME')])) {
							$session_id = $_GET[C('SESSION_USE_TRANS_NAME')];
						}
						break;
					}
					default : {
						$session_id = false;
					}
				}
				if ($session_id !== false) {
					ini_set("session.use_trans_sid",1);
					ini_set("session.use_only_cookies",0);
					ini_set("session.use_cookies",0);
					if($session_id !== '') {
						session_id($session_id);
					}
				}
			}
			session_start();
			define('SESSION_ID', session_id());
		}
	}
	static public function start(){
		self::load_config();
		date_default_timezone_set('Asia/Shanghai');//'Asia/Shanghai' 亚洲/上海
		self::session_handler();
		if(MODE == 'tpl') {
			require MODE_PATH.'/CApp.class.php';
			CApp::run();
		} else if(self::is_cli() && MODE == 'cron') {
			//分析URL参数
			$action = C('DEFAULT_ACTION');
			$method = C('DEFAULT_METHOD');
			if(isset($_SERVER['argv'][1])) {
				$s_arr = explode('/',trim($_SERVER['argv'][1],'/'));//s参数
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
					$ActionObj->_init();//先执行init方法
				}
				if(method_exists($ActionObj, $method)) {
					call_user_func(array($ActionObj, $method));
				}
			} else {
				die("操作不存在");
			}
			exit;			
		} else if(MODE == 'simple') {
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
					$ActionObj->_init();//先执行init方法
				}
				if(method_exists($ActionObj, $method)) {
					call_user_func(array($ActionObj, $method));
				}
			}
		} else if(MODE == 'include') {
			//分析URL参数
			$action = C('DEFAULT_ACTION');
			$method = C('DEFAULT_METHOD');
			if(defined('INC_ACT_MET')) {
				$s_arr = explode('/',trim(INC_ACT_MET,'/'));//s参数
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
				global $inc_res_data;
				require_cache(APP_ACTION_PATH.$action."Action.class.php");
				$ActionClassName = $action."Action";
				$ActionObj = new $ActionClassName;
				if(method_exists($ActionObj, '_init')) {
					$res = $ActionObj->_init();//先执行init方法
					if($res) { $inc_res_data = $res; return;}
				}
				if(method_exists($ActionObj, $method)) {
					$inc_res_data = call_user_func(array($ActionObj, $method));
				}
			}
		} else {
			if (defined('USE_POST_STREAM')) {
				$_POST = json_decode(file_get_contents('php://input'),true);
			}
			self::analyse();
		}
	}
	static public function analyse(){
		if(!isset($_POST['request'])){
			$_POST['request']=array();
		}
		try {//处理异常
			foreach($_POST['request'] as $k=>$v) {
				if(isset($v['cmd'])){
					$arg = isset($v['arg'])?$v['arg']:array();
					if(!self::action($v['cmd'], $arg)){
						break;
					}
				}
			}
		}catch(Exception $e) {
			Circle::$re = array('status'=>1,'msg'=>$e->getMessage());
		}
		self::send(json_encode(Circle::$re));
	}
	static public function send($data) {
		echo $data;
	}
	static public function action($cmd, $arg) {
		//分析参数
		@list($action, $method) = explode('/',$cmd);
		$action = $action?$action:C('default_action');
		$method = $method?$method:C('default_method');
		//执行Action
		if(file_exists(APP_ACTION_PATH.$action."Action.class.php")) {
			require_cache(APP_ACTION_PATH.$action."Action.class.php");
			$ActionClassName = $action."Action";
			$ActionObj = get_instance_of($ActionClassName);
			$init_res = array();
			if(method_exists($ActionObj, $method)) {
				//执行初始化
				if(method_exists($ActionObj, '_init')) {
					$return = $ActionObj->_init($cmd,$arg,$init_res);
					if($return === false) {
						self::$re = $init_res;
						if(!isset(self::$re['status']) || self::$re['status'] == 0) {
							self::$re['status'] = -1;
						}
						return false;
					}
					if(is_array($return)) {
						$res = array(
								'cmd'=>$cmd,
								'data'=>$return
						);
						if(APP_DEBUG) {
							$res["arg"] = $arg;
						}
						self::$re['respond'][] = $res;
						return true;
					}
				}
				//调用工作函数
				$action_res = call_user_func(array($ActionObj, $method),$arg);
				$action_res = ($action_res != false)?$action_res:array();
				$res = array(
						'cmd'=>$cmd,
						'data'=>array_merge((array)$init_res,(array)$action_res)
				);
				if(APP_DEBUG) {
					$res["arg"] = $arg;
				}
				self::$re['respond'][] = $res;
				//调用结束回调
				if(method_exists($ActionObj, '_after')) {
					$ActionObj->_after($cmd,$arg,$res);
				}
			} else {
				$res = array(
						'cmd'=>$cmd,
						'data'=>array('status'=>'1','msg'=>$action.' 操作 '.$method.' 方法不存在')
				);
				if(APP_DEBUG) {
					$res["arg"] = $arg;
				}
				self::$re['respond'][] = $res;
			}
		} else {
			$res = array(
					'cmd'=>$cmd,
					'data'=>array('status'=>'1','msg'=>'请求的操作不存在')
			);
			if(APP_DEBUG) {
				$res["arg"] = $arg;
			}
			self::$re['respond'][] = $res;
		}
		return true;
	}
}