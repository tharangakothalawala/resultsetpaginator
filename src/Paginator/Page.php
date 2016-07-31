<?php
/**
 * Page.php
 *
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 30-07-2016
 */

namespace TSK\ResultSetPaginator\Paginator;

/**
 * Class Page
 * @package TSK\ResultSetPaginator\Paginator
 */
class Page
{
    /**
     * @var bool
     */
    private $isCurrentPage;

    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @var string
     */
    private $displayValue;

    /**
     * @param bool $isCurrentPage
     * @param int $pageNumber
     * @param string $displayValue
     */
    public function __construct($isCurrentPage, $pageNumber, $displayValue)
    {
        $this->isCurrentPage = $isCurrentPage;
        $this->pageNumber = $pageNumber;
        $this->displayValue = $displayValue;
    }

    /**
     * @return mixed
     */
    public function isCurrentPage()
    {
        return $this->isCurrentPage;
    }

    /**
     * @return mixed
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * @return mixed
     */
    public function getDisplayValue()
    {
        return $this->displayValue;
    }
}
