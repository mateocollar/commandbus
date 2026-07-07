# CommandBus API

## Table of Contents

* [Overview](#overview)
* [CommandBus](#commandbus)

  * [create()](#createstring-name-customcommand)
  * [getInstance()](#getinstance-commandbus)
* [CustomCommand](#customcommand)

  * [rule()](#rulecallable-rule-customcommand)
  * [playerOnly()](#playeronly-customcommand)
  * [permission()](#permissionstring-permission-customcommand)
  * [description()](#descriptionstring-description-customcommand)
  * [usage()](#usagestring-usage-customcommand)
  * [aliases()](#aliasesarray-aliases-customcommand)
  * [arg()](#argarg-argument-customcommand)
  * [handler()](#handlerclosure-handler-customcommand)
  * [sub()](#substring-name-callable-callback-customcommand)
  * [getUsage()](#getusage-string)
* [Arg](#arg)

  * [int()](#intstring-name-arg)
  * [string()](#stringstring-name-arg)
  * [player()](#playerstring-name-arg)
  * [optional()](#optional-arg)
  * [required()](#required-arg)
  * [default()](#defaultmixed-value-arg)

---

# Overview

CommandBus is a lightweight and fluent command framework for PocketMine-MP.

Instead of extending large command classes or manually parsing arguments, commands are built through a small chainable API.

The framework remains completely unopinionated, allowing developers to structure commands however they prefer.

---

# CommandBus

The static entry point used to create and register commands.

---

## create(string $name): CustomCommand

Creates a new command and automatically registers it in PocketMine's `CommandMap`.

```php
$cmd = CommandBus::create("heal");
```

Returns a `CustomCommand` instance that can be configured using the fluent API.

---

## getInstance(): CommandBus

Returns the running CommandBus plugin instance.

```php
$plugin = CommandBus::getInstance();
```

---

# CustomCommand

Represents a command registered through CommandBus.

Every configuration method returns the current instance, allowing method chaining.

---

## rule(callable $rule): CustomCommand

Adds a validation rule executed before the command handler.

The callback receives the command sender.

Returning `false` immediately stops command execution.

```php
->rule(function($sender){

    if(!$sender->isOp()){
        $sender->sendMessage("No.");
        return false;
    }

    return true;

});
```

Multiple rules may be registered.

They are executed in the order they were added.

---

## playerOnly(): CustomCommand

Shortcut for creating a rule that only allows players to execute the command.

Console execution is automatically denied.

```php
->playerOnly()
```

Equivalent to manually adding a rule that checks whether the sender is a `Player`.

---

## permission(string $permission): CustomCommand

Adds a permission requirement to the command.

Operators bypass this check by default.

```php
->permission("gamemode.use")
```

Internally this method also calls PocketMine's native `setPermission()`.

If different behavior is required, use `rule()` instead.

---

## description(string $description): CustomCommand

Sets the command description.

```php
->description("Changes a player's gamemode.")
```

This description is shown by PocketMine's help system.

---

## usage(string $usage): CustomCommand

Sets the command usage string.

```php
->usage("/heal <player>")
```

Normally this method is unnecessary since CommandBus automatically generates usage information from registered arguments.

---

## aliases(array $aliases): CustomCommand

Registers aliases for the command.

```php
->aliases([
    "gm",
    "gamemode"
])
```

---

## arg(Arg $argument): CustomCommand

Registers a command argument.

Arguments are parsed automatically before the handler is executed.

```php
->arg(
    Arg::int("mode")
)

->arg(
    Arg::player("target")->optional()
)
```

Arguments are passed to the handler in the same order they were registered.

---

## handler(Closure $handler): CustomCommand

Sets the command callback.

The callback receives two parameters.

* Command sender
* Parsed arguments

```php
->handler(function($sender, $args){

    $sender->sendMessage("Hello!");

});
```

Parsed arguments are provided as an associative array using the names defined through `Arg`.

```php
$args["mode"]

$args["target"]
```

## sub(string $name, callable $callback): CustomCommand

Creates a subcommand.

The callback receives a new `CustomCommand` instance that can be configured independently.

```php
CommandBus::create("team")

    ->sub("create", function(CustomCommand $cmd){

        $cmd

            ->arg(
                Arg::string("name")
            )

            ->handler(function($sender, $args){

                $sender->sendMessage(
                    "Created team " . $args["name"]
                );

            });

    });
```

Subcommands may define their own:

* Rules
* Permissions
* Arguments
* Handler
* Aliases (if desired)

Subcommands behave exactly like normal commands.

---

## getUsage(): string

Returns the automatically generated usage string based on the registered arguments.

```php
echo $command->getUsage();
```

Example output:

```text
<mode> <target?>
```

Optional arguments are automatically marked with `?`.

---

# Arg

Represents a command argument.

Arguments describe how CommandBus should parse raw command input before invoking the handler.

Instead of manually casting values, simply declare the desired argument type.

---

## int(string $name): Arg

Creates an integer argument.

```php
Arg::int("amount")
```

Input:

```text
/pay 250
```

Handler:

```php
$args["amount"]; // int(250)
```

---

## string(string $name): Arg

Creates a string argument.

```php
Arg::string("message")
```

Input:

```text
/msg Hello
```

Handler:

```php
$args["message"]; // "Hello"
```

---

## player(string $name): Arg

Creates a player argument.

The player is automatically resolved through PocketMine.

```php
Arg::player("target")
```

Input:

```text
/heal Steve
```

Handler:

```php
$args["target"]; // Player instance
```

If the player cannot be found, command execution stops automatically and the sender receives:

```text
Player not found
```

---

## optional(): Arg

Marks the argument as optional.

```php
Arg::player("target")
    ->optional()
```

When omitted, its value becomes `null` unless a default value is specified.

---

## required(): Arg

Marks an argument as required.

```php
Arg::string("name")
    ->required()
```

This is the default behavior.

---

## default(mixed $value): Arg

Sets the default value used when an optional argument is omitted.

```php
Arg::int("amount")
    ->optional()
    ->default(10)
```

Handler:

```php
$args["amount"]; // 10
```

---

# Complete Example

```php
CommandBus::create("gamemode")

    ->aliases(["gm"])

    ->description("Changes a player's gamemode.")

    ->permission("gamemode.use")

    ->playerOnly()

    ->arg(
        Arg::int("mode")
    )

    ->arg(
        Arg::player("target")
            ->optional()
    )

    ->handler(function($sender, $args){

        $target = $args["target"] ?: $sender;

        $target->setGamemode($args["mode"]);

        $sender->sendMessage("Done.");

    });
```

---

# Best Practices

* Prefer using `Arg` instead of manually parsing command arguments.
* Use `rule()` for custom validation logic.
* Keep handlers focused on command behavior.
* Use subcommands to organize large command trees.
* Prefer `playerOnly()` and `permission()` over manually implementing the same checks.
* Group related commands using subcommands instead of creating dozens of root commands.

---

# Notes

CommandBus is intentionally **unopinionated**.

It does not require inheritance, abstract classes or predefined project structures.

Every command is simply a `CustomCommand`, allowing developers to compose commands naturally while keeping complete control over their architecture.
