<?php
/**
 * AbstractResultSetPaginator.php
 *
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator\Paginator;

use Exception;
use mysqli_result;
use PDOStatement;

/**
 * Class AbstractResultSetPaginator
 * @package TSK\ResultSetPaginator\Paginator
 */
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
     * @var int $visiblePaginationRange
     */
    protected $visiblePaginationRange = self::DEFAULT_VISIBLE_PAGINATION_RANGE;

    /**
     * Current page
     *
     * @var int $currentPage
     */
    protected $currentPage = null;

    /**
     * @var int $offset
     */
    protected $offset = 0;

    /**
     * @var int $limit
     */
    protected $limit = self::DEFAULT_VISIBLE_RESULT_COUNT;

    /**
     * @var string $limitClause
     */
    protected $limitClause = null;

    /**
     * @var int $foundRows
     */
    protected $foundRows = null;

    /**
     * Get the current page
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Get the the number of visible results per page
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get the number of links shown within the pagination links
     *
     * @return int
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
     * @param int $visiblePaginationRange
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
     * @return int
     */
    public function getFoundRows()
    {
        return $this->foundRows;
    }

    /**
     * Get the pagination data. Using this data, you can build the pagination bar according to how you need
     * @return Page[]
     * @throws Exception
     */
    public function getPagination()
    {
        if (empty($this->currentPage)) {
            throw new Exception(_('Cannot construct the pagination without the current page'));
        }

        $pagination = array();

        // if the found rows is less than the required amount, it only contain one page
        if ($this->foundRows < $this->limit) {
            $pagination[] = new Page(true, 1, '1');
            return $pagination;
        }

        // find out total pages
        $totalPages = ceil($this->foundRows / $this->limit);

        // if not on page 1, don't show back links
        if ($this->currentPage > 1) {
            // show << link to go back to page 1
            $pagination[] = new Page(false, 1, '<<');
            // get previous page num
            $previousPage = $this->currentPage - 1;
            // show < link to go back to 1 page
            $pagination[] = new Page(false, $previousPage, '<');
        }

        // loop to show links to range of pages around current page
        for ($pageNumber = ($this->currentPage - $this->visiblePaginationRange);
            $pageNumber < (($this->currentPage + $this->visiblePaginationRange) + 1);
            $pageNumber++
        ) {
            if (($pageNumber > 0) && ($pageNumber <= $totalPages)) {
                if ($pageNumber == $this->currentPage) {
                    $pagination[] = new Page(true, $pageNumber, $pageNumber);
                } else {
                    $pagination[] = new Page(false, $pageNumber, $pageNumber);
                }
            }
        }

        // if not on last page, show forward and last page links
        if ($this->currentPage != $totalPages) {
            $nextPage = $this->currentPage + 1;
            $pagination[] = new Page(false, $nextPage, '>');
            $pagination[] = new Page(false, $totalPages, '>>');
        }

        return $pagination;
    }

    protected function getOffset()
    {
        if (empty($this->currentPage)) {
            throw new Exception(_('Current page is not set'));
        }

        $offset = 0;
        if (intval($this->currentPage) > 1) {
            $offset = ($this->currentPage - 1) * $this->limit;
        } elseif (intval($this->currentPage) < 1) {
            throw new Exception(_('Invalid page number'));
        }

        return $offset;
    }

    /**
     * @param $sql
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
