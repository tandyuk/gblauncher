GUNBOT LAUNCHER
===

Gunbot launcher is used to deploy and maintain many individual gunbots.

Updated for v9.
For v8 use https://github.com/tandyuk/gblauncher/blob/578e120a9ab93667b15c9d9aeb363f7ca696b200/gblaunch.php

Usage instructions:
===

build               build and start bots

buildonly           build config files only

stop                Stop bots

clean               Delete everything and stop bots

reload              Rebuild config in-place




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

 Gunbot.XT.Edition.-.Linux.package.zip  (latest version from github)

edit the top of config.js and set server variables if you like.



php gblaunch.php build

sit back and wait while each exchange/pair/strategy is launched in its own process, with full colour logging courtesy of pm2.

pm2 l   - list all bots

pm2 log [id]  - logs for a bot



Changing config:
===
Edit config.js as you wish

php gblaunch.php reload

All configs are rebuilt in place.


Custom Config:
===

see end of example config.js

you can define your own strategy name, and include a "REQUIRES":".." parameter to specify the real type


Comments:
===
config.js is now parsed and stripped of comments before processing.

This means you can now use /*   */  and // style comment blocks in your main config.js


Thanks
===
If you like my work, consider buying me a beer or using one of my referral links :)

BTC: 3A3MHPnHW1A98wBbdNC2JZ19MrVa4dmPdU

Binance: https://www.binance.com/?ref=11890686

Cointracking: https://cointracking.info?ref=T995032

LocalBitcoins: https://localbitcoins.com/?ch=jzm6

http://viperbots.com 10% off when you use code TANDY at the checkout.
