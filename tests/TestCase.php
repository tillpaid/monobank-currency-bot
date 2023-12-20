<?php

namespace Tests;

use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Helpers\FixturesHelper;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;
    use DatabaseMigrations;

    protected FixturesHelper $fixturesHelper;
    protected Generator $faker;
    protected Carbon $carbon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturesHelper = new FixturesHelper();
        $this->faker = new Generator();
        $this->carbon = new Carbon();
    }
}
