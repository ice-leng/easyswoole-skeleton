<?php

namespace EasySwoole\Skeleton\Component\Sort;

use EasySwoole\Skeleton\BaseObject;

/**
 * Class Sort
 *
 * $sort = [
 *      [
 *          'asc' => ['name' => SORT_ASC],
 *          'desc' => ['name' => SORT_DESC],
 *      ];
 * ];
 *
 * // demo2
 * [
 *     [
 *         'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
 *         'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
 *     ],
 * ]
 *
 */
class SortAttribute extends BaseObject
{
    /**
     * @var array
     */
    private $asc = [];

    /**
     * @var array
     */
    private $desc = [];

    /**
     * @param array $asc
     *
     * @return $this
     */
    public function setAsc(array $asc): self
    {
        $this->asc = $asc;
        return $this;
    }

    /**
     * @return array
     */
    public function getAsc(): array
    {
        return $this->asc;
    }

    /**
     * @param array $desc
     *
     * @return $this
     */
    public function setDesc(array $desc): self
    {
        $this->desc = $desc;
        return $this;
    }

    /**
     * @return array
     */
    public function getDesc(): array
    {
        return $this->desc;
    }
}
