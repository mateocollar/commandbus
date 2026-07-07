<?php

namespace mateocollar\CommandBus;

use pocketmine\plugin\PluginBase;

class CommandBus extends PluginBase
{
    /** @var CommandBus */
    private static $instance;

    public function onLoad(){
        self::$instance = $this;
    }

    public static function getInstance(){
        return self::$instance;
    }

    public static function create($name){
        $cmd = new CustomCommand($name);

        self::$instance
            ->getServer()
            ->getCommandMap()
            ->register("CommandBus", $cmd);

        return $cmd;
    }
}