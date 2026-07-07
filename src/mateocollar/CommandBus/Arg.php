<?php

namespace mateocollar\CommandBus;

/**
 * Class Arg
 * @package mateocollar\CommandBus
 */
class Arg
{
    /** @var int */
    const TYPE_INT = 0;
    /** @var int */
    const TYPE_STRING = 1;
    /** @var int */
    const TYPE_PLAYER = 2;

    /** @var string */
    private $name;
    /** @var int */
    private $type = null;
    /** @var bool */
    private $optional = false;
    /** @var mixed */
    private $default = null;

    /**
     * Arg constructor.
     * @param string $n
     * @param int $t
     */
    public function __construct(string $n, int $t)
    {
        $this->name = $n;
        $this->type = $t;
    }

    /**
     * Creates an integer argument.
     * @param string $n
     * @return Arg
     */
    public static function int(string $n)
    {
        return new self($n, self::TYPE_INT);
    }

    /**
     * Creates a string argument.
     * @param string $n
     * @return Arg
     */
    public static function string(string $n)
    {
        return new self($n, self::TYPE_STRING);
    }

    /**
     * Creates a player argument.
     * @param string $n
     * @return Arg
     */
    public static function player(string $n)
    {
        return new self($n, self::TYPE_PLAYER);
    }

    /**
     * Marks the argument as optional.
     * @return Arg
     */
    public function optional()
    {
        $this->optional = true;
        return $this;
    }

    /**
     * Marks the argument as required.
     * @return Arg
     */
    public function required()
    {
        $this->optional = false;
        return $this;
    }

    /**
     * Sets the default value for the argument.
     * @param mixed $v
     * @return Arg
     */
    public function default($v)
    {
        $this->default = $v;
        return $this;
    }

    /**
     * Returns the default value for the argument.
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Returns the name of the argument.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the type of the argument.
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Checks if the argument is optional.
     * @return bool
     */
    public function isOptional()
    {
        return $this->optional;
    }
}
