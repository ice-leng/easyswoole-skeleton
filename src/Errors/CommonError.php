<?php
declare(strict_types=1);

namespace EasySwoole\Skeleton\Errors;

use EasySwoole\Skeleton\Framework\BaseEnum;

class CommonError extends BaseEnum
{
    /**
     * @Message("成功")
     */
    const SUCCESS = '0';

    /**
     * @Message("系统错误，稍后刷新重试")
     */
    const SERVER_ERROR = 'F-000-000-500';

    /**
     * @Message("错误的请求参数")
     */
    const INVALID_PARAMS = 'F-000-000-400';

    /**
     * @Message("请重新登录")
     */
    const INVALID_TOKEN = 'F-000-000-413';

    /**
     * @Message("请重新登录")
     */
    const TOKEN_EXPIRED = 'F-000-000-403';

    /**
     * @Message("未找到")
     */
    const SERVER_NOT_FOUND = 'F-000-000-404';

    /**
     * @Message("没权限")
     */
    const SERVER_NOT_PERMISSION = 'F-000-000-401';
}
