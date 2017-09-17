GUNBOT LAUNCHER
===

Gunbot launcher is used to deploy and maintain many individual gunbots.


NOTES
=====
exportlogs function is not written yet - use pm2 log [id] for now

no limiting to specific id code is in yet - all operations currently affect ALL bots

There is no current checking/stopping/deleting of bots if you remove the pair from your config. - use pm2 delete id and delete the bot folder.

pm2 list  to get numeric ids of bots

bot folder name will be eg:
  gunbot_launcher/b_BTC-USD_bb          -     BTC-USD on bittrex using bb strategy
  gunbot_launcher/p_BTC_BCH_stepgain    -     BTC-BCH on poloniex using stepgain



Usage instructions:
===

build [id]          build bots

start [id]          Start bot, optional id or all according to start_delay

stop [id]           Stop bot, optional id or all, according to stop_delay

restart [id]        Restart bot(s)

reload [id]         Rebuild config in-place [for bot(s)]

update              Extract new Gunbot, rebuild configs and restart all bots

exportlog id file   Export logfile from specified bot to file




Pre-requisites:
===
debian 8.8 x64 (tested), other linux/mac variants should be fine.

Windows is not supported, but windows users may be able to use this to just build configs.

php5

pm2 process manager

valid gunbot config.js



Initial setup:
===
Clone this repo into a directory of your choice.

All files except logs will be created under this directory.

Add the following files:

 config.js          (your gunbot v5 config file)

 GUNBOT_V5.x.x.zip  (latest version from github)

edit the top of gblaunch.php and set variables as you like.



php gblaunch.php build

sit back and wait while each exchange/pair/strategy is launched in its own process, with full colour logging courtesy of pm2.

pm2 l   - list all bots

pm2 log [id]  - logs for a bot



Changing config:
===
Edit config.js as you wish

php gblaunch.php reload

All configs are rebuilt in place.



Updating:
===
Download new gunbot, eg GUNBOT_V5_3_2.zip

Edit gblaunch.php $gbver value to "5_3_2"

php gblaunch.php update

Pair by pair, new gb is unzipped, config rebuilt, and bot restarted using pm2.


Custom Config:
===

see example config.js

you can either:

define a list of override options in the overrides array, and call them using the first 2 examples

define your own strategy name, and include a "REQUIRES":".." parameter to specify the real type


Comments:
===
config.js is now parsed and stripped of comments before processing.

This means you can now use /*   */  and // style comment blocks in your main config.js
