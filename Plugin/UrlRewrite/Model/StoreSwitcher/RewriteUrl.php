<?php
namespace Flexor\CMSPageTie\Plugin\UrlRewrite\Model\StoreSwitcher;

use Magento\Framework\HTTP\PhpEnvironment\RequestFactory;
use Flexor\CMSPageTie\Api\TieManagementInterface;
use Magento\Framework\UrlInterface as UrlBuilder;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollection;
use Magento\UrlRewrite\Model\StoreSwitcher\RewriteUrl as StoreRewriteUrl;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Cms\Model\ResourceModel\PageFactory as CmsPageModelFactory;

/**
 * Class RewriteUrl
 * @package Flexor\CMSPageTie\Plugin\UrlRewrite\Model\StoreSwitcher
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
     * @var CmsPageModelFactory
     */
    private $cmsPageModelFactory;

    /**
     * RewriteUrl constructor.
     * @param RequestFactory $requestFactory
     * @param TieManagementInterface $tieManagement
     * @param CmsPageCollection $cmsPageCollection
     * @param UrlBuilder $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param CmsPageModelFactory $cmsPageModelFactory
     */
    public function __construct(
        RequestFactory $requestFactory,
        TieManagementInterface $tieManagement,
        CmsPageCollection $cmsPageCollection,
        UrlBuilder $urlBuilder,
        ScopeConfigInterface $scopeConfig,
        CmsPageModelFactory $cmsPageModelFactory
    )
    {
        $this->requestFactory = $requestFactory;
        $this->tieManagement = $tieManagement;
        $this->cmsPageCollection = $cmsPageCollection;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->cmsPageModelFactory = $cmsPageModelFactory;
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
        $currentStoreId = $oldRewrite->getData()['store_id'];
        $request = $this->requestFactory->create(['uri' => $targetUrl]);
        $urlPath = ltrim($request->getPathInfo(), '/');
        $isStoreCodeEnabled = $this->scopeConfig->getValue(
            'web/url/use_store', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($isStoreCodeEnabled) {
            $urlPath = substr($urlPath,strrpos($urlPath,"/")+1);
        }
        $currentPageCollection = $this->cmsPageCollection->create()->addFieldToFilter('identifier', $urlPath);
        $currentPage = $currentPageCollection->getData();
        if (!empty($currentPage)) {
            $currentPageIdentifier = (int) array_column($currentPage, 'page_id')[0];
            $linkedPageName = $this->tieManagement->getLinkedCmsKey($currentPageIdentifier, $currentStoreId);
            $linkedPageId = $this->tieManagement->getLinkedCmsIdByStoreId($currentPageIdentifier, $currentStoreId);
            $attachedStores = $this->cmsPageModelFactory->create()->lookupStoreIds($linkedPageId);

            if (isset($linkedPageId) && $linkedPageId != 0) {
                $this->urlBuilder->setScope($attachedStores[0]);
                $result = $this->urlBuilder->getUrl(null, ['_direct' => $linkedPageName]);
            }
        }
        return $result;
    }
}
