<?php

namespace mateocollar\CommandBus;

class Arg
{
    const TYPE_INT=0;
    const TYPE_STRING=1;
    const TYPE_PLAYER=2;
    
    private $name;
    private $type=null;
    private $optional=false;
    private $default=null;

    public function __construct($n,$t)
    {
        $this->name=$n;
        $this->type=$t;
    }

    public static function int($n)
    {
        return new self($n,self::TYPE_INT);
    }

    public static function string($n)
    {
        return new self($n,self::TYPE_STRING);
    }

    public static function player($n)
    {
        return new self($n,self::TYPE_PLAYER);
    }

    public function optional()
    {
        $this->optional=true;
        return $this;
    }

    public function required()
    {
        $this->optional = false;
        return $this;
    }

    public function default($v)
    {
        $this->default = $v;
        return $this;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function isOptional()
    {
        return $this->optional;
    }
}