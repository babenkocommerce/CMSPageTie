<?php
namespace Flexor\CMSPageTie\Model\ResourceModel;

/**
 * Class Tie
 *
 * @package Flexor\CMSPageTie\Model\ResourceModel
 */
class Tie extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var string
     */
    protected $tieTable;

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->tieTable = $this->getTable('cms_page_tie');
    }

    /**
     * @param int $currentPageId
     * @param int $linkedPageId
     * @param int $storeId
     * @return $this
     */
    public function add($currentPageId, $linkedPageId, $storeId)
    {
        $this->getConnection()->insert(
            $this->getTieTable(),
            [
                'page_id' => $currentPageId,
                'linked_page_id' => $linkedPageId,
                'store_id' => $storeId
            ]
        );

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
        $this->getConnection()->update(
            $this->getTieTable(),
            [
                'page_id' => $currentPageId,
                'linked_page_id' => $linkedPageId,
                'store_id' => $storeId
            ],
            [
                'page_id = ?' => (int)$currentPageId,
                'store_id = ?' => (int)$storeId
            ]);

        return $this;
    }

    /**
     * @param int $currentPageId
     * @return $this
     */
    public function remove($currentPageId)
    {
        $this->getConnection()->delete($this->getTieTable(), ['page_id = ?' => (int)$currentPageId]);

        return $this;
    }

    /**
     * @param int $currentPageId
     * @return array
     */
    public function get($currentPageId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getTieTable())->where('page_id = :page_id');

        return $connection->fetchAll($select, ['page_id' => (int)$currentPageId]);
    }


    /**
     * Get Tie table name
     *
     * @return string
     */
    protected function getTieTable()
    {
        return $this->tieTable;
    }
}
