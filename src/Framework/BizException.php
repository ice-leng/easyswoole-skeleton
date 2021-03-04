<?php
declare(strict_types=1);

namespace EasySwoole\Skeleton\Framework;

use EasySwoole\Skeleton\Errors\CommonError;
use Exception;
use Throwable;

class BizException extends Exception
{
    /**
     * @var string
     */
    private $realCode;

    public function __construct($code, array $replace = [], string $message = null, Throwable $previous = null)
    {
        if (is_null($message)) {
            $config = config('errorCode', []);
            $class = !empty($config['classNamespace']) ? $config['classNamespace'] . '\\' . $config['classname'] : CommonError::class;
            $message = $class::byValue($code)->getMessage($replace);
        }
        $this->realCode = $code;
        parent::__construct($message, 0, $previous);
    }

    public function getRealCode(): string
    {
        return $this->realCode;
    }
}
