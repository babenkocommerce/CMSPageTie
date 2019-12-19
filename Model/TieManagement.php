<?php

namespace Flexor\CMSPageTie\Model;

use Magento\Cms\Helper\Page as PageHelper;
use Magento\Framework\Locale\Resolver as LocaleResolver;
use Magento\Cms\Api\PageRepositoryInterface as PageRepository;
use Magento\Cms\Model\ResourceModel\Page as CmsPageModel;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Framework\UrlInterface as UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\System\Store as SystemStore;

/**
 * Class TieManagement - tie management
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
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SystemStore
     */
    private $systemStore;

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
     * @param StoreManagerInterface $storeManager
     * @param SystemStore $systemStore
     */
    public function __construct(
        PageHelper $pageHelper,
        LocaleResolver $localeResolver,
        PageRepository $pageRepository,
        TieRepository $tieRepository,
        CmsPageModel $cmsPageModel,
        ScopeConfig $scopeConfig,
        UrlInterface $urlInterface,
        StoreManagerInterface $storeManager,
        SystemStore $systemStore
    ) {
        $this->localeResolver = $localeResolver;
        $this->pageHelper = $pageHelper;
        $this->pageRepository = $pageRepository;
        $this->tieRepository = $tieRepository;
        $this->cmsPageModel = $cmsPageModel;
        $this->scopeConfig = $scopeConfig;
        $this->urlInterface = $urlInterface;
        $this->storeManager = $storeManager;
        $this->systemStore = $systemStore;
    }

    /**
     * Retrieves the linked CMS pages array by current page
     *
     * @param $currentPageId
     * @param $storeId
     * @param bool $withCurrentPage
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLinkedPageKeys($currentPageId, $storeId, $withCurrentPage = true)
    {
        $storeBasedLinks = $this->scopeConfig->getValue(
            'web/seo/only_store_based_links',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $currentWebsiteId = $this->urlInterface->getScope()->getWebsiteId();
        $locales = [];
        $currentPageStores = $this->pageRepository->getById($currentPageId)->getStores();
        if (!$withCurrentPage && (($key = array_search($storeId, $currentPageStores)) !== false)) {
            unset($currentPageStores[$key]);
        }
        if ($storeBasedLinks) {
            $storesByGroupIds = $this->getStoresByGroupIds($storeId);
            $linkedPages = $this->tieRepository->getPagesByStoreId($currentPageId, $storesByGroupIds);
            foreach ($currentPageStores as $key => $currentPageStore) {
                if (!in_array($currentPageStore, $storesByGroupIds)) {
                    unset($currentPageStores[$key]);
                }
            }
        } else {
            $linkedPages = $this->tieRepository->get($currentPageId);
        }
        foreach ($currentPageStores as $currentPageStore) {
            $linkedPages[] = [
                'page_id' => $currentPageId,
                'linked_page_id' => $currentPageId,
                'store_id' => $currentPageStore,
            ];
        }
        foreach ($linkedPages as $linkedPage) {
            $targetStoreId = (int)$linkedPage['store_id'];
            $linkedPageName = $this->getCmsPageUrlKey($linkedPage['linked_page_id']);
            $this->urlInterface->setScope($targetStoreId);
            if ($this->urlInterface->getScope()->getWebsiteId() === $currentWebsiteId) {
                $record = [
                    'locale' => str_replace('_', '-', $this->scopeConfig->getValue(
                        'general/locale/code',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $targetStoreId
                    )),
                    'url' => $this->urlInterface->getUrl(
                        null,
                        [
                            '_direct' => $linkedPageName,
                            'code' => $this->urlInterface->getScope()->getCode(),
                        ]
                    ),
                ];
                if (!in_array($record, $locales)) {
                    $locales[] = $record;
                }
            }
        }
        $this->urlInterface->setScope($storeId);
        return $locales;
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
            'page_id' => (int) $currentPageId,
        ];

        $existingLinks = $this->tieRepository->get($currentPageId);
        $existingLinkedPageId = [];
        foreach ($existingLinks as $existingLink) {
            $existingLinkedPageId[] = $existingLink['linked_page_id'];
        }

        $linksToDelete = array_values($linksArray);
        $linksToDelete[] = $currentPageId;
        $finalLinksToDelete = array_merge($linksToDelete, $existingLinkedPageId);
        $this->tieRepository->remove($finalLinksToDelete);

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
        return false;
    }

    /**
     * Get URL key for CMS page by page id
     *
     * @param $pageId
     * @return string
     */
    public function getCmsPageUrlKey($pageId)
    {
        try {
            $page = $this->pageRepository->getById($pageId);
            $result = $page->getIdentifier();
        } catch (\Exception $e) {
            $result = '';
        }
        return !empty($result) ? $result : '';
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
        $result = isset($linkedPageId) ? (int)$linkedPageId['linked_page_id'] : null;
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
                if ($pageId != $linkedCmsPageId && $cmsStoreId != $storeId) {
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

    /**
     * Get stores by group id
     *
     * @param $storeId
     * @return array|mixed
     */
    private function getStoresByGroupIds($storeId)
    {
        try {
            $storeGroupId = $this->storeManager->getStore($storeId)->getStoreGroupId();
            $storeCollections = $this->systemStore->getStoreCollection();
            $storesByGroupIds = [];
            foreach ($storeCollections as $store) {
                if ($store->getGroupId() === $storeGroupId) {
                    $storesByGroupIds[] = $store->getStoreId();
                }
            }
        } catch (\Exception $e) {
            $storesByGroupIds = [];
        }
        return $storesByGroupIds;
    }
}
