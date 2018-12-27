<?php
/**
 * PaginationFactory.php
 *
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator;

use InvalidArgumentException;
use mysqli;
use PDO;
use TSK\ResultSetPaginator\MySQL\PdoTotalCountCollectingQueryExecer;
use TSK\ResultSetPaginator\MySQL\MySQLiTotalCountCollectingQueryExecer;
use TSK\ResultSetPaginator\Paginator\PaginationProvider;

class QueryExecerFactory
{
    /**
     * @var TotalCountCollectingQueryExecer $queryExecer
     */
    private $queryExecer = null;

    /**
     * QueryExecerFactory constructor.
     * @param PDO|mysqli $databaseConnectionObject
     * @param int $currentPage
     * @param int $limit
     * @throws InvalidArgumentException
     */
    public function __construct($databaseConnectionObject, $currentPage, $limit)
    {
        $class = get_class($databaseConnectionObject);
        switch ($class) {
            case 'PDO':
                $this->queryExecer = new PdoTotalCountCollectingQueryExecer(
                    $databaseConnectionObject,
                    new PaginationProvider($currentPage, $limit)
                );
                break;

            case 'mysqli':
                $this->queryExecer = new MySQLiTotalCountCollectingQueryExecer(
                    $databaseConnectionObject,
                    new PaginationProvider($currentPage, $limit)
                );
                break;

            default:
                throw new InvalidArgumentException(
                    sprintf('Given \'%s\' database connection is not yet supported', $class)
                );
        }
    }

    /**
     * @return TotalCountCollectingQueryExecer
     */
    public function getQueryExecer()
    {
        return $this->queryExecer;
    }
}
