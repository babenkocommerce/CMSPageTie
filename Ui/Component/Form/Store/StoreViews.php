<?php
namespace Flexor\CMSPageTie\Ui\Component\Form\Store;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

class StoreViews extends StoreOptions
{
    /**
     * Return array of store view ids & labels for Dynamic Rows ui-component
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function toOptionArray()
    {
        $result = [];
        $i=0;
        $storeCollection = $this->systemStore->getStoreCollection();
        /** @var  \Magento\Store\Model\Store $store */
        foreach ($storeCollection as $store) {
            $storeLabel = $this->sanitizeName($store->getWebsite()->getName()). ' | ' .
                $this->sanitizeName($store->getGroup()->getName()). ' | ' .
                $this->sanitizeName($store->getName());
            $storeId = $store->getId();
            $result[$i] = [
                'record_id' => $i,
                'store_view' => $storeId,
                'store_view_label' => $storeLabel,
                'tied_cms_page' => '0',
                /**
                 * TODO: Implement getting of tied cms page for specific store_view.
                 */
            ];
            $i++;
        }
        return $result;
    }
}
