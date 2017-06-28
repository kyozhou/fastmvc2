
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
 
## about db init
    CREATE TABLE `user_session` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `ip` varchar(15) NOT NULL DEFAULT '',
      `session_id` char(32) NOT NULL DEFAULT '',
      `data` varchar(500) NOT NULL DEFAULT '',
      `is_deleted` bit(1) NOT NULL DEFAULT b'0',
      `time_created` int(11) NOT NULL,
      `time_updated` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `session_id` (`session_id`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

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
            fastcgi_param  SCRIPT_FILENAME  /path/to/fastmvc2$fastcgi_script_name;
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
        
