
# requirement
 - php 5.4+
 - php-pdo
 - php-mysql

# quick start
## cp config/env.php.example config/env.php
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
