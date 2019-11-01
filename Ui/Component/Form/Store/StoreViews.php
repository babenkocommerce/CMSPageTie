<?php
namespace Flexor\CMSPageTie\Ui\Component\Form\Store;

class StoreViews extends \Magento\Store\Ui\Component\Listing\Column\Store\Options
{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    protected $cmsPageCollectionFactory;

    /**
     * StoreViews constructor.
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $cmsPageCollectionFactory
     */
    public function __construct(
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Escaper $escaper,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $cmsPageCollectionFactory
    ) {
        $this->cmsPageCollectionFactory = $cmsPageCollectionFactory;
        parent::__construct($systemStore, $escaper);
    }

    /**
     * Return array of store view ids & labels for Dynamic Rows ui-component
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function toOptionArray()
    {
        $result = [];
        $stores = [];
        $i=0;
        $storeCollection = $this->systemStore->getStoreCollection();
        foreach ($storeCollection as $store) {
            $stores[] = $store->getId();
        }
        $cmsPageCollection = $this->cmsPageCollectionFactory->create()
            ->addFilter('store', ['in' => $stores], 'public')->load();
        /** @var  \Magento\Store\Model\Store $store */
        foreach ($storeCollection as $store) {
            $storeLabel = $this->sanitizeName($store->getWebsite()->getName()). ' | ' .
                $this->sanitizeName($store->getGroup()->getName()). ' | ' .
                $this->sanitizeName($store->getName());
            $storeId = $store->getId();
            $cmsPageOptions = [[
                'label' => __('-- No Linked Page --'),
                'value' => '0',
            ]];
            foreach ($cmsPageCollection as $cmsPage) {
                $storeIds = $cmsPage->getStoreId();
                if ((count($storeIds) != count($storeCollection)) and (in_array($storeId, $storeIds))){
                    $cmsPageOptions[] = [
                        'label' => $cmsPage->getTitle(),
                        'value' => $cmsPage->getId(),
                    ];
                }
            }
            $result[$i] = [
                'record_id' => $i,
                'store_id' => $storeId,
                'store_label' => $storeLabel,
                'linked_page_id' => '0',
                'cms_page_options' => $cmsPageOptions,
            ];
            $i++;
        }
        return $result;
    }
}
