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

### Known bugs

- RAM Options on the frontend

### Env vars

```
APP_ENV=
APP_SECRET=
COMPOSE_FILE=docker-compose.yml:docker-compose.custom.yml
REDIS_PORT=
HTTP_PORT=
HTTPS_PORT
```

### Requirements

- docker
- node & npm

### Setup

- git clone `https://github.com/ClaudioVarandas/lw-servers`
- cd lw-servers
- create the `.env` and populate with the env vars
- npm i
- npm run build
- docker compose up -d
