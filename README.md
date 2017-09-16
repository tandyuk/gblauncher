Usage instructions:

build [id]          build bots

start [id]          Start bot, optional id or all according to start_delay

stop [id]           Stop bot, optional id or all, according to stop_delay

restart [id]        Restart bot(s)

reload [id]         Rebuild config in-place [for bot(s)]

update              Extract new Gunbot, rebuild configs and restart all bots

exportlog id file   Export logfile from specified bot to file


Pre-requisites:

pm2 process manager

valid gunbot config.js



Initial setup:

Clone this repo into a directory of your choice.

All files except logs will be created under this directory.

Add the following files:

 config.js          (your gunbot v5 config file)

 GUNBOT_V5.x.x.zip  (latest version from github)

edit the top of gblaunch.php and set variables as you like.



php gblaunch.php build

sit back ans wait while each exchange/pair/strategy is launched in its own process, with full colour logging courtesy of pm2.

pm2 l   - list all bots

pm2 log [id]  - logs for a bot



Changing config:

Edit config.js as you wish

php gblaunch.php reload

All configs are rebuilt in place.



Updating:

Download new gunbot, eg GUNBOT_V5_3_2.zip

Edit gblaunch.php $gbver value to "5_3_2"

php gblaunch.php update

Pair by pair, new gb is unzipped, config rebuilt, and bot restarted using pm2.
