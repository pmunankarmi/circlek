# Circle K Locations

Installable WordPress plugin for the redesigned Circle K locations directory supplied in `CircleK-Locations-HTML.zip`.

Current plugin version: **1.5.0**.

## Updates from GitHub

Version 1.2.0 adds a GitHub Releases updater. After this version is installed once, WordPress checks the latest release in `pmunankarmi/circlek` and shows newer versions on the Plugins and Dashboard Updates screens.

Each GitHub release must:

1. Use a semantic version tag such as `v1.2.1`.
2. Include an asset named exactly `circlek-locations.zip`.
3. Keep the plugin version in `circlek-locations.php` equal to the release version.

The repository and release asset must remain publicly downloadable for unattended WordPress updates.

## Install

1. Upload `circlek-locations.zip` in **Plugins → Add New → Upload Plugin**.
2. Activate **Circle K Locations**.
3. Open the existing WordPress page whose slug is `locations`.

The plugin automatically replaces that page's old table content and preserves the active theme header and footer. The bundled roster contains every row from the supplied update sheets: 42 Saudi Arabia locations (13 fuel stations and 29 convenience stores) and 14 United Arab Emirates stores. Store codes are visible in the directory and searchable. Version 1.5.0 adds native WordPress taxonomies for hierarchical location areas and location types, and automatically migrates existing location classifications without replacing location posts. It also removes the editable store-number and display-order fields; the visible row number is generated automatically in PHP, so published locations are always numbered consecutively without gaps. Fuel-group counts use station/stations (and the Arabic equivalents) instead of store/stores, and each store-code badge is aligned with the start of its store details. The plugin refreshes GitHub release metadata immediately when an administrator uses WordPress's **Check again** action. Use a full-width page template if the active theme constrains page content to a narrow column.

## Location taxonomies

Version 1.5.0 registers two native WordPress taxonomies:

- **Location Areas** — hierarchical terms arranged as Country → Region → City.
- **Location Types** — Fuel Stations and Convenience Stores.

Existing country, region, city and type metadata is migrated to taxonomy terms on update. The existing metadata remains as a backwards-compatible fallback, and saving a location keeps both representations synchronized. Geography and type can still be edited through the controlled fields in the Location Details panel, while the resulting terms can be reviewed from the Locations admin menu and queried through the REST API.

For a page with a different slug, add this shortcode to the page:

```text
[circlek_locations]
```

## Editable custom fields

Each item under **Locations** is a native WordPress custom post with these fields:

- Store name (the post title)
- Source store code
- Country
- Region
- City
- Store name (Arabic; initially copied from English)
- City (Arabic; initially copied from English)
- Location type (Fuel Station or Convenience Store)
- Address
- Address (Arabic; initially copied from English)
- Directions URL (optional; a Google Maps search is generated when blank)

The row number shown on the public directory is generated automatically from the current rendered order and is not stored as an editable field.

The `locations` page also gets a **Locations Page Settings** panel with fields for:

- Hero title
- Hero image URL
- Strategy title and description
- Button label and URL

All fields are registered with the WordPress REST API. Advanced Custom Fields is not required.

## Arabic support

The directory automatically detects the Arabic/RTL route (including Polylang's `/ar/locations/` route), mirrors the layout, and loads the Arabic name, city and address fields. Existing translations supplied in `CircleK-Locations-Arabic-Content.xlsx` are retained where their branch remains in the replacement roster. New entries use English until translations are supplied. The RTL directory uses Noto Kufi Arabic consistently, search supports Arabic, English and store codes, Arabic store-count grammar is handled dynamically, and filter URLs keep their existing stable slugs.

## Existing deep links

The current footer links remain compatible:

- `?region=34` → Western Region
- `?region=35` → Central Region
- `?region=36` → Eastern Region

Search, country, region, city and type filters are reflected in the URL so filtered views can be shared.

## Theme integration override

Automatic replacement only applies to the page slug `locations`. A developer can disable it and place the shortcode manually:

```php
add_filter( 'circlek_locations_auto_replace', '__return_false' );
```

The plugin does not remove location content when deactivated or deleted.
