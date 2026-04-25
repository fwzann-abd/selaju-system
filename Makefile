# ============================================================================
# Selaju System — Makefile
# ============================================================================
# Onboarding:  make setup → isi .env → make install
# Daily:       make run
# ============================================================================

.PHONY: setup install run stop help _serve _preflight _kill_stale

# ANSI colors
GREEN  := \033[0;32m
YELLOW := \033[1;33m
CYAN   := \033[0;36m
RED    := \033[0;31m
BOLD   := \033[1m
DIM    := \033[2m
RESET  := \033[0m

# PID file for background processes
PID_FILE := .selaju.pids

# ── Setup (first time / safe to re-run) ────────────────────────────────────
setup:
	@echo ""
	@echo "$(CYAN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(RESET)"
	@echo "$(BOLD)  🚀 Selaju System — Setup$(RESET)"
	@echo "$(CYAN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(RESET)"
	@echo ""
	@# ── Composer install (idempotent) ──
	@echo "$(YELLOW)▸ Installing PHP dependencies...$(RESET)"
	@composer install --no-interaction 2>&1 || { \
		echo "$(RED)✘ composer install gagal. Pastikan PHP & Composer sudah terinstall.$(RESET)"; \
		exit 1; \
	}
	@echo ""
	@# ── Copy .env (skip if exists) ──
	@if [ -f .env ]; then \
		echo "$(DIM)▸ .env sudah ada, skip copy $(RESET)"; \
	else \
		echo "$(YELLOW)▸ Copying .env.example → .env$(RESET)"; \
		if [ -f .env.example ]; then \
			cp .env.example .env; \
		else \
			echo "$(RED)✘ .env.example tidak ditemukan!$(RESET)"; \
			exit 1; \
		fi; \
	fi
	@echo ""
	@# ── Generate key (skip if already set) ──
	@if grep -q "^APP_KEY=base64:" .env 2>/dev/null; then \
		echo "$(DIM)▸ APP_KEY sudah ada, skip generate $(RESET)"; \
	else \
		echo "$(YELLOW)▸ Generating application key...$(RESET)"; \
		php artisan key:generate --no-interaction; \
	fi
	@echo ""
	@# ── NPM install (idempotent) ──
	@echo "$(YELLOW)▸ Installing NPM dependencies...$(RESET)"
	@npm install 2>&1 || { \
		echo "$(RED)✘ npm install gagal. Pastikan Node.js & npm sudah terinstall.$(RESET)"; \
		exit 1; \
	}
	@echo ""
	@echo "$(GREEN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(RESET)"
	@echo "$(BOLD)  ✅ Setup selesai!$(RESET)"
	@echo "$(GREEN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(RESET)"
	@echo ""
	@echo "  $(RED)⚠  WAJIB dilakukan sebelum lanjut:$(RESET)"
	@echo ""
	@echo "  1. Edit file $(BOLD).env$(RESET) dan isi konfigurasi database:"
	@echo "     $(CYAN)DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD$(RESET)"
	@echo ""
	@echo "  2. Pastikan database server sudah $(BOLD)running$(RESET)"
	@echo ""
	@echo "  3. Lalu jalankan:"
	@echo "     $(GREEN)make install$(RESET)"
	@echo ""

# ── Install (fresh database + services) ────────────────────────────────────
install:
	@echo ""
	@echo "$(CYAN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(RESET)"
	@echo "$(BOLD)  📦 Selaju System — Install$(RESET)"
	@echo "$(CYAN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(RESET)"
	@echo ""
	@# ── Preflight: check setup was done ──
	@if [ ! -d vendor ]; then \
		echo "$(RED)✘ Folder vendor/ tidak ditemukan. Jalankan 'make setup' dulu.$(RESET)"; \
		exit 1; \
	fi
	@if [ ! -f .env ]; then \
		echo "$(RED)✘ File .env tidak ditemukan. Jalankan 'make setup' dulu.$(RESET)"; \
		exit 1; \
	fi
	@if [ ! -d node_modules ]; then \
		echo "$(RED)✘ Folder node_modules/ tidak ditemukan. Jalankan 'make setup' dulu.$(RESET)"; \
		exit 1; \
	fi
	@# ── Preflight: check DB connection ──
	@echo "$(YELLOW)▸ Checking database connection...$(RESET)"
	@php artisan db:show --no-interaction 2>/dev/null | head -1 > /dev/null 2>&1 || { \
		echo "$(RED)✘ Tidak bisa konek ke database!$(RESET)"; \
		echo "  Pastikan:"; \
		echo "  - Database server sudah running"; \
		echo "  - Konfigurasi DB_* di .env sudah benar"; \
		exit 1; \
	}
	@echo "$(GREEN)  ✓ Database connected$(RESET)"
	@echo ""
	@# ── Kill stale services if any ──
	@$(MAKE) --no-print-directory _kill_stale
	@# ── Fresh migrate + seed ──
	@echo "$(YELLOW)▸ Running fresh migration + seed...$(RESET)"
	php artisan migrate:fresh --seed --no-interaction
	@echo ""
	@# ── Broadcasting (skip if already installed) ──
	@if [ -f config/reverb.php ]; then \
		echo "$(DIM)▸ Broadcasting (Reverb) sudah terinstall, skip $(RESET)"; \
	else \
		echo "$(YELLOW)▸ Installing broadcasting (Laravel Reverb)...$(RESET)"; \
		php artisan install:broadcasting --no-interaction; \
	fi
	@echo ""
	@echo "$(GREEN)✅ Install selesai! Starting services...$(RESET)"
	@echo ""
	@echo "  $(YELLOW)💡 Selanjutnya cukup gunakan $(BOLD)make run$(RESET)$(YELLOW) untuk start services$(RESET)"
	@echo ""
	@$(MAKE) --no-print-directory _serve

# ── Run (daily start) ──────────────────────────────────────────────────────
run:
	@echo ""
	@echo "$(CYAN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(RESET)"
	@echo "$(BOLD)  ▶  Selaju System — Run$(RESET)"
	@echo "$(CYAN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(RESET)"
	@echo ""
	@# ── Preflight checks ──
	@if [ ! -d vendor ]; then \
		echo "$(RED)✘ Folder vendor/ tidak ditemukan. Jalankan 'make setup' dulu.$(RESET)"; \
		exit 1; \
	fi
	@if [ ! -f .env ]; then \
		echo "$(RED)✘ File .env tidak ditemukan. Jalankan 'make setup' dulu.$(RESET)"; \
		exit 1; \
	fi
	@if [ ! -d node_modules ]; then \
		echo "$(RED)✘ Folder node_modules/ tidak ditemukan. Jalankan 'make setup' dulu.$(RESET)"; \
		exit 1; \
	fi
	@# ── Kill stale services if any ──
	@$(MAKE) --no-print-directory _kill_stale
	@$(MAKE) --no-print-directory _serve

# ── Stop background processes ──────────────────────────────────────────────
stop:
	@if [ -f $(PID_FILE) ]; then \
		echo "$(YELLOW)▸ Stopping background services...$(RESET)"; \
		while read pid; do \
			if kill -0 $$pid 2>/dev/null; then \
				kill $$pid 2>/dev/null && echo "  Stopped PID $$pid"; \
			else \
				echo "  $(DIM)PID $$pid already stopped$(RESET)"; \
			fi; \
		done < $(PID_FILE); \
		rm -f $(PID_FILE); \
		echo "$(GREEN)✅ All services stopped.$(RESET)"; \
	else \
		echo "$(YELLOW)No running services found.$(RESET)"; \
	fi

# ── Internal: kill stale background processes ──────────────────────────────
_kill_stale:
	@if [ -f $(PID_FILE) ]; then \
		echo "$(YELLOW)▸ Cleaning up stale services...$(RESET)"; \
		while read pid; do \
			kill $$pid 2>/dev/null && echo "  $(DIM)Killed stale PID $$pid$(RESET)" || true; \
		done < $(PID_FILE); \
		rm -f $(PID_FILE); \
	fi

# ── Internal: serve all 3 processes ────────────────────────────────────────
_serve:
	@echo "$(YELLOW)▸ Starting Reverb WebSocket server (background)...$(RESET)"
	@php artisan reverb:start & echo $$! >> $(PID_FILE)
	@echo "$(YELLOW)▸ Starting Queue Worker (background)...$(RESET)"
	@php artisan queue:work & echo $$! >> $(PID_FILE)
	@sleep 1
	@echo ""
	@echo "$(GREEN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(RESET)"
	@echo "$(BOLD)  🟢 All services running$(RESET)"
	@echo "$(GREEN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(RESET)"
	@echo ""
	@echo "  $(CYAN)Vite Dev Server$(RESET)   → terminal ini (foreground)"
	@echo "  $(CYAN)Reverb WebSocket$(RESET)  → background (PID $$(sed -n '1p' $(PID_FILE)))"
	@echo "  $(CYAN)Queue Worker$(RESET)      → background (PID $$(sed -n '2p' $(PID_FILE)))"
	@echo ""
	@echo "  $(YELLOW)Tekan Ctrl+C untuk menghentikan semua services$(RESET)"
	@echo ""
	@trap 'echo ""; echo "$(YELLOW)▸ Shutting down background services...$(RESET)"; \
		while read pid; do kill $$pid 2>/dev/null; done < $(PID_FILE); \
		rm -f $(PID_FILE); \
		echo "$(GREEN)✅ All services stopped.$(RESET)"' EXIT INT TERM; \
	composer run dev

# ── Help ────────────────────────────────────────────────────────────────────
help:
	@echo ""
	@echo "$(BOLD)Selaju System — Available Commands$(RESET)"
	@echo ""
	@echo "  $(GREEN)make setup$(RESET)     Pertama kali: install deps, copy .env, generate key"
	@echo "  $(GREEN)make install$(RESET)   Fresh migrate + seed, install broadcasting, start services"
	@echo "  $(GREEN)make run$(RESET)       Start semua services (daily use)"
	@echo "  $(GREEN)make stop$(RESET)      Stop background services (Reverb + Queue)"
	@echo "  $(GREEN)make help$(RESET)      Tampilkan bantuan ini"
	@echo ""
	@echo "$(BOLD)Onboarding Flow:$(RESET)"
	@echo "  1. $(CYAN)make setup$(RESET)   → isi .env → $(CYAN)make install$(RESET)"
	@echo "  2. Selanjutnya cukup: $(CYAN)make run$(RESET)"
	@echo ""
	@echo "$(BOLD)Safety:$(RESET)"
	@echo "  • Semua command aman dijalankan berkali-kali (idempotent)"
	@echo "  • Jika dijalankan tanpa urutan, akan muncul pesan error yang jelas"
	@echo "  • Background services otomatis di-cleanup sebelum start baru"
	@echo ""
