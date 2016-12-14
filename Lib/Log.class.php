<?php

class Log {
	static public function write($msg, $level="ERROR") {
		$not_log_level = C('NOT_LOG_LEVEL');
		if ($not_log_level && in_array($level,$not_log_level) ) return;
		// 目录不存在则创建
		if (!is_dir(LOG_PATH))
			mkdir(LOG_PATH,0755,true);
		file_put_contents(LOG_PATH.$level.date("Y-m-d-H").".log", $msg." [".date("Y-m-d H:i:s")."]\r\n", FILE_APPEND);
	}
	static public function record($msg, $sub_path='', $type="INFO") {
		$path = DATA_PATH.'Record/'.$sub_path.'/';
		if (!is_dir($path))
			mkdir($path,0755,true);
		file_put_contents($path.$type.".txt", $msg." [".date("Y-m-d H:i:s")."]\r\n", FILE_APPEND);
	}
}