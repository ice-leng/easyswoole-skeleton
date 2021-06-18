<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\HttpAnnotation\Swagger\Annotation\ApiSuccessTemplate;
use EasySwoole\HttpAnnotation\Utility\Scanner;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    /**
     * @param RouteCollector $routeCollector
     * @throws \EasySwoole\Annotation\Exception
     */
    function initialize(RouteCollector $routeCollector)
    {
        $scanner = new Scanner();
        $scanner->getParser()->getAnnotation()->addParserTag(new ApiSuccessTemplate());
        $scanner->mappingRouter($routeCollector, EASYSWOOLE_ROOT.'/App/HttpController');
    }
}
