<?php

namespace mateocollar\CommandBus;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use Closure;
use mateocollar\CommandBus\Arg;

class CustomCommand extends Command
{
    /** @var callable[] */
    private $rules = [];

    /** @var Arg[]*/
    private $arguments = [];

    /** @var Closure */
    private $handler = null;

    /** @var CustomCommand[] */
    private $subcommands = [];

    /**
     * CustomCommand constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    /**
     * Adds a rule to the command.
     * @param callable $r
     * @return CustomCommand
     */
    public function rule(callable $r)
    {
        $this->rules[] = $r;
        return $this;
    }

    /** These methods are syntactic sugar */

    /**
     * Adds a rule to the command that only allows players to execute it.
     * @return CustomCommand
     */
    public function playerOnly()
    {
        return $this->rule(function ($s) {
            if (!($s instanceof Player)) {
                $s->sendMessage("Use this command in-game");
                return false;
            }
            return true;
        });
    }

    /**
     * Adds a rule to the command that only allows players with the given permission to execute it.
     * @param string $p
     * @return CustomCommand
     */
    public function permission(string $p)
    {
        parent::setPermission($p);
        return $this->rule(function ($s) use ($p) {
            // OPs bypass this check by default. Use rule() to override this behavior.
            if (!$s->hasPermission($p) && !$s->isOp()) {
                $s->sendMessage("No permission");
                return false;
            }
            return true;
        });
    }

    /** end of syntactic sugar*/

    /**
     * Adds an argument to the command.
     * @param Arg $arg
     * @return CustomCommand
     */
    public function arg(Arg $arg)
    {
        $this->arguments[] = $arg;
        return $this;
    }

    /**
     * Sets the handler for the command.
     * @param Closure $handler
     * @return CustomCommand
     */
    public function handler(Closure $handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * Sets the description for the command.
     * @param string $description
     * @return CustomCommand
     */
    public function description(string $description)
    {
        parent::setDescription($description);
        return $this;
    }

    /**
     * Sets the usage for the command.
     * @param string $usage
     * @return CustomCommand
     */
    public function usage(string $usage)
    {
        parent::setUsage($usage);
        return $this;
    }

    /**
     * Sets aliases for the command.
     * @param array $aliases
     * @return CustomCommand
     */
    public function aliases(array $aliases)
    {
        parent::setAliases($aliases);
        return $this;
    }

    /**
     * Executes the command.
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $rawArgs
     * @return bool
     */
    public function execute(CommandSender $sender, $commandLabel, array $rawArgs)
    {
        if (isset($rawArgs[0])) {
            $sub = strtolower($rawArgs[0]);

            if (isset($this->subcommands[$sub])) {
                $subcommand = $this->subcommands[$sub];

                array_shift($rawArgs);

                return $subcommand->execute($sender, $commandLabel, $rawArgs);
            }
        }

        foreach ($this->rules as $r) {
            if (!$r($sender)) {
                return true;
            }
        }

        $parsed = [];
        $i = 0;

        foreach ($this->arguments as $arg) {
            $optional = $arg->isOptional();
            $argName = $arg->getName();

            if (!isset($rawArgs[$i])) {
                if ($optional) {
                    $parsed[$argName] = null;
                    continue;
                }

                $sender->sendMessage("Use: /" . $this->getName() . " " . $this->getUsage());
                return true;
            }

            $v = $rawArgs[$i];

            switch ($arg->getType()) {
                case Arg::TYPE_INT:
                    $parsed[$argName] = (int) $v;
                    break;

                case Arg::TYPE_STRING:
                    $parsed[$argName] = (string) $v;
                    break;

                case Arg::TYPE_PLAYER:
                    $parsed[$argName] = $sender->getServer()->getPlayer($v);
                    break;

                default:
                    $parsed[$argName] = $v;
                    break;
            }

            if ($arg->getType() === Arg::TYPE_PLAYER && $parsed[$argName] === null) {
                $sender->sendMessage("Player not found");
                return true;
            }

            $i++;
        }

        if ($this->handler !== null) {
            call_user_func($this->handler, $sender, $parsed);
        }

        return true;
    }

    /**
     * Adds a subcommand to the command.
     * @param string $name
     * @param callable $cb
     * @return CustomCommand
     */
    public function sub(string $name, callable $cb)
    {
        $cmd = new self($name);
        $cb($cmd);
        $this->subcommands[strtolower($name)] = $cmd;
        return $this;
    }

    /**
     * Returns the usage string for the command.
     * @return string
     */
    public function getUsage()
    {
        $parts = [];
        foreach ($this->arguments as $arg) {
            $opt = $arg->isOptional();
            $n = $arg->getName();
            $parts[] = $opt ? "<{$n}?>" : "<{$n}>";
        }
        return implode(" ", $parts);
    }
}
