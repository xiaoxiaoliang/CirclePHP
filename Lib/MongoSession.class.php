<?php
class MongoSession {
	static private $mongo = null;
    static private $db = null;
	static private $col = null;
    static function open($savePath, $sessionName) {
		self::$mongo = new MongoClient(C('SESSION_MONGO_LINK'));
		self::$db = self::$mongo->$sessionName;
		self::$col = self::$db->session;
        return true;
    }

    static function close() {
		self::$mongo->close();
        return true;
    }

    static function read($id) {
       $session = self::$col->findOne(array('sid'=>$id), array('data'));
	   return $session['data'];
    }

    static function write($id, $data) {
       return (self::$col->update(array('sid'=>$id), array('sid'=>$id,'data'=>$data, 'utime'=>time()), array("upsert"=>true)) === false)?false:true;
    }

    static function destroy($id) {
		return (self::$col->remove(array('sid'=>$id), array("justOne" => true)) === false)?false:true;
    }

    static function gc($maxlifetime) {
		$overtime = time()-$maxlifetime;
		return (self::$col->remove(array('utime'=>array('$lte'=>$overtime)), array("justOne" => false)) === false)?false:true;
    }
}