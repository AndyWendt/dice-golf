<?php

namespace Tests;

use Faker\Factory;
use Faker\Generator;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function faker(): Generator
    {
        return Factory::create();
    }
}