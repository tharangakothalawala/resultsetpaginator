<?php
/**
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator\MySQL;

use Exception;
use TSK\ResultSetPaginator\Paginator\AbstractResultSetPaginator;

class TotalCountAwareResultSetPaginator extends AbstractResultSetPaginator
{
    /**
     * @var int
     */
    protected $totalCount = 0;

    /**
     * Constructor
     *
     * @param mysqli $databaseConnection
     * @param int $page
     * @param int $limit
     */
    public function __construct($page, $limit, $totalCount)
    {
        $this->currentPage = $page;
        $this->limit = $limit;
        $this->totalCount = $totalCount;
    }

    /**
     * @param string $sql sql statement
     * @return mysqli_result
     * @throws Exception
     */
    public function query($sql)
    {
        $sql = trim($sql);

        // inject LIMIT if not set explicitly
        if (strpos(strtoupper($sql), 'LIMIT') === false) {
            $sql .= ' ' . $this->getLimitClause();
        }

        // inject SQL_CALC_FOUND_ROWS if not set explicitly
        if (strpos(strtoupper($sql), 'SQL_CALC_FOUND_ROWS') === false) {
            $sql = 'SELECT SQL_CALC_FOUND_ROWS ' . substr($sql, 6, strlen($sql));
        }

        $result = $this->databaseConnection->query($sql);
        if (!$result) {
            throw new Exception($this->databaseConnection->error);
        }

        $this->setFoundRows();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function setLimitClause()
    {
        $this->limitClause = sprintf($this->limitClause, $this->offset, $this->limit);
    }

    /**
     * {@inheritdoc}
     */
    protected function setFoundRows()
    {
        $result = $this->databaseConnection->query('SELECT FOUND_ROWS() AS `foundRows`');
        $this->foundRows = (int) $result->fetch_object()->foundRows;
    }
}
