# Circle K Locations

Installable WordPress plugin for the redesigned Circle K locations directory supplied in `CircleK-Locations-HTML.zip`.

Current plugin version: **1.2.0**.

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

The plugin automatically replaces that page's old table content and preserves the active theme header and footer. It seeds the 47 reconciled operating locations on first activation (35 Saudi Arabia, 12 United Arab Emirates). Version 1.2.0 also updates existing installations from the two September 2026 location workbooks. Branches explicitly marked closed or scheduled for closure are moved to Draft, so their records remain recoverable in WordPress. Use a full-width page template if the active theme constrains page content to a narrow column.

For a page with a different slug, add this shortcode to the page:

```text
[circlek_locations]
```

## Editable custom fields

Each item under **Locations** is a native WordPress custom post with these fields:

- Store name (the post title)
- Store number
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
- Display order

The `locations` page also gets a **Locations Page Settings** panel with fields for:

- Hero title
- Hero image URL
- Strategy title and description
- Button label and URL

All fields are registered with the WordPress REST API. Advanced Custom Fields is not required.

## Arabic support

The directory automatically detects the Arabic/RTL route (including Polylang's `/ar/locations/` route), mirrors the layout, and loads the Arabic name, city and address fields. Version 1.2.0 retains the completed translations supplied in `CircleK-Locations-Arabic-Content.xlsx`, uses the theme's Noto Kufi Arabic typography consistently throughout the RTL directory, and copies English into the Arabic fields for newly added locations until translations are supplied. Search supports Arabic and English text, Arabic store-count grammar is handled dynamically, and filter URLs keep their existing stable slugs.

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
