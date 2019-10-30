<?php

namespace Flexor\CMSPageTie\Plugin\Controller\Adminhtml\Page;

class Save
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Flexor\CMSPageTie\Api\TieManagementInterfaceFactory
     */
    private $tieManagement;

    /**
     * Save constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Flexor\CMSPageTie\Api\Data\TieManagementInterfaceFactory $tieManagementFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Flexor\CMSPageTie\Api\TieManagementInterfaceFactory $tieManagementFactory
    ) {
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->tieManagement = $tieManagementFactory->create();
    }

    /**
     * @param \Magento\Cms\Controller\Adminhtml\Page\Save $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(\Magento\Cms\Controller\Adminhtml\Page\Save $subject, $result)
    {
        try {
            $currentPageId = $subject->getRequest()->getParam('page_id');
            if ($currentPageId) {
                $ties = $subject->getRequest()->getParam('cms_page_tie_rows');
                $linksArray = [];
                foreach ($ties as $tie) {
                    if ($tie['linked_page_id'] > 0) {
                        $linksArray[$tie['store_id']] = $tie['linked_page_id'];
                    }
                }
                $this->tieManagement->updateCmsLinks($currentPageId, $linksArray);
            }
        } catch (\Exception $e) {
            $exceptionMessage = __('Something went wrong while saving linked pages ties.');
            $this->messageManager->addExceptionMessage($e, $exceptionMessage);
            $this->logger->debug($exceptionMessage . ' ' . $e->getMessage(), ['exception' => $e]);
        }

        return $result;
    }
}
