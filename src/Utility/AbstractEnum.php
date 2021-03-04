<?php

namespace EasySwoole\Skeleton\Utility;

use EasySwoole\Skeleton\Helpers\Arrays\ArrayHelper;
use MabeEnum\Enum;
use MabeEnum\EnumSerializableTrait;
use Roave\BetterReflection\BetterReflection;
use Serializable;

class AbstractEnum extends Enum implements Serializable
{
    use EnumSerializableTrait;

    /**
     * @param string $doc
     * @param array  $previous
     *
     * @return array
     */
    protected function parse(string $doc, array $previous = [])
    {
        $pattern = '/\\@(\\w+)\\(\\"(.+)\\"\\)/U';
        if (preg_match_all($pattern, $doc, $result)) {
            if (isset($result[1], $result[2])) {
                $keys = $result[1];
                $values = $result[2];

                foreach ($keys as $i => $key) {
                    if (isset($values[$i])) {
                        $previous[strtolower($key)] = $values[$i];
                    }
                }
            }
        }
        return $previous;
    }

    /**
     * 获得
     *
     * @param array $replace
     *
     * @return string
     */
    public function getMessage(array $replace = []): string
    {
        $className = get_called_class();
        $classInfo = (new BetterReflection())->classReflector()->reflect($className);
        $constant = $classInfo->getReflectionConstant($this->getName());
        if ($constant === null) {
            return '';
        }
        $constantDocComment = $constant->getDocComment();
        $message = ArrayHelper::getValue($this->parse($constantDocComment), 'message', '');
        return strtr($message, $replace);
    }

    /**
     * map
     * @return array
     */
    public static function getMapJson()
    {
        $data = [];
        $values = static::getValues();
        foreach ($values as $value) {
            $data[] = [
                'key'   => $value,
                'value' => static::byValue($value)->getMessage(),
            ];
        }
        return $data;
    }

}
