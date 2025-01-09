<?php


namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;
use Exception;
use SQLite3;

class Index extends Controller
{

    public function index(): void
    {
        $file = EASYSWOOLE_ROOT.'/vendor/easyswoole/easyswoole/src/Resource/Http/welcome.html';
        if(!is_file($file)){
            $file = EASYSWOOLE_ROOT.'/src/Resource/Http/welcome.html';
        }
        $this->response()->write(file_get_contents($file));
    }

    function test(): void
    {
        $this->response()->write('this is test');
    }

    protected function actionNotFound(?string $action): void
    {
        $this->response()->withStatus(404);
        $file = EASYSWOOLE_ROOT.'/vendor/easyswoole/easyswoole/src/Resource/Http/404.html';
        if(!is_file($file)){
            $file = EASYSWOOLE_ROOT.'/src/Resource/Http/404.html';
        }
        $this->response()->write(file_get_contents($file));
    }

    function login(): void
    {
        try{
            $params=$this->request()->getRequestParam();
            if(empty($params['username'])){
                throw new Exception('username缺失');
            }
            if(strlen($params['username'])>10){
                throw new Exception('username不能超过10位');
            }
            if(empty($params['password'])){
                throw new Exception('password缺失');
            }
            if(strlen($params['password'])>10){
                throw new Exception('password不能超过10位');
            }
            $db = new SQLite3(EASYSWOOLE_ROOT . '/sl.db');
            $result = $db->query("SELECT * FROM users WHERE username ='".$params['username']."' AND password ='".$params['password']."'");
            $list=[];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $list[]=$row;
            }
            if(count($list)==0){
                throw new Exception('用户名或密码错误');
            }

            $db->close();
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->write(json_encode([
                'data'=>$list
            ],JSON_UNESCAPED_UNICODE));
            $this->response()->end();
        }catch (\Throwable $th){
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->write(json_encode([
                'msg'=>$th->getMessage()
            ],JSON_UNESCAPED_UNICODE));
            $this->response()->end();
        }

    }
}