<?php


namespace Cli\Strategy;

use Cli\Registry\HandlerRegistry;
use Exception;

use Cli\Basic\Cli;
use Cli\Registry\Config;
use Cli\Exception\RegistryException;

/**
 * Class Value
 * @package Cli/Strategy
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class CliInitializeStrategy extends Strategy
{
    /**
     * @var Cli
     */
    private $cli;

    /**
     * @var array
     */
    private $configuration;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var HandlerRegistry
     */
    private $handlers;

    public function __construct(Cli $cli, array $configuration)
    {
        $this->cli = $cli;
        $this->configuration = $configuration;
    }

    public function run()
    {
        try {
            $this->setConfig();
            $this->setHandlers();
        } catch (RegistryException $e) {
            throw new Exception($e->getMessage() . ' in Cli::initialize');
        }

        if ($this->config->enable_list === 'Y') {
            $this->setListCommand();
        }
    }

    private function setConfig()
    {
        $config = new Config();
        $config->load($this->configuration);

        $this->config = $config;
        $this->cli->setConfig($config);
    }

    private function setHandlers()
    {
        $this->handlers = new HandlerRegistry();
        $this->cli->setHandlers($this->handlers);
    }

    private function setListCommand()
    {
        Cli::handle(
            'list',
            ['Cli\Command\ListCommand', 'run'],
            [],
            ['handlers' => $this->handlers]
        );
    }
}
