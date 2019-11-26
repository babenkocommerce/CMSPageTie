<?php

namespace Flexor\CMSPageTie\Api\Data;

/**
 * Interface TieInterface
 *
 * @api
 */
interface TieInterface
{
    /**
     * @param $relations
     * @return $this
     */
    public function add($relations);

    /**
     * @param $pageIds
     * @return $this
     */
    public function remove($pageIds);

    /**
     * @param int $currentPageId
     * @return mixed
     */
    public function get($currentPageId);
}
