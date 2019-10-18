<?php


namespace Flexor\CMSPageTie\Api;


interface TieManagementInterface
{
    /**
     * Get URL key for linked CMS page by targeted store view id
     *
     * @param $currentPageId
     * @param $targetStoreViewId
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
     * @param $storeViewId
     * @return int | null
     */
    public function getLinkedCmsIdByStoreId($currentPageId, $storeId);

    /**
     * Updates the current CMS links
     *
     * @param $linksArray ['current_page_id' => ['store_view_id' => 'cms_page_id']]
     * @return mixed
     */
    public function updateCmsLinks($linksArray);
}
