<?php
/**
 * @author : Tharanga Kothalawala <tharanga.kothalawala@tsk-webdevelopment.com>
 * @date   : 14/07/2018
 */

namespace TSK\ResultSetPaginator\Paginator;

use PHPUnit\Framework\TestCase;

class PaginationProviderTest extends TestCase
{
    /**
     * @var PaginationProvider
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new PaginationProvider(3, 10, 100);
    }

    /**
     * @test
     */
    public function shouldSendExpectedPagesAsperTotalCount()
    {
        // test if we get the following expected array of data
        $expectedPagination = array(
            // 1st page should be the current page as we requested
            new Page(false, 1, '<<'), // first page is 1
            new Page(false, 2, '<'), // previous page is 2
            new Page(false, 1, 1),
            new Page(false, 2, 2),
            new Page(true, 3, 3), // this is the current page
            new Page(false, 4, 4),
            new Page(false, 5, 5),
            new Page(false, 6, 6),
            new Page(false, 4, '>'), // next page is 4
            new Page(false, 10, '>>'), // last page is 10
        );
        $this->assertEquals($expectedPagination, $this->sut->pages());

        // LIMIT 20, 10
        $this->assertEquals(20, $this->sut->offset());
        $this->assertEquals(10, $this->sut->limit());
        $this->assertEquals(100, $this->sut->totalCount());
    }

    /**
     * @test
     */
    public function shouldDisplayRequestedIntermediateVisiblePages()
    {
        $this->sut->setVisiblePaginationRange(1);

        // test if we get the following expected array of data
        $expectedPagination = array(
            // 1st page should be the current page as we requested
            new Page(false, 1, '<<'), // first page is 1
            new Page(false, 2, '<'), // previous page is 2
            new Page(false, 2, 2),
            new Page(true, 3, 3), // this is the current page
            new Page(false, 4, 4),
            new Page(false, 4, '>'), // next page is 4
            new Page(false, 10, '>>'), // last page is 10
        );
        $this->assertEquals($expectedPagination, $this->sut->pages());

        // LIMIT 20, 10
        $this->assertEquals(20, $this->sut->offset());
        $this->assertEquals(10, $this->sut->limit());
        $this->assertEquals(100, $this->sut->totalCount());
    }
}
