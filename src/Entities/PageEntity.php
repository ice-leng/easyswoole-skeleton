<?php

namespace EasySwoole\Skeleton\Entities;

use EasySwoole\Skeleton\BaseObject;

class PageEntity extends BaseObject
{
    /**
     * 分页
     * @var int
     */
    private $page = 1;

    /**
     * 每页
     * @var int
     */
    private $pageSize = 10;

    /**
     * @param int $page
     *
     * @return self
     */
    public function setPage(int $page): self
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $pageSize
     *
     * @return self
     */
    public function setPageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }
}
