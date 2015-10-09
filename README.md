# Telegram WeightLogBot  

Send it your weight; it gets logged.


To get started add the bot as a Telegram contact [https://telegram.me/WeightLogBot](https://telegram.me/WeightLogBot) then send it your weight

# Commands

`/output` - Shows a text log, and line chart of your weight

# API  

GET your data out to use as you like:

*Endpoint*: `http://weightlog.ashleyhindle.com/?token=[apitoken]` 



# I want my own  

Go to `https://telegram.me/BotFather`, send him `/newbot` and go through the motions until you get a `token`.  

`git clone git@github.com:ashleyhindle/weightlog.git; cd weightlog; composer install`  

*Run the bot*: `TELEGRAM_BOT_TOKEN='tokenfrombotfather' php bot.php`  

Go to `https://telegram.me/[yourbotname]` and send it your weight  
