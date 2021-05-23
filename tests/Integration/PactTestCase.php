<?php declare(strict_types=1);

namespace Tests\Integration;

use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServer;
use PhpPact\Standalone\MockService\MockServerConfig;
use Tests\TestCase;

class PactTestCase extends TestCase
{
    private static ?MockServer         $server             = null;
    private static ?InteractionBuilder $interactionBuilder = null;
    private static bool                $isRunning          = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (! self::$server) {
            $this->setupMockServer();
        }
    }

    private function setupMockServer(): void
    {
        $config = new MockServerConfig();
        $config->setHost(config('pact.host'));
        $config->setPort(config('pact.port'));
        $config->setPactDir(config('pact.pact_dir'));
        $config->setConsumer(config('pact.consumer_name'));
        $config->setProvider(config('pact.provider_name'));
        $config->setCors(true);
        $config->setHealthCheckTimeout(1);
        $config->setHealthCheckRetrySec(1);
        $config->setLog(config('pact.log_path'));

        self::$server             = new MockServer($config);
        self::$interactionBuilder = new InteractionBuilder($config);
    }

    protected function mockInteraction(array $requestData, array $responseData): void
    {
        if (! self::$isRunning) {
            self::$server->start();
            self::$isRunning = true;
        }

        $request = $this->buildRequest($requestData);

        self::$interactionBuilder
            ->uponReceiving(sprintf('a %s request to %s', $request->getMethod(), $request->getPath()))
            ->with($request)
            ->willRespondWith($this->buildResponse($responseData));
    }

    private function buildRequest(array $requestData): ConsumerRequest
    {
        $request = new ConsumerRequest();
        $request->setMethod($requestData['method'] ?? 'GET');
        $request->setPath($requestData['path'] ?? '/');
        $request->setBody($requestData['body'] ?? null);

        if (! empty($requestData['headers'])) {
            $request->setHeaders($requestData['headers']);
        }

        return $request;
    }

    private function buildResponse(array $responseData): ProviderResponse
    {
        $response = new ProviderResponse();
        $response->setStatus($responseData['status'] ?? 200);
        $response->setBody($responseData['body'] ?? null);

        if (! empty($requestData['headers'])) {
            $response->setHeaders($responseData['headers']);
        }

        return $response;
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$isRunning) {
            self::$interactionBuilder->verify();
            self::$interactionBuilder->finalize();
            self::$server->stop();
        }
    }
}
