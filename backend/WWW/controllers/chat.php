<?php
namespace hsC;
class chat{
	public function add(){
		// 验证签名
//		checkSign();
		// 验证用户合法性
//		$user = checkUser();
		// 提交主要信息
		$dbChat = \hsTool\db::getInstance('chats');
		$addData = array(
			'group'      => $_POST['group'],
			'uindex'     => $_POST['uindex'],
			'uname'      => $_POST['uname'],
			'contentType'=> $_POST['contentType'],
			'uface' 	 => $_POST['uface'],
			'content' 	 => $_POST['content'],
			'length' 	 => $_POST['length'],
			'date' 		 => $_POST['date'],
			'uid' 		 => $_POST['uid'],
		);
		$dbChat->add($addData);
		exit(jsonCode('ok', $addData));
		
	}
	public function getOldChats(){
		$dbChats = \hsTool\db::getInstance('chats');
		$chats = $dbChats
				->where("uid = ?" , $_POST['uid'])
				->order('date asc')
				->fetchAll();
		if(empty($chats)){exit(jsonCode('empty', ''));}
   	    exit(jsonCode('ok', $chats));
		}
		
	public function getNewSupperChat(){
		$dbChats = \hsTool\db::getInstance('chats');
		$chats = $dbChats
				->where("uid = ?" , $_POST['uid'])
				->where("uindex = ?" , '10001')
				->where("date > ?" , $_POST['date'])
				->order('date desc')
				->fetchAll();
		if(empty($chats)){exit(jsonCode('empty', ''));}
   	    exit(jsonCode('ok', $chats));
	}	
	public function getNewChat(){
		$dbChats = \hsTool\db::getInstance('chats');
		$chats = $dbChats
				->where("uid = ?" , $_POST['uid'])
				->where("uindex = ?" , '10000')
				->where("date > ?" , $_POST['date'])
				->order('date desc')
				->fetchAll();
		if(empty($chats)){exit(jsonCode('empty', ''));}		
   	    exit(jsonCode('ok', $chats));
	}
	
	public function getList(){
		$dbChats = \hsTool\db::getInstance('chats');
		$chats = $dbChats
				->where("date >= ?", $_POST['date'])
				->order('date desc')
				->fetchAll();
		if(empty($chats)){exit(jsonCode('empty', $_POST['date']));}
   	    exit(jsonCode('ok', $chats));
		}
}