<?php

namespace Flexor\CMSPageTie\ViewModel;

use Flexor\CMSPageTie\Api\TieManagementInterface;
use Magento\Cms\Model\Page;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AlternateLinks - ViewModel for Alternative Language Links
 */
class AlternateLinks implements ArgumentInterface
{
    /**
     * @var TieManagementInterface
     */
    private $tieManagement;

    /**
     * @var Page
     */
    private $page;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * AlternateLinks constructor.
     *
     * @param TieManagementInterface $tieManagement
     * @param Page $page
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        TieManagementInterface $tieManagement,
        Page $page,
        StoreManagerInterface $storeManager
    ) {
        $this->tieManagement = $tieManagement;
        $this->page = $page;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieves array of available linked CMS pages for all storeViews
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAlternativeLinks()
    {
        return $this->tieManagement->getLinkedPageKeys(
            $this->page->getId(),
            $this->storeManager->getStore()->getId(),
            true
        );
    }
}
