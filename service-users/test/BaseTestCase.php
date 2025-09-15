<?php

declare(strict_types=1);

namespace HyperfTest;

use PHPUnit\Framework\TestCase;
use Hyperf\Context\ApplicationContext;

abstract class BaseTestCase extends TestCase
{
    protected $container;

    protected function setUp(): void
    {
        parent::setUp();

        if (file_exists(BASE_PATH . '/.env.testing')) {
            \Dotenv\Dotenv::createImmutable(BASE_PATH, '.env.testing')->safeLoad();
        } else {
            \Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();
        }

        $this->container = require BASE_PATH . '/config/container.php';
        ApplicationContext::setContainer($container);
        $this->container = $container;
    }

}
