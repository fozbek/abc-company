# ABC-Company
This project helps you to manage products and orders. 
Customers can manage their orders.

## Requirements
    - Docker
    - docker-compose
    - openssl

## Installation
Run containers

    docker-compose up -d
    
Run migrations and load fixtures. These commands should run inside php-fpm container.
Container names should match. You may have configured container names. if you did, you know what to do.
    
    docker exec abc-company_php-fpm_1 bin/console doctrine:migrations:migrate
    docker exec abc-company_php-fpm_1 bin/console doctrine:fixtures:load -n
    
Generate .pem files. Password should be 'verysecurepassword' otherwise you should change it in lexik_jwt_authentication.yaml. Password is easy beacuse this project is built for development purpose.
    
    mkdir -p config/jwt
    openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
    chmod -R 644 config/jwt/*
    
## How to run
To login

    curl -X POST -H "Content-Type: application/json" http://localhost:8000/api/login_check -d '{"username":"customer1@domain.com","password":"verylongpassword"}'

Respose will be similar to this

    {
       "token" : "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJleHAiOjE0MzQ3Mjc1MzYsInVzZXJuYW1lIjoia29ybGVvbiIsImlhdCI6IjE0MzQ2NDExMzYifQ.nh0L_wuJy6ZKIQWh6OrW5hdLkviTs1_bau2GqYdDCB0Yqy_RplkFghsuqMpsFls8zKEErdX5TYCOR7muX0aQvQxGQ4mpBkvMDhJ4-pE4ct2obeMTr_s4X8nC00rBYPofrOONUOR4utbzvbd4d2xT_tj4TdR_0tsr91Y7VskCRFnoXAnNT-qQb7ci7HIBTbutb9zVStOFejrb4aLbr7Fl4byeIEYgp2Gd7gY"
    }

You should send token in request header for all /api/* endpoints. You can send like that.
    
    Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1ODg5NjcyMjQsImV4cCI6MTU4ODk3MDgyNCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiY3VzdG9tZXIxQGRvbWFpbi5jb20ifQ.YyMtU9CMzVe6iKLyxCLWLqudFQMJwDB5YH8veOc6DyvXLpkCi03L222G1noHk_zQNSKPSibKi9tsgacqhFmtCS-10cdurHm73jHkqbSMnQyiVlChLCCqBGbI9G8GhFVjjmx4OA1rSjGV6adDuPzOTk901EQqPKNpdncPuE7Mqo-VVpnUyokTZ8AC8Ix3c6baoX2RnSm9DPhRKWFWYqA_uACY75GRn5Y4qqEb4eFacvzkI81jCEKgqsFImP1YJ0xMUHnWIIsud2b_4yhFx-jMaJFKyj3wP8dlndTM3kFqs-FXxdcsGS24bC-7-3ccXJ8ytgxEgMqwCasEovYJIxJyTvkcQO7wMrJHfS8VhlkIwHX3O7eSwugx0j1otz7LF8lTECwbVUlRlynitGqpUblnEg_QofANpEJiZ3hmyAxjDEmhk4MF0FPrqZCZUIdhWrXjTpwW4NiCaM68kbjj63zc4XWHwW9FD-wgKLVRSfRwoQez2NlgfeHirnPQJP9wYX-I1urV9ecjg6r1fEMMr9p8xtT-Lh4Th0aUYF-mJDMVaHTZ2tS2qT_UmwD6KHMXnivmzwXxGARl3M-e4-0VlehObnDHA5cpTaRRY-CNeFRM-f3HZrYteiWAGgEGWxR-EytALM9DLfUC1iUJb7pEkSPqTTfPKj-0LxWnckzqZ-nVRy0

## Test
Just run 

    php bin/phpunit

## License

[![License](http://img.shields.io/:license-mit-blue.svg?style=flat-square)](http://badges.mit-license.org)

- **[MIT license](http://opensource.org/licenses/mit-license.php)**
 