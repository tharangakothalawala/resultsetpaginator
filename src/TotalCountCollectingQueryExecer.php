<?php
/**
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator;

use mysqli_result;
use PDOStatement;
use TSK\ResultSetPaginator\Paginator\PaginationProvider;

interface TotalCountCollectingQueryExecer
{
    /**
     * @param string $sql
     * @return PDOStatement|mysqli_result
     */
    public function query($sql);

    /**
     * @return PaginationProvider
     */
    public function paginationProvider();
}
