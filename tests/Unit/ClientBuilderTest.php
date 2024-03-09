<?php declare(strict_types=1);

namespace OpenSearch\Laravel\Client\Tests\Unit;

use ErrorException;
use OpenSearch\Client;
use OpenSearch\Laravel\Client\ClientBuilder;
use Orchestra\Testbench\TestCase;

/**
 * @covers \OpenSearch\Laravel\Client\ClientBuilder
 */
final class ClientBuilderTest extends TestCase
{
    private ClientBuilder $clientBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('opensearch.client', [
            'default' => 'read',
            'connections' => [
                'read' => [
                    'hosts' => ['https://read.io'],
                ],
                'write' => [
                    'hosts' => ['https://write.io'],
                ],
            ],
        ]);

        $this->clientBuilder = new ClientBuilder();
    }

    public function test_client_with_default_connection_can_be_built(): void
    {
        $client = $this->clientBuilder->default();
        $this->assertHost($client, 'https://read.io');
    }

    public function test_client_with_existing_connection_can_be_built(): void
    {
        $client = $this->clientBuilder->connection('write');
        $this->assertHost($client, 'https://write.io');
    }

    public function test_exception_is_thrown_when_building_client_with_non_existing_connection(): void
    {
        $this->expectException(ErrorException::class);
        $this->clientBuilder->connection('foo');
    }

    private function assertHost(Client $client, string $host): void
    {
        $transport = $client->transport;
        $node = $transport->connectionPool->nextConnection();

        $this->assertSame($host, "{$node->getTransportSchema()}://{$node->getHost()}");
    }
}
