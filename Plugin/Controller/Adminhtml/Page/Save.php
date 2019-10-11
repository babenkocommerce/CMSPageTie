<?php

namespace Flexor\CMSPageTie\Plugin\Controller\Adminhtml\Page;

class Save
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function afterExecute(\Magento\Cms\Controller\Adminhtml\Page\Save $subject, $result)
    {
        $this->logger->notice('For QA');

        return $result;
    }
}
