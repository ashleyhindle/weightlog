# Telegram WeightLogBot  

This bot was made to make it super easy to log your weight.  Simply send it your weight, and it gets logged.

You don't need to open an app, press three icons, then get logging

To get started add the bot as a Telegram contact [https://telegram.me/WeightLogBot](https://telegram.me/WeightLogBot)

# API  

GET your data out to use as you like:

*Endpoint*: `http://weightlog.ashleyhindle.com/?token=[apitoken]` 



# I want my own  

Go to `https://telegram.me/BotFather`, send him `/newbot` and go through the motions until you get a `token`.  

`git clone git@github.com:ashleyhindle/weightlog.git; cd weightlog; composer install`  

*Run the bot*: `TELEGRAM_BOT_TOKEN='tokenfrombotfather' php bot.php`  

Go to `https://telegram.me/[yourbotname]` and send it your weight  
