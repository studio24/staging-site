<?php

declare(strict_types=1);

namespace Studio24\StagingSite\Tests;

use PHPUnit\Framework\TestCase;
use Studio24\StagingSite\Exception\StagingSiteException;
use Studio24\StagingSite\StagingSite;

class StagingSiteTest extends TestCase
{
    protected function setUp(): void
    {
        // Ensure the ENVIRONMENT env var is clean before each test
        putenv('ENVIRONMENT');
    }

    protected function tearDown(): void
    {
        putenv('ENVIRONMENT');
    }

    public function testGetStagingEnvironmentsFromString(): void
    {
        $site = new StagingSite();
        $this->assertSame(['staging'], $site->getStagingEnvironments());
    }

    public function testGetStagingEnvironmentsFromArray(): void
    {
        $site = new StagingSite();
        $site->stagingEnvironments = ['staging', 'preview'];
        $this->assertSame(['staging', 'preview'], $site->getStagingEnvironments());
    }

    public function testSetStagingEnvironments(): void
    {
        $site = new StagingSite();
        $site->setStagingEnvironments(['staging', 'uat']);
        $this->assertSame(['staging', 'uat'], $site->getStagingEnvironments());
    }

    public function testSetEnvironmentAndGet(): void
    {
        $site = new StagingSite();
        $site->setEnvironment('production');
        $this->assertSame('production', $site->getEnvironment());
    }

    public function testGetEnvironmentFromEnvVariable(): void
    {
        $_ENV['ENVIRONMENT'] = 'staging';
        $site = new StagingSite();
        $this->assertSame('staging', $site->getEnvironment());
    }

    public function testGetEnvironmentThrowsWhenUnset(): void
    {
        $site = new StagingSite();
        // Use a uniquely named variable guaranteed not to exist
        $site->environmentVariable = 'STAGING_SITE_TEST_NONEXISTENT_XYZ_12345';
        $this->expectException(StagingSiteException::class);
        $site->getEnvironment();
    }

    public function testIsStagingTrue(): void
    {
        $site = new StagingSite();
        $site->setEnvironment('staging');
        $this->assertTrue($site->isStaging());
    }

    public function testIsStagingFalse(): void
    {
        $site = new StagingSite();
        $site->setEnvironment('production');
        $this->assertFalse($site->isStaging());
    }

    public function testIsStagingWithMultipleEnvironments(): void
    {
        $site = new StagingSite();
        $site->setStagingEnvironments(['staging', 'preview']);
        $site->setEnvironment('preview');
        $this->assertTrue($site->isStaging());
    }
}
