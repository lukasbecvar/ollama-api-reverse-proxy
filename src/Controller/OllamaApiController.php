<?php

namespace App\Controller;

use Exception;
use App\Util\AppUtil;
use OpenApi\Attributes\Tag;
use App\Manager\ErrorManager;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\RequestBody;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class OllamaApiController
 *
 * Controller for handling requests to Ollama API
 *
 * @package App\Controller
 */
class OllamaApiController extends AbstractController
{
    private AppUtil $appUtil;
    private ErrorManager $errorManager;
    private HttpClientInterface $httpClient;

    public function __construct(AppUtil $appUtil, ErrorManager $errorManager, HttpClientInterface $httpClient)
    {
        $this->appUtil = $appUtil;
        $this->httpClient = $httpClient;
        $this->errorManager = $errorManager;
    }

    /**
     * Get available models list from ollama
     *
     * @return JsonResponse List of available models from Ollama
     */
    #[Tag('ollama-api')]
    #[Response(
        response: JsonResponse::HTTP_OK,
        description: 'List of available models from Ollama',
        content: new JsonContent(
            type: 'object',
            properties: [
                new Property(property: 'model_name', type: 'string', example: 'deepseek-r1:8b'),
                new Property(property: 'status', type: 'string', example: 'available')
            ]
        )
    )]
    #[Route('/api/ollama/models', name: 'ollama_models', methods: ['GET'])]
    public function getModelsList(): JsonResponse
    {
        try {
            // send request to ollama api
            $response = $this->httpClient->request('GET', $this->appUtil->getEnvValue('OLLAMA_API_URL') . '/api/tags');

            // get response status code
            $statusCode = $response->getStatusCode();
            $data = $response->toArray(false);

            // return response
            return $this->json($data, $statusCode);
        } catch (Exception $e) {
            $this->errorManager->handleError(
                message: 'Error to get models list from Ollama API',
                code: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                exceptionMessage: $e->getMessage()
            );
        }
    }

    /**
     * Execute prompt using the Ollama API
     *
     * @param Request $request Request object
     *
     * @return JsonResponse Response from Ollama API
     */
    #[Response(
        response: JsonResponse::HTTP_OK,
        description: 'Successfully proxied the request to Ollama API',
        content: new JsonContent(
            type: 'object',
            properties: [
                new Property(property: 'response', type: 'object', description: 'The response from Ollama API')
            ]
        )
    )]
    #[Response(
        response: JsonResponse::HTTP_BAD_REQUEST,
        description: 'Bad Request - Missing required parameters',
        content: new JsonContent(
            type: 'object',
            properties: [
                new Property(property: 'status', type: 'string', example: 'error'),
                new Property(property: 'message', type: 'string', example: 'Missing required parameters')
            ]
        )
    )]
    #[RequestBody(
        content: new JsonContent(
            type: 'object',
            properties: [
                new Property(property: 'model', type: 'string', example: 'deepseek-r1:8b'),
                new Property(property: 'prompt', type: 'string', example: 'Hello, how are you?'),
                new Property(property: 'stream', type: 'boolean', example: false)
            ]
        )
    )]
    #[Tag('ollama-api')]
    #[Route('/api/ollama/prompt', name: 'ollama_proxy', methods: ['POST'])]
    public function executePrompt(Request $request): JsonResponse
    {
        // get data from request
        $content = json_decode($request->getContent(), true);

        // check if required parameters are set
        if (!isset($content['model']) || !isset($content['prompt'])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Missing required parameters'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // send request to ollama api
        $response = $this->httpClient->request('POST', $this->appUtil->getEnvValue('OLLAMA_API_URL') . '/api/generate', [
            'json' => $content,
        ]);

        // get response status code
        $statusCode = $response->getStatusCode();
        $data = $response->toArray(false);

        // return response
        return new JsonResponse(json_encode($data, JSON_UNESCAPED_SLASHES), $statusCode, json: true);
    }

    /**
     * Execute chat with Ollama API
     *
     * @param Request $request Request object
     *
     * @return JsonResponse Response from Ollama API
     */
    #[Response(
        response: JsonResponse::HTTP_OK,
        description: 'Successfully proxied the chat request to Ollama API',
        content: new JsonContent(
            type: 'object',
            properties: [
                new Property(property: 'message', type: 'object', description: 'The chat response from Ollama API')
            ]
        )
    )]
    #[Response(
        response: JsonResponse::HTTP_BAD_REQUEST,
        description: 'Bad Request - Missing required parameters',
        content: new JsonContent(
            type: 'object',
            properties: [
                new Property(property: 'status', type: 'string', example: 'error'),
                new Property(property: 'message', type: 'string', example: 'Missing required parameters')
            ]
        )
    )]
    #[RequestBody(
        content: new JsonContent(
            type: 'object',
            properties: [
                new Property(property: 'model', type: 'string', example: 'deepseek-r1:8b'),
                new Property(
                    property: 'messages',
                    type: 'array',
                    items: new Items(
                        properties: [
                            new Property(property: 'role', type: 'string', example: 'user'),
                            new Property(property: 'content', type: 'string', example: 'Hello, how are you?')
                        ]
                    ),
                ),
                new Property(property: 'stream', type: 'boolean', example: false)
            ]
        )
    )]
    #[Tag('ollama-api')]
    #[Route('/api/ollama/chat', name: 'ollama_chat', methods: ['POST'])]
    public function executeChat(Request $request): JsonResponse
    {
        // get data from request
        $content = json_decode($request->getContent(), true);

        // check if required parameters are set
        if (!isset($content['model']) || !isset($content['messages'])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Missing required parameters'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // send request to ollama chat API
        $response = $this->httpClient->request('POST', $this->appUtil->getEnvValue('OLLAMA_API_URL') . '/api/chat', [
            'json' => $content,
        ]);

        // get response status code
        $statusCode = $response->getStatusCode();
        $data = $response->toArray(false);

        // return response
        return new JsonResponse(json_encode($data, JSON_UNESCAPED_SLASHES), $statusCode, json: true);
    }
}
