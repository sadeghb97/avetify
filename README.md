# Avetify

**Avetify** is a lightweight PHP framework for building data-driven admin panels and internal tools. It uses plain PHP (no full-stack MVC stack), renders HTML directly, and centers on three building blocks—**entities** (record forms), **tables** (editable grids), and **listers** (sortable collections)—backed by MySQL via `mysqli`.

The framework powers several production apps, including [Matchstick](https://github.com/sadeghb97/matchstick) (notably its **transfers** module), **skills**, and **puzzlinho** (Composer integration).

| | |
|---|---|
| **PHP** | `>= 8.0` |
| **License** | MIT |
| **Version** | `0.1.0` (build `1`) |
| **Package** | [`sadeghb97/avetify`](https://github.com/sadeghb97/avetify) |

---

## Table of contents

- [Philosophy](#philosophy)
- [Requirements](#requirements)
- [Installation](#installation)
- [Project layout](#project-layout)
- [Bootstrapping](#bootstrapping)
- [Core concepts](#core-concepts)
- [Building a page](#building-a-page)
- [Themes and assets](#themes-and-assets)
- [Extending the framework](#extending-the-framework)
- [Additional modules](#additional-modules)
- [Real-world usage](#real-world-usage)
- [Scaffolding scripts](#scaffolding-scripts)
- [License](#license)

---

## Philosophy

Avetify is intentionally small and explicit:

- **No magic router** — each screen is a PHP entry file (e.g. `footballers.php`, `avetify.php`).
- **Server-rendered UI** — pages are built with PHP classes that echo HTML through `HTMLInterface` and theme renderers.
- **MySQL-first** — `DBConnection` extends `mysqli` with helpers; `QueryBuilder` and filter collections compose SQL.
- **Composable UI** — tables, entity forms, and listers share sorting, filtering, and pagination via `SetModifier`.
- **Extend in the host app** — domain logic, custom table fields, themes, and navigation live in the consuming project’s `lib/` tree.

---

## Requirements

### PHP extensions

| Extension | Purpose |
|-----------|---------|
| `mysqli` | Database access |
| `gd` | Image processing |
| `fileinfo` | MIME detection |
| `exif` | Image metadata |
| `posix`, `pcntl` | CLI / background tasks |

### Runtime

- PHP **8.0+**
- MySQL or MariaDB
- A web server (Apache/Nginx) or PHP CLI for scripts

---

## Installation

Avetify supports two integration styles used in real projects.

### Option A — Git clone (sibling directory)

Used by **matchstick** and **skills**: clone the framework next to your app and load the monolithic bootstrap.

```bash
cd /path/to/htdocs/your-project
git clone https://github.com/sadeghb97/avetify.git avetify
```

Add `avetify` to your project `.gitignore` if you treat it as a vendored dependency.

Or use the helper script from the framework repo:

```bash
/path/to/avetify/avtclone   # clones into ./avetify
```

### Option B — Composer

Used by **puzzlinho**:

```bash
composer require sadeghb97/avetify:dev-main
```

Composer autoloads the `Avetify\` PSR-4 namespace from `src/`. You still need to initialize paths and expose static assets (see [Bootstrapping](#bootstrapping) and [Themes and assets](#themes-and-assets)).

### Quick scaffold

From a directory under `htdocs/`, run `avtcreate` (see [Scaffolding scripts](#scaffolding-scripts)) to clone Avetify and generate starter `lib/lib.php`, `*Connection.php`, `avetify.php`, and `index.php`.

---

## Project layout

A typical consuming application looks like this:

```
your-project/
├── avetify/              # cloned framework (Option A) or vendor/sadeghb97/avetify (Option B)
├── avetify.php           # optional front controller
├── index.php             # often redirects to avetify.php
├── lib/
│   ├── lib.php           # bootstrap: init manager, require domain code
│   ├── YourConnection.php
│   ├── entities/         # AvtEntity subclasses
│   ├── tables/           # DBTable subclasses
│   ├── listers/          # DBLister subclasses
│   ├── models/           # DataModel subclasses
│   ├── fields/           # custom table/entity fields
│   └── theme/            # ThemesManager subclass, navigation
├── footballers.php       # example page: table view
├── footballer.php        # example page: entity form
└── .avtfiles/            # runtime uploads/backups (created by Routing)
```

The framework itself is organized as:

```
avetify/
├── avetify.php           # explicit require_once bootstrap (all classes)
├── assets/               # CSS, JS, fonts, themes (main, green, modern, …)
├── data/                 # bundled datasets (e.g. countries)
├── src/
│   ├── AvetifyManager.php
│   ├── DB/               # DBConnection, QueryBuilder, filters
│   ├── Entities/         # AvtEntity, fields, sorters, filters
│   ├── Table/            # AvtTable, DBTable, field types
│   ├── Lister/           # AvtLister, DBLister
│   ├── Themes/           # Green, Modern, Modernix, Classic, …
│   ├── Components/       # dialogs, charts, selectors, cropper, …
│   ├── Forms/, Auth/, Api/, GalRepo/, Standings/, …
│   └── …
├── avtcreate, avtclone
└── composer.json
```

---

## Bootstrapping

Every app must call `AvetifyManager::init()` once during startup.

```php
use Avetify\AvetifyManager;

// $basePath: project root on disk
// $publicPath: web-accessible root (often same as base)
// $publicUrl: URL prefix for the app, e.g. "/transfers"
// $assetUrl: URL prefix for framework assets, e.g. "/avetify/assets"

AvetifyManager::init(__DIR__, __DIR__, '/your-app', '/avetify/assets');
```

### Option A — full bootstrap

```php
require_once __DIR__ . '/../avetify/avetify.php';

use Avetify\AvetifyManager;

AvetifyManager::init(__DIR__, __DIR__, '/skills', '/avetify/assets');

require_once __DIR__ . '/SkillsConnection.php';
// … require entities, tables, themes, etc.
```

`avetify.php` registers every framework class via `require_once` (no Composer autoload needed).

### Option B — Composer autoload

```php
require_once __DIR__ . '/../vendor/autoload.php';

use Avetify\AvetifyManager;

AvetifyManager::init(dirname(__DIR__), dirname(__DIR__), '/puzzlinho', '/avetify/assets');
```

Use `use Avetify\…` imports for framework types. Ensure `/avetify/assets` is mapped in your web server to the framework `assets/` directory (clone path or `vendor/sadeghb97/avetify/assets`).

### Database connection

Subclass `DBConnection` and implement credentials:

```php
use Avetify\DB\DBConnection;

class YourConnection extends DBConnection {
    public function getHost(): string { return 'localhost'; }
    public function getUser(): string { return 'root'; }
    public function getPassword(): string { return ''; }
    public function getDBName(): string { return 'your_database'; }
}
```

`DBConnection` provides `fetchRow`, `fetchSet`, `fetchMap`, `fetchAvtSet`, filter-aware queries, and a singleton via `getInstance()`.

---

## Core concepts

### SetModifier — shared behavior

`AvtTable`, `AvtLister`, and entity/set views inherit from `SetModifier`, which handles:

- URL-driven **sorting** (`?{setKey}_sort=…`)
- **Filtering** via filter factors and discrete qualifiers
- **Pagination** through `PaginationConfigs`
- **Rendering** through a `SetRenderer` and `renderPage($title)`

### Tables (`DBTable`)

Editable admin grids bound to a MySQL table. On construction, `DBTable` loads field definitions, fetches rows, and can persist inline edits through `QueryBuilder`.

Typical subclass responsibilities:

| Method | Role |
|--------|------|
| `makeTableFields()` | Column definitions (`EditableField`, `ExtendedAvatarField`, custom fields, …) |
| `fetchDBRecords()` | Rows to display (often via connection helpers) |
| `getItemLink()` | Link from a row to a detail page |
| `getTableRenderer()` | Optional theme override |

**Example (Matchstick transfers):** `FootballersTable` extends `DBTable`, defines avatar/name/club columns, and loads footballers through `TransfersConnection::getFootballers()` with optional `club` / `country` / `mode` query params.

### Entities (`AvtEntity`)

Single-record create/edit forms with field metadata, validation hooks, image handling, and optional redirect after insert.

Typical subclass responsibilities:

| Method | Role |
|--------|------|
| `getTableName()` / `getSuperKey()` | DB mapping |
| `dataFields()` | Writable `EntityTextField`, `EntitySelectField`, wrappers, … |
| `getTheme()` | `ThemesManager` for layout and assets |
| `renderEntityPage()` | Renders the full entity UI |

**Example:** `footballer.php` instantiates `FootballerEntity` and calls `renderEntityPage()`.

### Listers (`DBLister`)

Ordered, often drag-aware lists backed by the database—used for priorities, categories, and gallery ordering.

**Example (skills):** `SkillsLister` loads skills ordered by `priority` and renders through a custom `SkillsTheme`.

### Models (`DataModel`)

Typed row objects with hydration from associative arrays (`Footballer`, `Club`, `Skill`, …), usually created via `AvtEntityItem::mapArray()`.

### Routing

`Avetify\Routing\Routing` provides URL/path helpers: `publicUrl`, `serverRootPath`, query param add/remove, HTTPS detection, and `.avtfiles/` backup directory resolution.

### HTML layer

`HTMLInterface`, `Styler`, `WebModifier`, and `Placeable` components generate markup without a template engine. Custom fields implement `place()` / rendering hooks and attach CSS or JS through the active theme.

---

## Building a page

### Table listing page

```php
<?php
require_once 'lib/lib.php';

$conn = new YourConnection();
$table = new YourRecordsTable($conn, 'records');
$table->renderPage('Records');
```

### Entity detail page

```php
<?php
require_once 'lib/lib.php';

$conn = new YourConnection();
$entity = new YourRecordEntity($conn, 'record');
$entity->renderEntityPage();
```

### Custom theme and navigation

Extend `GreenTheme` or `ModernTheme`, override `getNavigationBar()`, favicon, and `moreHeaderTags()` for project CSS/JS:

```php
class YourTheme extends GreenTheme {
    public function getNavigationBar(): ?NavigationBar {
        return new YourNavigation();
    }
}
```

Wire the theme in your entity’s `getTheme()` or table’s `getTableRenderer()`.

---

## Themes and assets

Built-in themes under `assets/themes/`:

| Theme | Typical use |
|-------|-------------|
| **Green** | Default admin tables and listers |
| **Modern** | Galleries, cards, markdown viewers |
| **Modernix** | Compact icon-forward layouts |
| **Classic** | Alternate navigation styling |

`ThemesManager` controls optional includes: Bootstrap, Font Awesome, cropper, CodeMirror-style coding fields, markdown, charts, and more—enabled per theme subclass.

Static assets are served from `AvetifyManager::assetUrl()`. Configure your web server so `/avetify/assets` resolves correctly for both clone and Composer layouts.

---

## Extending the framework

Host applications routinely add:

1. **Custom table fields** — extend `TableField` / `EditableField` (e.g. `FootballerClubsField`, `PostField` in transfers).
2. **Custom entity fields** — wrap selectors, flags, or JS datasets.
3. **Connection methods** — complex SQL and domain queries on `DBConnection` subclasses.
4. **Themes & navigation** — branding, menus, and page-specific JS.
5. **Task pages** — extend `TaskPageRenderer` for long-running web or CLI jobs.

Keep framework code untouched; domain code stays in the app’s `lib/` directory.

---

## Additional modules

| Module | Description |
|--------|-------------|
| **Auth** (`AvtAuth`) | Session and token auth against `users` / `tokens` tables |
| **Api** (`JsonApiResponder`) | Structured JSON responses with error capture |
| **GalRepo** | Filesystem gallery scanning and management UI |
| **Standings** | League tables, date stats, scoring ranges |
| **Components** | Charts (linear/pie), country selectors, image cropper, markdown, dialogs |
| **Crawling** | `Scrapper`, `RawDocumentLoader` for HTTP/HTML fetch |
| **Network** | `NetworkFetcher`, proxy-aware fetchers |
| **Files** | `Filer`, `ImageUtils`, `RecycleCan`, FFmpeg helpers |
| **Modules/Cli** | Colored terminal output for scripts |
| **Repo/Countries** | World country datalist (used heavily in transfers) |
| **Externals** | JDF (Jalali dates), Gumlet image resize |

---

## Real-world usage

### Matchstick — `transfers/`

- Bootstrap: `mainlib.php` → `avetify/avetify.php` + `AvetifyManager::init(…, "/matchstick", …)`.
- `TransfersConnection` — footballers, clubs, nations, transfer joins.
- Pages: `footballers.php`, `footballer.php`, `clubs.php`, `nation.php`, …
- Custom fields for clubs, flags, hints, avatars; scrapers under `scrap/`.
- Theme: `TransfersTheme` + `TransfersNavigation`.

### Skills

- Standalone app under `/skills` with `lib/lib.php` requiring sibling `avetify`.
- Entities: `SkillEntity`, `SectionEntity`, `TipEntity`.
- Listers for ordered skills/tips/sections; coding-aware theme (`SKCodingContents`).

### Puzzlinho

- `composer require sadeghb97/avetify:dev-main`.
- `lib/init_avt.php` — Composer autoload + `AvetifyManager::init`.
- Analytics-style `DBTable` subclasses (`StatusTable`, `RegStatsTable`, …) with bridged field definitions.
- Shared `MatchstickTheme`; charts and Shamsi date utilities in page scripts.

---

## Scaffolding scripts

| Script | Purpose |
|--------|---------|
| `avtcreate` | Run inside `htdocs/<project>/`: clones Avetify, creates `lib/lib.php`, `*Connection.php`, `avetify.php`, `index.php`, `.gitignore` |
| `avtclone` | Clone only `avetify/` into the current directory |

Install globally (optional):

```bash
chmod +x /path/to/avetify/avtcreate
cp /path/to/avetify/avtcreate ~/bin/
```

---

## Examples

The `examples/readme/` directory demonstrates rendering a Markdown README with `MarkdownBox` and `ModernTheme`—useful as a minimal theme/rendering sample.

---

## License

MIT © [sadeghb97](https://github.com/sadeghb97)

Issues and source: [github.com/sadeghb97/avetify](https://github.com/sadeghb97/avetify)
