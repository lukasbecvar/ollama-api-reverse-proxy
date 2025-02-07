<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class IndexControllerTest
 *
 * Test cases for ollama api proxy controller
 *
 * @package App\Tests\Controller
 */
class OllamaApiControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test get models list when request method is not valid
     *
     * @return void
     */
    public function testGetModelsListWhenRequestMethodIsNotValid(): void
    {
        $this->client->request('POST', '/api/ollama/models');

        /** @var array<mixed> $responseData */
        $responseData = json_decode(($this->client->getResponse()->getContent() ?: '{}'), true);

        // assert response
        $this->assertSame('error', $responseData['status']);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * Test get models list when api token is not provided
     *
     * @return void
     */
    public function testGetModelsListWhenApiTokenIsNotProvided(): void
    {
        $this->client->request('GET', '/api/ollama/models');

        /** @var array<mixed> $responseData */
        $responseData = json_decode(($this->client->getResponse()->getContent() ?: '{}'), true);

        // assert response
        $this->assertSame('Invalid access token.', $responseData['message']);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Test get models list when api token is invalid
     *
     * @return void
     */
    public function testGetModelsListWhenApiTokenIsInvalid(): void
    {
        $this->client->request('GET', '/api/ollama/models', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_API_TOKEN' => 'invalid-token',
        ]);

        /** @var array<mixed> $responseData */
        $responseData = json_decode(($this->client->getResponse()->getContent() ?: '{}'), true);

        // assert response
        $this->assertSame('Invalid access token.', $responseData['message']);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Test get models list with success response
     *
     * @return void
     */
    public function testGetModelsListWithSuccessResponse(): void
    {
        // mock ollama api response
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(JsonResponse::HTTP_OK);
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())->method('request')->willReturn($mockResponse);
        $this->client->getContainer()->set(HttpClientInterface::class, $mockHttpClient);

        // send request to models list controller
        $this->client->request('GET', '/api/ollama/models', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_API_TOKEN' => $_ENV['API_TOKEN'],
        ]);

        /** @var array<mixed> $responseData */
        $responseData = json_decode(($this->client->getResponse()->getContent() ?: '{}'), true);

        // assert response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }

    /**
     * Test execute prompt when request method is not valid
     *
     * @return void
     */
    public function testExecutePromptWhenRequestMethodIsNotValid(): void
    {
        $this->client->request('GET', '/api/ollama/prompt');

        /** @var array<mixed> $responseData */
        $responseData = json_decode(($this->client->getResponse()->getContent() ?: '{}'), true);

        // assert response
        $this->assertSame('error', $responseData['status']);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * Test execute prompt when api token is not provided
     *
     * @return void
     */
    public function testExecutePromptWhenApiTokenIsNotProvided(): void
    {
        $this->client->request('POST', '/api/ollama/prompt', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);

        /** @var array<mixed> $responseData */
        $responseData = json_decode(($this->client->getResponse()->getContent() ?: '{}'), true);

        // assert response
        $this->assertSame('Invalid access token.', $responseData['message']);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Test execute prompt when api token is invalid
     *
     * @return void
     */
    public function testExecutePromptWhenApiTokenIsInvalid(): void
    {
        $this->client->request('POST', '/api/ollama/prompt', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_API_TOKEN' => 'invalid-token',
        ]);

        /** @var array<mixed> $responseData */
        $responseData = json_decode(($this->client->getResponse()->getContent() ?: '{}'), true);

        // assert response
        $this->assertSame('Invalid access token.', $responseData['message']);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Test execute prompt when request parameters is missing
     *
     * @return void
     */
    public function testExecutePromptWhenRequestParametersIsMissing(): void
    {
        $this->client->request('POST', '/api/ollama/prompt', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_API_TOKEN' => $_ENV['API_TOKEN'],
        ]);

        /** @var array<mixed> $responseData */
        $responseData = json_decode(($this->client->getResponse()->getContent() ?: '{}'), true);

        // assert response
        $this->assertSame('error', $responseData['status']);
        $this->assertSame('Missing required parameters', $responseData['message']);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * Test execute prompt with success response
     *
     * @return void
     */
    public function testExecutePromptWithSuccessResponse(): void
    {
        // mock ollama api response
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(JsonResponse::HTTP_OK);
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())->method('request')->willReturn($mockResponse);
        $this->client->getContainer()->set(HttpClientInterface::class, $mockHttpClient);

        // send request to prompt execute controller
        $this->client->request('POST', '/api/ollama/prompt', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_API_TOKEN' => $_ENV['API_TOKEN']
        ], json_encode([
            'model' => 'deepseek-r1:8b',
            'prompt' => 'Hi, how are you?',
            'stream' => false
        ]) ?: null);

        // assert response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }

    /**
     * Test execute chat when request method is not valid
     *
     * @return void
     */
    public function testExecuteChatWhenRequestMethodIsNotValid(): void
    {
        $this->client->request('GET', '/api/ollama/chat');

        /** @var array<mixed> $responseData */
        $responseData = json_decode(($this->client->getResponse()->getContent() ?: '{}'), true);

        // assert response
        $this->assertSame('error', $responseData['status']);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * Test execute chat when api token is not provided
     *
     * @return void
     */
    public function testExecuteChatWhenApiTokenIsNotProvided(): void
    {
        $this->client->request('POST', '/api/ollama/chat', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);

        /** @var array<mixed> $responseData */
        $responseData = json_decode(($this->client->getResponse()->getContent() ?: '{}'), true);

        // assert response
        $this->assertSame('Invalid access token.', $responseData['message']);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Test execute chat when api token is invalid
     *
     * @return void
     */
    public function testExecuteChatWhenApiTokenIsInvalid(): void
    {
        $this->client->request('POST', '/api/ollama/chat', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_API_TOKEN' => 'invalid-token',
        ]);

        /** @var array<mixed> $responseData */
        $responseData = json_decode(($this->client->getResponse()->getContent() ?: '{}'), true);

        // assert response
        $this->assertSame('Invalid access token.', $responseData['message']);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Test execute chat when request parameters is missing
     *
     * @return void
     */
    public function testExecuteChatWhenRequestParametersIsMissing(): void
    {
        $this->client->request('POST', '/api/ollama/chat', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_API_TOKEN' => $_ENV['API_TOKEN'],
        ]);

        /** @var array<mixed> $responseData */
        $responseData = json_decode(($this->client->getResponse()->getContent() ?: '{}'), true);

        // assert response
        $this->assertSame('error', $responseData['status']);
        $this->assertSame('Missing required parameters', $responseData['message']);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * Test execute chat with success response
     *
     * @return void
     */
    public function testExecuteChatWithSuccessResponse(): void
    {
        // mock ollama api response
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(JsonResponse::HTTP_OK);
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())->method('request')->willReturn($mockResponse);
        $this->client->getContainer()->set(HttpClientInterface::class, $mockHttpClient);

        // send request to chat execute controller
        $this->client->request('POST', '/api/ollama/chat', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_API_TOKEN' => $_ENV['API_TOKEN']
        ], json_encode([
            'model' => 'deepseek-r1:1.5b',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => 'What is the capital of France?'],
                ['role' => 'assistant', 'content' => 'The capital of France is Paris.'],
                ['role' => 'user', 'content' => 'And what about Germany?']
            ],
            'stream' => false
        ]) ?: null);

        // assert response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }
}
