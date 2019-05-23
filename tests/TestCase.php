<?php

namespace Tests;

use Faker\Factory;
use Faker\Generator;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    protected function faker(): Generator
    {
        return Factory::create();
    }
}