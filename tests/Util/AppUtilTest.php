<?php

namespace App\Tests\Util;

use App\Util\AppUtil;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AppUtilTest
 *
 * Test cases for app util
 *
 * @package App\Tests\Util
 */
class AppUtilTest extends TestCase
{
    private AppUtil $appUtil;
    private KernelInterface & MockObject $kernelInterface;

    protected function setUp(): void
    {
        // mock dependencies
        $this->kernelInterface = $this->createMock(KernelInterface::class);

        // create the app util instance
        $this->appUtil = new AppUtil($this->kernelInterface);
    }

    /**
     * Test get app version
     *
     * @return void
     */
    public function testGetAppRootDir(): void
    {
        // expect call get project dir
        $this->kernelInterface->expects($this->once())
            ->method('getProjectDir');

        // call tested method
        $result = $this->appUtil->getAppRootDir();

        // assert result
        $this->assertIsString($result);
    }

    /**
     * Test get environment variable value
     *
     * @return void
     */
    public function testGetEnvValue(): void
    {
        // set env value
        $_ENV['TEST_KEY'] = 'test-value';

        // call tested method
        $result = $this->appUtil->getEnvValue('TEST_KEY');

        // assert result
        $this->assertIsString($result);
    }

    /**
     * Test check if dev mode is enabled when dev mode is on
     *
     * @return void
     */
    public function testCheckIfDevModeIsEnabledWhenDevModeIsOn(): void
    {
        // simulate dev mode enabled
        $_ENV['APP_ENV'] = 'dev';

        // call tested method
        $result = $this->appUtil->isDevMode();

        // assert result
        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    /**
     * Test check if dev mode is disabled when dev mode is off
     *
     * @return void
     */
    public function testCheckIfDevModeIsDisabledWhenDevModeIsOff(): void
    {
        // simulate dev mode disabled
        $_ENV['APP_ENV'] = 'prod';

        // call tested method
        $result = $this->appUtil->isDevMode();

        // assert result
        $this->assertIsBool($result);
        $this->assertFalse($result);
    }

    /**
     * Test check if maintenance mode is enabled when maintenance mode is on
     *
     * @return void
     */
    public function testCheckIfMaintenanceModeIsEnabledWhenMaintenanceModeIsOn(): void
    {
        // simulate maintenance mode enabled
        $_ENV['MAINTENANCE_MODE'] = 'true';

        // call tested method
        $result = $this->appUtil->isMaintenance();

        // assert result
        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    /**
     * Test check if maintenance mode is disabled when maintenance mode is off
     *
     * @return void
     */
    public function testCheckIfMaintenanceModeIsDisabledWhenMaintenanceModeIsOff(): void
    {
        // simulate maintenance mode disabled
        $_ENV['MAINTENANCE_MODE'] = 'false';

        // call tested method
        $result = $this->appUtil->isMaintenance();

        // assert result
        $this->assertIsBool($result);
        $this->assertFalse($result);
    }

    /**
     * Test check if ssl only is enabled when ssl only is on
     *
     * @return void
     */
    public function testCheckIfSslOnlyIsEnabledWhenSslOnlyIsOn(): void
    {
        // simulate ssl only enabled
        $_ENV['SSL_ONLY'] = 'true';

        // call tested method
        $result = $this->appUtil->isSslOnly();

        // assert result
        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    /**
     * Test check if ssl only is disabled when ssl only is off
     *
     * @return void
     */
    public function testCheckIfSslOnlyIsDisabledWhenSslOnlyIsOff(): void
    {
        // simulate ssl only disabled
        $_ENV['SSL_ONLY'] = 'false';

        // call tested method
        $result = $this->appUtil->isSslOnly();

        // assert result
        $this->assertIsBool($result);
        $this->assertFalse($result);
    }

    /**
     * Test check if request is secure when https is on
     *
     * @return void
     */
    public function testCheckIfRequestIsSecureWithHttpsWhenHttpsIsOn(): void
    {
        $_SERVER['HTTPS'] = 1;
        $this->assertTrue($this->appUtil->isSsl());

        $_SERVER['HTTPS'] = 'on';
        $this->assertTrue($this->appUtil->isSsl());
    }

    /**
     * Test check if request is secure when https is off
     *
     * @return void
     */
    public function testCheckIfRequestIsSecureWithHttpWhenHttpsIsOff(): void
    {
        $_SERVER['HTTPS'] = 0;
        $this->assertFalse($this->appUtil->isSsl());

        $_SERVER['HTTPS'] = 'off';
        $this->assertFalse($this->appUtil->isSsl());

        unset($_SERVER['HTTPS']);
        $this->assertFalse($this->appUtil->isSsl());
    }
}
