<?php
define('PSTART_TIME', microtime(true));
defined('CIRCLE_PATH') or exit();
defined('APP_DEBUG') 	or define('APP_DEBUG',false); // 是否调试模式
defined('MODE') 	or define('MODE','json'); // 定义运行模式
// 路径设置 可在入口文件中重新定义 所有路径常量都必须以/ 结尾
defined('CORE_PATH')    or define('CORE_PATH',      CIRCLE_PATH.'Core/'); // 系统核心类库目录
defined('EXT_PATH')  	or define('EXT_PATH',       CIRCLE_PATH.'Ext/'); // 系统扩展目录
defined('LIB_PATH')  	or define('LIB_PATH',       CIRCLE_PATH.'Lib/'); // 系统类库目录
defined('COMM_PATH') 	or define('COMM_PATH',      CIRCLE_PATH.'Comm/'); // 系统公共目录
defined('CONF_PATH') 	or define('CONF_PATH',      CIRCLE_PATH.'Conf/'); // 系统配置目录
defined('TPL_PATH') 	or define('TPL_PATH',      CIRCLE_PATH.'Tpl/'); // 系统模版目录

defined('APP_CORE_PATH')	  or define('APP_CORE_PATH',  	  APP_PATH.APP_NAME.'/'); // 项目核心目录
defined('APP_CONF_PATH')	  or define('APP_CONF_PATH',      APP_CORE_PATH.'Conf/'); // 项目配置目录
defined('APP_LANG_PATH')  	  or define('APP_LANG_PATH',      APP_CORE_PATH.'Lang/'); // 项目语言包目录
defined('APP_TPL_PATH')   	  or define('APP_TPL_PATH',       APP_CORE_PATH.'Tpl/'); // 项目模板目录
defined('APP_ACTION_PATH')    or define('APP_ACTION_PATH',    APP_CORE_PATH.'Action/');//操作类目录
defined('APP_TRAIT_PATH') 	  or define('APP_TRAIT_PATH',     APP_CORE_PATH.'Trait/');//多重用类目录
defined('APP_MODEL_PATH')     or define('APP_MODEL_PATH',     APP_CORE_PATH.'Model/');//模型目录
defined('APP_CLASS_PATH')  	  or define('APP_CLASS_PATH',     APP_CORE_PATH.'Class/'); //项目类库目录
defined('MODE_PATH')          or define('MODE_PATH',     	  CORE_PATH.'/mode/'.MODE.'/');//核心扩展目录

defined('RUN_CACHE_PATH') or define('RUN_CACHE_PATH',APP_PATH.'RunCache/');
defined('LOG_PATH')     or define('LOG_PATH',       RUN_CACHE_PATH.'Logs/'); // 项目日志目录
defined('TEMP_PATH')    or define('TEMP_PATH',      RUN_CACHE_PATH.'Temp/'); // 项目缓存目录
defined('DATA_PATH')    or define('DATA_PATH',      RUN_CACHE_PATH.'Data/'); // 项目数据目录
defined('TPL_PATH')    or define('TPL_PATH',      RUN_CACHE_PATH.'Tpl/'); // 项目模版缓存目录

// 系统信息
if(version_compare(PHP_VERSION,'5.4.0','<')) {
	ini_set('magic_quotes_runtime',0);
	define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?true:false);
}else{
	define('MAGIC_QUOTES_GPC',false);
}

define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );//判断是否Windows

require COMM_PATH.'function.php';
require CORE_PATH.'Circle.class.php';
require MODE_PATH.'/Action.class.php';
if(!APP_DEBUG) {
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	set_error_handler('displayErrorHandler');//定义自定义错误处理函数
}
foreach (glob(APP_CORE_PATH.'Apl/*.php') as $filename) {
	require_cache($filename);
}
//spl_autoload_unregister(array('Circle', 'autoload'));
if(!spl_autoload_register(array('Circle', 'autoload'))){
	die("spl autoload err");
}
Circle::start();
//Log::write($_SERVER['REQUEST_URI'].(isset($_SERVER['HTTP_REFERER'])?(' '.$_SERVER['HTTP_REFERER']):'').'  执行时长:'.(microtime(true) - PSTART_TIME)	.' 使用内存:'.memory_get_usage().'byte 峰值内存:'.memory_get_peak_usage().'byte  ', 'ACCESS_'.MODE);