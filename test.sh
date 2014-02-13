php app/console doctrine:schema:update --force --env=test
php app/console cache:clear --env=test

if [ "$1" == "cc" ]
then
    echo "Test avec couverture de code"
    phpunit -c app/ --coverage-html=build
else
    echo "Test sans couverture de code"
    echo "Pour activer la couverture: $0 cc"
    bin/behat @IOMenuBundle $@
fi
