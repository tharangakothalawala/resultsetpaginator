<?php
/**
 * PaginationFactory.php
 *
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator;

use Exception;
use TSK\ResultSetPaginator\Paginator\AbstractResultSetPaginator;
use TSK\ResultSetPaginator\MySQL\PdoResultSetPaginator;
use TSK\ResultSetPaginator\MySQL\MySQLiResultSetPaginator;

class PaginationFactory
{
    /**
     * @var AbstractResultSetPaginator $paginator
     */
    private $paginator = null;

    /**
     * Constructor
     *
     * @param object $databaseConnectionObject
     * @param integer $page the page number
     * @param integer $limit the number of results per page
     * @throws Exception
     */
    public function __construct($databaseConnectionObject, $page, $limit)
    {
        $class = get_class($databaseConnectionObject);
        switch ($class) {
            case 'PDO':
                $this->paginator = new PdoResultSetPaginator($databaseConnectionObject, $page, $limit);
                break;

            case 'mysqli':
                $this->paginator = new MySQLiResultSetPaginator($databaseConnectionObject, $page, $limit);
                break;

            default:
                throw new Exception(_('Database Connection is not supported yet'));
        }
    }

    public function getPaginator()
    {
        return $this->paginator;
    }
}
