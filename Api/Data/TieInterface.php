<?php
namespace Flexor\CMSPageTie\Api\Data;

/**
 * Interface TieInterface
 * @package Flexor\CMSPageTie\Api\Data
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
