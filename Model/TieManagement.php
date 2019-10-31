<?php
namespace Flexor\CMSPageTie\Model;

use Magento\Cms\Helper\Page as PageHelper;
use Magento\Framework\Locale\Resolver as LocaleResolver;

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
     * TieManagement constructor.
     *
     * @param PageHelper $pageHelper
     * @param LocaleResolver $localeResolver
     */
    public function __construct(
        PageHelper $pageHelper,
        LocaleResolver $localeResolver
    ) {
        $this->localeResolver = $localeResolver;
        $this->pageHelper = $pageHelper;
    }

    /**
     * TODO: implement logic to receive exampled result
     * Retrieves the linked CMS pages array by current page
     *
     * @param $currentPageId
     * @param $storeId
     * @param bool $withCurrentPage
     * @return array
     */
    public function getLinkedPageKeys($currentPageId, $storeId, $withCurrentPage = false)
    {
        return [$this->localeResolver->getLocale() => $this->pageHelper->getPageUrl($currentPageId)];
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
