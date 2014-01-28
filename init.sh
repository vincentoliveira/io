chmod -R 0777 app/cache app/logs
rm -rf app/cache/*

php app/console assets:install web --symlink
php app/console assetic:dump

php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:create
php app/console doctrine:fixtures:load --append --purge-with-truncate


