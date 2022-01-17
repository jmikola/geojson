.PHONY: composer test docs mkdocs

COMPOSER_ARGS=update --no-interaction --prefer-source
PHPUNIT_ARGS=--process-isolation

composer:
	@command -v composer >/dev/null 2>&1; \
	if test $$? -eq 0; then \
		composer $(COMPOSER_ARGS); \
	elif test -r composer.phar; then \
		php composer.phar $(COMPOSER_ARGS); \
	else \
		echo >&2 "Cannot find composer; aborting."; \
		false; \
	fi

test: composer
	@command -v phpunit >/dev/null 2>&1; \
	if test $$? -eq 0; then \
		phpunit $(PHPUNIT_ARGS); \
	elif test -r phpunit.phar; then \
		php phpunit.phar $(PHPUNIT_ARGS); \
	else \
		echo >&2 "Cannot find phpunit; aborting."; \
		false; \
	fi

mkdocs:
	@command -v mkdocs >/dev/null 2>&1; \
	if test $$? -eq 0; then \
		mkdocs build --clean; \
	else \
		echo >&2 "Cannot find mkdocs; aborting."; \
		false; \
	fi

docs: mkdocs

publish-docs: docs
	mkdocs gh-deploy
	@echo "If origin is your local fork, you may need to run:"
	@echo "    " git push REMOTE gh-pages:gh-pages
