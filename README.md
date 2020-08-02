# Installation

Execute `make build` to download Docker container and install all composer dependencies.

### Enabling  xdebug in PhpStorm

The xdebug port is defined in the `docker-compose.yml`:
``` yaml
services:
  app:
    build: .
    volumes:
      - ./:/app
    working_dir: /app
    ports:
      - 9001:9000
```

In this case, use port `9000` in the PhpStorm config:

![Xdebug ports](docs/images/xdebug_ports.png)

Then, we need to configure servers and how to accept xdebug connections & mappings: 

![Xdebug mappings](docs/images/xdebug_mappings.png)

### Enabling phpUnit in PhpStorm

First create a remote interpret for `phpUnit` referencing the Docker container 

![Xdebug mappings](docs/images/phpunit_remote.png)

Then, on the IDE top dropdown select `Edit configuration` and create a new one called `Full Unit`:

![Xdebug mappings](docs/images/phpunit_config.png)

# Basic usage

Once project is downloaded, we will execute:
- `make start` to start the container.
- `make stop` to stop the container.

For more actions, execute `make` without arguments.

