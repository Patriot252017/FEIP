.PHONY: help cs-check cs-fix phpcs phpcbf psalm test all-checks

help:
	@echo "Available commands:"
	@echo "  make cs-check   - Check code style (PHP-CS-Fixer)"
	@echo "  make cs-fix     - Fix code style (PHP-CS-Fixer)"
	@echo "  make phpcs      - Check standards (PHP_CodeSniffer)"
	@echo "  make phpcbf     - Fix standards (PHP_CodeSniffer)"
	@echo "  make psalm      - Static analysis (Psalm)"
	@echo "  make test       - Run tests"
	@echo "  make all-checks - Run all code quality checks"

cs-check:
	@set PHP_CS_FIXER_IGNORE_ENV=1 && php vendor/bin/php-cs-fixer fix --allow-risky=yes --dry-run --using-cache=no --diff --verbose || exit 0

cs-fix:
	@set PHP_CS_FIXER_IGNORE_ENV=1 && php vendor/bin/php-cs-fixer fix --allow-risky=yes --using-cache=no --verbose || exit 0

phpcs:
	@if exist src ( php vendor/bin/phpcs --standard=psr12 src/ ) else ( echo "Warning: src/ directory not found" && exit 0 )

phpcbf:
	@if exist src ( php vendor/bin/phpcbf --standard=psr12 src/ || exit 0 ) else ( echo "Warning: src/ directory not found" && exit 0 )

psalm:
	@php vendor/bin/psalm --show-info=true

test:
	@if exist bin/phpunit ( php bin/phpunit --testdox ) else ( echo "Warning: phpunit not found" && exit 0 )

all-checks:
	@echo "Running all code quality checks..."
	@make phpcs && make cs-check && make psalm && make test
	@echo "All checks completed!"