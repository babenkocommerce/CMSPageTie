<?php
namespace Flexor\CMSPageTie\Model;

/**
 * Class Tie
 *
 * @package Flexor\CMSPageTie\Model
 */
class Tie implements \Flexor\CMSPageTie\Api\Data\TieInterface
{
    /**
     * @var \Flexor\CMSPageTie\Model\ResourceModel\Tie
     */
    private $resourceTie;

    /**
     * Constructor
     *
     * @param \Flexor\CMSPageTie\Model\ResourceModel\Tie $resourceTie,
     * @return void
     */
    public function __construct(ResourceModel\Tie $resourceTie)
    {
        $this->resourceTie = $resourceTie;
    }

    /**
     * @param $relations
     * @return $this
     */
    public function add($relations)
    {
        $this->getResource()->add($relations);
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
     * @param $relations
     * @return $this
     */
    public function remove($relations)
    {
        $this->getResource()->remove($relations);
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
     * @param $pageId
     * @param $storeId
     * @return string
     */
    public function getLinkedPageId($pageId, $storeId)
    {
        return $this->getResource()->getLinkedPageId($pageId, $storeId);
    }

    /**
     * @return \Flexor\CMSPageTie\Model\ResourceModel\Tie
     */
    protected function getResource()
    {
        return $this->resourceTie;
    }
}
