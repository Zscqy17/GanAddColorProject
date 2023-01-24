<?php
namespace hsC;
class test{
	
	public function api(){
		$resslut=exec('D:/Miniconda3/python ./controllers/test.py 2>error.txt '.$_POST['imgs']." ".$_POST['option'],$output,$ret);
		echo $resslut;
	}
}