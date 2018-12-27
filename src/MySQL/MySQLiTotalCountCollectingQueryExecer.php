<?php
/**
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator\MySQL;

use Exception;
use mysqli;
use mysqli_result;
use TSK\ResultSetPaginator\Paginator\PaginationProvider;
use TSK\ResultSetPaginator\TotalCountCollectingQueryExecer;

class MySQLiTotalCountCollectingQueryExecer implements TotalCountCollectingQueryExecer
{
    /**
     * @var mysqli $databaseConnection
     */
    private $databaseConnection;

    /**
     * @var PaginationProvider
     */
    private $paginationProvider;

    /**
     * MySQLiTotalCountCollectingQueryExecer constructor.
     * @param mysqli $databaseConnection
     * @param PaginationProvider $paginationProvider
     */
    public function __construct(mysqli $databaseConnection, PaginationProvider $paginationProvider)
    {
        $this->databaseConnection = $databaseConnection;
        $this->paginationProvider = $paginationProvider;
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
            $sql .= sprintf(' LIMIT %s, %s', $this->paginationProvider->offset(), $this->paginationProvider->limit());
        }

        // inject SQL_CALC_FOUND_ROWS if not set explicitly
        if (strpos(strtoupper($sql), 'SQL_CALC_FOUND_ROWS') === false) {
            $sql = 'SELECT SQL_CALC_FOUND_ROWS ' . substr($sql, 6, strlen($sql));
        }

        $result = $this->databaseConnection->query($sql);
        if (!$result) {
            throw new Exception($this->databaseConnection->error);
        }

        $this->loadTotalCount();

        return $result;
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
        $result = $this->databaseConnection->query('SELECT FOUND_ROWS() AS `totalCount`');
        $this->paginationProvider->setTotalCount($result->fetch_object()->totalCount);
    }
}
