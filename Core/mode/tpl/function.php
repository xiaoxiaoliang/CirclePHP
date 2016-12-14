<?php

function U($path = '', $arg=NULL) {
	$p = array();
	$parm = '';
	if(is_array($arg)) {
		foreach ($arg as $k=>$v) {
			$parm .= '&'.$k.'='.$v;
		}
	}
	if(is_string($path) && $path != '') {
		$p = explode('/',$path);
	} else if(is_array($path)) {
		$p = $path;
	}
	
	if(count($p) >= 2) {
		return _PHP_FILE_.'?s='.$p[0].'/'.$p[1].$parm;
	} else if(count($p) == 1) {
		return _PHP_FILE_.'?s='.ACTION.'/'.$p[0].$parm;
	} else {
		if (stripos(trim(__SELF__,'?'),'?')) {
			return trim(__SELF__,'?').$parm;
		} else {
			return trim(__SELF__,'?').'?'.ltrim($parm,'&');
		}
	}
}