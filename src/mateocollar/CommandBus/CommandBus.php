<?php

namespace mateocollar\CommandBus;

use pocketmine\plugin\PluginBase;

/**
 * Class CommandBus
 * @package mateocollar\CommandBus
 */
class CommandBus extends PluginBase
{
    /** @var CommandBus */
    private static $instance;

    public function onLoad()
    {
        self::$instance = $this;
    }

    /**
     * Returns the instance of the CommandBus plugin.
     * @return CommandBus
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * Creates a new command.
     * @param string $name
     * @return CustomCommand
     */
    public static function create(string $name)
    {
        $cmd = new CustomCommand($name);

        self::$instance
            ->getServer()
            ->getCommandMap()
            ->register("CommandBus", $cmd);

        return $cmd;
    }
}
