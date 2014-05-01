<?php

namespace IO\DefaultBundle\Tests\Controller;

use Behat\Behat\Console\BehatApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BehaviorTest extends WebTestCase
{

    /**
     * @var Client;
     */
    protected $client;

    protected function setup()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        
        $this->client = static::createClient(array('environment' => 'test'));
        $this->client->followRedirects();
    }

    /**
     * @test
     * @group behat
     */
    public function behatTest($bundle)
    {
        $input = new ArrayInput(array(
                    '--ansi' => '',
                    '--verbose' => '',
                    '--format' => 'progress',
                    'features' => $bundle
                ));
        $output = new ConsoleOutput();
        $app = new BehatApplication('DEV');
        $app->setAutoExit(false);
        $result = $app->run($input, $output);
        
        $this->assertEquals(0, $result);
    }
}
