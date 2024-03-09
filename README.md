# OpenSearch Client for Laravel

---

The official PHP OpenSearch client integrated with Laravel.

## Contents

* [Compatibility](#compatibility)
* [Installation](#installation) 
* [Configuration](#configuration)
* [Usage](#usage)

## Compatibility

The current version of OpenSearch Client has been tested with the following configuration:

* PHP 7.4-8.x
* OpenSearch 2.x 
* Laravel 6.x-10.x

## Installation

The library can be installed via Composer:

```bash
composer require haridarshan/opensearch-client
```

## Configuration

To change the client settings you need to publish the configuration file first:

```bash
php artisan vendor:publish --provider="OpenSearch\Laravel\Client\ServiceProvider"
```

In the newly created `config/opensearch.client.php` file you can define the default connection name and describe multiple 
connections using configuration hashes. You can read more about building the client from a configuration hash [here](https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/node_pool.html#config-hash).

```php
return [
    'default' => env('OPENSEARCH_CONNECTION', 'default'),
    'connections' => [
        'default' => [
            'hosts' => [
                env('OPENSEARCH_HOST', 'localhost:9200'),
            ],
            // configure basic authentication
            'basicAuthentication' => [
                env('OPENSEARCH_USERNAME'),
                env('OPENSEARCH_PASSWORD'),
            ],
            // disable SSL Verification
            'sslVerification' => env('OPENSEARCH_SSL_VERIFICATION', false),
            // configure HTTP client (Guzzle by default)
            'httpClientOptions' => [
                'timeout' => 2,
            ],
        ],
    ],
];
```

If you need more control over the client creation, you can create your own client builder:

```php
// see OpenSearch\Laravel\Client\ClientBuilder for the reference
class MyClientBuilder implements OpenSearch\Laravel\Client\ClientBuilderInterface
{
    public function default(): Client
    {
        // should return a client instance for the default connection 
    }
    
    public function connection(string $name): Client
    {
        // should return a client instance for the connection with the given name 
    }
}
```

Do not forget to register the builder in your application service provider:

```php
class MyAppServiceProvider extends Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ClientBuilderInterface::class, MyClientBuilder::class);
    }
}
```

## Usage

Use `OpenSearch\Laravel\Client\ClientBuilderInterface` to get access to the client instance:

```php
namespace App\Console\Commands;

use OpenSearch\Client;
use OpenSearch\Laravel\Client\ClientBuilderInterface;
use Illuminate\Console\Command;

class CreateIndex extends Command
{
    protected $signature = 'create:index {name}';

    protected $description = 'Creates an index';

    public function handle(ClientBuilderInterface $clientBuilder)
    {
        // get a client for the default connection
        $client = $clientBuilder->default();
        // get a client for the connection with name "write"
        $client = $clientBuilder->connection('write');
    
        $client->indices()->create([
            'index' => $this->argument('name')
        ]);
    }
}
```
