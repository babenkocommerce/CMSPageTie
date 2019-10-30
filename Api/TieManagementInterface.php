<?php
namespace Flexor\CMSPageTie\Api;

/**
 * Interface TieManagementInterface
 * @package Flexor\CMSPageTie\Api
 * @api
 */
interface TieManagementInterface
{
    /**
     * Updates the current CMS links
     *
     * @param $currentPageId
     * @param $linksArray ['store_id' => 'linked_page_id']
     * @return mixed
     */
    public function updateCmsLinks($currentPageId, $linksArray);

    /**
     * Retrieves the linked CMS pages array by current page - see example format in return
     *
     * @param $currentPageId
     * @param $storeId
     * @param bool $withCurrentPage add current page node to result
     * @return array ['store_code' => 'linked_page_url']
     */
    public function getLinkedPageKeys($currentPageId, $storeId, $withCurrentPage = false);
}
