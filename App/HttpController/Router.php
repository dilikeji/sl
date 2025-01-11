<?php


namespace App\HttpController;


use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;
use SQLite3;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->get('/{id}', function (Request $request, Response $response) {
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $id = $context['id'];
            $db = new SQLite3(EASYSWOOLE_ROOT . '/sl.db');
            $stmt = $db->prepare("SELECT * FROM urls WHERE name = :name");
            $stmt->bindValue(':name', $context['id']);
            $result=$stmt->execute();
            $list=[];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $list[]=$row;
            }
            if(!empty($list)){
                $query='';
                if($list[0]['is_params']==1){
                    $params=$request->getQueryParams();
                    if(!empty($params)){
                        $query.='?';
                        $query.=http_build_query($params);
                    }
                }
                $response->redirect($list[0]['url'].$query);
                return false;
            }else{
                return true;
            }
        });
    }
}