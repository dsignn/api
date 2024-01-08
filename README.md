# Dsign Api

This project is build on [Slim framework](http://www.slimframework.com/) and compose the rest api to usi dsign application.
The development env in build with [Docker](https://www.docker.com/) and [Docker compose](https://docs.docker.com/compose/).

## Install

```bash
docker-compose up 
```

## Setup

Steps to config the API.

### Generate password
```bash
php console/console.php oauth:generate-password
```

### Generate private key
```bash
php console/console.php oauth:generate-private-key
```

### Generate public key
```bash
php console/console.php oauth:generate-public-key
```

### Generate client
```bash
php console/console.php oauth:create-client dsign-client dsign-client dsign4!
```  

### Generate user
```bash
php console/console.php user-repo:create
```
