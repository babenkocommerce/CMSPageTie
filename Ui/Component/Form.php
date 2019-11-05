<?php

namespace Flexor\CMSPageTie\Ui\Component;

/**
 * Custom Form Ui component for CMS Page
 * @package Flexor\CMSPageTie\Ui\Component
 */
class Form extends \Magento\Ui\Component\Form
{
    /**
     * @var \Flexor\CMSPageTie\Ui\Component\Form\Store\StoreViews
     */
    protected $storeViews;

    /**
     * @var \Flexor\CMSPageTie\Api\TieRepositoryInterface
     */
    private $tieRepository;

    /**
     * Form constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Flexor\CMSPageTie\Ui\Component\Form\Store\StoreViews $storeViews
     * @param \Flexor\CMSPageTie\Api\TieRepositoryInterface $tieRepositoryFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Flexor\CMSPageTie\Ui\Component\Form\Store\StoreViews $storeViews,
        \Flexor\CMSPageTie\Api\TieRepositoryInterface $tieRepository,
        array $components = [],
        array $data = []
    ) {
        $this->storeViews = $storeViews;
        $this->tieRepository = $tieRepository;
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
        $ties = $this->tieRepository->get($dataSource['data']['page_id']);
        $dataSource['data']['cms_page_tie_rows'] = $this->storeViews->toOptionArray();
        foreach ($ties as $tie) {
            $dataSource['data']['cms_page_tie_rows'][$tie['store_id']]['linked_page_id'] = $tie['linked_page_id'];
        }
        return $dataSource;
    }
}
