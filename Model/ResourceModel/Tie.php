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
     * @param $pageIds
     * @return int The number of affected rows.
     */
    public function remove($pageIds)
    {
        $connection = $this->getConnection();
        $result = $connection->delete($this->getTieTable(), ['page_id IN (?)' => $pageIds]);
        $result += $connection->delete($this->getTieTable(), ['linked_page_id IN (?)' => $pageIds]);
        return $result;
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
     * @return array
     */
    public function getLinkedPageId($pageId, $storeId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getTieTable())
            ->where('page_id = :page_id')
            ->where('store_id = :store_id');

        return $connection->fetchRow(
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
