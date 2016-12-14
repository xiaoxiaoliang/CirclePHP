<?php
class Action {
	protected $tVar = array();
	protected function assign($name,$value='') {
	    if(is_array($name)) {
            $this->tVar   =  array_merge($this->tVar,$name);
        }elseif(is_object($name)){
            foreach($name as $key =>$val)
                $this->tVar[$key] = $val;
        }else {
            $this->tVar[$name] = $value;
        }
	}
	protected function display($file = '') {
		extract($this->tVar);
		if (empty($file)) {
			$file = APP_TPL_PATH.'/'.ACTION.'/'.METHOD.'.php';
		} else if (file_exists($file)) {
			include $file;
			return;
		} else {
			$file = APP_TPL_PATH.'/'.$file;
		}
		if (file_exists($file)) {
			include $file;
		} else {
			die($file."模版文件不存在");
		}
	}
	protected function redirect($path, $arg=array()) {
		header("location: ".U($path,$arg));
		exit;
	}

	protected function dispatch($msg, $path = '', $status = 0) {
		$url = '';
		if($path) {
			$url = U($path);
		}
		if (IS_AJAX) {
			echo json_encode(array(
					'status'=>$status,
					'msg'=>$msg,
					'url'=>$url
			));
		} else {
			if($status) {
				include TPL_PATH.'error.php';
			} else {
				include TPL_PATH.'success.php';
			}
		}
	}
	
	protected function error($msg, $path = '', $status = 1, $exit = true) {
		$this->dispatch($msg, $path, $status);
		if($exit) exit;
	}
	
	protected function success($msg, $path = '', $status = 0) {
		$this->dispatch($msg, $path, $status);
	}
}