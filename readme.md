
# requirement
 - php 5.4+
 - php-pdo
 - php-mysql
 - composer

# quick start

## about code
 - cp config/env.php.example config/env.php
 - modify config/dev.php to change your own db config
 - composer install

## vim /etc/hosts
    127.0.0.1 fastmvc2.local.com

## nginx configure
    server {  
        listen       80;
        server_name  fastmvc2.local.com;
        rewrite ^\/([0-9]+)\.([0-9]+)\/(.+)\/?$ /index.php?v=$1.$2&c=$3 last;
        location ^~ index.php {
            root   /path/to/fastmvc2;
            index  index.html index.htm index.php;
        }
        location ~ \.php$ {
            root           /path/to/fastmvc2;
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME  /path/to/fastmvc2fastmvc2$fastcgi_script_name;
            include        fastcgi.conf;
        }
    }

## test url
`http://fastmvc2.local.com/1.0/test/test`


# project structure
    index.php
    composer.json
    common
        common.php #file must to loaded every time
        lib #custom libs
        function
            util.php #utilities(function)
    config
        env.php.example #change to env.php(define the environment)
        dev.php
    src
        view
        controller
            Base.php #base class for controller
            Test.php #test controller class
        model
            Base.php #base class for model
            Test.php #test model class
            UserSessioin.php #user session class for mysql
        
