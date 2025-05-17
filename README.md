Create autoloader:
 - composer dump-autoload

Create tables:
- php bin/console create-tables

I use script on rapberry to send me email to game after reboot,
in crontab:
@reboot python /var/www/html/quiz/mail.py