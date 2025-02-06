#!/bin/bash

# delete docker services data
sudo rm -rf .docker/services/

# delete bundles assets
rm -rf public/bundles/

# delete symfony cache folder
sudo rm -rf var/

# delete composer packages
sudo rm -rf composer.lock
sudo rm -rf vendor/
