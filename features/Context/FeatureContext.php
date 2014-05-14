<?php

namespace Context;

use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Common\Util\Inflector;
use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\When;
use Behat\Behat\Context\Step\Then;

class FeatureContext extends MinkContext implements KernelAwareInterface
{
    private $kernel;
    protected static $db;

    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }
}
