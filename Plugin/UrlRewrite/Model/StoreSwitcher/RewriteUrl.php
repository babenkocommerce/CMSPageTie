<?php
namespace Flexor\CMSPageTie\Plugin\UrlRewrite\Model\StoreSwitcher;

use Magento\Framework\HTTP\PhpEnvironment\RequestFactory;
use Flexor\CMSPageTie\Model\TieManagement;
use Magento\Framework\UrlInterface as UrlBuilder;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollection;
use Magento\Cms\Model\ResourceModel\Page as CmsPageModel;
use Magento\UrlRewrite\Model\StoreSwitcher\RewriteUrl as StoreRewriteUrl;

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
     * @var TieManagement
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
     * @var CmsPageModel
     */
    private $cmsPageModel;

    /**
     * RewriteUrl constructor.
     * @param RequestFactory $requestFactory
     * @param TieManagement $tieManagement
     * @param CmsPageCollection $cmsPageCollection
     * @param UrlBuilder $urlBuilder
     * @param CmsPageModel $cmsPageModel
     */
    public function __construct(
        RequestFactory $requestFactory,
        TieManagement $tieManagement,
        CmsPageCollection $cmsPageCollection,
        UrlBuilder $urlBuilder,
        CmsPageModel $cmsPageModel
    )
    {
        $this->requestFactory = $requestFactory;
        $this->tieManagement = $tieManagement;
        $this->cmsPageCollection = $cmsPageCollection;
        $this->urlBuilder = $urlBuilder;
        $this->cmsPageModel = $cmsPageModel;
    }

    /**
     * @param \Magento\UrlRewrite\Model\StoreSwitcher\RewriteUrl $subject
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

        $currentPageCollection = $this->cmsPageCollection->create()->addFieldToFilter('identifier', $urlPath);
        $currentPage = $currentPageCollection->getData();
        if (!empty($currentPage)) {
            $currentPageIdentifier = (int) array_column($currentPage, 'page_id')[0];
            $linkedPageId = $this->tieManagement->getLinkedCmsKey($currentPageIdentifier, $currentStoreId);
            if (isset($linkedPageId)) {
                return $this->urlBuilder->getUrl(null, ['_direct' => $linkedPageId]);
            }
        }

        return $result;
    }
}
