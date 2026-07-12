# Repository Guidelines

## Project Structure & Module Organization
- `src/`: PHP package source (PSR-4: `MacCesar\\LaravelGlideEnhanced\\`). Key modules: `ImageProcessor`, `Http/Controllers/ImageController`, `Facades/Img` and `ImageProcessor`, `Console/Commands/CleanImageCache`.
- `config/images.php`: Publishable config with cache, defaults, routes, disk, and presets.
- `routes/web.php`: Registers image processing route under a configurable prefix (default `glide`).
- `docs/`: Package docs and usage references.
- `tests/`: Add unit/feature tests here when contributing (uses Orchestra Testbench).

## Build, Test, and Development Commands
- `composer install`: Install dependencies for local development.
- `vendor/bin/phpunit`: Run the test suite (Orchestra Testbench).
- `php artisan vendor:publish --tag=images-config`: Publish package config into a host Laravel app.
- `php artisan images:clean-cache [--days=7]`: Clean generated Glide cache in the host app.

## Coding Style & Naming Conventions
- Standard: PSR-12; autoload via PSR-4.
- Indentation: 2 spaces (match repository style).
- Naming: `PascalCase` classes, `camelCase` methods/variables, `UPPER_SNAKE_CASE` for constants.
- Namespace: `MacCesar\\LaravelGlideEnhanced\\...` under `src/` (e.g., controllers in `Http/Controllers`).
- Keep code and comments in English. Prefer small, focused methods and clear docblocks for public APIs.

## Testing Guidelines
- Frameworks: PHPUnit + Orchestra Testbench.
- Layout: `tests/Unit/*Test.php` and `tests/Feature/*Test.php`.
- Focus: cover `ImageProcessor`, `ImageController` behavior (routing, parameters, cache), and `Traits/HasImages` path resolution.
- Run: `vendor/bin/phpunit`.

## Commit & Pull Request Guidelines
- Commits: Follow Conventional Commits (`feat:`, `fix:`, `docs:`, `refactor:`, `BREAKING CHANGE:`). Keep changes atomic and messages imperative.
- PRs: Provide a clear description, rationale, and test plan. Link related issues. Include config/routing examples and before/after snippets where relevant. Update README/CHANGELOG/UPGRADE when behavior changes.
- Branching: Create topic branches from `develop` and open PRs against `develop`.

## Security & Configuration Tips
- Routes: Avoid collisions by customizing `images.routes.prefix` (default `glide`).
- Storage: Ensure `images.disk` matches where your files live (`public` or `local`).
- Cache: Use `images:clean-cache` or tune `config/images.php` (`cache.lifetime`, `cache.path`).
