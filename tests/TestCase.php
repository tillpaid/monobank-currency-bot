<?php

namespace Tests;

use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;
    use DatabaseMigrations;

    protected Generator $faker;
    protected Carbon $carbon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->faker = new Generator();
        $this->carbon = new Carbon();
    }
}
