Cave
---

A CLI tool to automate everything I can think of.

## Installing

Just clone the project and composer install it:

```
$ git clone https://github.com/nawarian/cave
$ cd cave/
$ composer install
```

Optionally you may add it to your PATH. I chose to create an ALIAS for the `cave` command:

```
$ echo 'alias cave=/path/to/cave/bin/console' >> ~/.bash_profile
```

This way `cave` should become an available command from every directory in your command line.

## Getting started

First and foremost you need to create a profile. Everything that is stateful will then be
attached to that profile.

```
$ cave profile:set nawarian
```

The above command will set the currently active profile to `nawarian`. If it doesn't exist yet,
it will be created.

You may change profiles at any given moment. To see a list of profiles, run the following command:

```
$ cave profile:list
```

## Commands and automations

### Key-value store

Each profile has ist own key-value store which can be managed anytime by the `profile:kv` command.

**Other commands may depend on values present in this KV store.**

The command definition is the following:

```
$cave profile:kv [-d|--delete] [--] <key> [<value>]
```

So the following command creates a key named `author` with the value `nawarian`:

```
$ cave profile:kv author nawarian
```

If you want to overwrite the value of `author`, just call it again with different arguments:

```
$ cave profile:kv author nickolas
```

If you'd like to delete that key, just pass the `-d` flag:

```
$ cave profile:kv -d author
```

### Twitter

Posting on twitter can take quite a bit of time. And how can you explain your employer
that you're outisde your terminal screen, heh?

This command depends on the following KV keys:
- `twitter.consumer_key`
- `twitter.consumer_secret`
- `twitter.access_token`
- `twitter.access_token_secret`

To generate these keys you'll have to create a Twitter App following the instructions
in [Twitter's Developer Console](https://developer.twitter.com/).

The command definition is the following:

```
$ cave twitter:tweet [-a img1.jpg [-a img2.png]] [-f|--force] [-q|--quiet] [--] <text>
```

To publish a tweet from your command line run the following:

```
$ cave twitter:tweet "I'm tweeting this from my #cli" -a tweet.png
```

### Scheduler

The scheduler maintains a simple command queue which allows you to schedule commands
to be run in the future.

This is super useful if combined with your machine's crontab.

The command definition os the following:

```
$ cave schedule:command <cmd> <date> 
```
Where `cmd` is the actual command line you'd like to execute and `date` is a `DateTime` compatible string.

Let's send a tweet 2 hours from here:

```
$ cave schedule:command 'cave twitter:tweet "This is a scheduled tweet" -f -q' '+2 hours'
```

In order to run all pending commands (all commands that have a due date <= now), just run `schedule:run`:

```
$ cave schedule:run 10
```

The above command should pick at most 10 pending commands to be executed.

I've installed this into my crontab so it runs every minute. Here's how it looks like:

```
* * * * * cave schedule:run 10
```

## Contributing

If you'd like to improve the project by adding tests or commands, I'll be more than happy!

But I encourage you to add commands by writing symfony bundles instead of commiting to this
project. This way you get way more freedom to implement and use whatever you wish.

