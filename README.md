# LW Servers 

## Demo

### What is done

- Backend UI to upload the excel file with the data.
- Frontend UI to provide servers listing with filters:
  - locations
  - storage
  - ram
  - storage types
- API (Json), with the endpoints:
  - servers list filtered
  - locations
  - storage
  - ram
  - storage types

### API Endpoints

| Endpoint      | Method | Action  | Url                                         |
|---------------|--------|---------|---------------------------------------------|
| Servers       | GET    | index   | `http://localhost:89/servers`               |
| Locations     | GET    | index   | `http://localhost:89/options/locations`     |
| Storage Types | GET    | index   | `http://localhost:89/options/storage-types` |
| Locations     | GET    | index   | `http://localhost:89/options/storage`       |
| Locations     | GET    | index   | `http://localhost:89/options/ram`           |


### TODO

- Unit test
- Feature test
- Bug fix

### Known bugs

- Frontend
  - RAM Options checkbox

### Env vars

```
APP_ENV=dev
APP_SECRET=389a7b6a7721515acbc66605f3ee12c2
COMPOSE_FILE=docker-compose.yml:docker-compose.custom.yml
REDIS_PORT=6385
HTTP_PORT=89
HTTPS_PORT=8443
```

### Requirements

- docker and docker compose
- node 16 & npm 8.11

### Setup

- git clone `https://github.com/ClaudioVarandas/lw-servers`
- cd lw-servers
- create the `.env` and populate with the env vars
- npm i
- npm run build
- docker compose up -d
- docker exec -it lw-servers-php-1 sh
- composer install
- Navigate to: `http://<hostname>:89/`

Or ask for the public link :)

### Tests

- Run `docker exec -it leaseweb-servers-php_test-1 sh` to enter the tests container
- Run `php bin/phpunit`


