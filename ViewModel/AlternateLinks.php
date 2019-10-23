<?php

namespace Flexor\CMSPageTie\ViewModel;

use Flexor\CMSPageTie\Api\TieManagementInterface;
use Magento\Cms\Helper\Page as PageHelper;
use Magento\Cms\Model\Page;
use Magento\Framework\Locale\Resolver as LocaleResolver;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * ViewModel of Alternative Lang Links
 * Class AlternateLinks
 * @package Flexor\CMSPageTie\ViewModel
 */
class AlternateLinks implements ArgumentInterface
{
    /**
     * @var TieManagementInterface
     */
    protected $tieManagement;
    /**
     * @var Page
     */
    protected $page;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var LocaleResolver
     */
    protected $localeResolver;
    /**
     * @var PageHelper
     */
    protected $pageHelper;


    /**
     * AlternateLinks constructor.
     * @param TieManagementInterface $tieManagement
     * @param Page $page
     * @param PageHelper $pageHelper
     * @param StoreManagerInterface $storeManager
     * @param LocaleResolver $resolver
     */
    public function __construct(
        TieManagementInterface $tieManagement,
        Page $page,
        PageHelper $pageHelper,
        StoreManagerInterface $storeManager,
        LocaleResolver $resolver
    )
    {
        $this->tieManagement = $tieManagement;
        $this->page = $page;
        $this->storeManager = $storeManager;
        $this->localeResolver = $resolver;
        $this->pageHelper = $pageHelper;
    }

    /**
     * Retrieves array of available linked CMS pages for all storeViews
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAlternativeLinks()
    {
        return array_merge(
            $this->tieManagement->getLinkedPageKeys(
                $this->page->getId(),
                $this->storeManager->getStore()->getId()
            ),
            [ $this->localeResolver->getLocale() => $this->pageHelper->getPageUrl($this->page->getId()) ]
        );
    }
}
