<?php

namespace Flexor\CMSPageTie\Plugin\Cms\Page;

use Flexor\CMSPageTie\Ui\Component\Form\Store\StoreViews;

class DataProviderPlugin
{
    /**
     * @var StoreViews
     */
    private $storeViews;

    public function __construct(StoreViews $storeViews)
    {
        $this->storeViews = $storeViews;
    }

    public function afterGetData($subject, $result) {

        $result[1]['cms_page_tie_rows'] = $this->storeViews->toOptionArray();

        return $result;
    }
}
