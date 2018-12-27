<?php
/**
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator\MySQL;

use PDO;
use PDOStatement;
use TSK\ResultSetPaginator\Paginator\PaginationProvider;
use TSK\ResultSetPaginator\TotalCountCollectingQueryExecer;

class PdoTotalCountCollectingQueryExecer implements TotalCountCollectingQueryExecer
{
    /**
     * @var PDO $databaseConnection
     */
    private $databaseConnection;

    /**
     * @var PaginationProvider
     */
    private $paginationProvider;

    /**
     * PdoTotalCountCollectingQueryExecer constructor.
     * @param PDO $databaseConnection
     * @param PaginationProvider $paginationProvider
     */
    public function __construct(PDO $databaseConnection, PaginationProvider $paginationProvider)
    {
        $this->databaseConnection = $databaseConnection;
        $this->paginationProvider = $paginationProvider;
    }

    /**
     * @param string $sql sql statement
     *
     * @return PDOStatement
     */
    public function query($sql)
    {
        $sql = trim($sql);

        // inject LIMIT if not set explicitly
        if (strpos(strtoupper($sql), 'LIMIT') === false) {
            $sql .= sprintf(' LIMIT %s, %s', $this->paginationProvider->offset(), $this->paginationProvider->limit());
        }

        // inject SQL_CALC_FOUND_ROWS if not set explicitly
        if (strpos(strtoupper($sql), 'SQL_CALC_FOUND_ROWS') === false) {
            $sql = 'SELECT SQL_CALC_FOUND_ROWS ' . substr($sql, 6, strlen($sql));
        }

        $stmt = $this->databaseConnection->query($sql);
        $this->loadTotalCount();

        return $stmt;
    }

    /**
     * @return PaginationProvider
     */
    public function paginationProvider()
    {
        return $this->paginationProvider;
    }

    private function loadTotalCount()
    {
        $stmt = $this->databaseConnection->query('SELECT FOUND_ROWS() AS `totalCount`');
        $this->paginationProvider->setTotalCount($stmt->fetch(PDO::FETCH_COLUMN));
    }
}
