<?php
declare(strict_types=1);

namespace EasySwoole\Skeleton\Framework\Http;

use EasySwoole\Http\Request as HttpRequest;
use EasySwoole\Skeleton\Helpers\Arrays\ArrayHelper;

class Request extends HttpRequest
{
    protected $data;

    public function __construct(HttpRequest $request)
    {
        parent::__construct($request->getSwooleRequest());

        $contentType = strtolower($this->getHeaderLine('content-type'));
        if (strpos($contentType, 'application/json') === 0) {
            $data = json_decode($this->getBody()->__toString(), true) ?? [];
        } else {
            $data = $this->getParsedBody();
        }
        //TODO 增加XML支持
        $this->data = $data;
    }

    public function getRequestParam(...$key)
    {
        $data = array_merge($this->data,$this->getQueryParams());;
        if(empty($key)){
            return $data;
        }else{
            $res = [];
            foreach ($key as $item){
                $res[$item] = ArrayHelper::get($data, $item);
            }
            if(count($key) == 1){
                return array_shift($res);
            }else{
                return $res;
            }
        }
    }
}
