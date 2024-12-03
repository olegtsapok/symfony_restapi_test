Project description.
Symfony7, php8

Task: Create REST API to be able to send info about company(nazwa, NIP, adres, miasto, kod pocztowy) 
and employees(imię, nazwisko, email, numer telefonu(opcjonalne)).
 Create CRUD actions.

Implemented features:
1. Base CRUD controller that can be user for any new model with all implemented base actions.
https://github.com/olegtsapok/symfony_restapi_test/blob/master/src/Controller/Api/CrudController.php

2. Validation for companies and employeers is implemented in models constraints
https://github.com/olegtsapok/symfony_restapi_test/tree/master/src/Entity

3. Soft deleted feature, so records will not be removed from database at once. 
Instead of deleting, field deletedAt will be populated on delete action and will be used for filtering records.

4. Simple authentication by querry parameter.

5. Unit testing. Unit tests for all api endpoints.

6. Swagger file with description of how to use all endpoints. Can be viewed online by https://editor.swagger.io/
https://github.com/olegtsapok/symfony_restapi_test/tree/master/swagger/swagger_openapi.yaml

7. Deployment steps:
https://github.com/olegtsapok/symfony_restapi_test/blob/master/deployment.md
