# ABC Academy - Automated Testing Makefile
# Cross-platform automation for testing and development

.PHONY: help test test-unit test-feature test-browser test-e2e test-dusk test-coverage test-watch test-parallel test-performance test-security test-api test-all test-ci install setup clean

# Default target
help:
	@echo "ABC Academy - Testing Automation"
	@echo "================================"
	@echo ""
	@echo "Available commands:"
	@echo "  make install     - Install dependencies"
	@echo "  make setup       - Setup development environment"
	@echo "  make test        - Run all tests"
	@echo "  make test-unit   - Run unit tests only"
	@echo "  make test-feature- Run feature tests only"
	@echo "  make test-browser- Run browser/E2E tests (Dusk)"
	@echo "  make test-e2e    - Run end-to-end tests (alias for test-browser)"
	@echo "  make test-dusk   - Run Dusk tests (alias for test-browser)"
	@echo "  make test-coverage- Run tests with coverage report"
	@echo "  make test-watch  - Run tests in watch mode"
	@echo "  make test-parallel- Run tests in parallel"
	@echo "  make test-performance- Run performance tests"
	@echo "  make test-security- Run security tests"
	@echo "  make test-api    - Run API tests"
	@echo "  make test-all    - Run all tests with coverage"
	@echo "  make test-ci     - Run CI-style tests (coverage + parallel)"
	@echo "  make clean       - Clean cache and temporary files"
	@echo ""

# Install dependencies
install:
	@echo "📦 Installing dependencies..."
	composer install --optimize-autoloader
	@echo "✅ Dependencies installed!"

# Setup development environment
setup: install
	@echo "🔧 Setting up development environment..."
	@php artisan key:generate --ansi
	@php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
	@php artisan migrate --force
	@php artisan db:seed
	@echo "✅ Development environment ready!"

# Run all tests
test:
	@echo "🧪 Running all tests..."
	@php artisan test
	@echo "✅ Tests completed!"

# Run unit tests only
test-unit:
	@echo "🔬 Running unit tests..."
	@php artisan test --testsuite=Unit
	@echo "✅ Unit tests completed!"

# Run feature tests only
test-feature:
	@echo "🎭 Running feature tests..."
	@php artisan test --testsuite=Feature
	@echo "✅ Feature tests completed!"

# Run browser/E2E tests (Dusk)
test-browser:
	@echo "🌐 Running browser/E2E tests..."
	@php artisan dusk
	@echo "✅ Browser tests completed!"

# Run end-to-end tests (alias)
test-e2e: test-browser
	@echo "✅ E2E tests completed!"

# Run Dusk tests (alias)
test-dusk: test-browser
	@echo "✅ Dusk tests completed!"

# Run tests with coverage
test-coverage:
	@echo "📊 Running tests with coverage..."
	@php artisan test --coverage
	@echo "✅ Coverage report generated!"

# Run tests in watch mode
test-watch:
	@echo "👀 Running tests in watch mode..."
	@php artisan test --watch

# Run tests in parallel
test-parallel:
	@echo "⚡ Running tests in parallel..."
	@php artisan test --parallel
	@echo "✅ Parallel tests completed!"

# Run performance tests
test-performance:
	@echo "🚀 Running performance tests..."
	@php artisan test tests/Performance
	@echo "✅ Performance tests completed!"

# Run security tests
test-security:
	@echo "🔒 Running security tests..."
	@php artisan test tests/Feature/SecurityTest.php
	@echo "✅ Security tests completed!"

# Run API tests
test-api:
	@echo "🌐 Running API tests..."
	@php artisan test tests/Feature/ApiTest.php
	@echo "✅ API tests completed!"

# Run all tests with coverage
test-all: test-coverage
	@echo "🎯 All tests with coverage completed!"

# Run CI-style tests
test-ci:
	@echo "🔄 Running CI-style tests..."
	@php artisan test --coverage --min=80 --parallel
	@echo "✅ CI tests completed!"

# Clean cache and temporary files
clean:
	@echo "🧹 Cleaning cache and temporary files..."
	@php artisan config:clear
	@php artisan cache:clear
	@php artisan route:clear
	@php artisan view:clear
	@php artisan optimize:clear
	@echo "✅ Cleanup completed!"

# Development server with tests
dev:
	@echo "🚀 Starting development environment..."
	@composer run dev

# Quick test before commit
precommit: test
	@echo "✅ Pre-commit tests passed!"

# Full test suite for deployment
deploy: test-ci
	@echo "🚀 Deployment tests passed!"
