<?php
namespace Flexor\CMSPageTie\Model;

use Magento\Cms\Helper\Page as PageHelper;
use Magento\Framework\Locale\Resolver as LocaleResolver;
use Magento\Cms\Api\PageRepositoryInterface as PageRepository;
use Magento\Cms\Model\ResourceModel\Page as CmsPageModel;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Framework\UrlInterface as UrlInterface;

/**
 * Class TieManagement
 * @package Flexor\CMSPageTie\Model
 */
class TieManagement implements \Flexor\CMSPageTie\Api\TieManagementInterface
{
    /**
     * @var PageHelper
     */
    private $pageHelper;

    /**
     * @var LocaleResolver
     */
    private $localeResolver;

    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var TieRepository
     */
    private $tieRepository;

    /**
     * @var CmsPageModel
     */
    private $cmsPageModel;

    /**
     * @var ScopeConfig
     */
    private $scopeConfig;

    /**
     * @var UrlInterface
     */
    private $urlInterface;

    /**
     * TieManagement constructor.
     *
     * @param PageHelper $pageHelper
     * @param LocaleResolver $localeResolver
     * @param PageRepository $pageRepository
     * @param TieRepository $tieRepository
     * @param CmsPageModel $cmsPageModel
     * @param ScopeConfig $scopeConfig
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        PageHelper $pageHelper,
        LocaleResolver $localeResolver,
        PageRepository $pageRepository,
        TieRepository $tieRepository,
        CmsPageModel $cmsPageModel,
        ScopeConfig $scopeConfig,
        UrlInterface $urlInterface
    ) {
        $this->localeResolver = $localeResolver;
        $this->pageHelper = $pageHelper;
        $this->pageRepository = $pageRepository;
        $this->tieRepository = $tieRepository;
        $this->cmsPageModel = $cmsPageModel;
        $this->scopeConfig = $scopeConfig;
        $this->urlInterface = $urlInterface;
    }

    /**
     * Retrieves the linked CMS pages array by current page
     *
     * @param $currentPageId
     * @param $storeId
     * @param bool $withCurrentPage
     * @return array
     */
    public function getLinkedPageKeys($currentPageId, $storeId, $withCurrentPage = false)
    {
        $attachedStores = $this->cmsPageModel->lookupStoreIds($currentPageId);
        $locale = [];
        foreach ($attachedStores as $attachedStore) {
            $addLocaleUrl = true;
            foreach ($locale as $existing) {
                if ($this->pageHelper->getPageUrl($currentPageId) == $existing) {
                    $addLocaleUrl = false;
                }
            }
            if ($addLocaleUrl) {
                $locale[] = [
                    $this->scopeConfig->getValue(
                        'general/locale/code',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $attachedStore
                    ) => $this->pageHelper->getPageUrl($currentPageId)
                ];
            }
        }
        if ($withCurrentPage) {
            $locale[$this->localeResolver->getLocale()] = $this->urlInterface->getCurrentUrl();
        }
        if (count($locale) <= 1) {
            $locale = [];
        }
        return $locale;
    }

    /**
     * Updates the current CMS links
     *
     * @param $currentPageId
     * @param $linksArray ['store_id' => 'cms_page_id']
     * @return mixed
     */
    public function updateCmsLinks($currentPageId, $linksArray)
    {
        $currentPageInfo = [
            'stores' => $this->cmsPageModel->lookupStoreIds($currentPageId),
            'page_id' => (int) $currentPageId
        ];

        $linksToDelete = array_values($linksArray);
        $linksToDelete[] = $currentPageId;
        $this->tieRepository->remove($linksToDelete);

        if (count($linksArray)) {
            $createdRelations = [];
            foreach ($linksArray as $storeId => $linkedCmsPageId) {
                $createdRelations = $this->addTieRelations(
                    $currentPageInfo,
                    $storeId,
                    $linkedCmsPageId,
                    $linksArray,
                    $createdRelations
                );
            }
            $links = $this->tieRepository->add($createdRelations);
            return $links;
        }
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
        try {
            $linkedPageId = $this->getLinkedCmsIdByStoreId($currentPageId, $targetStoreId);
            $page = $this->pageRepository->getById($linkedPageId);
            $result = $page->getIdentifier();
        } catch (\Exception $e) {
            $result = "";
        }
        return $result;
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
        $resultArray = [];
        $existingLinks = $this->tieRepository->get($currentPageId);
        foreach ($existingLinks as $existingLink) {
            $resultArray[] = [$existingLink['store_id'] => (int) $existingLink['linked_page_id']];
        }
        return $resultArray;
    }

    /**
     * Get Linked CMS id for current page and store view
     *
     * @param $currentPageId
     * @param $storeId
     * @return int | null
     */
    public function getLinkedCmsIdByStoreId($currentPageId, $storeId)
    {
        $linkedPageId = $this->tieRepository->getLinkedPageId($currentPageId, $storeId);
        if (isset($linkedPageId)) {
            $result = $linkedPageId;
        } else {
            $result = null;
        }
        return $result;
    }

    /**
     * Add relations between Cms pages
     *
     * @param $currentPageArray
     * @param $storeId
     * @param $pageId
     * @param array $linksArray
     * @param array $result
     * @return array
     */
    private function addTieRelations($currentPageArray, $storeId, $pageId, $linksArray, $result = [])
    {
        if ($pageId) {
            $valuesToAdd = [$this->populateTieArray($currentPageArray['page_id'], $pageId, $storeId)];
            foreach ($currentPageArray['stores'] as $currentPageStoreId) {
                $valuesToAdd[] = $this->populateTieArray($pageId, $currentPageArray['page_id'], $currentPageStoreId);
            }
            foreach ($linksArray as $cmsStoreId => $linkedCmsPageId) {
                if ($cmsStoreId != $storeId) {
                    $valuesToAdd[] = $this->populateTieArray($pageId, $linkedCmsPageId, $cmsStoreId);
                    $valuesToAdd[] = $this->populateTieArray($linkedCmsPageId, $pageId, $storeId);
                }
            }
            foreach ($valuesToAdd as $newTie) {
                if ($this->compareResult($newTie, $result)) {
                    $result[] = $newTie;
                }
            }
        }
        return $result;
    }

    /**
     * Populate formatted Tie array from data
     *
     * @param $pageId
     * @param $linkedPageId
     * @param $storeId
     * @return array
     */
    private function populateTieArray($pageId, $linkedPageId, $storeId)
    {
        return [
            'page_id' => (int) $pageId,
            'linked_page_id' => (int) $linkedPageId,
            'store_id' => (int) $storeId,
        ];
    }

    /**
     * Compare values to avoid duplication
     *
     * @param $valueToAdd
     * @param $rows
     * @return bool
     */
    private function compareResult($valueToAdd, $rows)
    {
        $result = true;
        foreach ($rows as $row) {
            if ($row == $valueToAdd) {
                $result = false;
            }
        }
        return $result;
    }
}
