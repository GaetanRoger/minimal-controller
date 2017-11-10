<?php


namespace Gaetanroger\MinimalControllerTest;

use Gaetanroger\MinimalController\ContainerException;
use PHPUnit\Framework\TestCase;


/**
 * Class ContainerExceptionTest
 *
 * @author Gaetan
 * @date   04/11/2017
 */
class ContainerExceptionTest extends TestCase
{
    /**
     * @throws ContainerException
     *
     * @expectedException \Gaetanroger\MinimalController\ContainerException
     * @expectedExceptionMessage message
     * @expectedExceptionCode    1
     */
    public function testThrowing()
    {
        throw new ContainerException("message", 1);
    }
}