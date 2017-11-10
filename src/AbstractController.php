<?php

namespace Gaetanroger\MinimalSlim3Framework;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouterInterface;


/**
 * Class AbstractController
 *
 * @author Gaetan
 * @date   04/11/2017
 */
abstract class AbstractController
{
    public const MOVED_PERMANENTLY = 301;
    public const MOVED_TEMPORALLY = 302;
    
    /**
     * Name of the dependency the `render()` method will be called on when
     * asked to render something.
     *
     * @var string
     */
    public static $rendererContainerName = 'renderer';
    
    /**
     * Name of the dependency stored in the container and used to get access to the router.
     *
     * @var string
     */
    public static $routerContainerName = 'router';
    
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * AbstractController constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
     * Calls `render()` on the renderer contained by the container.
     *
     * By default, the dependency is called "renderer". It is also possible
     * to change this name by modifying the value of `$rendererContainerName` which is a public static attribute
     * if this class.
     *
     * @param ResponseInterface $response
     * @param string            $template
     * @param array             $args
     * @return ResponseInterface
     */
    protected function render(ResponseInterface $response, string $template, array $args = []): ResponseInterface
    {
        $renderer = $this->container->get(static::$rendererContainerName);
        
        if (!method_exists($renderer, 'render')) {
            throw new ContainerException("Object found in container with key " . static::$rendererContainerName . ' does not have a render method.');
        }
        
        return $renderer->render($response, $template, $args);
    }
    
    /**
     * Gets the URL from `$pathName` using the container and redirect to it.
     *
     * By default, the dependency is called "router". It is also possible to change this name by modifying the value of
     * `$pathName` which is a public static attribute if this class.
     *
     * @param ResponseInterface $response
     * @param string            $pathName Name of the path set in the router matching the URL to be redirected to.
     * @param array             $args
     * @param int               $code     Redirection code sent in the header status.
     * @return ResponseInterface
     */
    protected function redirect(
        ResponseInterface $response,
        string $pathName,
        array $args,
        int $code = self::MOVED_TEMPORALLY
    ): ResponseInterface {
        /**
         * @var RouterInterface
         */
        $router = $this->container->get(static::$routerContainerName);
        
        if (!($router instanceof RouterInterface)) {
            throw new ContainerException("Did not get " . RouterInterface::class . " out of container using key " . static::$routerContainerName . ".");
        }
        
        /**
         * Path to new page.
         *
         * @var string
         */
        $path = $router->pathFor($pathName, $args);
        
        return $response->withStatus($code)->withHeader('Location', $path);
    }
    
}