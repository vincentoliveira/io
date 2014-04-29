<?php

namespace IO\DefaultBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;

class BehaviorTest extends IOTestCase
{
    /**
     * @test
     * @group behat
     */
    public function testBehavior()
    {
        parent::behaviorTestSuite('@IODefaultBundle');
    }
}
