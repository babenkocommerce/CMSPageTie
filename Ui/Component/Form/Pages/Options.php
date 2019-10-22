<?php

namespace Flexor\CMSPageTie\Ui\Component\Form\Pages;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 * @package Flexor\CMSPageTie\Ui\Component\Form\Pages
 */
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

    /**
     * Options constructor.
     * @param CmsPageCollectionFactory $cmsPageCollectionFactory
     */
    public function __construct(CmsPageCollectionFactory $cmsPageCollectionFactory)
    {
        $this->cmsPageCollectionFactory = $cmsPageCollectionFactory;
    }

    /**
     * Cms Pages list as Options for Ui-Select component
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'))
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
