<?php

namespace Flexor\CMSPageTie\Plugin\Model\Page;

use Flexor\CMSPageTie\Ui\Component\Form\Store\StoreViews;

/**
 * Class DataProvider
 * @package Flexor\CMSPageTie\Plugin\Model\Page
 */
class DataProvider
{
    /**
     * @var StoreViews
     */
    private $storeViews;

    /**
     * DataProvider constructor.
     * @param StoreViews $storeViews
     */
    public function __construct(StoreViews $storeViews)
    {
        $this->storeViews = $storeViews;
    }

    /**
     * @param $subject \Magento\Cms\Model\Page\DataProvider
     * @param $result
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetData($subject, $result) {
        $result[array_keys($result)[0]]['cms_page_tie_rows'] = $this->storeViews->toOptionArray();
        return $result;
    }
}
