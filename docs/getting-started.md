Getting started
---

Let's start working with `cave` in 4 steps:

## Step 1 – Clone the project

In your local machine let's clone the project with the following command:

```
$ git clone https://github.com/nawarian/cave
$ cd cave/
$ composer install
```

## Step 2 – Add an alias to `cave`

Make sure the `alias` command will run every time you start a new terminal
session.

```
$ echo 'alias cave=/path/to/cave/bin/console' >> ~/.bash_profile
```

## Step 3 – Create the database and load schemas

```
$ cave doctrine:database:create
$ cave doctrine:schema:update --force
```

## Step 4 – Create your initial profile

Cave commands often require _context_ which are provided by profiles. You may have multiple
profiles if you want.

To create a new profile type the following command:

```
$ cave profile:set <your-profile-name>
```

If you want to check what is your current profile just run `cave profile:current`.

## The world is yours!

You're now set up for success! Go ahead and play with all available commands!
