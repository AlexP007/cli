<?php


namespace Cli\Strategy;

use Cli\Command\FindFileCommand;
use Cli\Command\FindInFileCommand;
use Exception;

use Cli\Basic\Cli;
use Cli\Command\ListCommand;
use Cli\Registry\Config;
use Cli\Registry\HandlerRegistry;
use Cli\Exception\RegistryException;

/**
 * Class CliInitializeStrategy
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

        $this->config->isEnableList() and $this->setListCommand();
        $this->config->isEnableBasicFindCommandPackage() and $this->setFindCommandPackage();
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
        ListCommand::handle(['handlers' => $this->handlers]);
    }

    public function setFindCommandPackage()
    {
        FindFileCommand::handle([]);
        FindInFileCommand::handle([]);
    }
}
