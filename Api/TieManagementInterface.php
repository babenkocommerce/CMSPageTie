<?php

namespace Flexor\CMSPageTie\Api;

/**
 * Interface TieManagementInterface
 *
 * @api
 */
interface TieManagementInterface
{
    /**
     * Get URL key for linked CMS page by targeted store view id
     *
     * @param $currentPageId
     * @param $targetStoreId
     * @return string
     */
    public function getLinkedCmsKey($currentPageId, $targetStoreId);

    /**
     * Get linked CMS array in  next format
     *  [
     *      'store_view_id' => 'linked_cms_page_id'
     *  ]
     *
     * @param $currentPageId
     * @return array
     */
    public function getLinkedCmsArray($currentPageId);

    /**
     * Get Linked CMS id for current page and store view
     *
     * @param $currentPageId
     * @param $storeId
     * @return int | null
     */
    public function getLinkedCmsIdByStoreId($currentPageId, $storeId);

    /**
     * Updates the current CMS links
     *
     * @param $currentPageId
     * @param $linksArray ['store_id' => 'cms_page_id']
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
    public function getLinkedPageKeys($currentPageId, $storeId, $withCurrentPage = true);
}
