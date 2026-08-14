.PHONY: up down status sync-ui cert test help php ui

# Extra goals for `make test php` / `make test ui` (no-op targets).
php ui:
	@:

help:
	@echo "WebHemi hub — local development"
	@echo ""
	@echo "  make up         Start Storybook, UI watch→sync, Symfony HTTPS (p12)"
	@echo "  make down       Stop all of the above"
	@echo "  make status     Show process status"
	@echo "  make sync-ui    One-shot UI build + sync into webhemi-php"
	@echo "  make cert       Generate *.webhemi.local PKCS#12 (Symfony CA)"
	@echo "  make test       Run PHP + UI tests"
	@echo "  make test php   PHPUnit (webhemi-php)"
	@echo "  make test ui    typecheck, lint, Storybook Vitest, Chromatic"
	@echo ""
	@echo "Node/npm only in webhemi-ui. webhemi-php stays Composer + AssetMapper."
	@echo "Chromatic needs CHROMATIC_PROJECT_TOKEN in hub .env (see docs/local-dev.md)."
	@echo ""
	@echo "URLs after make up:"
	@echo "  Storybook  http://127.0.0.1:6006"
	@echo "  Login      https://127.0.0.1:8000/admin/login"
	@echo "  Admin      https://admin.webhemi.local:8000/login"
	@echo "  Site       https://www.webhemi.local:8000/"
	@echo "  Site API   https://www.webhemi.local:8000/api/site"
	@echo "  See docs/local-dev.md for hosts + certificate setup"

up:
	@bash scripts/dev/up.sh

down:
	@bash scripts/dev/down.sh

status:
	@bash scripts/dev/status.sh

sync-ui:
	@cd webhemi-ui && npm run build
	@cd webhemi-php && bash bin/sync-ui.sh

cert:
	@bash scripts/dev/generate-cert.sh

# Usage: make test | make test php | make test ui
test:
	@bash scripts/test/run.sh $(word 2,$(MAKECMDGOALS))
