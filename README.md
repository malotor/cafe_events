# CQRS

This project is an example of Command Query Responsibility Segregation ( CQRS ) architecture. Is based on the example describe in ( http://cqrs.nu/ ) and rewrited in PHP.

## Start the project

    $ docker-compose build
    $ docker-compose run --rm composer install
    $ docker-compose up -d tests
    $ sh scripts/init_project
    $ docker-compose up -d app
    
    
## Run the tests

    $ docker-compose up -d tests
    $ sh scripts/phpunit
    
    $ sh scripts/phpunit --coverage-html ./coverage

## JSON API

    http://localhost:8080
    
## The domain

For this tutorial, we'll work in the cafe domain. Our focus will be on the concept of a tab, which tracks the visit of an individual or group to the cafe. When people arrive to the cafe and take a table, a tab is opened. They may then order drinks and food. Drinks are served immediately by the table staff, however food must be cooked by a chef. Once the chef has prepared the food, it can then be served.

During their time at the restaurant, visitors may order extra food or drinks. If they realize they ordered the wrong thing, they may amend the order - but not after the food and drink has been served to and accepted by them.

Finally, the visitors close the tab by paying what is owed, possibly with a tip for the serving staff. Upon closing a tab, it must be paid for in full. A tab with unserved items cannot be closed unless the items are either marked as served or cancelled first.
 
## Documentations

- https://github.com/rojoangel/event-sourcing
- https://github.com/dddinphp/blog-cqrs
- https://github.com/broadway/broadway
- https://gist.github.com/jsor/6e79afb989c866915f20
- http://eventuate.io/exampleapps.html
- http://squirrel.pl/blog/2015/08/31/introduction-to-event-sourcing-and-command-query-responsibility-segregation/
- https://blog.oasisdigital.com/2014/task-based-user-interfaces/
- https://www.securityartwork.es/2012/04/23/arquitecturas-robustas-y-seguras-con-cqrs-i/


## Doctrine

    sh scripts/doctrine orm:generate-entities src/Domain/ReadModel
    sh scripts/doctrine orm:schema-tool:drop --force
    sh scripts/doctrine orm:schema-tool:create
    sh scripts/doctrine orm:schema-tool:create --dump-sql > resources/db/events_cafe.sql
  
    sh scripts/doctrine orm:generate-entities src/Domain/ReadModel --update-entities
  
    sh scripts/doctrine orm:clear-cache:metadata
    sh scripts/doctrine orm:clear-cache:query
    sh scripts/doctrine orm:clear-cache:result
    
    sh scripts/doctrine orm:convert-mapping --from-database yml resources/doctrine
