# LetShout API

### Requirements
* Docker
* Docker compose

### Setup
    docker pull adrianet86/php-redis
    
    docker pull composer

    git clone https://github.com/adrianet86/letshout.git
    
    cd letshout/php/src 
    
    cp .env.example .env
    
    docker run --rm -ti -v $PWD:/app -w /app composer install --ignore-platform-reqs
    
    sudo chmod 777 -R var
    
    # Back to root path of project
    
    cd ../../
    
    docker-compose up -d --build 
    
    curl -S http://127.0.0.1/shout/nba?limit=5&cache=1

### Setup prod environment

Prod environmet must be set in order to use Twitter API instead of file repository.

Change the variable `PHP_ENVIRONMENT` to `prod` in the `/.env` file.

However the vars `TWITTER_KEY` and `TWITTER_SECRET` from the `/php/src/.env` must be changed for
valid Twitter credentials.   

### API
Api has just one endpoint:

>GET /shout/nba

And two parameters:

Integer **limit**: limit of messages. Default and max are 10.

Bool (integer) **cache**: use of cache 

>GET /shout/nba?limit=5&cache=1


        
### Test
#### Unit testing
    #  From path php/src
    
    docker run --rm -ti -v $PWD:/app -w /app adrianet86/php-redis php vendor/bin/phpunit -c phpunit.xml --testsuite Unit

#### Integration 
    #  From path php/src
    docker network create testing_network
    
    docker run -d --network testing_network --name redis_testing redis:4.0.5-alpine
    
    docker run --rm --network testing_network -ti -v $PWD:/app -w /app adrianet86/php-redis php vendor/bin/phpunit -c phpunit.xml --testsuite Integration
    
    docker rm --force redis_testing
    
    docker network rm testing_network
