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
    private $tieTable;

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
     * @return int The number of affected rows.
     */
    public function add($currentPageId, $linkedPageId, $storeId)
    {
        return $this->getConnection()->insert(
            $this->getTieTable(),
            [
                'page_id' => (int) $currentPageId,
                'linked_page_id' => (int) $linkedPageId,
                'store_id' => (int) $storeId
            ]
        );
    }

    /**
     * @param int $currentPageId
     * @param int $linkedPageId
     * @param int $storeId
     * @return int The number of affected rows.
     */
    public function update($currentPageId, $linkedPageId, $storeId)
    {
        return $this->getConnection()->insertOnDuplicate(
            $this->getTieTable(),
            [
                'page_id' => (int) $currentPageId,
                'linked_page_id' => (int) $linkedPageId,
                'store_id' => (int) $storeId
            ],
            ['page_id', 'store_id']
        );
    }

    /**
     * @param int $currentPageId
     * @return int The number of affected rows.
     */
    public function remove($currentPageId)
    {
        return $this->getConnection()->delete($this->getTieTable(), ['page_id = ?' => (int) $currentPageId]);
    }

    /**
     * @param int $currentPageId
     * @return array
     */
    public function get($currentPageId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getTieTable())->where('page_id = :page_id');

        return $connection->fetchAll($select, ['page_id' => (int) $currentPageId]);
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
