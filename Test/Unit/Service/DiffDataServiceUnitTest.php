<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace Magenerds\SystemDiff\Test\Unit\Service;

use Magenerds\SystemDiff\Differ\DifferInterface;
use Magenerds\SystemDiff\Differ\DifferPool;
use Magenerds\SystemDiff\Service\DiffDataService;

class DiffDataServiceUnitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DifferPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $differPoolMock;

    /**
     * @var DiffDataService
     */
    private $sut;

    protected function setUp()
    {
        $this->differPoolMock = $this->getMockBuilder(DifferPool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new DiffDataService(
            $this->differPoolMock
        );
    }

    /**
     * @test
     */
    public function diffData()
    {
        $localData = ['foo' => 1];
        $remoteData = ['foo' => 2];
        $exampleDifferenceResult = ['foo' => 2];

        $differMock = $this->getMockBuilder(DifferInterface::class)->getMock();

        $differMock
            ->expects($this->once())
            ->method('diff')
            ->with($localData, $remoteData)
            ->willReturn($exampleDifferenceResult);

        $this->differPoolMock
            ->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator(['example' => $differMock]));

        $differences = $this->sut->diffData($localData, $remoteData);
        $this->assertArrayHasKey('example', $differences);
        $this->assertEquals(['example' => $exampleDifferenceResult], $differences);
    }
}