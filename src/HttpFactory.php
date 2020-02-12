<?php

namespace Bag2\HttpTest;

use Psr\Http\Message\{RequestFactoryInterface, RequestInterface, ResponseFactoryInterface, ResponseInterface, ServerRequestFactoryInterface, ServerRequestInterface, UriInterface};

final class HttpFactory implements RequestFactoryInterface, ResponseFactoryInterface
{
    /** @var int */
    private $backtrace_flag;
    /** @var int */
    private $backtrace_number;
    /** @var RequestFactoryInterface */
    private $request_factory;
    /** @var ResponseFactoryInterface */
    private $response_factory;
    /** @var ServerRequestFactoryInterface */
    private $server_request_factory;

    /**
     * @param array{flag?:int,number?:int} $backtrace_params
     */
    public function __construct(
        RequestFactoryInterface $request_factory,
        ResponseFactoryInterface $response_factory,
        ServerRequestFactoryInterface $server_request_factory,
        array $backtrace_params = []
    ) {
        $this->backtrace_flag = $backtrace_params['flag'] ?? 0;
        $this->backtrace_number = $backtrace_params['number'] ?? 1;
        $this->request_factory = $request_factory;
        $this->response_factory = $response_factory;
        $this->server_request_factory = $server_request_factory;
    }

    /**
     * @phpstan-param RequestFactoryInterface&ResponseFactoryInterface&ServerRequestFactoryInterface $http_factory
     * @param array{flag?:int,number?:int} $backtrace_params
     * @phan-suppress PhanTypeMismatchArgument
     */
    public static function fromHttpFactories (ServerRequestFactoryInterface $http_factory, array $backtrace_params = []): self
    {
        return new self($http_factory, $http_factory, $http_factory, $backtrace_params);
    }

    /**
     * Create a new request.
     *
     * @param string $method The HTTP method associated with the request.
     * @param UriInterface|string $uri The URI associated with the request. If
     *     the value is a string, the factory MUST create a UriInterface
     *     instance based on it.
     *
     * @return RequestInterface
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new Request(
            $this->request_factory->createRequest($method, $uri),
            [$this->backtrace_flag, $this->backtrace_number],
        );
    }

    /**
     * Create a new response.
     *
     * @param int $code HTTP status code; defaults to 200
     * @param string $reasonPhrase Reason phrase to associate with status code
     *     in generated response; if none is provided implementations MAY use
     *     the defaults as suggested in the HTTP specification.
     *
     * @return ResponseInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response(
            $this->response_factory->createResponse($code, $reasonPhrase),
            [$this->backtrace_flag, $this->backtrace_number],
        );
    }


   /**
     * Create a new server request.
     *
     * Note that server-params are taken precisely as given - no parsing/processing
     * of the given values is performed, and, in particular, no attempt is made to
     * determine the HTTP method or URI, which must be provided explicitly.
     *
     * @param string $method The HTTP method associated with the request.
     * @param UriInterface|string $uri The URI associated with the request. If
     *     the value is a string, the factory MUST create a UriInterface
     *     instance based on it.
     * @param array $serverParams Array of SAPI parameters with which to seed
     *     the generated request instance.
     *
     * @return ServerRequestInterface
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return new ServerRequest(
            $this->server_request_factory->createServerRequest($method, $uri, $serverParams),
            [$this->backtrace_flag, $this->backtrace_number],
        );
    }
}
