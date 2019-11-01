<?php

namespace Flexor\CMSPageTie\Ui\Component;

use Flexor\CMSPageTie\Ui\Component\Form\Store\StoreViews;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Custom Form Ui component for CMS Page
 * @package Flexor\CMSPageTie\Ui\Component
 */
class Form extends \Magento\Ui\Component\Form
{
    /**
     * @var StoreViews
     */
    protected $storeViews;

    /**
     * Form constructor.
     * @param ContextInterface $context
     * @param FilterBuilder $filterBuilder
     * @param StoreViews $storeViews
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        FilterBuilder $filterBuilder,
        StoreViews $storeViews,
        array $components = [],
        array $data = []
    ) {
        $this->storeViews = $storeViews;
        parent::__construct($context, $filterBuilder, $components, $data);
    }

    /**
     * Add store view dynamic rows data
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDataSourceData()
    {
        $dataSource = parent::getDataSourceData();
        $dataSource['data']['cms_page_tie_rows'] = $this->storeViews->toOptionArray();
        return $dataSource;
    }
}
