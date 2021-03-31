<?php

namespace EasySwoole\Skeleton\Component\Sort;

use EasySwoole\Skeleton\BaseObject;
use EasySwoole\Skeleton\Helpers\Arrays\ArrayHelper;
use EasySwoole\Skeleton\Helpers\StringHelper;

/**
 * Class Sort
 *
 * $sort = [
 *     'attributes' = [
 *          'id',
 *          'name' => [
 *              'asc' => ['name' => SORT_ASC],
 *              'desc' => ['name' => SORT_DESC],
 *          ]
 *     ],
 * ];
 */
class Sort extends BaseObject
{

    public function __construct(array $config = [])
    {
        $attributes = ArrayHelper::get($config, 'attributes', []);
        if (!empty($attributes)) {
            $config['attributes'] = $this->initAttribute($attributes);
        }
        parent::__construct($config);
    }

    /**
     * 属性 初始化
     *
     * @param array $attributes
     *
     * @return array
     */
    private function initAttribute(array $attributes): array
    {
        $data = [];
        foreach ($attributes as $name => $attribute) {
            if ($attribute instanceof SortAttribute) {
                $data[$name] = $attribute;
            } elseif (is_string($attribute)) {
                $data[$attribute] = self::generateSortAttribute($attribute, SORT_DESC);
            } elseif (is_array($attribute) && !empty($attribute['asc']) && !empty($attribute['desc'])) {
                $data[$name] = new SortAttribute($attribute);
            }
        }
        return $data;
    }

    /**
     * 是否支持 多个排序
     */
    private $enableMultiSort = false;

    /**
     * @var SortAttribute[]
     */
    private $attributes = [];

    /**
     * [name => SORT_ASC]
     * @var array
     */
    private $defaultOrder = [];

    /**
     * @var string
     */
    private $separator = ',';

    /**
     * eg: name,-age
     *
     * @var string
     */
    private $sort;

    /**
     *
     * @var string[]
     */
    private $direction = [
        SORT_ASC  => 'ASC',
        SORT_DESC => 'DESC',
    ];

    /**
     * @param bool $enableMultiSort
     *
     * @return $this
     */
    public function setEnableMultiSort(bool $enableMultiSort): self
    {
        $this->enableMultiSort = $enableMultiSort;
        return $this;
    }

    /**
     * @return bool
     */
    public function getEnableMultiSort(): bool
    {
        return $this->enableMultiSort;
    }

    /**
     * @param string $separator
     *
     * @return $this
     */
    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * @param string $sort
     *
     * @return $this
     */
    public function setSort(string $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return string
     */
    public function getSort(): ?string
    {
        return $this->sort;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return array|SortAttribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array
     */
    public function getDefaultOrder(): array
    {
        return $this->defaultOrder;
    }

    /**
     * @param array $defaultOrder
     *
     * @return Sort
     */
    public function setDefaultOrder(array $defaultOrder): Sort
    {
        $this->defaultOrder = $defaultOrder;
        return $this;
    }

    /**
     * 获得排序
     * @return array
     */
    protected function getAttributeOrders(): array
    {
        $attributeOrders = [];
        $attributes = $this->getAttributes();

        if (!StringHelper::isEmpty($this->getSort())) {
            foreach (explode($this->getSeparator(), $this->getSort()) as $attribute) {
                $descending = false;
                if (strncmp($attribute, '-', 1) === 0) {
                    $descending = true;
                    $attribute = substr($attribute, 1);
                }

                if (isset($attributes[$attribute])) {
                    $attributeOrders[$attribute] = $descending ? SORT_DESC : SORT_ASC;
                    if (!$this->getEnableMultiSort()) {
                        return $attributeOrders;
                    }
                }
            }
        }

        if (empty($attributeOrders) && !empty($this->getDefaultOrder())) {
            $attributeOrders = $this->getDefaultOrder();
        }

        return $attributeOrders;

    }

    /**
     * 获得 排序
     *
     * @return array
     */
    public function getOrders(): array
    {

        $attributeOrders = $this->getAttributeOrders();
        $attributes = $this->getAttributes();
        $orders = [];
        foreach ($attributeOrders as $attribute => $direction) {
            $definition = $attributes[$attribute];
            $columns = $direction === SORT_ASC ? $definition->getAsc() : $definition->getDesc();
            if (is_array($columns)) {
                foreach ($columns as $name => $dir) {
                    $orders[$name] = $this->direction[$dir];
                }
            } else {
                $orders[] = $columns;
            }
        }

        return $orders;
    }

    /**
     * 生成
     *
     * @param string $attribute
     * @param int    $default
     *
     * @return SortAttribute
     */
    public static function generateSortAttribute(string $attribute, int $default = SORT_ASC): SortAttribute
    {
        $data = [
            'asc'     => [$attribute => SORT_ASC],
            'desc'    => [$attribute => SORT_DESC],
            'default' => $default,
        ];
        return new SortAttribute($data);
    }

    /**
     * sql
     */
    public function buildSql(): string
    {
        $orders = [];
        foreach ($this->getOrders() as $name => $direction) {
            $orders[] = $name . ' ' . $direction;
        }

        return implode(', ', $orders);
    }
}
