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
     * @param int $currentPageId
     * @param int $linkedPageId
     * @param int $storeId
     * @return \Flexor\CMSPageTie\Api\Data\TieInterface
     */
    public function add($currentPageId, $linkedPageId, $storeId);

    /**
     * @param int $currentPageId
     * @param int $linkedPageId
     * @param int $storeId
     * @return \Flexor\CMSPageTie\Api\Data\TieInterface
     */
    public function update($currentPageId, $linkedPageId, $storeId);

    /**
     * @param int $currentPageId
     * @return \Flexor\CMSPageTie\Api\Data\TieInterface
     */
    public function remove($currentPageId);

    /**
     * @param int $currentPageId
     * @return mixed
     */
    public function get($currentPageId);
}
