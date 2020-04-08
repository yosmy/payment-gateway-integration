# Test

export UID
export GID
docker-compose \
-f docker/all.yml \
-p yosmy_payment_gateway_integration \
up -d \
--remove-orphans --force-recreate

docker exec -it yosmy_payment_gateway_integration_php sh

./vendor/bin/phpunit

./vendor/bin/phpunit --coverage-html tests/report