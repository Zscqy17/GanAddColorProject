<?php
namespace hsC;
class uploadController {
    public function index(){
//      $this->allowSize = 5;
        if(!empty($_FILES['img'])){
            //获取扩展名
            $exename  = $this->getExeName($_FILES['img']['name']);
            //检查扩展名
            if(!in_array($exename, array('png', 'gif', 'jpeg', 'jpg'))){$this->json('格式错误', 'error');}
            //本地上传
            $file = uniqid().'.'.$exename;
            $imageSavePath = 'imgs/'.$file;
            if(move_uploaded_file($_FILES['img']['tmp_name'], $imageSavePath)){
                // 请严格按照下面的 json 的格式返回数据
                $arr = array('status'=>'ok', 'data' => 'http://498f26878q.qicp.vip/'.$imageSavePath, 'result' => '您的自定义内容');
                exit(json_encode($arr));
            }else{
                $arr = array('status'=>'error', 'data' => '具体的错误信息', 'result' => '您的自定义内容');
                exit(json_encode($arr));
            }
        }
    }
    public function getExeName($file){
        $pathinfo      = pathinfo($file);
        return strtolower($pathinfo['extension']);
    }
}