<?php
class  verify{
	//检查用户名合法性
	static function check_username($username=''){  //参考的 uc_server的 用户名
		$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
		$len = helper::dstrlen($username);
		if($len > 15 || $len < 3 || preg_match("/\s+|^c:\\con\\con|[%,\*\"\s\<\>\&]|$guestexp/is", $username)) {
			return 0;
		} else {
			return 1;
		}
	}
	//检查email合法性
	static function check_email($email=''){
		$re="~^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$~";
		if(!preg_match($re,$email)){
			return 0;
		}else{
			return 1;
		}
	}
	//检查手机号合法性
	static function check_mobile($mobile=''){
		$re="~^1\d{10}$~";
		if(!preg_match($re,$mobile)){
			return 0;
		}else{
			return 1;
		}
	}
	//验证密码合法性
	static function check_password($password){
		$re="~^(\w){6,12}$~";
		if(!preg_match($re,$password)){
			return 0;
		}else{
			return 1;
		}

	}

}