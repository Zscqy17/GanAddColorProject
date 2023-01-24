<?php
namespace hsC;
class member{
	
	public function wxaes(){
		if(empty($_POST['session_key']) || empty($_POST['encryptedData']) || empty($_POST['iv'])){exit(jsonCode('error', 'data error'));}
		include HS_TOOLS.'WXBizDataCrypt.php';
		$pc = new \WXBizDataCrypt(HS_APPID, $_POST['session_key']);
		$data = '';
        $errCode = $pc->decryptData($_POST['encryptedData'], $_POST['iv'], $data);
        if ($errCode == 0) {
            exit($data);
        } else {
            exit(jsonCode('error', $errCode));
        }
	}
	
	public function codeToSession(){
		$url =  "https://api.weixin.qq.com/sns/jscode2session?appid=".HS_APPID.
        "&secret=".HS_SECRET."&js_code=".$_GET['code']."&grant_type=authorization_code";
		
		$curl = new \hsTool\curl();
		$res = $curl->get($url);
		echo $res;
	}

	public function login(){
		// 签名验证
		//checkSign();
		//调用模型完成用户登录及注册
		if(empty($_POST['username'])){
			$memberModel = new \hsModel\member();
        	$memberModel->login();
        	exit(jsonCode('ok', 'ok'));
		}
		$dbMembers = \hsTool\db::getInstance('members');
		$member = $dbMembers->where('u_openid = ?', $_POST['username'])->fetch();
		if($member['u_password'] == $_POST['password']){exit(jsonCode('ok', 'ok'));}
       
	}
	public function buy(){
		if(empty($_GET['u_id'])){exit(jsonCode('error', 'u_id data error'));}
		$dbMembers = \hsTool\db::getInstance('members');
		$member = $dbMembers->where('u_id = ?', $_GET['u_id'])->fetch();
		if(empty($member)){exit(jsonCode('error', '$member data error'));}
		$data = array(
			'u_id'=>intval($member['u_id']),
			'u_openid'=>$member['u_openid'],
			'u_name'=>$member['u_name'],
			'u_face'=>$member['u_face'],
			'u_random'=>$member['u_random'],
			'u_regtime'=>intval($member['u_regtime']),
			"u_asset" => $_POST['u_asset'],
			"u_integral" => intval($_POST['u_integral']),
		);
		$dbMembers->where('u_id = ?', $_GET['u_id'])->update($data);
		exit(jsonCode('ok', 'ok'));
	}
}