# squid-db-auth-ip
This is the squid module that allows IP addresses in Database.

Used in [squid-db-auth-web](https://github.com/39ff/squid-db-auth-web) projects.


## Configuration squid.conf
example
```
external_acl_type ipdbauth ttl=60 children-startup=1 concurrency=100000 %>a /usr/bin/php /etc/squid/basic_db_ip_auth.php --dsn=mysql:dbname=test;host=127.0.0.1;charset=utf8mb4 --user=test --password=test
acl ipauth external ipdbauth
http_access allow ipauth
```

