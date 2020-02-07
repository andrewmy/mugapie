ci: composer-validate composer-install cbf cs require-check unused-check security-check test stan mutate deptrac

deptrac:
	php vendor/bin/deptrac

composer-validate:
	composer validate

composer-install:
	composer install

require-check:
	php vendor/bin/composer-require-checker check composer.json

unused-check:
	php vendor/bin/unused_scanner

security-check:
	php bin/console security:check

cbf:
	php vendor/bin/phpcbf

cs:
	php vendor/bin/phpcs

stan:
	php vendor/bin/phpstan analyse && php vendor/bin/psalm --show-info=false

test:
	phpdbg -qrr -dmemory_limit=-1 vendor/bin/phpunit --stop-on-failure && vendor/bin/coverage-check var/coverage.xml 90

phpunit-xml:
	[ -f phpunit.xml ] || cp phpunit.xml.dist phpunit.xml

mutate: test phpunit-xml
	phpdbg -qrr vendor/bin/infection run --verbose --show-mutations --no-interaction --only-covered --coverage var --min-msi=85 --min-covered-msi=85 -j2

clean:
	rm -rf var/
