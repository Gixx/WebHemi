.PHONY: up down status sync-ui help

help:
	@echo "WebHemi hub — local development"
	@echo ""
	@echo "  make up       Start Storybook, UI watch→sync, Symfony server"
	@echo "  make down     Stop all of the above"
	@echo "  make status   Show process status"
	@echo "  make sync-ui  One-shot UI build + sync into webhemi-php"
	@echo ""
	@echo "URLs after make up:"
	@echo "  Storybook  http://127.0.0.1:6006"
	@echo "  Login      http://127.0.0.1:8000/login"
	@echo "  Admin      http://127.0.0.1:8000/admin"
	@echo "  See docs/local-dev.md for admin.webhemi.local hosts setup"

up:
	@bash scripts/dev/up.sh

down:
	@bash scripts/dev/down.sh

status:
	@bash scripts/dev/status.sh

sync-ui:
	@cd webhemi-ui && npm run build
	@cd webhemi-php && bash bin/sync-ui.sh
