# Simple example of MySQL, Traefik and two PHP projects with Nginx frontends and common loadbalancer

### Prepare
- check and change IP adderss (` - "baza:5.9.59.102"`) in `app1/docker-compose.yaml` and `app2/docker-compose.yaml` to IP address of our host
- check and change port number `1082` to whatever you want (available) on your host in `traefik/docker-compose.yaml`


### Create external docker network for load balancer (traefik)

```
$ docker network create proxy
```

### Start Mysql, Traefik and apps (app1, app2)

```
$ for i in mysql traefik app1 app2; do cd $i; docker-compose up -d --build; cd ..; done
```

### Ensure MySQL started and ready for connections

```
$ cd mysql; docker-compose logs --tail=3; cd ..
Attaching to mysql_mysql_1
mysql_1  | 2018-06-10T07:38:32.344711Z 0 [Note] Event Scheduler: Loaded 0 events
mysql_1  | 2018-06-10T07:38:32.345128Z 0 [Note] mysqld: ready for connections.
mysql_1  | Version: '5.7.22'  socket: '/var/run/mysqld/mysqld.sock'  port: 3306  MySQL Community Server (GPL)
```

### Check how everything works

app1:

```
$ curl -H "Host: app1.localhost" localhost:1082
Hello from PHP1!
PHP 1 Connected successfully to MySQL server
```

app2:

```
$ curl -H "Host: app2.localhost" localhost:1082
Hello from PHP2!
PHP 2 Connected successfully to MySQL server
```

### Stop everything

containers:

```
$ for i in app1 app2 traefik mysql; do cd $i; docker-compose down; cd ..; done
```

network:

```
$ docker network rm proxy
```

### What's under hood

- MySQl - running as container but in host network space (available on host interfaces)
- Traefik - running as container and binds to external docker network `proxy` only. Exposing port `80` to host (mapped to port `1082`)
- App1 - `php-fpm` and `nginx` based apps running as containers and binds to
    - web - internal `project1` docker network created by docker-compose and external `proxy` docker network
    - php - internal `project1` docker netowrk only
- App2 - `php-fpm` and `nginx` based apps running as containers and binds to
    - web - internal `project2` docker network created by docker-compose and external `proxy` docker network
    - php - internal `project2` docker netowrk only

>NOTE: php apps connects to MySQL by hostname `baza` configured as `external_hosts` in docker-compose yamls
