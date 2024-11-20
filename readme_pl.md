Opis projektu.
Symfony7, php8

Github repositorium:
https://github.com/olegtsapok/symfony_restapi_test

Zaimplementowane funkcje:
1. Podstawowy kontroler CRUD, który może być użytkownikiem dla dowolnego nowego modelu ze wszystkimi zaimplementowanymi 
operacjami bazowymi.
https://github.com/olegtsapok/symfony_restapi_test/blob/master/src/Controller/Api/CrudController.php

2. Walidacja dla firm i pracowników jest zaimplementowana w ograniczeniach modeli
https://github.com/olegtsapok/symfony_restapi_test/tree/master/src/Entity

3. Funkcja miękkiego usuwania, więc rekordy nie zostaną usunięte z bazy danych od razu.
Zamiast usuwania, pole "removedAt" zostanie wypełnione podczas akcji usuwania i będzie używane do filtrowania rekordów.

4. Proste uwierzytelnianie za pomocą parametru zapytania.

5. Testowanie jednostkowe. Testy jednostkowe dla wszystkich endpointów interfejsu API.

6. Plik Swagger z opisem sposobu korzystania ze wszystkich endpointów. Można go obejrzeć online pod adresem https://editor.swagger.io/
https://github.com/olegtsapok/symfony_restapi_test/tree/master/swagger/swagger_openapi.yaml
