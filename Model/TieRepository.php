<?php

namespace Flexor\CMSPageTie\Model;

use Flexor\CMSPageTie\Api\Data\TieInterface;
use Flexor\CMSPageTie\Api\Data\TieInterfaceFactory;

/**
 * Class TieRepository - repository for processing data models for cms page ties
 */
class TieRepository implements \Flexor\CMSPageTie\Api\TieRepositoryInterface
{
    /**
     * @var TieInterface
     */
    private $tieModel;

    /**
     * TieRepository constructor.
     *
     * @param TieInterfaceFactory $tieModelFactory
     */
    public function __construct(TieInterfaceFactory $tieModelFactory)
    {
        $this->tieModel = $tieModelFactory->create();
    }

    /**
     * @param $relations
     * @return TieInterface
     */
    public function add($relations)
    {
        return $this->tieModel->add($relations);
    }

    /**
     * @param $relations
     * @return TieInterface
     */
    public function remove($relations)
    {
        return $this->tieModel->remove($relations);
    }

    /**
     * @param int $currentPageId
     * @return mixed
     */
    public function get($currentPageId)
    {
        return $this->tieModel->get($currentPageId);
    }

    /**
     * @param $pageId
     * @param $storeId
     * @return array
     */
    public function getLinkedPageId($pageId, $storeId)
    {
        return $this->tieModel->getLinkedPageId($pageId, $storeId);
    }

    /**
     * @param $pageId
     * @param $storeIds
     * @return mixed
     */
    public function getPagesByStoreId($pageId, $storeIds)
    {
        return $this->tieModel->getPagesByStoreId($pageId, $storeIds);
    }
}
