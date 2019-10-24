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
     * @param int $currentPageId
     * @param int $linkedPageId
     * @param int $storeId
     * @return \Flexor\CMSPageTie\Api\Data\TieInterface
     */
    public function add($currentPageId, $linkedPageId, $storeId)
    {
        return $this->tieModel->add($currentPageId, $linkedPageId, $storeId);
    }

    /**
     * @param int $currentPageId
     * @param int $linkedPageId
     * @param int $storeId
     * @return \Flexor\CMSPageTie\Api\Data\TieInterface
     */
    public function update($currentPageId, $linkedPageId, $storeId)
    {
        return $this->tieModel->update($currentPageId, $linkedPageId, $storeId);
    }

    /**
     * @param int $currentPageId
     * @return \Flexor\CMSPageTie\Api\Data\TieInterface
     */
    public function remove($currentPageId)
    {
        return $this->tieModel->remove($currentPageId);
    }

    /**
     * @param int $currentPageId
     * @return mixed
     */
    public function get($currentPageId)
    {
        return $this->tieModel->get($currentPageId);
    }
}
