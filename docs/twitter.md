Twitter 
---

Cave has a module (soon to become a symfony bundle + flex recipe) focused in managing Twitter.

It allows you to:
* Fetch your own profile's stats
* Send tweets
* Send twitter threads based on an `.md` file

## Requirements

In order to use this module you must have access to the Twitter API. To be more precise you
need oAuth access and have the credentials stored in your cave profile's Key-Value store.

This command depends on the following KV keys:
- `twitter.consumer_key`
- `twitter.consumer_secret`
- `twitter.access_token`
- `twitter.access_token_secret`

To generate these keys you'll have to create a Twitter App following the instructions
in [Twitter's Developer Console](https://developer.twitter.com/).

Make sure the above keys are set using commands such as the one below:

```
$ cave profile:kv twitter.consumer_key <paste-your-consumer-key-here>
$ cave profile:kv twitter.consumer_secret <paste-your-consumer-secret-here>
$ cave profile:kv twitter.access_token <paste-your-access-token-here>
$ cave profile:kv twitter.access_token_secret <paste-your-access-token-here>
```

## Sending a tweet

**Command**: `cave twitter:tweet <text> [-a <img> [-f]]`

With the above command you can publish a twitter status, optionally with an image (from disk).

The `-f` option (`--force`) doesn't ask you to confirm whether you want to publish the tweet or not.

Here's an example tweet with image and without forcing:

```
$ cave twitter:tweet 'This is a CLI tweet' -a ~/Desktop/kitty.jpg

 -------------- --------------------------------------------
  Text           This is a CLI tweet
  Images         /home/nawarian/Desktop/kitty.jpg
  Account        nawarian
  Profile name   Níckolas Da Silva (nawarian)
 -------------- --------------------------------------------

 Proceed with this tweet? (yes/no) [yes]:
```

## Sending a twitter thread

**Command**: `cave twitter:thread <markdown-file>`

This command reads a markdown file and treats each heading as a tweet.

A tweet (heading) may or not have an image attachment, and they all should reference a file
in your disk.

Here's an example markdown file that publishes a thread of three tweets:

```markdown
## First tweet

![](picture.jpg)
This tweet will be sent with `picture.jpg` attached.

## Second tweet

This tweet doesn't use images at all

## Third tweet

![](second-picture.jpg)
But this one has and that's alright. You may as well paste links like https://codamos.com.br !
```

Assuming the above content is stored in a file named `thread.md` and both image files live
in the same directory, you can publish this thread by running the following command:

```
$ cave twitter:thread thread.md
```

**Important!** This command does not ask you to confirm before sending tweets.
