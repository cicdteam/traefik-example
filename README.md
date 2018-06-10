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

### Check how everything works

app1:

```
$ curl -H "Host: app1.localhost" localhost:1082
Hello from PHP1!
PHP 1 Connection to MySQL server failed: SQLSTATE[HY000] [2002] Connection refused
```

app2:

```
$ curl -H "Host: app2.localhost" localhost:1082
Hello from PHP2!
PHP 2 Connection to MySQL server failed: SQLSTATE[HY000] [2002] Connection refused
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
