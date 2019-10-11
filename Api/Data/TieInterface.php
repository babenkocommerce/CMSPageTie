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
     * @param int $currentPageId
     * @param int $linkedPageId
     * @param int $storeId
     * @return $this
     */
    public function add($currentPageId, $linkedPageId, $storeId);

    /**
     * @param int $currentPageId
     * @param int $linkedPageId
     * @param int $storeId
     * @return $this
     */
    public function update($currentPageId, $linkedPageId, $storeId);

    /**
     * @param int $currentPageId
     * @return $this
     */
    public function remove($currentPageId);

    /**
     * @param int $currentPageId
     * @return mixed
     */
    public function get($currentPageId);
}
