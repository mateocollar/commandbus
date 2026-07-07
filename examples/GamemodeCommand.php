<?php

namespace mateocollar\CommandBus\Examples;

use mateocollar\CommandBus\CommandBus;
use mateocollar\CommandBus\CustomCommand;
use mateocollar\CommandBus\Arg;
use pocketmine\plugin\PluginBase;

class CGamemodeCommand extends PluginBase
{
    public function onEnable()
    {
        $gamemodeCommand = CommandBus::create("cgm")
            ->playerOnly()
            ->permission("cgm.use")
            ->args(Arg::int("mode"))
            ->args(Arg::player("target")->optional())
            ->handler(function ($sender, $args) {
                $mode = $args["mode"];
                $target = $args["target"] ?: $sender;
                $target->setGamemode($mode);
            });
    }
}
