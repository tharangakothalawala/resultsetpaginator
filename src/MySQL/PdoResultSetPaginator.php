<?php
/**
 * PdoResultSetPaginator.php
 *
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator\MySQL;

use PDO;
use PDOStatement;
use TSK\ResultSetPaginator\Paginator\AbstractResultSetPaginator;

/**
 * Class PdoResultSetPaginator
 * @package TSK\ResultSetPaginator\MySQL
 */
class PdoResultSetPaginator extends AbstractResultSetPaginator
{
    /**
     * @var PDO $databaseConnection
     */
    protected $databaseConnection = null;

    /**
     * @var string $limitClause
     */
    protected $limitClause = 'LIMIT %s, %s';

    /**
     * Constructor
     *
     * @param PDO $databaseConnection
     * @param int $page
     * @param int $limit
     */
    public function __construct(PDO $databaseConnection, $page, $limit)
    {
        $this->databaseConnection = $databaseConnection;
        $this->currentPage = $page;
        $this->limit = $limit;

        $this->offset = $this->getOffset();
        $this->setLimitClause();
    }

    /**
     * @param string $sql sql statement
     *
     * @return PDOStatement
     */
    public function query($sql)
    {
        $sql = trim($sql);

        // inject LIMIT if not set explicitely
        if (strpos(strtoupper($sql), 'LIMIT') === false) {
            $sql .= ' ' . $this->getLimitClause();
        }

        // inject SQL_CALC_FOUND_ROWS if not set explicitely
        if (strpos(strtoupper($sql), 'SQL_CALC_FOUND_ROWS') === false) {
            $sql = 'SELECT SQL_CALC_FOUND_ROWS ' . substr($sql, 6, strlen($sql));
        }

        $stmt = $this->databaseConnection->query($sql);
        $this->setFoundRows();

        return $stmt;
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
        $stmt = $this->databaseConnection->query('SELECT FOUND_ROWS() AS `foundRows`');
        $this->foundRows = (int) $stmt->fetch(\PDO::FETCH_COLUMN);
    }
}
