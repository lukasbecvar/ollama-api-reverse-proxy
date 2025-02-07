# Ollama API Reverse Proxy
A reverse proxy API that serves as a layer for the Ollama API, allowing to securely call Ollama from external applications while ensuring that requests are validated with API token.

## Authentication
The Ollama API Reverse Proxy uses an API token to authenticate requests. The API token is passed in the `X-API-TOKEN` header.

## Request examples
##### execute prompt (ollama generate)
```
curl -X POST http://localhost/api/ollama/prompt \
    -H "Content-Type: application/json" \
    -H "X-API-TOKEN: 1234" \
    -d '{
        "model": "deepseek-r1:8b",
        "prompt": "Hi, how are you?",
        "stream": false
    }'
```
##### execute chat prompt with context history
```
curl -X 'POST' 'http://localhost/api/ollama/chat' \
    -H 'accept: application/json' \
    -H 'X-API-TOKEN: 1234' \
    -H 'Content-Type: application/json' \
    -d '{
        "model": "deepseek-r1:1.5b",
        "messages": [
            {"role": "system", "content": "You are a helpful assistant."},
            {"role": "user", "content": "What is the capital of France?"},
            {"role": "assistant", "content": "The capital of France is Paris."},
            {"role": "user", "content": "And what about Germany?"}
        ],
    "stream": false
}'
```
##### get list of available models
```
curl -X GET http://localhost/api/ollama/models \
    -H "Content-Type: application/json" \
    -H "X-API-TOKEN: 1234"
```

## Dependencies & requirements
Based on [api-base](https://github.com/lukasbecvar/api-base)
* PHP 8.4
    * [Website](https://php.net)
* Symfony framework
    * [Website](https://symfony.com)
* PHPUnit
    * [Github](https://github.com/sebastianbergmann/phpunit)
* Better PHPUnit CLI output
    * [Github](https://github.com/robiningelbrecht/phpunit-pretty-print)
* PHPStan
    * [Github](https://github.com/phpstan/phpstan)

## License
This software is licensed under the [MIT license](LICENSE)
