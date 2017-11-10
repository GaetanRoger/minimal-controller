<?php


namespace Gaetanroger\MinimalSlim3FrameworkTest\Mocks;

use Psr\Http\Message\ResponseInterface;


/**
 * Class Renderer
 *
 * @author Gaetan
 * @date   04/11/2017
 */
class Renderer
{
    public function render($response, $template, $args): ResponseInterface
    {
        return $response;
    }
}