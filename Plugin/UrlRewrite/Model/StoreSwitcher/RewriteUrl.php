<?php

namespace Flexor\CMSPageTie\Plugin\UrlRewrite\Model\StoreSwitcher;

use Magento\Framework\HTTP\PhpEnvironment\RequestFactory;
use Flexor\CMSPageTie\Api\TieManagementInterface;
use Magento\Framework\UrlInterface as UrlBuilder;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
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
     * @var UrlRewriteCollectionFactory
     */
    private $urlRewriteCollectionFactory;

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
     * @param UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     * @param UrlBuilder $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        RequestFactory $requestFactory,
        TieManagementInterface $tieManagement,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        UrlBuilder $urlBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->requestFactory = $requestFactory;
        $this->tieManagement = $tieManagement;
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
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
        $targetStoreId = $oldRewrite->getStoreId();
        $request = $this->requestFactory->create(['uri' => $targetUrl]);
        $urlPath = ltrim($request->getPathInfo(), '/');
        $isStoreCodeEnabled = $this->scopeConfig->getValue(
            'web/url/use_store',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($isStoreCodeEnabled) {
            $urlPath = substr($urlPath, strrpos($urlPath, "/") + 1);
        }

        $urlArray = [];
        $lastRecord = false;
        foreach (explode('/', $urlPath) as $urlPart) {
            $lastRecord = $lastRecord ? $lastRecord . '/' . $urlPart : $urlPart;
            array_unshift($urlArray, $lastRecord);
        }

        $urlRewriteData = $this->urlRewriteCollectionFactory->create()
            ->addFieldToFilter('store_id', $targetStore->getStoreId())
            ->addFieldToFilter('entity_type', ['custom', 'cms-page'])
            ->getData();

        foreach ($urlArray as $url) {
            do {
                preg_match('/(?<=^cms\/page\/view\/page_id\/)\d+(?=$)/', $url, $matches);
                if ($matches) {
                    $currentPageIdentifier = (int)$matches[0];
                } elseif ($urlRewriteDataKeys = array_keys(array_column($urlRewriteData, 'request_path'), $url)) {
                    $url = array_column($urlRewriteData, 'target_path')[$urlRewriteDataKeys[0]];
                } else {
                    break;
                }
            } while (!isset($currentPageIdentifier));

            if (isset($currentPageIdentifier)) {
                $linkedPageName = $this->tieManagement->getLinkedCmsKey($currentPageIdentifier, $targetStoreId);
                $linkedPageId = $this->tieManagement->getLinkedCmsIdByStoreId($currentPageIdentifier, $targetStoreId);

                if (isset($linkedPageId) && $linkedPageId != 0) {
                    $this->urlBuilder->setScope($targetStoreId);
                    $result = $this->urlBuilder->getUrl(null, ['_direct' => $linkedPageName]);
                }
                break;
            }
        }

        return $result;
    }
}
