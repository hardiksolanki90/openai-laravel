# openai-laravel demo SaaS app

Working example of a multi-tenant SaaS application built on top of the
`hardiksolanki/openai-laravel` package.

This directory is an **overlay**, not a standalone Laravel install: the
`app/`, `resources/`, `routes/`, and `database/` files here are meant to be
copied on top of a fresh Laravel skeleton. It is intentionally not vendored
as a full framework install to keep the package repository lightweight.

## Setup

```bash
composer create-project laravel/laravel demo-app-runtime
cd demo-app-runtime
composer require laravel/breeze --dev
php artisan breeze:install blade

composer config repositories.openai-laravel path ../
composer require hardiksolanki/openai-laravel:@dev

# copy the overlay from this directory on top of demo-app-runtime
cp -R ../demo-app/app/Models/. app/Models/
cp -R ../demo-app/app/Http/Controllers/. app/Http/Controllers/
cp -R ../demo-app/app/Http/Livewire/. app/Http/Livewire/
cp -R ../demo-app/resources/views/. resources/views/
cp ../demo-app/routes/web.php routes/web.php
cp ../demo-app/tailwind.config.js tailwind.config.js

php artisan vendor:publish --tag=openai-config
php artisan migrate
php artisan serve
```

## Features

- User authentication (Laravel Breeze)
- Team management (create team, invite members, assign roles)
- Conversation interface (create/list/chat)
- Prompt template library
- Usage dashboard (tokens, cost, by-model breakdown)
- Settings: API keys, budget limits, team members
