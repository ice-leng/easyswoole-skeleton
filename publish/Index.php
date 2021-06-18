<?php

namespace App\HttpController;

use EasySwoole\Skeleton\Framework\BaseController;;

class Index extends BaseController
{
    public function index()
    {
        $this->response()->redirect("./index.html");
    }
}
