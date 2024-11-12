Project deployment, commands to run:

1. Clone project to your folder "git clone https://github.com/olegtsapok/symfony_restapi_test.git"
2. Go to project directory "cd symfony_restapi_test"
3. Load vendors "composer update"
4. Create database "php bin/console doctrine:database:create"
5. Create tables "php bin/console doctrine:schema:create"
6. Load initial data "php bin/console doctrine:fixtures:load"
7. Run project as server "symfony server:start"
8. Now project is available by url "http://127.0.0.1:8000"


Additional steps for unit tests:
1. Create test database "php bin/console --env=test doctrine:database:create"
2. Create tables "php bin/console --env=test doctrine:schema:create"
3. Run unit tests "php bin/phpunit"