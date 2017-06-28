
# session_handle
# PDO Mysql
# libs of client: redis
# namespace: common, common/lib, common/func, controller, model, view

# requirement
### php 5.4+, php-pdo, php-mysql

# quick start
---
## nginx configure
`
server {  
    listen       80;
    server_name  fastmvc2.local.com;
    rewrite ^\/([0-9]+)\.([0-9]+)\/(.+)\/?$ /index.php?v=$1.$2&c=$3 last;
    location ^~ index.php {
        root   /Users/zhoubin/dev/php/fastmvc2;
        index  index.html index.htm index.php;
    }
    location ~ \.php$ {
        root           /Users/zhoubin/dev/php/fastmvc2;
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  /Users/zhoubin/dev/php/fastmvc2$fastcgi_script_name;
        include        fastcgi.conf;
    }
}
`
