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
     * @param $relations
     * @return int The number of affected rows.
     */
    public function add($relations)
    {
        return $this->getConnection()->insertMultiple($this->getTieTable(), $relations);
    }

    /**
     * @param $relations
     * @return int The number of affected rows.
     */
    public function remove($relations)
    {
        $connection = $this->getConnection();
        $res = $connection->delete($this->getTieTable(), ['page_id IN (?)' => $relations]);
        $res += $connection->delete($this->getTieTable(), ['linked_page_id IN (?)' => $relations]);
        return $res;
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
     * @param $pageId
     * @param $storeId
     * @return string
     */
    public function getLinkedPageId($pageId, $storeId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getTieTable())
            ->columns('linked_page_id')
            ->where('page_id = :page_id')
            ->where('store_id = :store_id');

        return $connection->fetchOne(
            $select,
            [
                'page_id' => (int) $pageId,
                'store_id' => (int) $storeId
            ]
        );
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
