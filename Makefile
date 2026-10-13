# ============================================================================
# Makefile — RPMS Developer Shortcuts
#
# Usage:
#   make install    Install all dependencies and prepare environment
#   make dev        Start local development server
#   make test       Run the full test suite
#   make build      Build the Docker production image
#   make deploy     Build + push + trigger Render deploy
#   make shell      Open a shell in the running Docker app container
#
# ============================================================================

.DEFAULT_GOAL := help
.PHONY: help install dev test lint build deploy shell migrate seed db-reset \
        queue-work cache-clear logs health

# ──────────────────────────────────────────────────────────────────────────────
# Variables
# ──────────────────────────────────────────────────────────────────────────────
PHP        := php
COMPOSER   := composer
ARTISAN    := $(PHP) artisan
DOCKER     := docker
DC         := docker compose
IMAGE      := ghcr.io/$(shell git config user.name | tr '[:upper:]' '[:lower:]')/rpms

# ──────────────────────────────────────────────────────────────────────────────
# Help
# ──────────────────────────────────────────────────────────────────────────────
help: ## Show this help message
	@echo ""
	@echo "  \033[1;34mRPMS — Research Project Management System\033[0m"
	@echo "  \033[0;90mGambella University, Ethiopia\033[0m"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-18s\033[0m %s\n", $$1, $$2}'
	@echo ""

# ──────────────────────────────────────────────────────────────────────────────
# Local Development
# ──────────────────────────────────────────────────────────────────────────────
install: ## Install dependencies, copy .env, generate key, migrate & seed
	@echo "→ Installing Composer dependencies..."
	$(COMPOSER) install
	@if [ ! -f .env ]; then \
		cp .env.example .env; \
		echo "→ Copied .env.example to .env"; \
	fi
	@touch database/database.sqlite
	$(ARTISAN) key:generate
	$(ARTISAN) migrate --seed
	$(ARTISAN) storage:link
	@echo "\033[32m✓ Installation complete\033[0m"

dev: ## Start the local development server (http://127.0.0.1:8000)
	@echo "→ Starting Laravel dev server..."
	$(ARTISAN) serve

test: ## Run the full PHPUnit test suite
	@echo "→ Running test suite..."
	$(ARTISAN) test --parallel

lint: ## Run PHP syntax and code style checks
	$(PHP) -l app/**/*.php
	@echo "→ Lint passed"

migrate: ## Run database migrations
	$(ARTISAN) migrate

seed: ## Re-seed the database with RBAC + demo data
	$(ARTISAN) db:seed --class=RbacSeeder
	$(ARTISAN) db:seed

db-reset: ## Drop all tables, re-migrate, re-seed (dev only!)
	@echo "\033[31m⚠  This will DESTROY all data!\033[0m"
	$(ARTISAN) migrate:fresh --seed

cache-clear: ## Clear all Laravel caches
	$(ARTISAN) config:clear
	$(ARTISAN) route:clear
	$(ARTISAN) view:clear
	$(ARTISAN) cache:clear
	@echo "\033[32m✓ Caches cleared\033[0m"

queue-work: ## Start the queue worker (database driver)
	$(ARTISAN) queue:work --sleep=3 --tries=3 --max-time=3600

health: ## Ping the local /health endpoint
	curl -s http://127.0.0.1:8000/health | $(PHP) -r 'echo json_encode(json_decode(file_get_contents("php://stdin")), JSON_PRETTY_PRINT);'

# ──────────────────────────────────────────────────────────────────────────────
# Docker
# ──────────────────────────────────────────────────────────────────────────────
build: ## Build the Docker production image
	@echo "→ Building Docker image: $(IMAGE)"
	$(DOCKER) build -t $(IMAGE):latest .
	@echo "\033[32m✓ Image built: $(IMAGE):latest\033[0m"

deploy: build ## Build image, push to GHCR, trigger Render deploy
	@echo "→ Pushing image to registry..."
	$(DOCKER) push $(IMAGE):latest
	@if [ -n "$$RENDER_DEPLOY_HOOK_URL" ]; then \
		echo "→ Triggering Render deploy hook..."; \
		curl -fsSL -X POST "$$RENDER_DEPLOY_HOOK_URL"; \
	else \
		echo "ℹ  RENDER_DEPLOY_HOOK_URL not set — skipping deploy hook"; \
	fi
	@echo "\033[32m✓ Deploy triggered\033[0m"

shell: ## Open an interactive shell in the running Docker app container
	$(DC) exec app bash

logs: ## Tail Docker app container logs
	$(DC) logs -f app

dc-up: ## Start all Docker Compose services (detached)
	$(DC) up -d
	@echo "\033[32m✓ Services started\033[0m"

dc-down: ## Stop all Docker Compose services
	$(DC) down

dc-restart: ## Restart all Docker Compose services
	$(DC) restart
