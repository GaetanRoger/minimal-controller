<?php


namespace Gaetanroger\MinimalControllerTest;

use Gaetanroger\MinimalController\AbstractController;
use Gaetanroger\MinimalControllerTest\Mocks\Renderer;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouterInterface;


/**
 * Class AbstractControllerTest
 *
 * @author Gaetan
 * @date   04/11/2017
 */
class AbstractControllerTest extends TestCase
{
    /**
     * @var AbstractController
     */
    private $controller;
    
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * @var Response
     */
    private $emptyResponse;
    
    /**
     * @param $controller
     * @param $response
     * @return mixed
     */
    private static function callRedirect(AbstractController $controller, ResponseInterface $response): ResponseInterface
    {
        $reflection = new \ReflectionClass($controller);
        $redirectMethod = $reflection->getMethod('redirect');
        $redirectMethod->setAccessible(true);
        
        return $redirectMethod->invokeArgs($controller, [$response, '', []]);
    }
    
    /**
     * Testing the tests set up to ensure everything was done correctly.
     */
    public function testSetUp()
    {
        self::assertInstanceOf(ContainerInterface::class, $this->container);
        self::assertInstanceOf(AbstractController::class, $this->controller);
        self::assertInstanceOf(ResponseInterface::class, $this->emptyResponse);
    }
    
    /**
     * Testing AbstractController::render().
     */
    public function testRender()
    {
        $response = static::callRender($this->controller, $this->emptyResponse);
        
        self::assertEquals(999, $response->getStatusCode());
    }
    
    /**
     * Testing AbstractController::redirect().
     */
    public function testRedirect()
    {
        $response = self::callRedirect($this->controller, $this->emptyResponse);
        
        self::assertEquals(302, $response->getStatusCode());
        self::assertCount(1, $response->getHeader('Location'));
        self::assertContains('generatedUrl', $response->getHeader('Location'));
    }
    
    /**
     * @expectedException \Gaetanroger\MinimalController\ContainerException
     */
    public function testWrongRouterRedirect()
    {
        // Remocking the ContainerInterface to make getMethod return wrong class instead of router.
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('get')->will($this->returnValueMap([
            [AbstractController::$routerContainerName, "Wrong class"],
        ]));
        
        // Mocking our AbstractController to give it the container in constructor argument.
        $this->controller = $this
            ->getMockBuilder(AbstractController::class)
            ->setConstructorArgs([$this->container])->getMock();
        
        static::callRedirect($this->controller, $this->emptyResponse);
    }
    
    /**
     * @expectedException \Gaetanroger\MinimalController\ContainerException
     */
    public function testWrongRendererRender()
    {
        // Remocking the ContainerInterface to make getMethod return wrong class instead of router.
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('get')->will($this->returnValueMap([
            [AbstractController::$rendererContainerName, "Wrong class"],
        ]));
        
        // Mocking our AbstractController to give it the container in constructor argument.
        $this->controller = $this
            ->getMockBuilder(AbstractController::class)
            ->setConstructorArgs([$this->container])->getMock();
        
        static::callRender($this->controller, $this->emptyResponse);
    }
    
    /**
     * Setting up all mocks needed in the test.
     */
    protected function setUp()
    {
        // Create a new Response implementing PSR-7 ResponseInterface.
        $this->emptyResponse = new Response();
        
        // Mocking the RouterInterface to make `pathFor` always return the string "generatedUrl".
        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->method('pathFor')->willReturn('generatedUrl');
        
        // Mocking a renderer class to make `render` always return an empty ResponseInterface with stats code 999.
        $rendererMock = $this->createMock(Renderer::class);
        $rendererMock->method('render')->willReturn($this->emptyResponse->withStatus(999));
        
        // Mocking the ContainerInterface to make getMethod return the renderer and router.
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('get')->will($this->returnValueMap([
            [AbstractController::$rendererContainerName, $rendererMock],
            [AbstractController::$routerContainerName, $routerMock],
        ]));
        
        // Mocking our AbstractController to give it the container in constructor argument.
        $this->controller = $this
            ->getMockBuilder(AbstractController::class)
            ->setConstructorArgs([$this->container])->getMock();
    }
    
    /**
     * @param AbstractController $container
     * @param ResponseInterface  $response
     * @return ContainerInterface
     */
    private static function callRender(AbstractController $container, ResponseInterface $response): ResponseInterface
    {
        $reflection = new \ReflectionClass($container);
        $redirectMethod = $reflection->getMethod('render');
        $redirectMethod->setAccessible(true);
        
        return $redirectMethod->invokeArgs($container, [$response, '', []]);
    }
}