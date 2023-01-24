<?php
namespace hsC;
class commodit{
	public function getList(){
		$_GET['page'] = empty($_GET['page'])? 1 :intval($_GET['page']);
		$dbcommodits = \hsTool\db::getInstance('commodits');
		$commodits = $dbcommodits
				->order('name desc')
				->limit(($_GET['page']-1)*10,10)
				->fetchAll();
		if(empty($commodits)){exit(jsonCode('empty', ''));}
   	    exit(jsonCode('ok', $commodits));
		}
		
		
	public function add(){
		$dbcommodits = \hsTool\db::getInstance('commodits');
		$addData = array(
		'name' => $_POST['name'],
		'img' => $_POST['img'],
		'priceMarket' => $_POST['priceMarket'],
		'price' => $_POST['price'],
		);
		$result=$dbcommodits->add($addData);
		if(!$result){exit(jsonCode('ok', 'ok'));}
	}
	public function get(){
		$dbcommodits = \hsTool\db::getInstance('commodits');
		$commodits = $dbcommodits
				->where("name = ?" , $_POST['name'])
				->fetch();
		if(empty($commodits)){exit(jsonCode('empty', ''));}
   	    exit(jsonCode('ok', $commodits));
		}

}
