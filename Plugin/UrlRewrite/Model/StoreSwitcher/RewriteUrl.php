<?php

namespace Flexor\CMSPageTie\Plugin\UrlRewrite\Model\StoreSwitcher;

use Magento\Framework\HTTP\PhpEnvironment\RequestFactory;
use Flexor\CMSPageTie\Api\TieManagementInterface;
use Magento\Framework\UrlInterface as UrlBuilder;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollection;
use Magento\UrlRewrite\Model\StoreSwitcher\RewriteUrl as StoreRewriteUrl;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class RewriteUrl - plugin to process the linking functionality on store switch
 */
class RewriteUrl
{
    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var TieManagementInterface
     */
    private $tieManagement;

    /**
     * @var CmsPageCollection
     */
    private $cmsPageCollection;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * RewriteUrl constructor.
     *
     * @param RequestFactory $requestFactory
     * @param TieManagementInterface $tieManagement
     * @param CmsPageCollection $cmsPageCollection
     * @param UrlBuilder $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        RequestFactory $requestFactory,
        TieManagementInterface $tieManagement,
        CmsPageCollection $cmsPageCollection,
        UrlBuilder $urlBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->requestFactory = $requestFactory;
        $this->tieManagement = $tieManagement;
        $this->cmsPageCollection = $cmsPageCollection;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param StoreRewriteUrl $subject
     * @param $result
     * @param $targetStore
     * @param $oldRewrite
     * @param $targetUrl
     * @return mixed
     */
    public function afterSwitch(StoreRewriteUrl $subject, $result, $targetStore, $oldRewrite, $targetUrl)
    {
        $targetStoreId = $oldRewrite->getData()['store_id'];
        $request = $this->requestFactory->create(['uri' => $targetUrl]);
        $urlPath = ltrim($request->getPathInfo(), '/');
        $isStoreCodeEnabled = $this->scopeConfig->getValue(
            'web/url/use_store',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($isStoreCodeEnabled) {
            $urlPath = substr($urlPath, strrpos($urlPath, "/") + 1);
        }
        $currentPageCollection = $this->cmsPageCollection->create()->addFieldToFilter('identifier', $urlPath);
        $currentPage = $currentPageCollection->getData();
        if (!empty($currentPage)) {
            $currentPageIdentifier = (int) array_column($currentPage, 'page_id')[0];
            $linkedPageName = $this->tieManagement->getLinkedCmsKey($currentPageIdentifier, $targetStoreId);
            $linkedPageId = $this->tieManagement->getLinkedCmsIdByStoreId($currentPageIdentifier, $targetStoreId);

            if (isset($linkedPageId) && $linkedPageId != 0) {
                $this->urlBuilder->setScope($targetStoreId);
                $result = $this->urlBuilder->getUrl(null, ['_direct' => $linkedPageName]);
            }
        }
        return $result;
    }
}
