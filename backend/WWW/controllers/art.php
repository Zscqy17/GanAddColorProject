<?php
namespace hsC;
class art{
	
	public function infoWithUser(){
		if(empty($_GET['artid'])){exit(jsonCode('error', 'art data error'));}
        $_GET['artid'] = intval($_GET['artid']);
        $dbArticles = \hsTool\db::getInstance('articles');
        $art = $dbArticles
                ->join('as a left join yuedu_members as b on a.art_uid = b.u_id')
                ->where('a.art_id = ?', $_GET['artid'])
                ->fetch('a.*, b.u_id, b.u_name, b.u_face');
        if(empty($art)){exit(jsonCode('empty', ''));}
        $art['art_createtime'] = date('Y-m-d H:i', $art['art_createtime']);
        exit(jsonCode('ok', $art));
	}
	
	public function getList(){
        $_GET['page'] = empty($_GET['page']) ? 1 : intval($_GET['page']);
        $dbArticles = \hsTool\db::getInstance('articles');
        $arts = $dbArticles
           ->where('art_uid = ?', array($_GET['u_id']))
           ->order('art_id desc')
           ->limit(($_GET['page'] - 1) * 10, 10)->fetchAll();
        
        if(empty($arts)){exit(jsonCode('empty', ''));}
        exit(jsonCode('ok', $arts));
	}
	
	public function edit(){
		// 验证签名
		checkSign();
		// 验证用户合法性
		$user = checkUser();
		// 检查文章
		if(empty($_GET['artid'])){exit(jsonCode('error', 'art data error'));}
		$dbArticles = \hsTool\db::getInstance('articles');
		$art = $dbArticles->where('art_id = ?', $_GET['artid'])->fetch();
		if(empty($art)){exit(jsonCode('error', 'art data error'));}
		if($art['art_uid'] != $user['u_id']){exit(jsonCode('error', 'art data error'));}
		$data = array(
			'art_title'      => $_POST['title'],
			'art_uid'        => $user['u_id'],
			'art_cate'       => intval($_POST['cate']),
			'art_content'    => $_POST['content']
		);
		$dbArticles->where('art_id = ?', $_GET['artid'])->update($data);
		exit(jsonCode('ok', 'ok'));
	}
	
	public function info(){
		if(empty($_GET['artid'])){exit(jsonCode('error', 'art data error'));}
		$_GET['artid'] = intval($_GET['artid']);
		$dbArticles = \hsTool\db::getInstance('articles');
		$art = $dbArticles->where('art_id = ?', $_GET['artid'])->fetch();
		if(empty($art)){exit(jsonCode('empty', ''));}
		exit(jsonCode('ok', $art));
	}
	
	public function add(){
		// 验证签名
//		checkSign();
		// 验证用户合法性
		$user = checkUser();
		// 提交主要信息
		$dbArticles = \hsTool\db::getInstance('articles');
		$addData = array(
			'art_title'      => $_POST['title'],
			'art_uid'        => $user['u_id'],
			'art_cate'       => intval($_POST['model']),
			'art_content'    => $_POST['imgs'],
			'art_createtime' => time()
		);
		
		$articleId = $dbArticles->add($addData);
		$resslut=exec('D:/Miniconda3/python ./controllers/test.py 2>error.txt '.$_POST['imgs']." ".$_POST['model'],$output,$ret);
		
		if(!$articleId){exit(jsonCode('error', '服务器忙请重试'));}
		// 更新会员积分
		$memberDb = \hsTool\db::getInstance('members');
		$memberDb->where('u_id = ?', array($user['u_id']))->filed('u_integral', 10);
		exit(jsonCode('ok', $resslut));
	}
	
}
