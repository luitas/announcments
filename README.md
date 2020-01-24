# Announcements
Simple announcements site

# Steps to start
* Download
* Create DB and set privileges to db_user 
* Edit .env file to your user
`DATABASE_URL=mysql://db_user:db_password@127.0.0.1:db_port/db_name?serverVersion=5.7`
* Run `composer install`
* Run `php bin/console doctrine:migrations:migrate` - for DB init
* Run `php bin/console doctrine:fixtures:load` - for data init
* Run `symfony server:run` or `php bin/console server:run`


