<?php

namespace App\HttpController;

use EasySwoole\EasySwoole\Config;
use EasySwoole\HttpAnnotation\Swagger\AnnotationParser;
use EasySwoole\HttpAnnotation\Swagger\GenerateSwagger;
use EasySwoole\Skeleton\Framework\BaseController;
use EasySwoole\Spl\SplArray;

class Swagger extends BaseController
{

    public function onRequest(?string $action): ?bool
    {
        if (isOnline()) {
            $this->actionNotFound($action);
        }
        return true;
    }

    public function admin()
    {
        $path = EASYSWOOLE_ROOT . '/App/HttpController/Platform';
        $config = new SplArray(Config::getInstance()->getConf("swagger"));
        $annotationParser = new AnnotationParser($path);
        $swagger = new GenerateSwagger($config, $annotationParser);

        $type = $this->request()->getRequestParam('type') ?? 'html';
        if ($type === 'html') {
            $string = $swagger->scan2Html();
            $this->response()->withAddedHeader('Content-type', "text/html;charset=utf-8");
            $this->response()->write($string);
            return;
        }

        if ($type === 'json') {
            $this->response()->write(json_encode($swagger->scan2Json(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus(200);
            return;
        }

        $this->actionNotFound($this->getActionName());
    }
}
