<?php

namespace Flexor\CMSPageTie\Ui\Component;

/**
 * Custom Form Ui component for CMS Page
 */
class Form extends \Magento\Ui\Component\Form
{
    /**
     * @var \Flexor\CMSPageTie\Ui\Component\Form\Store\StoreViews
     */
    private $storeViews;

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
        $dataSource['data']['cms_page_tie_rows'] = $this->storeViews->toOptionArray();
        if (isset($dataSource['data']['page_id'])) {
            $ties = $this->tieRepository->get($dataSource['data']['page_id']);
            foreach ($ties as $tie) {
                $cmsPageOptions = ['pageOptions' => [], 'row' => ''];
                foreach ($dataSource['data']['cms_page_tie_rows'] as $rowKey => $rowData) {
                    if ($rowData['store_id'] == $tie['store_id']) {
                        $cmsPageOptions = [
                            'pageOptions' => $rowData['cms_page_options'],
                            'row' => $rowKey,
                        ];
                        break;
                    }
                }
                foreach ($cmsPageOptions['pageOptions'] as $cmsPageOption) {
                    if ($cmsPageOption['value'] === $tie['linked_page_id']) {
                        $dataSource['data']['cms_page_tie_rows'][$cmsPageOptions['row']]['linked_page_id'] =
                            $tie['linked_page_id'];
                        break;
                    }
                }
            }
        }
        return $dataSource;
    }
}
