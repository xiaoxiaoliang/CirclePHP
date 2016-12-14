<?php
// 系统目录定义 
defined('CIRCLE_PATH') 	or define('CIRCLE_PATH', dirname(__FILE__).'/');
defined('APP_PATH') 	or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');
require CIRCLE_PATH.'Comm/start.php';