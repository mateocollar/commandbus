# CommandBus

[![Join our Discord](https://img.shields.io/badge/Join-PocketMine--MP%202.0%20Community-5865F2?logo=discord&logoColor=white)](https://discord.gg/cbrTNz4CUb)

[![PHP](https://img.shields.io/badge/PHP-7.0+-777BB4?logo=php\&logoColor=white)]()
[![PocketMine](https://img.shields.io/badge/PocketMine-PMMP%202.0-blue)]()
[![License](https://img.shields.io/github/license/mateocollar/CommandBus)]()
[![GitHub stars](https://img.shields.io/github/stars/mateocollar/CommandBus?style=social)]()

A lightweight, fluent and unopinionated command framework for PocketMine-MP.

CommandBus removes command boilerplate while keeping the API simple and flexible. It does not force a specific project structure or architecture—you stay in control.

---

## Features

* Fluent API
* Lightweight
* Zero dependencies
* Automatic command registration
* Automatic argument parsing
* Optional arguments
* Built-in player arguments
* Rules system
* Subcommands
* Method chaining
* Easy to extend

---

## Philosophy

CommandBus is inspired by frameworks such as Express.

Instead of providing a rigid command system, it offers small composable building blocks that let developers organize commands however they prefer.

---

## Requirements

* PHP 7.0+
* PocketMine-MP 2.0

---

## Installation

Clone or download the repository and place it inside your server's `plugins` directory.

```
plugins/
├── CommandBus/
└── YourPlugin/
```

Then simply import the library.

```php
use mateocollar\CommandBus\CommandBus;
use mateocollar\CommandBus\Arg;
```

---

# Quick Start

Creating a command takes only a few lines.

```php
CommandBus::create("heal")
    ->playerOnly()
    ->handler(function($sender){

        $sender->setHealth(20);

        $sender->sendMessage("You have been healed.");

    });
```

---

# Arguments

Arguments are automatically parsed before reaching your callback.

```php
CommandBus::create("gamemode")

    ->arg(Arg::int("mode"))

    ->arg(
        Arg::player("target")->optional()
    )

    ->handler(function($sender, $args){

        $target = $args["target"] ?: $sender;

        $target->setGamemode($args["mode"]);

    });
```

Available argument types:

* `Arg::int()`
* `Arg::string()`
* `Arg::player()`

Arguments can also be marked as optional.

```php
Arg::player("target")->optional();
```

---

# Rules

Rules are callbacks executed before the command handler.

Returning `false` stops command execution.

```php
CommandBus::create("random")

    ->rule(function($sender){

        if(rand(0,1)){

            $sender->sendMessage("Try again.");

            return false;

        }

        return true;

    })

    ->handler(function($sender){

        $sender->sendMessage("Success!");

    });
```

CommandBus also provides some built-in rules.

```php
->playerOnly()

->permission("example.use")
```

---

# Subcommands

Subcommands are regular `CustomCommand` instances.

```php
CommandBus::create("team")

    ->sub("create", function($cmd){

        $cmd

            ->arg(
                Arg::string("name")
            )

            ->handler(function($sender, $args){

                $sender->sendMessage("Created team " . $args["name"]);

            });

    })

    ->sub("delete", function($cmd){

        $cmd

            ->arg(
                Arg::string("name")
            )

            ->handler(function($sender, $args){

                $sender->sendMessage("Deleted team " . $args["name"]);

            });

    });
```

Usage:

```
/team create Red
/team delete Red
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
        Arg::player("target")->optional()
    )

    ->handler(function($sender, $args){

        $target = $args["target"] ?: $sender;

        $target->setGamemode($args["mode"]);

        $sender->sendMessage("Done.");

    });
```

---

# Why CommandBus?

Traditional PocketMine commands usually require a considerable amount of boilerplate before reaching the actual command logic.

CommandBus focuses on letting developers write only what matters.

Instead of extending large base classes or implementing multiple methods, commands are built through a small fluent API.

---

# API Documentation

The complete API reference is available in:

```
API.md
```

---

# Links

* GitHub: https://github.com/mateocollar
* Repository: https://github.com/mateocollar/CommandBus
* API Documentation: ./API.md
* License: ./LICENSE

---

# Contributing

Issues, pull requests and suggestions are always welcome.

If you have ideas for improving CommandBus, feel free to open an issue or submit a pull request.

---

# Credits

Created and maintained by Mateo Collar.

---

# License

Licensed under the Mozilla Public License.
