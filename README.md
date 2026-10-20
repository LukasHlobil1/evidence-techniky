# Evidence techniky a servisních zásahů

Webová aplikace v **Laravelu 12** pro evidenci firemního nářadí. Každé zařízení má vlastní evidenční číslo, stav a historii servisních zásahů včetně součtu nákladů.

## Funkce

- **CRUD zařízení** – seznam, detail, přidání, úprava, smazání (servisní záznamy se mažou kaskádově).
- **Filtr podle stavu** `active` / `service` / `retired` (s počty) + vyhledávání podle názvu či evidenčního čísla.
- **Detail zařízení se servisní historií** a formulářem pro **přidání servisního záznamu** přímo z detailu.
- **Součet servisních nákladů** jednoho zařízení (v detailu i v seznamu).
- **Barevně i textově rozlišený stav** – zelená „✔ Aktivní“, oranžová „🔧 V servisu“, šedá přeškrtnutá „✖ Vyřazeno“.
- **Volitelné rozšíření:** export servisní historie jednoho zařízení do **CSV** (oddělovač `;`, UTF-8 s BOM pro Excel).

## Datový model

| Tabulka | Sloupce |
|---|---|
| `equipment` | `id`, `name`, `inventory_number` (unique), `status`, `purchased_at` (date), timestamps |
| `service_records` | `id`, `equipment_id` (FK → equipment, cascade delete), `description`, `serviced_at` (date), `cost` (decimal 10,2), timestamps |

Vztahy: `Equipment hasMany ServiceRecord`, `ServiceRecord belongsTo Equipment`.

## Validační pravidla

| Pravidlo | Implementace |
|---|---|
| Evidenční číslo je jedinečné | `Rule::unique(...)->ignore($equipment)` + unikátní index v DB; číslo se před validací normalizuje (trim, velká písmena) |
| Status z povoleného seznamu | `Rule::in(['active', 'service', 'retired'])` (konstanta `Equipment::STATUSES`) |
| Datum nákupu/servisu v platném formátu | `date_format:Y-m-d` (odmítne např. `2023-02-30`, `01.05.2023`), nákup od 1950 a ne v budoucnosti; servis ne před nákupem a ne v budoucnosti |
| Cena je nezáporné desetinné číslo | `numeric`, `decimal:0,2`, `min:0` (přijímá i desetinnou čárku `250,50`) |

Validace jsou ve Form Requestech `app/Http/Requests/EquipmentRequest.php` a `ServiceRecordRequest.php`, chybové hlášky jsou česky.

## Instalace a spuštění

Požadavky: PHP 8.2+, Composer, rozšíření `pdo_sqlite`.

```bash
git clone <url-repozitare> evidence-techniky
cd evidence-techniky
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite          # Windows: type nul > database\database.sqlite
php artisan migrate --seed              # tabulky + testovací data (16 zařízení se servisní historií)
php artisan serve
```

Aplikace poběží na <http://127.0.0.1:8000>. Bootstrap je přibalen v `public/vendor/bootstrap`, není potřeba `npm`.

## Testy

```bash
php artisan test      # nebo vendor/bin/phpunit
```

16 feature testů pokrývá CRUD, filtr, jedinečnost evidenčního čísla, povolené stavy, formát dat, nezápornou cenu, přidání/smazání servisního záznamu, součet nákladů a CSV export.

## Přehled souborů

- `database/migrations/2026_10_20_*` – migrace tabulek
- `app/Models/Equipment.php`, `app/Models/ServiceRecord.php` – modely a vztahy
- `database/factories/*`, `database/seeders/DatabaseSeeder.php` – testovací data
- `app/Http/Controllers/EquipmentController.php` – resource controller + CSV export
- `app/Http/Controllers/ServiceRecordController.php` – přidání a smazání servisního záznamu
- `routes/web.php` – routy (`Route::resource('equipment', …)`)
- `resources/views/equipment/*` – šablony, `resources/views/components/status-badge.blade.php` – odznak stavu
- `tests/Feature/*` – testy
