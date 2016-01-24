<?php
/**
 * MySQLiResultSetPaginatorTest.php
 *
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator\Tests\MySQL;

use mysqli;
use mysqli_result;
use TSK\ResultSetPaginator\PaginationFactory;

class MySQLiResultSetPaginatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sorry, I know this is not really a unit test with having a live db connection
     * @test
     */
    public function testGetPagination()
    {
        // let's request for the page one
        $page = 1;
        // let's tell the paginator to display on two records per page
        $limit = 2;
        // establish a database connection
        $dbConn = new mysqli('localhost', 'tharanga', 'qwerty', 'test');

        $paginationFactory = new PaginationFactory($dbConn, $page, $limit);
        $paginator = $paginationFactory->getPaginator();

        /** @var mysqli_result $result */
        $result = $paginator->query('SELECT NOW() UNION ALL SELECT NOW() UNION ALL SELECT NOW() UNION ALL SELECT NOW()');

        // let's make sure we have expected 4 rows
        $expectedNumberOfRows = 4;
        $this->assertEquals($expectedNumberOfRows, $paginator->getFoundRows());

        // test if we get the following expected array of data
        $expectedPagination = array(
            // 1st page should be the current page as we requested
            array('isCurrentPage' => true, 'pageNumber' => 1, 'displayValue' => 1),
            array('isCurrentPage' => false, 'pageNumber' => 2, 'displayValue' => 2),
            array('isCurrentPage' => false, 'pageNumber' => 2, 'displayValue' => '>'),
            array('isCurrentPage' => false, 'pageNumber' => 2, 'displayValue' => '>>'),
        );
        $this->assertEquals($expectedPagination, $paginator->getPagination());
    }
}
