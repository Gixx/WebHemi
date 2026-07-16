.PHONY: up down status sync-ui cert help

help:
	@echo "WebHemi hub — local development"
	@echo ""
	@echo "  make up       Start Storybook, UI watch→sync, Symfony HTTPS (p12)"
	@echo "  make down     Stop all of the above"
	@echo "  make status   Show process status"
	@echo "  make sync-ui  One-shot UI build + sync into webhemi-php"
	@echo "  make cert     Generate *.webhemi.local PKCS#12 (Symfony CA)"
	@echo ""
	@echo "Node/npm only in webhemi-ui. webhemi-php stays Composer + AssetMapper."
	@echo ""
	@echo "URLs after make up:"
	@echo "  Storybook  http://127.0.0.1:6006"
	@echo "  Login      https://127.0.0.1:8000/login"
	@echo "  Admin      https://admin.webhemi.local:8000/login"
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
