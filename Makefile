ci: composer-validate cbf cs require-check unused-check security-check test stan mutate deptrac

deptrac:
	php vendor/bin/deptrac

composer-validate:
	composer validate

require-check:
	php -dmemory_limit=4G vendor/bin/composer-require-checker --config-file=require-checker.json check composer.json

unused-check:
	php -dmemory_limit=4G vendor/bin/unused_scanner

security-check:
	php bin/console security:check

cbf:
	php -dmemory_limit=4G vendor/bin/phpcbf

cs:
	php -dmemory_limit=4G vendor/bin/phpcs

stan:
	php -dmemory_limit=4G vendor/bin/phpstan analyse && php vendor/bin/psalm --show-info=false

test:
	phpdbg -qrr -dmemory_limit=4G vendor/bin/phpunit --stop-on-failure && vendor/bin/coverage-check var/coverage.xml 90

phpunit-xml:
	[ -f phpunit.xml ] || cp phpunit.xml.dist phpunit.xml

mutate: test phpunit-xml
	phpdbg -qrr -dmemory_limit=4G vendor/bin/infection run --verbose --show-mutations --no-interaction --only-covered --coverage var --min-msi=85 --min-covered-msi=85 -j2

clean:
	rm -rf var/
