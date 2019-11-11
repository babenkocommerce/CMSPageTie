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
        foreach($attachedStores as $attachedStore) {
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
        $existingLinks = $this->tieRepository->get($currentPageId);
        $attachedStores = $this->cmsPageModel->lookupStoreIds($currentPageId);

        $currentPageArray = [];
        foreach ($attachedStores as $attachedStore) {
            $currentPageArray[] = ['store_id' => (int) $attachedStore, 'page_id' => $currentPageId];
        }

        $compareLinks = $this->compareLinks($linksArray, $existingLinks);
        list($linksToInsert, $linksToDelete) = $compareLinks;

        if ($linksToDelete) {
            $this->tieRepository->remove($linksToDelete);
        }

        if ($linksToInsert) {
            $createdRelations = [];
            foreach ($linksToInsert as $storeId => $linkedCmsPageId) {
                $createdRelations = $this->addTieRelations($currentPageArray, $storeId, $linkedCmsPageId, $linksArray, $createdRelations);
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
        foreach ($currentPageArray as $currentPageArrays) {
            $valuesToAdd = [
                [
                    'page_id' => $currentPageArrays['page_id'],
                    'linked_page_id' => $pageId,
                    'store_id' => $storeId
                ]
            ];
            $valuesToAdd[] = [
                'page_id' => $pageId,
                'linked_page_id' => $currentPageArrays['page_id'],
                'store_id' => $currentPageArrays['store_id']
            ];
            foreach ($linksArray as $cmsStoreId => $linkedCmsPageId) {
                if ($pageId != $linkedCmsPageId) {
                    $valuesToAdd = [
                        [
                            'page_id' => (int) $pageId,
                            'linked_page_id' => (int) $linkedCmsPageId,
                            'store_id' => (int) $cmsStoreId
                        ],
                        [
                            'page_id' => (int) $linkedCmsPageId,
                            'linked_page_id' => (int) $pageId,
                            'store_id' => (int) $storeId
                        ]
                    ];
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
     * Compare values to avoid duplication
     *
     * @param $valueToAdd
     * @param $results
     * @return bool
     */
    private function compareResult($valueToAdd, $results)
    {
        $result = true;
        foreach ($results as $row)
        {
            if ($row == $valueToAdd) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Compare links, which data to delete, which to insert
     *
     * @param $frontData
     * @param $dbArray
     * @return array
     */
    private function compareLinks($frontData, $dbArray)
    {
        $linksToInsert = $frontData;
        $linksToDelete = $frontData;

        foreach($dbArray as $oldLink) {
            if (isset($frontData[$oldLink['store_id']]) && $oldLink['store_id']) {
                unset($linksToInsert[$oldLink['store_id']]);
                unset($linksToDelete[$oldLink['store_id']]);
            } else {
                $linksToDelete[] = [$oldLink['linked_page_id']];
            }
        }
        return [$linksToInsert, array_values($linksToDelete)];
    }

    /**
     * @param $currentPageId
     * @param $linksArray
     * @return mixed|void
     */
    public function updateCmsLinks($currentPageId, $linksArray)
    {
        // TODO: Implement updateCmsLinks() method.
    }
}
