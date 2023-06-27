# AlertApi Project

The base of this project is scanning barcode from product and then add it to virtual stores and you can add an alert date and you will receive notification as reminder, you cant forget item now !

## Getting Started

1. Clone https://github.com/clmntgbr/setup and run `make start`
2. Clone this repo
3. Run `cp .env.dist .env`
4. Run `make start`
5. Run `make init` to initialize the project
6. You can run `make help` to see all commands available

## Overview

Open `https://traefik.traefik.me/dashboard/#/` in your favorite web browser for traefik dashboard

Open `https://maildev.traefik.me` in your favorite web browser for maildev

Open `https://rabbitmq.traefik.me` in your favorite web browser for rabbitmq

Open `https://alert.traefik.me` in your favorite web browser for symfony app

## Features

* PHP 8.1.3
* Nginx 1.20
* RabbitMQ 3-management
* MariaDB 10.4.19
* MailDev
* Traefik latest
* Symfony 6.1.3

**Enjoy!**
