<?php
declare(strict_types=1);

namespace EasySwoole\Skeleton\Framework;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Skeleton\Helpers\StringHelper;
use EasySwoole\Skeleton\Errors\CommonError;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Exception\Annotation\ParamValidateError;
use EasySwoole\Skeleton\Framework\Http\Request as BaseRequest;
use EasySwoole\Validate\Validate;
use Exception;
use stdClass;
use Throwable;

/**
 * 公共Controller类
 */
class BaseController extends AnnotationController
{
    public function __hook(?string $actionName, Request $request, Response $response)
    {
        $request = new BaseRequest($request);
        return parent::__hook($actionName, $request, $response);
    }

    protected function onException(Throwable $throwable): void
    {
        Logger::getInstance()->error(format_throwable($throwable));
        if ($throwable instanceof BizException) {
            $this->fail($throwable->getRealCode(), $throwable->getMessage());
            return;
        }

        if ($throwable instanceof ParamValidateError) {
            /** @var Validate $validate */
            $validate = $throwable->getValidate();
            $errorMsg = $validate->getError()->getErrorRuleMsg();
            $this->fail(CommonError::INVALID_PARAMS, "{$errorMsg}");
            return;
        }

        if (!isOnline()) {
            $this->fail(CommonError::SERVER_ERROR, format_throwable($throwable));
            return;
        }

        $error = CommonError::SERVER_ERROR();
        $this->fail($error->getValue(), $error->getMessage());
    }

    public function getLocalhost(): string
    {
        $uri = $this->request()->getUri();
        $localhost = '';
        if ($uri->getScheme() != '') {
            $localhost .= $uri->getScheme() . ':';
        }
        if ($uri->getAuthority() != '') {
            $localhost .= '//' . $uri->getAuthority();
        }
        return $localhost;
    }

    public function onRequest(?string $action): ?bool
    {
        $local = config('thirdparty.local.url_name', 'localhost');
        $url = config('thirdparty.local.url') ?? $this->getLocalhost();
        ContextManager::getInstance()->set($local, $url);
        //TODO 版本号控制
        return parent::onRequest($action);
    }

    protected function actionNotFound(?string $action)
    {
        throw new BizException(CommonError::SERVER_NOT_FOUND);
    }

    public function required(string $key)
    {
        $value = $this->request()->getRequestParam($key);
        if (empty($value)) {
            throw new BizException(CommonError::INVALID_PARAMS, [], $key . ' 参数不存在');
        }
        return $value;
    }

    /**
     * @param string $enumClass
     * @param        $key
     *
     * @return mixed
     * @throws BizException
     */
    protected function requiredEnum(string $enumClass, $key)
    {
        if (!class_exists($enumClass) || !is_subclass_of($enumClass, BaseEnum::class)) {
            throw new Exception($enumClass . ' not exists');
        }
        $value = $this->required($key);
        return $enumClass::byValue($value);
    }

    /**
     * @param string $enumClass
     * @param        $key
     * @param null   $defaultValue
     *
     * @return mixed
     * @throws Exception
     */
    protected function optionalEnum(string $enumClass, $key, $defaultValue = null)
    {
        if (!class_exists($enumClass) || !is_subclass_of($enumClass, BaseEnum::class)) {
            throw new Exception($enumClass . ' not exists');
        }
        $value = $this->optional($key, $defaultValue);
        if ($value === null) {
            return null;
        }
        return $enumClass::byValue($value);
    }

    public function optional(string $key, $default)
    {
        $value = $this->request()->getRequestParam($key);
        if (empty($value)) {
            return $default;
        }
        return $value;
    }

    /**
     * 返回成功
     *
     * @param null|array|Object $data
     * @param string            $msg
     *
     * @return bool
     */
    protected function success($data = null, string $msg = 'ok'): bool
    {
        if (StringHelper::isEmpty($data)) {
            $data = new stdClass();
        }
        return $this->writeJson(CommonError::SUCCESS, $data, $msg);
    }

    /**
     * 返回 失败
     *
     * @param string      $code
     * @param string|null $message
     *
     * @return bool
     */
    protected function fail(string $code, string $message): bool
    {
        return $this->writeJson($code, new stdClass(), $message);
    }

    protected function writeJson($statusCode = 200, $result = null, $msg = null)
    {
        if (!$this->response()->isEndResponse()) {
            $data = [
                "code"   => $statusCode,
                "result" => $result,
                "msg"    => $msg,
            ];
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus(200);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function getContentType(string $type): string
    {
        $map = [
            "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "dotx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.template",
            "pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
            "ppsx" => "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
            "potx" => "application/vnd.openxmlformats-officedocument.presentationml.template",
            "xltx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
            'xlsx' => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            'pdo'  => 'application/pdf',
        ];
        return $map[strtolower($type)] ?? '';
    }

    /**
     * @param string $file
     * @param string $name
     *
     * @return bool
     */
    protected function download(string $file, string $name): bool
    {
        if (!$this->response()->isEndResponse()) {
            // 获取指定路径下的 excel 文件，例如这里获取项目根目录下的 test.xlsx 文件
            $this->response()->sendFile($file);
            $contentType = $this->getContentType(explode('.', $file)[1]);
            // 设置文件流内容类型
            if ($contentType) {
                $this->response()->withHeader('Content-Type', $contentType . ';charset=utf-8');
            }
            // 设置要下载的文件名称，一定要带文件类型后缀
            $this->response()->withHeader('Content-Disposition', 'attachment;filename=' . urlencode($name));
            $this->response()->withHeader('Access-Control-Expose-Headers', 'Content-Disposition');
            $this->response()->withHeader('Cache-Control', 'max-age=0');
            $this->response()->end();
            return true;
        } else {
            return false;
        }
    }

    /**
     * 真实 ip
     *
     * @param string $headerName
     *
     * @return mixed|string
     */
    public function clientRealIP($headerName = 'x-real-ip')
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $client = $server->getClientInfo($this->request()->getSwooleRequest()->fd);
        $xri = $this->request()->getHeader($headerName);
        if (!empty($xri)) {
            $clientAddress = $xri[0];
        } else {
            $clientAddress = $client['remote_ip'];
        }
        $xff = $this->request()->getHeader('x-forwarded-for');
        if ($clientAddress === '127.0.0.1') {
            if (!empty($xri)) {  // 如果有xri 则判定为前端有NGINX等代理
                $clientAddress = $xri[0];
            } elseif (!empty($xff)) {  // 如果不存在xri 则继续判断xff
                $list = explode(',', $xff[0]);
                if (isset($list[0])) {
                    $clientAddress = $list[0];
                }
            }
        }
        return $clientAddress;
    }

}
