<?php

namespace Flexor\CMSPageTie\Ui\Component\Form\Pages;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Options implements OptionSourceInterface
{
    /**
     * @var CmsPageCollectionFactory
     */
    protected $cmsPageCollectionFactory;

    /**
     * @var array
     */
    protected $cmsPageOptions;

    public function __construct(CmsPageCollectionFactory $cmsPageCollectionFactory)
    {
        $this->cmsPageCollectionFactory = $cmsPageCollectionFactory;
    }

    /**
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'))
     * Cms Pages list as Options for Ui-Select component
     */
    public function toOptionArray()
    {
        return $this->getCmsPageOptions();
    }

    /**
     * @return array
     */
    public function getCmsPageOptions(): array
    {
        if ($this->cmsPageOptions === null) {
            $this->cmsPageOptions[] = [
                'label' => __('-- No Linked Page --'),
                'value' => 0
            ];
            $collection = $this->cmsPageCollectionFactory->create();
            foreach ($collection as $cmsPage) {
                $this->cmsPageOptions[] = [
                    'label' => $cmsPage->getTitle(),
                    'value' => $cmsPage->getId()
                ];
            }
        }
        return $this->cmsPageOptions;
    }
}
