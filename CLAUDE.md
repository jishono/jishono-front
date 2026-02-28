# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
npm run dev      # Start dev server (Vite, default port 5173)
npm run build    # Production build to dist/
npm run preview  # Preview production build
```

No test suite is configured.

## Environment

- `.env` — dev: `VITE_NODE_HOST=http://localhost:3001/`
- `.env.production` — prod: `VITE_NODE_HOST=https://api.jisho.no`

The backend API must be running locally on port 3001 for development.

## Architecture

This is a Vue 3 SPA for [jisho.no](https://www.jisho.no), a Norwegian–Japanese online dictionary. Built with Vite, Vue Router, vue-i18n, and Axios.

**Key design: client-side search.** On startup, `Search.vue` fetches the entire word list from `GET /items/all` and stores it in memory. All filtering and sorting happens client-side by matching `item.oppslag` against the query string. Example sentences (`GET /example_sentences/:id`) and conjugations (`POST /conjugations/:id`) are fetched lazily on demand.

**Data model fields used in templates:**
- `oppslag` — Norwegian headword
- `lemma_id` — unique word ID
- `definisjoner` — array of definitions (each with `def_id`, `definisjon`, `prioritet`, `wiki`)
- `uttale` — pronunciation array (`transkripsjon`)
- `boy_tabell` — part-of-speech code (`subst`, `verb`, `adj`, `adv`, `det`, `pron`, etc.)
- `pos` — gender/paradigm array (used to derive `en`/`ei`/`et` prefix for nouns)
- `relatert` — array of related word strings
- `example_sentences` — lazily loaded array of `{no_setning, ja_setning}`

**Routing:** All routes render `Search.vue`. The `/about` path sets `showAbout = true` which injects the `About` component inline. Catch-all routes also go to `Search`.

**i18n:** Two locales — `ja` (Japanese, default) and `no` (Norwegian). Locale is persisted in `localStorage`. Translation keys are in `src/locales/ja.json` and `src/locales/no.json`.

**jQuery/Bootstrap dependency:** Bootstrap 4.5 + jQuery 3.5 are loaded via CDN in `index.html`. The collapse behavior for example sentences and conjugations uses `window.$(str).collapse('toggle')` (Bootstrap jQuery plugin), accessed as `window.$` to avoid conflicts with Vue's module system.

**Furigana rendering:** `ResultBox.vue` parses a custom bracket notation (e.g. `漢字[かんじ]`) into HTML `<ruby>` tags via the `addFurigana` method, rendered with `v-html`.

**Components:**
- `Header.vue` — logo linking to `/search/`
- `Footer.vue` — donation progress bar (fetched from Google Sheets API), locale switcher, VippsDialog trigger
- `ResultBox.vue` — per-word card with lazy example sentences, conjugations, feedback button
- `ConjugationBox.vue` — conjugation table rendered from paradigm data; splits columns for mobile vs desktop
- `FeedbackDialog.vue` — modal overlay for submitting word feedback (`POST /words/:id/feedback`)
- `VippsDialog.vue` — donation modal
- `About.vue` — static about page content (all text via i18n)

**API module:** `src/api.js` exports a single Axios instance with `VITE_NODE_HOST` as `baseURL`.
