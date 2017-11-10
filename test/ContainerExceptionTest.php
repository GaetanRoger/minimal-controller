<?php


namespace Gaetanroger\MinimalSlim3FrameworkTest;

use Gaetanroger\MinimalSlim3Framework\ContainerException;
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
     * @expectedException \Gaetanroger\MinimalSlim3Framework\ContainerException
     * @expectedExceptionMessage message
     * @expectedExceptionCode    1
     */
    public function testThrowing()
    {
        throw new ContainerException("message", 1);
    }
}