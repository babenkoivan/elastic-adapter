<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Search;

use Elastic\Adapter\Search\PointInTimeManager;
use Elastic\Client\ClientBuilderInterface;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Response\Elasticsearch;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Search\PointInTimeManager
 */
final class PointInTimeManagerTest extends TestCase
{
    private MockObject $client;
    private PointInTimeManager $pointInTimeManager;

    /**
     * @noinspection ClassMockingCorrectnessInspection
     * @noinspection PhpUnitInvalidMockingEntityInspection
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(Client::class);
        $this->client->method('setAsync')->willReturnSelf();

        $clientBuilder = $this->createMock(ClientBuilderInterface::class);
        $clientBuilder->method('default')->willReturn($this->client);

        $this->pointInTimeManager = new PointInTimeManager($clientBuilder);
    }

    public function test_point_in_time_can_be_opened(): void
    {
        $response = $this->createMock(Elasticsearch::class);

        $response
            ->expects($this->once())
            ->method('asArray')
            ->willReturn([
                'id' => '46ToAwMDaWR5BXV1',
            ]);

        $this->client
            ->expects($this->once())
            ->method('openPointInTime')
            ->with([
                'index' => 'test',
                'keep_alive' => '1m',
            ])
            ->willReturn($response);

        $this->assertSame('46ToAwMDaWR5BXV1', $this->pointInTimeManager->open('test', '1m'));
    }

    public function test_point_in_time_can_be_closed(): void
    {
        $this->client
            ->expects($this->once())
            ->method('closePointInTime')
            ->with([
                'body' => [
                    'id' => '46ToAwMDaWR5BXV1',
                ],
            ]);

        $this->assertSame($this->pointInTimeManager, $this->pointInTimeManager->close('46ToAwMDaWR5BXV1'));
    }

    /**
     * @noinspection ClassMockingCorrectnessInspection
     * @noinspection PhpUnitInvalidMockingEntityInspection
     */
    public function test_connection_can_be_changed(): void
    {
        $defaultClient = $this->createMock(Client::class);
        $defaultClient->method('setAsync')->willReturnSelf();

        $defaultClient
            ->expects($this->never())
            ->method('closePointInTime');

        $testClient = $this->createMock(Client::class);
        $testClient->method('setAsync')->willReturnSelf();

        $testClient
            ->expects($this->once())
            ->method('closePointInTime')
            ->with([
                'body' => [
                    'id' => 'foo',
                ],
            ]);

        $clientBuilder = $this->createMock(ClientBuilderInterface::class);
        $clientBuilder->method('default')->willReturn($defaultClient);
        $clientBuilder->method('connection')->with('test')->willReturn($testClient);

        (new PointInTimeManager($clientBuilder))->connection('test')->close('foo');
    }
}
