### Start
	git clone -b master safebox.bundle safebox

	cd safebox

### Run project

    docker-compose up --build -d
    
    touch database/sqlite
    
    chmod -R 777 bootstrap/ storage/ database/
    
    composer install
    
    cp .env.example .env
    
    php artisan migrate
    
#### Go to [localhost:8080](http://localhost:8080)

### Api Urls [localhost:8080/v1/safebox/](http://localhost:8080/v1/safebox)

### Postman collection file
    SafeBox API.postman_collection.json

### Run tests

    php ./vendor/bin/phpunit --configuration phpunit.xml 
