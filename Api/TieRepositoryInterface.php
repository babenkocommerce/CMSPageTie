<?php
namespace Flexor\CMSPageTie\Api;

/**
 * Interface TieRepository
 * @package Flexor\CMSPageTie\Api
 * @api
 */
interface TieRepositoryInterface
{
    /**
     * @param $relations
     * @return \Flexor\CMSPageTie\Api\Data\TieInterface
     */
    public function add($relations);

    /**
     * @param $relations
     * @return \Flexor\CMSPageTie\Api\Data\TieInterface
     */
    public function remove($relations);

    /**
     * @param int $currentPageId
     * @return mixed
     */
    public function get($currentPageId);
}
