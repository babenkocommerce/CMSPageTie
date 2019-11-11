<?php
namespace Flexor\CMSPageTie\Model;

/**
 * Class TieRepository
 *
 * @package Flexor\CMSPageTie\Model
 */
class TieRepository implements \Flexor\CMSPageTie\Api\TieRepositoryInterface
{
    /**
     * @var
     */
    private $tieModel;

    /**
     * TieRepository constructor.
     * @param \Flexor\CMSPageTie\Api\Data\TieInterfaceFactory $tieModelFactory
     */
    public function __construct(\Flexor\CMSPageTie\Api\Data\TieInterfaceFactory $tieModelFactory)
    {
        $this->tieModel = $tieModelFactory->create();
    }

    /**
     * @param $relations
     * @return \Flexor\CMSPageTie\Api\Data\TieInterface
     */
    public function add($relations)
    {
        return $this->tieModel->add($relations);
    }

    /**
     * @param $relations
     * @return \Flexor\CMSPageTie\Api\Data\TieInterface
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
     * @return string
     */
    public function getLinkedPageId($pageId, $storeId)
    {
        return $this->tieModel->getLinkedPageId($pageId, $storeId);
    }
}
