<?php

namespace Flexor\CMSPageTie\Model;

/**
 * Class Tie - tie model
 */
class Tie implements \Flexor\CMSPageTie\Api\Data\TieInterface
{
    /**
     * @var ResourceModel\Tie
     */
    private $resourceTie;

    /**
     * Constructor
     *
     * @param ResourceModel\Tie $resourceTie,
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
     * @param $pageIds
     * @return $this
     */
    public function remove($pageIds)
    {
        $this->getResource()->remove($pageIds);
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
     * @return array
     */
    public function getLinkedPageId($pageId, $storeId)
    {
        return $this->getResource()->getLinkedPageId($pageId, $storeId);
    }

    /**
     * @param $pageId
     * @param $storeIds
     * @return array
     */
    public function getPagesByStoreId($pageId, $storeIds)
    {
        return $this->getResource()->getPagesByStoreId($pageId, $storeIds);
    }

    /**
     * @return ResourceModel\Tie
     */
    protected function getResource()
    {
        return $this->resourceTie;
    }
}
