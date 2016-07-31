<?php
/**
 * MySQLiResultSetPaginator.php
 *
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator\MySQL;

use Exception;
use mysqli;
use mysqli_result;
use PDOStatement;
use TSK\ResultSetPaginator\Paginator\AbstractResultSetPaginator;

/**
 * Class MySQLiResultSetPaginator
 * @package TSK\ResultSetPaginator\MySQL
 */
class MySQLiResultSetPaginator extends AbstractResultSetPaginator
{
    /**
     * @var mysqli $databaseConnection
     */
    protected $databaseConnection = null;

    /**
     * @var string $limitClause
     */
    protected $limitClause = 'LIMIT %s, %s';

    /**
     * Constructor
     *
     * @param mysqli $databaseConnection
     * @param int $page
     * @param int $limit
     */
    public function __construct(mysqli $databaseConnection, $page, $limit)
    {
        $this->databaseConnection = $databaseConnection;
        $this->currentPage = $page;
        $this->limit = $limit;

        $this->offset = $this->getOffset();
        $this->setLimitClause();
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
        /** @var PDOStatement $stmt */
        $result = $this->databaseConnection->query('SELECT FOUND_ROWS() AS `foundRows`');
        $this->foundRows = (int) $result->fetch_object()->foundRows;
    }
}
