<?php
/**
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator\Paginator;

class PaginationProvider
{
    const DEFAULT_VISIBLE_RESULT_COUNT = 10;
    const DEFAULT_VISIBLE_PAGINATION_RANGE = 3;

    /**
     * number of links to show within the pagination link bar
     * ex: if the value is 3 and you are on page 13,
     *     it will show << < 10 11 12 and 14 15 16 > >>
     *
     * @var int
     */
    private $visiblePaginationRange = self::DEFAULT_VISIBLE_PAGINATION_RANGE;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * @var int
     */
    private $limit = self::DEFAULT_VISIBLE_RESULT_COUNT;

    /**
     * @var int
     */
    private $totalCount;

    /**
     * PaginationProvider constructor.
     * @param int $currentPage
     * @param int $limit
     * @param int $totalCount
     */
    public function __construct($currentPage, $limit, $totalCount = 0)
    {
        $this->currentPage = $currentPage;
        $this->limit = $limit;
        $this->totalCount = intval($totalCount);
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = intval($totalCount);
    }

    /**
     * Get the number of links shown within the pagination links
     *
     * @return int
     */
    public function visiblePaginationRange()
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
        $this->visiblePaginationRange = intval($visiblePaginationRange);
    }

    /**
     * Get the pagination data. Using this data, you can build the pagination bar according to how you need
     * @return Page[]
     */
    public function pages()
    {
        if (empty($this->currentPage)) {
            return array();
        }

        $pagination = array();

        // if the found rows is less than the required amount, it only contain one page
        if ($this->totalCount < $this->limit) {
            $pagination[] = new Page(true, 1, '1');
            return $pagination;
        }

        // find out total pages
        $totalPages = ceil($this->totalCount / $this->limit);

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

    public function offset()
    {
        if (empty($this->currentPage)) {
            return 0;
        }

        if (intval($this->currentPage) > 1) {
            return ($this->currentPage - 1) * $this->limit;
        }

        return 0;
    }

    /**
     * @return int
     */
    public function limit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function totalCount()
    {
        return $this->totalCount;
    }
}
