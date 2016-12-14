<?php
class Action {
	protected function err($status, $msg, $Model = null) {
		$re = array('status'=>$status, 'msg'=>$msg);
		if(APP_DEBUG && $Model !== null) {
			//$re['sql']=$Model->getlastsql();
			$re['error']=$Model->getDbError();
		} else if ($status == -1 && $Model !== null) {
			Log::write($Model->getDbError(),"SQL_ERROR");
		}
		return $re;
	}
	//得到参数的值
	protected function getarg(&$arg, $name, $null_val = 0) {
		return isset($arg[$name])?$arg[$name]:$null_val;
	}
}