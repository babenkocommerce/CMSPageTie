<?php
namespace Flexor\CMSPageTie\Api\Data;

use http\Exception;

/**
 * Interface TieInterface
 * @package Flexor\CMSPageTie\Api\Data
 * @api
 */
interface TieInterface
{
    /**
     * @param $relations
     * @return $this
     */
    public function add($relations);

    /**
     * @param $relations
     * @return $this
     */
    public function remove($relations);

    /**
     * @param int $currentPageId
     * @return mixed
     */
    public function get($currentPageId);

}
