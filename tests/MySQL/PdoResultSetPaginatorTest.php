<?php
/**
 * PdoResultSetPaginatorTest.php
 *
 * @author Tharanga S Kothalawala <tharanga.kothalawala@gmail.com>
 * @since 21-11-2015
 */

namespace TSK\ResultSetPaginator\Tests\MySQL;

use PHPUnit_Framework_TestCase;
use TSK\ResultSetPaginator\MySQL\PdoResultSetPaginator;
use TSK\ResultSetPaginator\Paginator\Page;

/**
 * Class PdoResultSetPaginatorTest
 * @package TSK\ResultSetPaginator\Tests\MySQL
 */
class PdoResultSetPaginatorTest extends PHPUnit_Framework_TestCase
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

        $paginator = new PdoResultSetPaginator($this->getDatabaseConnectionMock(), $page, $limit);
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
     * @return \PDO
     */
    public function getDatabaseConnectionMock()
    {
        $pdoStatementMock = $this->createMock('\PDOStatement');
        $pdoStatementMock
            ->expects($this->any())
            ->method('fetch')
            ->will($this->returnValue(6))
        ;

        $databaseConnectionMock = $this->getMockBuilder('\PDO')
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
            ->will($this->returnValue($pdoStatementMock))
        ;

        return $databaseConnectionMock;
    }
}
