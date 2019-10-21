<?php

namespace Flexor\CMSPageTie\Model;

use Magento\Framework\Exception\NoSuchEntityException;

class TieManagement implements \Flexor\CMSPageTie\Api\TieManagementInterface
{
    private $pageRepository;
    /**
     * @var Tie
     */
    private $tieModelFactory;


    public function __construct(
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Flexor\CMSPageTie\Model\TieFactory $tieModelFactory
    ) {
        $this->pageRepository = $pageRepository;
        $this->tieModelFactory = $tieModelFactory;
    }

    /**
     * Get URL key for linked CMS page by targeted store view id
     *
     * @param $currentPageId
     * @param $targetStoreId
     * @return string
     */
    public function getLinkedCmsKey($currentPageId, $targetStoreId)
    {
        $noResult = '';
        try {
            $linkedPageId = $this->getLinkedCmsIdByStoreId($currentPageId, $targetStoreId);
            $page = $this->pageRepository->getById($linkedPageId);
            return $page->getIdentifier();
        } catch (\Exception $e) {
            return $noResult;
        }
    }

    /**
     * Get linked CMS array in next format
     *  [
     *      'store_view_id' => 'linked_cms_page_id'
     *  ]
     *
     * @param $currentPageId
     * @return array
     */
    public function getLinkedCmsArray($currentPageId)
    {
        $linkedCmsFactory = $this->tieModelFactory->create();
        $linkedCms = $linkedCmsFactory->get($currentPageId);
        $resultArray = [$linkedCms[0]['store_id'] => (int) $linkedCms[0]['linked_page_id']];
        return $resultArray;
    }

    /**
     * Get Linked CMS id for current page and store view
     *
     * @param $currentPageId
     * @param $storeId
     * @return bool|int
     */
    public function getLinkedCmsIdByStoreId($currentPageId, $storeId)
    {
        $linkedPageIdFactory = $this->tieModelFactory->create();
        $linkedPageId = $linkedPageIdFactory->getLinkedPageId($currentPageId, $storeId);
        if (isset($linkedPageId)) {
            return $linkedPageId;
        }
    }

    /**
     * Updates the current CMS links
     *
     * @param $linksArray ['current_page_id' => ['store_view_id' => 'cms_page_id']]
     * @return mixed
     */
    public function updateCmsLinks($linksArray)
    {
        $UpdateCmsLinksModelFactory = $this->tieModelFactory->create();
        foreach ($linksArray as $key => $value) {
            $currentPageId = $key;
            foreach ($value as $keys => $values) {
                $linkedPageId = $keys;
                $storeId = (int) $values;
            }
        }
        return $UpdateCmsLinksModelFactory->update($currentPageId, $linkedPageId, $storeId);
    }
}
