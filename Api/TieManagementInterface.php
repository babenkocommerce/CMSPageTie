<?php
namespace Flexor\CMSPageTie\Api;

/**
 * Interface TieManagementInterface
 * @package Flexor\CMSPageTie\Api
 */
interface TieManagementInterface
{
    /**
     * Retrieves the linked CMS pages array by current page - see example format in return
     *
     * @param $currentPageId
     * @param $storeId
     * @param bool $withCurrentPage - add current page node to result
     * @return array - ['store_view_code' => 'linked_page_url']
     */
    public function getLinkedPageKeys($currentPageId, $storeId, $withCurrentPage = false);
}
