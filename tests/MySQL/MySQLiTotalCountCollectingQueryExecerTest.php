<?php
/**
 * @author : Tharanga Kothalawala <tharanga.kothalawala@tsk-webdevelopment.com>
 * @date   : 14/07/2018
 */

namespace TSK\ResultSetPaginator\MySQL;

use Mockery;
use Mockery\MockInterface;
use mysqli;
use PHPUnit\Framework\TestCase;
use stdClass;
use TSK\ResultSetPaginator\Paginator\PaginationProvider;

class MySQLiTotalCountCollectingQueryExecerTest extends TestCase
{
    const TEST_FOUND_TOTAL_COUNT = 1000;

    /**
     * @test
     */
    public function testGetPagination()
    {
        $testCurrentPage = 2;
        $testLimit = 10;

        /** @var MockInterface|PaginationProvider $paginationProviderMock */
        $paginationProviderMock = Mockery::mock('\TSK\ResultSetPaginator\Paginator\PaginationProvider');
        $paginationProviderMock->shouldReceive('offset')->once()->andReturn($testCurrentPage);
        $paginationProviderMock->shouldReceive('limit')->once()->andReturn($testLimit);
        $paginationProviderMock->shouldReceive('setTotalCount')->once()->with(self::TEST_FOUND_TOTAL_COUNT);
        $sut = new MySQLiTotalCountCollectingQueryExecer(
            $this->getDatabaseConnectionMock($testCurrentPage, $testLimit),
            $paginationProviderMock
        );
        $sut->query(
            'SELECT NOW() UNION ALL SELECT NOW()'
        );

        $this->assertSame($paginationProviderMock, $sut->paginationProvider());
    }

    /**
     * @return mysqli|MockInterface
     */
    public function getDatabaseConnectionMock($testCurrentPage, $testLimit)
    {
        $resultSetObject = new stdClass();
        $resultSetObject->totalCount = self::TEST_FOUND_TOTAL_COUNT;

        $pdoStatementMock = Mockery::mock('\mysqli_result');
        $pdoStatementMock
            ->shouldReceive('fetch_object')
            ->once()
            ->andReturn($resultSetObject);

        $databaseConnectionMock = Mockery::mock('\mysqli');
        $databaseConnectionMock
            ->shouldReceive('query')
            ->once()
            ->with(
                sprintf(
                    'SELECT SQL_CALC_FOUND_ROWS  NOW() UNION ALL SELECT NOW() LIMIT %d, %d',
                    $testCurrentPage,
                    $testLimit
                )
            )
            ->andReturn($pdoStatementMock);
        $databaseConnectionMock
            ->shouldReceive('query')
            ->once()
            ->with('SELECT FOUND_ROWS() AS `totalCount`')
            ->andReturn($pdoStatementMock);

        return $databaseConnectionMock;
    }
}
