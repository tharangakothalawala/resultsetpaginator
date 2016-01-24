<?php
/**
 * AbstractResultSetPaginator.php
 *
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator\Paginator;

use Exception;
use PDOStatement;

abstract class AbstractResultSetPaginator
{
    const DEFAULT_VISIBLE_RESULT_COUNT = 10;
    const DEFAULT_VISIBLE_PAGINATION_RANGE = 3;

    /**
     * @var object $databaseConnection
     */
    protected $databaseConnection = null;

    /**
     * number of links to show within the pagination link bar
     * ex: if the value is 3 and you are on page 13,
     *     it will show << < 10 11 12 and 14 15 16 > >>
     *
     * @var integer $visiblePaginationRange
     */
    protected $visiblePaginationRange = self::DEFAULT_VISIBLE_PAGINATION_RANGE;

    /**
     * Current page
     *
     * @var integer $currentPage
     */
    protected $currentPage = null;

    /**
     * @var integer $offset
     */
    protected $offset = 0;

    /**
     * @var integer $limit
     */
    protected $limit = self::DEFAULT_VISIBLE_RESULT_COUNT;

    /**
     * @var string $limitClause
     */
    protected $limitClause = null;

    /**
     * @var integer $foundRows
     */
    protected $foundRows = null;

    /**
     * Get the current page
     *
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Set the current page in order create the pagination data
     *
     * @param integer $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = (int) $currentPage;
    }

    /**
     * Get the the number of visible results per page
     *
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get the number of links shown within the pagination links
     *
     * @return integer
     */
    public function getVisiblePaginationRange()
    {
        return $this->visiblePaginationRange;
    }

    /**
     * Set the number of links to show within the pagination links
     * ex: if the value is 3 and you are on page 13,
     *     it will show << < 10 11 12 and 14 15 16 > >>
     *
     * @param integer $visiblePaginationRange
     */
    public function setVisiblePaginationRange($visiblePaginationRange)
    {
        $this->visiblePaginationRange = (int) $visiblePaginationRange;
    }

    /**
     * get the limit clause depending on the Paginator used
     *
     * @return string
     */
    public function getLimitClause()
    {
        return $this->limitClause;
    }

    /**
     * get the total number of results
     *
     * @return integer
     */
    public function getFoundRows()
    {
        return $this->foundRows;
    }

    /**
     * Get the pagination data. Using this data, you can build the pagination bar according to how you need
     *
     * @return array
     */
    public function getPagination()
    {
        if (empty($this->currentPage)) {
            throw new Exception(_('Cannot construct the pagination without the current page'));
        }

        $pagination = array();
        if ($this->foundRows > $this->limit) {
            // find out total pages
            $totalpages = ceil($this->foundRows / $this->limit);

            // if not on page 1, don't show back links
            if ($this->currentPage > 1) {
               // show << link to go back to page 1
               $pagination[] = array('isCurrentPage' => false, 'pageNumber' => 1, 'displayValue' => '<<');
               // get previous page num
               $prevpage = $this->currentPage - 1;
               // show < link to go back to 1 page
               $pagination[] = array('isCurrentPage' => false, 'pageNumber' => $prevpage, 'displayValue' => '<');
            }

            // loop to show links to range of pages around current page
            for ($pageNumber = ($this->currentPage - $this->visiblePaginationRange);
                $pageNumber < (($this->currentPage + $this->visiblePaginationRange) + 1);
                $pageNumber++
            ) {
                if (($pageNumber > 0) && ($pageNumber <= $totalpages)) {
                    if ($pageNumber == $this->currentPage) {
                        $pagination[] = array(
                            'isCurrentPage' => true,
                            'pageNumber' => $pageNumber,
                            'displayValue' => $pageNumber
                        );
                    } else {
                        $pagination[] = array(
                            'isCurrentPage' => false,
                            'pageNumber' => $pageNumber,
                            'displayValue' => $pageNumber
                        );
                    }
                }
            }

            // if not on last page, show forward and last page links
            if ($this->currentPage != $totalpages) {
               $nextpage = $this->currentPage + 1;
                $pagination[] = array('isCurrentPage' => false, 'pageNumber' => $nextpage, 'displayValue' => '>');
                $pagination[] = array('isCurrentPage' => false, 'pageNumber' => $totalpages, 'displayValue' => '>>');
            }
        } else {
            $pagination[] = array('isCurrentPage' => true, 'pageNumber' => 1, 'displayValue' => '1');
        }

        return $pagination;
    }

    /**
     * @return PDOStatement|mysqli_result
     */
    public abstract function query($sql);

    /**
     * set the limit clause using the current offset and limit
     */
    protected abstract function setLimitClause();

    /**
     * set the number of found rows as per the query ran
     */
    protected abstract function setFoundRows();
}
