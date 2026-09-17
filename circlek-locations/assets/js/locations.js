/* ==========================================================================
   Circle K — Locations
   One filter engine shared by all three layout options. It works off data
   attributes already in the HTML, so every store is in the markup and the page
   is complete with JavaScript off — this only adds search, filtering and the
   card/table switch.

   Contract:
     [data-store]                a filterable item (card, row, list entry)
       [data-primary]            the canonical copy of a store — counted once
       data-country / data-region / data-city / data-hay
     [data-groupable]            a section that hides when it has no visible store
       [data-group-count]        gets that section's visible count
     [data-country-tab="all|id"] [data-region-chip="id"] [data-city-chip="id"]
     [data-view-btn="grid|list"] #ckl-q  #ckl-shown  .ckl-empty  .ckl-reset
   ========================================================================== */
(function () {
  'use strict';

  var root = document.querySelector('.ckl');
  if (!root) return;

  var $  = function (sel) { return root.querySelector(sel); };
  var $$ = function (sel) { return Array.prototype.slice.call(root.querySelectorAll(sel)); };

  var input    = $('#ckl-q');
  var clearBtn = $('.ckl-search__clear');
  var shownEl  = $('#ckl-shown');
  var emptyEl  = $('.ckl-empty');
  var emptyRst = $('.ckl-empty__reset');
  var results  = $('.ckl-results');
  var finder   = $('.ckl-finder');
  var sentinel = $('.ckl-sentinel');

  var tabs      = $$('[data-country-tab]');
  var regionBtn = $$('[data-region-chip]');
  var cityBtn   = $$('[data-city-chip]');
  var typeBtn   = $$('[data-type-chip]');
  var viewBtns  = $$('[data-view-btn]');
  var resetBtns = $$('.ckl-reset');

  var items     = $$('[data-store]');
  var groupable = $$('[data-groupable]');
  var facets    = $$('[data-facet]');

  if (!items.length) return;

  // the live footer deep-links /locations/?region=35|36|34
  var WP_REGION = {};
  regionBtn.forEach(function (b) {
    var wp = b.getAttribute('data-wp');
    if (wp) WP_REGION[wp] = b.getAttribute('data-region-chip');
  });

  var state = { q: '', country: 'all', region: '', city: '', type: '' };

  // labels for filters, so one arriving by URL can be named even in a layout
  // that has no control for it
  var META = { region: {}, city: {}, wp: {} };
  var metaEl = document.getElementById('ckl-meta');
  if (metaEl) { try { META = JSON.parse(metaEl.textContent) || META; } catch (err) { /* leave empty */ } }
  var slot = $('[data-active-filters]');
  // layouts without region controls still have to honour those footer links
  var REGION_WP = {};                       // region id -> the footer's numeric id
  for (var wpId in (META.wp || {})) {
    if (!WP_REGION[wpId]) WP_REGION[wpId] = META.wp[wpId];
  }
  for (var w in WP_REGION) { REGION_WP[WP_REGION[w]] = w; }

  // which region / city ids actually exist inside each country — read off the
  // stores, not off the controls, so it holds for layouts that omit a control
  var HAS = {};
  items.forEach(function (el) {
    if (!el.hasAttribute('data-primary')) return;
    var c = el.getAttribute('data-country');
    HAS[c + '|r|' + el.getAttribute('data-region')] = true;
    HAS[c + '|c|' + el.getAttribute('data-city')] = true;
    HAS[c + '|t|' + el.getAttribute('data-type')] = true;
  });

  /* ------------------------------------------------------------- filtering */

  function matches(el) {
    if (state.country !== 'all' && el.getAttribute('data-country') !== state.country) return false;
    if (state.region && el.getAttribute('data-region') !== state.region) return false;
    if (state.city && el.getAttribute('data-city') !== state.city) return false;
    if (state.type && el.getAttribute('data-type') !== state.type) return false;
    if (state.q && el.getAttribute('data-hay').indexOf(state.q) === -1) return false;
    return true;
  }

  function apply() {
    var shown = 0;

    items.forEach(function (el) {
      var ok = matches(el);
      el.hidden = !ok;
      if (ok && el.hasAttribute('data-primary')) shown++;
    });

    groupable.forEach(function (g) {
      var live = g.querySelectorAll('[data-store][data-primary]:not([hidden])').length;
      g.hidden = live === 0;
      var n = g.querySelector('[data-group-count]');
      if (n) n.textContent = String(live);
      var word = g.querySelector('[data-plural]');
      if (word) {
        var stem = word.getAttribute('data-plural');
        if (root.getAttribute('dir') === 'rtl') {
          word.textContent = live === 1 ? 'متجر' : live === 2 ? 'متجران' :
                             live >= 3 && live <= 10 ? 'متاجر' : 'متجرًا';
        } else {
          word.textContent = live === 1 ? stem : stem + 's';
        }
      }
    });

    if (shownEl) shownEl.textContent = String(shown);
    if (emptyEl) emptyEl.hidden = shown !== 0;

    // a control only makes sense when the country on screen actually has it —
    // the UAE has no fuel stations, so that chip should not be offered there
    [[regionBtn, 'data-region-chip', 'r'],
     [cityBtn,   'data-city-chip',   'c'],
     [typeBtn,   'data-type-chip',   't']].forEach(function (pair) {
      pair[0].forEach(function (b) {
        var id = b.getAttribute(pair[1]);
        b.hidden = state.country !== 'all' && !HAS[state.country + '|' + pair[2] + '|' + id];
      });
    });

    // a facet block with nothing left to offer gets out of the way
    facets.forEach(function (f) {
      f.hidden = f.querySelectorAll('button:not([hidden])').length === 0;
    });

    renderOrphanFilters();

    var dirty = !!state.q || state.country !== 'all' || !!state.region ||
                !!state.city || !!state.type;
    resetBtns.forEach(function (b) { b.hidden = !dirty; });

    if (badge) {
      var active = (state.country !== 'all' ? 1 : 0) + (state.region ? 1 : 0) +
                   (state.city ? 1 : 0) + (state.type ? 1 : 0);
      badge.hidden = active === 0;
      badge.textContent = String(active);
    }
    if (clearBtn) clearBtn.hidden = !state.q;

    syncUrl();
  }

  function syncUrl() {
    if (!window.history || !history.replaceState || !window.URLSearchParams) return;
    var p = new URLSearchParams();
    if (state.q && input) p.set('q', input.value.trim());
    if (state.country !== 'all') p.set('country', state.country);
    if (state.region) {
      // keep the footer's ?region=34|35|36 form so shared links stay compatible
      p.set('region', REGION_WP[state.region] || state.region);
    }
    if (state.city) p.set('city', state.city);
    if (state.type) p.set('type', state.type);
    if (results && results.getAttribute('data-view') === 'list') p.set('view', 'list');
    var qs = p.toString();
    history.replaceState(null, '', qs ? '?' + qs : location.pathname);
  }

  // a filter with no control of its own gets a removable pill, so the page
  // never silently shows a subset with nothing to explain it
  function renderOrphanFilters() {
    if (!slot) return;
    slot.textContent = '';
    [['region', regionBtn, 'data-region-chip', setRegion],
     ['city',   cityBtn,   'data-city-chip',   setCity],
     ['type',   typeBtn,   'data-type-chip',   setType]].forEach(function (pair) {
      var kind = pair[0], list = pair[1], attr = pair[2], clear = pair[3];
      var id = state[kind];
      if (!id) return;
      var control = byValue(list, attr, id);
      if (control && !control.hidden) return;          // the control already says it
      var pill = document.createElement('button');
      pill.type = 'button';
      pill.className = 'ckl-activefilter';
      pill.setAttribute('aria-label', ((META.ui || {}).remove_filter || 'Remove filter') + ' ' + ((META[kind] || {})[id] || id));
      pill.innerHTML = '<span></span><i aria-hidden="true">&times;</i>';
      pill.firstChild.textContent = (META[kind] || {})[id] || id;
      pill.addEventListener('click', function () { clear(''); apply(); });
      slot.appendChild(pill);
    });
  }

  function byValue(list, attr, value) {
    return list.filter(function (b) { return b.getAttribute(attr) === value; })[0];
  }

  function press(btn, on) {
    btn.classList.toggle('is-active', !!on);
    btn.setAttribute('aria-pressed', on ? 'true' : 'false');
  }

  /* ---------------------------------------------------------------- search */

  if (input) {
    var debounce;
    input.addEventListener('input', function () {
      clearTimeout(debounce);
      debounce = setTimeout(function () {
        state.q = input.value.trim().toLowerCase();
        apply();
      }, 110);
    });

    input.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && input.value) { e.preventDefault(); clearSearch(); }
    });

    // "/" jumps to the search box, the way a directory should behave
    document.addEventListener('keydown', function (e) {
      if (e.key !== '/' || e.metaKey || e.ctrlKey || e.altKey) return;
      var t = e.target;
      if (t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.isContentEditable)) return;
      e.preventDefault();
      input.focus();
      input.select();
    });
  }

  function clearSearch() {
    if (!input) return;
    input.value = '';
    state.q = '';
    apply();
    input.focus();
  }
  if (clearBtn) clearBtn.addEventListener('click', clearSearch);

  /* --------------------------------------------------------------- filters */

  function setCountry(id) {
    state.country = id;
    tabs.forEach(function (t) { press(t, t.getAttribute('data-country-tab') === id); });
    // a region/city belonging to the country we just left no longer applies
    if (id !== 'all') {
      if (state.region && !HAS[id + '|r|' + state.region]) setRegion('');
      if (state.city && !HAS[id + '|c|' + state.city]) setCity('');
      if (state.type && !HAS[id + '|t|' + state.type]) setType('');
    }
  }

  function setRegion(id) {
    state.region = id;
    regionBtn.forEach(function (b) { press(b, id && b.getAttribute('data-region-chip') === id); });
    if (id) setCity('');            // region and city are two ways of saying where
  }

  function setType(id) {
    state.type = id;
    typeBtn.forEach(function (b) { press(b, id && b.getAttribute('data-type-chip') === id); });
  }

  function setCity(id) {
    state.city = id;
    cityBtn.forEach(function (b) { press(b, id && b.getAttribute('data-city-chip') === id); });
    if (id) {
      state.region = '';
      regionBtn.forEach(function (b) { press(b, false); });
    }
  }

  tabs.forEach(function (t) {
    t.addEventListener('click', function () {
      setCountry(t.getAttribute('data-country-tab'));
      apply();
    });
  });

  regionBtn.forEach(function (b) {
    b.addEventListener('click', function () {
      var id = b.getAttribute('data-region-chip');
      setRegion(state.region === id ? '' : id);   // a second click clears it
      apply();
    });
  });

  cityBtn.forEach(function (b) {
    b.addEventListener('click', function () {
      var id = b.getAttribute('data-city-chip');
      setCity(state.city === id ? '' : id);
      apply();
    });
  });

  typeBtn.forEach(function (b) {
    b.addEventListener('click', function () {
      var id = b.getAttribute('data-type-chip');
      setType(state.type === id ? '' : id);
      apply();
    });
  });

  function resetAll() {
    if (input) input.value = '';
    state.q = '';
    setRegion('');
    setCity('');
    setType('');
    setCountry('all');
    apply();
  }
  resetBtns.forEach(function (b) { b.addEventListener('click', resetAll); });
  if (emptyRst) emptyRst.addEventListener('click', function () {
    resetAll();
    if (input) input.focus();
  });

  /* ------------------------------------------- collapsible filter drawer -- */
  // the facet rail is a lot to scroll past on a phone, so it folds up there and
  // stays open on desktop, where there is room for it
  var drawer = $('[data-desktop-open]');
  var badge  = $('[data-filter-badge]');
  if (drawer && window.matchMedia) {
    var wide = window.matchMedia('(min-width: 940px)');
    var syncDrawer = function () { if (wide.matches) drawer.open = true; };
    syncDrawer();
    if (wide.addEventListener) wide.addEventListener('change', syncDrawer);
    else if (wide.addListener) wide.addListener(syncDrawer);
    // choosing a filter on a phone closes the drawer so the results are visible
    drawer.addEventListener('click', function (e) {
      if (wide.matches) return;
      var b = e.target.closest &&
              e.target.closest('[data-country-tab],[data-region-chip],[data-city-chip],[data-type-chip]');
      if (b) drawer.open = false;
    });
  }

  /* ------------------------------------------------------------------ view */

  viewBtns.forEach(function (b) {
    b.addEventListener('click', function () {
      var v = b.getAttribute('data-view-btn');
      if (results) results.setAttribute('data-view', v);
      viewBtns.forEach(function (o) { press(o, o === b); });
      try { localStorage.setItem('ckl-view', v); } catch (err) { /* private mode */ }
    });
  });

  /* ----------------------------------------------------- sticky compaction */

  if (finder && sentinel && 'IntersectionObserver' in window) {
    new IntersectionObserver(function (entries) {
      finder.classList.toggle('is-stuck', !entries[0].isIntersecting);
    }, { rootMargin: '-120px 0px 0px 0px', threshold: 0 }).observe(sentinel);
  }

  // does any store on the page carry this region / city id at all?
  function knownFilter(kind, id) {
    var suffix = '|' + kind + '|' + id;
    for (var key in HAS) {
      if (key.length > suffix.length && key.slice(-suffix.length) === suffix) return true;
    }
    return false;
  }

  /* --------------------------------------------------------- initial state */

  (function boot() {
    var p = window.URLSearchParams ? new URLSearchParams(location.search) : null;

    if (p) {
      var q = p.get('q');
      if (q && input) { input.value = q; state.q = q.trim().toLowerCase(); }

      var c = p.get('country');
      if (c && byValue(tabs, 'data-country-tab', c)) setCountry(c);

      var r = p.get('region');
      if (r) {
        var id = WP_REGION[r] || r;
        if (knownFilter('r', id)) setRegion(id);
      }

      var city = p.get('city');
      if (city && knownFilter('c', city)) setCity(city);

      var type = p.get('type');
      if (type && knownFilter('t', type)) setType(type);
    }

    // keep the country control in step with state even when no param was given
    tabs.forEach(function (t) { press(t, t.getAttribute('data-country-tab') === state.country); });

    // ?view=list is shareable and survives a reload; otherwise use the last choice
    var wanted = (p && p.get('view')) || null;
    if (!wanted) {
      try { wanted = localStorage.getItem('ckl-view'); } catch (err) { wanted = null; }
    }
    if (wanted) {
      var vb = byValue(viewBtns, 'data-view-btn', wanted);
      if (vb && !vb.classList.contains('is-active')) vb.click();
    }

    apply();
  })();

  /* QA handle */
  window.__ckl = {
    state:   function () { return JSON.parse(JSON.stringify(state)); },
    shown:   function () { return shownEl ? parseInt(shownEl.textContent, 10) : -1; },
    total:   root.querySelectorAll('[data-store][data-primary]').length,
    search:  function (v) { if (input) input.value = v; state.q = String(v).trim().toLowerCase(); apply(); },
    country: function (v) { setCountry(v); apply(); },
    region:  function (v) { setRegion(v); apply(); },
    city:    function (v) { setCity(v); apply(); },
    type:    function (v) { setType(v); apply(); },
    reset:   resetAll
  };
})();
