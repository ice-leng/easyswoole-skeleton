<?php

namespace EasySwoole\Skeleton\Framework;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\HyperfOrm\Db;
use EasySwoole\Skeleton\Helpers\RegularHelper;
use EasySwoole\Skeleton\Helpers\StringHelper;
use EasySwoole\Skeleton\Component\Sort\Sort;
use EasySwoole\Skeleton\Entities\PageEntity;
use EasySwoole\Skeleton\Framework\Exception\MethodNotImplException;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Query\Expression;

abstract class BaseService
{

    /**
     * page
     *
     * @param Builder    $query
     * @param PageEntity $pageEntity
     *
     * @return array
     */
    public function page(Builder $query, PageEntity $pageEntity): array
    {
        $pageSize = $pageEntity->getPageSize();
        $total = Db::selectOne("select count(*) as count from ({$query->toSql()}) as b", $query->getBindings())->count;
        $list = $query->forPage($pageEntity->getPage(), $pageSize)->get()->toArray();
        return [
            'list'     => $list,
            'page'     => $pageEntity->getPage(),
            'pageSize' => $pageSize,
            'total'    => $total,
        ];
    }

    /**
     * page
     *
     * @param array      $params
     * @param PageEntity $pageEntity
     *
     * @return array
     */
    public function pageByArray(array $params, PageEntity $pageEntity): array
    {
        $total = count($params);
        $pageSize = $pageEntity->getPageSize();
        $offset = ($pageEntity->getPage() - 1) * $pageSize;
        $params = array_values($params);
        $list = array_slice($params, $offset, $pageSize);
        return [
            'list'     => $list,
            'page'     => $pageEntity->getPage(),
            'pageSize' => $pageSize,
            'total'    => $total,
        ];
    }

    /**
     * @param string $attribute
     * @param int    $direction
     *
     * @return Sort
     */
    protected function getDefaultSort(string $attribute = 'create_at', int $direction = SORT_DESC): Sort
    {
        return new Sort([
            'attributes'   => [
                $attribute,
            ],
            'defaultOrder' => [$attribute => $direction],
        ]);
    }

    /**
     * order by
     *
     * @param Builder   $query
     * @param Sort|null $sort
     */
    public function orderBy(Builder $query, ?Sort $sort = null): void
    {
        $sort = $sort ?? $this->getDefaultSort();
        foreach ($sort->getOrders() as $column => $direction) {
            $query->orderBy($column, $direction);
        }
    }

    /**
     * @param array    $data
     * @param callable $call
     *
     * @return array
     */
    public function toArray(array $data, callable $call): array
    {
        $item = [];
        $results = isset($data['list']) ? $data['list'] : $data;
        foreach ($results as $result) {
            $content = call_user_func($call, $result);
            if (is_null($content)) {
                continue;
            }
            $item[] = $content;
        }
        if (isset($data['list'])) {
            $data['list'] = $item;
        } else {
            $data = $item;
        }
        return $data;
    }

    /**
     * list
     *
     * @param array           $params
     * @param array           $field
     * @param PageEntity|null $pageEntity
     *
     * @return mixed
     */
    public function getList(array $params = [], array $field = ['*'], ?PageEntity $pageEntity = null): array
    {
        throw new MethodNotImplException();
    }

    /**
     * create
     *
     * @param array $params
     *
     * @return mixed
     */

    public function create(array $params): array
    {
        throw new MethodNotImplException();
    }

    /**
     * update
     *
     * @param array $params
     *
     * @return mixed
     */

    public function update(array $params): array
    {
        throw new MethodNotImplException();
    }

    /**
     * detail
     *
     * @param array $params
     * @param array $field
     *
     * @return mixed
     */
    public function detail(array $params, array $field = ['*']): array
    {
        throw new MethodNotImplException();
    }

    /**
     * delete
     *
     * @param array $params
     *
     * @return mixed
     */
    public function remove(array $params): int
    {
        throw new MethodNotImplException();
    }

    /**
     * 图片地址
     *
     * @param string $path
     * @param string $config
     *
     * @return string
     */
    public function imageUrl(string $path, string $config = 'app.image_url'): string
    {
        if (StringHelper::isEmpty($path)) {
            return $path;
        }
        if (!RegularHelper::isInvalidUrl($path)) {
            return $path;
        }

        $bucket = config('thirdparty.local.bucket', '/public/upload');
        $localPath = EASYSWOOLE_ROOT . $bucket;
        if (is_file($localPath . $path)) {
            $local = config('thirdparty.local.url_name', 'localhost');
            $url = ContextManager::getInstance()->get($local) ?? '';
            return $url . $bucket . $path;
        }

        $config = config($config);

        if (is_callable($config)) {
            return call_user_func($config, $path);
        }

        if (RegularHelper::isInvalidUrl($config)) {
            $config = config($config);
        }
        return $config . $path;
    }
}
