<?php

namespace Flexor\CMSPageTie\Api;

use Flexor\CMSPageTie\Api\Data\TieInterface;

/**
 * Interface TieRepository
 *
 * @api
 */
interface TieRepositoryInterface
{
    /**
     * @param $relations
     * @return TieInterface
     */
    public function add($relations);

    /**
     * @param $pageIds
     * @return TieInterface
     */
    public function remove($pageIds);

    /**
     * @param int $currentPageId
     * @return mixed
     */
    public function get($currentPageId);
}
