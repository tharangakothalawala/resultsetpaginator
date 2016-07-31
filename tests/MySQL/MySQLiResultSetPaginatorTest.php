<?php
/**
 * MySQLiResultSetPaginatorTest.php
 *
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator\Tests\MySQL;

use PHPUnit_Framework_TestCase;
use TSK\ResultSetPaginator\MySQL\MySQLiResultSetPaginator;
use TSK\ResultSetPaginator\Paginator\Page;

/**
 * Class MySQLiResultSetPaginatorTest
 * @package TSK\ResultSetPaginator\Tests\MySQL
 */
class MySQLiResultSetPaginatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testGetPagination()
    {
        // let's request for the page two
        $page = 2;
        // let's tell the paginator to display on two records per page
        $limit = 2;

        $paginator = new MySQLiResultSetPaginator($this->getDatabaseConnectionMock(), $page, $limit);
        $paginator->query(
            'SELECT NOW() UNION ALL SELECT NOW() UNION ALL SELECT NOW() UNION ALL SELECT NOW()'
        );

        // let's make sure we have expected 6 rows
        $expectedNumberOfRows = 6;
        $this->assertEquals($expectedNumberOfRows, $paginator->getFoundRows());

        // test if we get the following expected array of data
        $expectedPagination = array(
            // 1st page should be the current page as we requested
            new Page(false, 1, '<<'), // first page is 1
            new Page(false, 1, '<'), // previous page is 1 too
            new Page(false, 1, 1),
            new Page(true, 2, 2), // this is the current page
            new Page(false, 3, 3), // last page is page 3
            new Page(false, 3, '>'), // next page is 3
            new Page(false, 3, '>>'), // last page is 3 too
        );
        $this->assertEquals($expectedPagination, $paginator->getPagination());
    }

    /**
     * @return \mysqli
     */
    public function getDatabaseConnectionMock()
    {
        $resultSetObject = new \stdClass();
        $resultSetObject->foundRows = 6;

        $mysqliResultMock = $this->getMockBuilder('\mysqli_result')
            ->disableOriginalConstructor()
            ->getMock();
        $mysqliResultMock
            ->expects($this->any())
            ->method('fetch_object')
            ->will($this->returnValue($resultSetObject))
        ;

        $databaseConnectionMock = $this->getMockBuilder('\mysqli')
            ->disableOriginalConstructor()
            ->getMock();
        $databaseConnectionMock
            ->expects($this->any())
            ->method('query')
            ->withConsecutive(
                array(
                    'SELECT SQL_CALC_FOUND_ROWS  NOW() UNION ALL SELECT NOW() UNION ALL SELECT NOW() UNION ALL SELECT NOW() LIMIT 2, 2',
                ),
                array(
                    'SELECT FOUND_ROWS() AS `foundRows`'
                )
            )
            ->will($this->returnValue($mysqliResultMock))
        ;

        return $databaseConnectionMock;
    }
}
