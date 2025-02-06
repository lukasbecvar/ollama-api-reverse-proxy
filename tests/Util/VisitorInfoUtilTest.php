<?php

namespace App\Tests\Util;

use App\Util\SecurityUtil;
use App\Util\VisitorInfoUtil;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class VisitorInfoUtilTest
 *
 * Test cases for visitor info util
 *
 * @package App\Tests\Util
 */
class VisitorInfoUtilTest extends TestCase
{
    private VisitorInfoUtil $visitorInfoUtil;
    private SecurityUtil & MockObject $securityUtilMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->securityUtilMock = $this->createMock(SecurityUtil::class);

        // mock escape string behavior
        $this->securityUtilMock->method('escapeString')->willReturnCallback(function ($string) {
            return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5);
        });

        // create visitor info util instance
        $this->visitorInfoUtil = new VisitorInfoUtil($this->securityUtilMock);
    }

    /**
     * Test get visitor ip when HTTP_CLIENT_IP header is set
     *
     * @return void
     */
    public function testGetIpWhenHttpClientIpHeaderIsSet(): void
    {
        // set server variables
        $_SERVER['HTTP_CLIENT_IP'] = '192.168.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '';
        $_SERVER['REMOTE_ADDR'] = '192.168.0.2';

        // call tested method
        $result = $this->visitorInfoUtil->getIP();

        // assert result
        $this->assertEquals('192.168.0.1', $result);

        // unset server variables
        unset($_SERVER['HTTP_CLIENT_IP']);
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        unset($_SERVER['REMOTE_ADDR']);
    }

    /**
     * Test get visitor ip when HTTP_X_FORWARDED_FOR header is set
     *
     * @return void
     */
    public function testGetIpWhenHttpXForwardedForHeaderIsSet(): void
    {
        // set server variables
        $_SERVER['HTTP_CLIENT_IP'] = '';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.0.3';
        $_SERVER['REMOTE_ADDR'] = '192.168.0.4';

        // call tested method
        $result = $this->visitorInfoUtil->getIP();

        // assert result
        $this->assertEquals('192.168.0.3', $result);

        // unset server variables
        unset($_SERVER['HTTP_CLIENT_IP']);
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        unset($_SERVER['REMOTE_ADDR']);
    }

    /**
     * Test get visitor ip when REMOTE_ADDR header is set
     *
     * @return void
     */
    public function testGetIpWhenRemoteAddrHeaderIsSet(): void
    {
        // set server variables
        $_SERVER['HTTP_CLIENT_IP'] = '';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '';
        $_SERVER['REMOTE_ADDR'] = '192.168.0.5';

        // call tested method
        $result = $this->visitorInfoUtil->getIP();

        // assert result
        $this->assertEquals('192.168.0.5', $result);

        // unset server variables
        unset($_SERVER['HTTP_CLIENT_IP']);
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        unset($_SERVER['REMOTE_ADDR']);
    }
}
