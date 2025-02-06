#!/bin/bash

# delete docker services data
sudo rm -rf .docker/services/

# delete symfony cache folder
sudo rm -rf var/

# delete composer packages
sudo rm -rf composer.lock
sudo rm -rf vendor/
