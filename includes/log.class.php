<?php
defined('ACC') || exit('Permission Denied');
/* 日志类 */
class Log {
	/**
	 * 写入日志
	 * @param $logmsg string 传入写入信息
	 */
	public static function log_save($logmsg) {
		$curr = self::_log_check();
		$logmsg .= "\r\n";
		$handle = fopen($curr, 'ab');
		fwrite($handle, $logmsg);
		fclose($handle);
	}

	/**
	 * 检查日志文件大小
	 */
	private static function _log_check() {
		$curr = ROOTPATH . 'temp/log/current.log';
		$new = ROOTPATH . 'temp/log/' . date('ymd_His', time()) . '.log';
		if(!is_dir(dirname($curr))){
			mkdir(dirname($curr),0777,true);
		}
		if(!file_exists($curr)){
			touch($curr);
			return $curr;
		}
		if(filesize($curr) >= 1024000){
			rename($curr, $new);
			touch($curr);
			return $curr;
		}
		return $curr;
	}
}


Log::log_save('111');
?>

