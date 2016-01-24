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
        $offset = 0;
        if (intval($page) > 1) {
            $offset = ($page - 1) * $limit;
        } elseif (intval($page) < 1) {
            throw new Exception(_('Invalid page number'));
        }

        $class = get_class($databaseConnectionObject);
        switch ($class) {
            case 'PDO':
                $this->paginator = new PdoResultSetPaginator($databaseConnectionObject, $offset, $limit);
                $this->paginator->setCurrentPage($page);
                break;

            case 'mysqli':
                $this->paginator = new MySQLiResultSetPaginator($databaseConnectionObject, $offset, $limit);
                $this->paginator->setCurrentPage($page);
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
