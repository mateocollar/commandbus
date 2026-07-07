<?php

namespace mateocollar\CommandBus;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use Closure;
use mateocollar\CommandBus\Arg;

class CustomCommand extends Command
{ 
    private $rules=[];
    
	/** @var Arg[]*/
	private $arguments = [];

	/** @var Closure */
	private $handler = null;

	public function __construct($name)
	{
		parent::__construct($name);
	}

    public function rule($r)
    {
        $this->rules[] = $r;
        return $this;
    }

    /** These methods are syntactic sugar */
    
	public function playerOnly()
	{
        return $this->rule(function($s){
            if(!($s instanceof Player)){
                $s->sendMessage("Use this command in-game");
                return false;
            }
            return true;
        });
    }

    public function permission($p)
    {
        parent::setPermission($p);
        return $this->rule(function($s) use($p){
            // OPs bypass this check by default. Use rule() to override this behavior.
            if(!$s->hasPermission($p) && !$s->isOp()){
                $s->sendMessage("No permission");
                return false;
            }
            return true;
        });
    }

    /** end of syntactic sugar*/

	public function arg(Arg $arg)
	{
		$this->arguments[]= $arg;
		return $this;
	}

	public function handler($handler)
	{
		$this->handler = $handler;
		return $this;
	}
    
    public function description($description)
    {
        parent::setDescription($description);
        return $this;
    }
    
    public function usage($usage)
    {
        parent::setUsage($usage);
        return $this;
    }
    
    public function aliases(array $aliases)
    {
        parent::setAliases($aliases);
        return $this;
    }

	public function execute(CommandSender $sender, $commandLabel, array $rawArgs)
	{
        foreach($this->rules as $r){
            if(!$r($sender)){
                return true;
            }
        }
        
        $parsed=[];
        $i=0;
        foreach($this->arguments as $arg){
            $optional=$arg->isOptional();
            $argName=$arg->getName();
            
            if(!isset($rawArgs[$i])){
                if($optional){
                    $parsed[$argName] = null;
                    continue;
                }
                $sender->sendMessage("Use: /" . $this->getName() . " " . $this->getUsage());
                return true;
            }
            
            $v=$rawArgs[$i];
            switch($arg->getType()){
                case Arg::TYPE_INT:
                    $parsed[$argName]=(int)$v;
                    break;
                case Arg::TYPE_STRING:
                    $parsed[$argName]=(string)$v;
                    break;
                case Arg::TYPE_PLAYER:
                    $parsed[$argName]=$sender->getServer()->getPlayer($v);
                    break;
                default:
                    $parsed[$argName]=$v;
                    break;#?
            }
            if($arg->getType() ===Arg::TYPE_PLAYER && $parsed[$argName] === null){
                $sender->sendMessage("Player not found");
                return true;
            }
            $i++;
        }
        if($this->handler !== null){
            call_user_func($this->handler, $sender, (object)$parsed);
        }
        return true;
	}

    public function getUsage()
    {
        $parts=[];
        foreach($this->arguments as $arg){
            $opt=$arg->isOptional();//true|false
            $n=$arg->getName();
            $parts[]=$opt ?"<{$n}?>":"<{$n}>";
        }
        return implode(' ',$parts);
    }
}
