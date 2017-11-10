<?php


namespace Gaetanroger\MinimalControllerTest\Mocks;

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