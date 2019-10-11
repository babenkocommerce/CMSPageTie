<?php
namespace Flexor\CMSPageTie\Model;

/**
 * Class Tie
 *
 * @package Flexor\CMSPageTie\Model
 */
class Tie extends \Magento\Framework\DataObject implements \Flexor\CMSPageTie\Api\Data\TieInterface
{
    /**
     * @var \Flexor\CMSPageTie\Model\ResourceModel\Tie
     */
    protected $resourceTie;

    /**
     * Constructor
     *
     * @param \Flexor\CMSPageTie\Model\ResourceModel\Tie $resourceTie,
     * @param array $data
     * @return void
     */
    public function __construct(
        ResourceModel\Tie $resourceTie,
        array $data = []
    ) {
        $this->resourceTie = $resourceTie;
        parent::__construct($data);
    }

    /**
     * @param int $currentPageId
     * @param int $linkedPageId
     * @param int $storeId
     * @return $this
     */
    public function add($currentPageId, $linkedPageId, $storeId)
    {
        $this->getResource()->add($currentPageId, $linkedPageId, $storeId);
        return $this;
    }

    /**
     * @param int $currentPageId
     * @param int $linkedPageId
     * @param int $storeId
     * @return $this
     */
    public function update($currentPageId, $linkedPageId, $storeId)
    {
        $this->getResource()->update($currentPageId, $linkedPageId, $storeId);
        return $this;
    }

    /**
     * @param int $currentPageId
     * @return $this
     */
    public function remove($currentPageId)
    {
        $this->getResource()->remove($currentPageId);
        return $this;
    }

    /**
     * @param int $currentPageId
     * @return mixed
     */
    public function get($currentPageId)
    {
        return $this->getResource()->get($currentPageId);
    }

    /**
     * @return \Flexor\CMSPageTie\Model\ResourceModel\Tie
     */
    protected function getResource()
    {
        return $this->resourceTie;
    }
}
