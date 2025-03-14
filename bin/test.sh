#!/bin/sh

# Run PHP tests
vendor/bin/phpunit

# Run JS build (to ensure no build errors)
npm run js-build
