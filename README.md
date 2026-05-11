# Logio Test — Product Detail API

Symfony 7.4 aplikace pro vyhledávání produktů s file-based cache a počítáním dotazů.

## Požadavky

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)

## Spuštění

```bash
docker compose up -d
```

Aplikace poběží na **http://localhost:8080**.

## API

### GET /api/products/{id}

Vrátí data produktu ve formátu JSON.

```bash
curl http://localhost:8080/api/products/123
```

**Workflow při každém volání:**
1. Zkontroluje se file cache (`var/product_cache/{id}.json`)
2. Pokud produkt není v cache → zavolá se DB driver (výchozí: ElasticSearch)
3. Výsledek se uloží do cache
4. Počet dotazů na produkt se zvýší o 1 (`var/query_counts.json`)
5. Vrátí se JSON s daty produktu

## Konfigurace

### Přepnutí DB driveru (ElasticSearch ↔ MySQL)

V `app/config/services.yaml` změň jeden alias:

```yaml
# ElasticSearch (výchozí)
app.active_product_repository:
    alias: App\Repository\ElasticSearchProductRepository

# MySQL
app.active_product_repository:
    alias: App\Repository\MysqlProductRepository
```

Po změně vymaž cache:

```bash
docker compose exec app php bin/console cache:clear
```

### Přepnutí cache technologie (filesystem → Redis)

Cache řeší Symfony `cache.app` pool — žádná vlastní implementace, jen konfigurace.

V `app/config/packages/cache.yaml`:

```yaml
framework:
    cache:
        app: cache.adapter.redis
        default_redis_dsn: 'redis://localhost'
```

### Přepnutí počítadla dotazů

Implementuj `App\Contract\ProductViewCounterInterface` a nastav alias v `services.yaml`:

```yaml
App\Contract\ProductViewCounterInterface:
    alias: App\Counter\TvujNovyCounter
```

### Cesta k souboru s počítadlem

```yaml
parameters:
    app.query_counter_file: '%kernel.project_dir%/var/query_counts.json'
```

### CORS

Povolené origins jsou nastaveny v `app/config/packages/nelmio_cors.yaml`:

```yaml
allow_origin: ['http://localhost:3000']
```

## Architektura

Dekorátorový řetězec — controller neví nic o cache ani o DB technologii:

```
ProductController
  └── CountingProductRepository   ← decorator: inkrementuje counter při každém dotazu
        └── CachedProductRepository  ← decorator: cache (výchozí: Symfony filesystem)
              └── ElasticSearchProductRepository  ← adapter nad ES driverem
                    └── ElasticSearchDriverInterface  ← dodáno frameworkem
```

Přepnutí na MySQL = změna jednoho aliasu v `services.yaml` (`app.active_product_repository`).  
Přepnutí cache technologie = konfigurace Symfony `cache.app` poolu (filesystem → Redis → Memcached).

## Struktura projektu

```
logio-test/
├── docker-compose.yml          # Docker služby (app, nginx, mysql)
├── Dockerfile                  # PHP 8.3-fpm image
├── docker/nginx/default.conf   # Nginx konfigurace
└── app/                        # Symfony 7.4 aplikace
    ├── src/
    │   ├── Contract/
    │   │   ├── Driver/         # ElasticSearchDriverInterface, MysqlDriverInterface
    │   │   ├── ProductFinderInterface.php
    │   │   └── ProductViewCounterInterface.php
    │   ├── Controller/         # ProductController — GET /api/products/{id}
    │   ├── Repository/
    │   │   ├── ElasticSearchProductRepository.php  ← adapter
    │   │   ├── MysqlProductRepository.php          ← adapter
    │   │   ├── CachedProductRepository.php         ← decorator
    │   │   └── CountingProductRepository.php       ← decorator
    │   ├── Counter/            # FileProductViewCounter (flock pro souběžnost)
    │   └── Driver/             # Stub implementace (nahradit drivery z frameworku)
    ├── config/
    │   ├── services.yaml       # DI — dekorátorový řetězec a aliasy
    │   └── packages/
    │       └── nelmio_cors.yaml
    └── var/
        └── query_counts.json   # Počty dotazů (generováno za běhu)
```

## Užitečné příkazy

```bash
# Spustit stack
docker compose up -d

# Zastavit stack
docker compose down

# Vymazat Symfony cache
docker compose exec app php bin/console cache:clear

# Zobrazit logy
docker compose logs -f

# SSH do PHP kontejneru
docker compose exec app bash

# Nainstalovat nový Composer balíček
docker compose exec app composer require vendor/package
```

## Docker služby

| Služba | Port | Popis |
|--------|------|-------|
| nginx  | 8080 | HTTP server |
| app    | —    | PHP 8.3-FPM |
| mysql  | 3306 | MySQL 8.0 |

### MySQL přihlašovací údaje

| Parametr | Hodnota |
|----------|---------|
| Host     | localhost:3306 |
| Database | logio |
| User     | logio |
| Password | logio |
| Root password | root |
