<?php
namespace Flexor\CMSPageTie\Plugin\UrlRewrite\Model\StoreSwitcher;

use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class DataProvider
 * @package Flexor\CMSPageTie\Plugin\UrlRewrite\Model\StoreSwitcher
 */
class RewriteUrl
{
    /**
     * RewriteUrl constructor.
     * @param UrlFinderInterface $urlFinder
     * @param \Magento\Framework\HTTP\PhpEnvironment\RequestFactory $requestFactory
     */
    public function __construct(
        UrlFinderInterface $urlFinder,
        \Magento\Framework\HTTP\PhpEnvironment\RequestFactory $requestFactory
    ) {
        $this->urlFinder = $urlFinder;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @param \Magento\UrlRewrite\Model\StoreSwitcher\RewriteUrl $subject
     * @param $result
     * @param $targetStore
     * @param $oldRewrite
     * @param $targetUrl
     * @return mixed
     */
    public function afterSwitch(\Magento\UrlRewrite\Model\StoreSwitcher\RewriteUrl $subject, $result, $targetStore, $oldRewrite, $targetUrl)
    {
        $oldStoreId = $oldRewrite->getData()['store_id'];
        $request = $this->requestFactory->create(['uri' => $targetUrl]);

        $urlPath = ltrim($request->getPathInfo(), '/');
        $oldRewriteForId = $this->urlFinder->findOneByData(
            [
                UrlRewrite::REQUEST_PATH => $urlPath,
                UrlRewrite::STORE_ID => $oldStoreId,
            ]
        );
        if(isset($oldRewriteForId)) {
            $targetStoreId = $targetStore->getData()['store_id'];
            $currentPageId = $oldRewriteForId->getByKey('entity_id');

        }
        return $result;
    }
}
