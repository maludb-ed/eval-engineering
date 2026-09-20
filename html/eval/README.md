# Eval Engineering — website

The study site for the **Evaluation, Testing & Optimisation** domain of the
Claude Certified Architect: Professional (CCAR-P) exam.

Built with plain **PHP + Bootstrap 5.3** (no build step, no framework), so it
drops straight onto an Apache server.

## Layout

```
eval/
├── index.php            # home page (this domain's landing page)
├── includes/
│   ├── config.php       # course metadata + all modules/topics/scenarios (edit here)
│   ├── header.php       # <head> + top navbar (set $page_title before including)
│   └── footer.php       # footer + scripts
├── assets/
│   ├── style.css        # theme layered on Bootstrap
│   └── app.js           # light/dark theme toggle
└── README.md
```

Content is defined once in `includes/config.php`; `index.php` renders from it.
To add or rename a module or topic, edit the `$MODULES` array — nothing else.

## Deploy to Apache (Ubuntu)

Copy this folder to the web root so it is served at `/eval`:

```bash
sudo cp -r eval /var/www/html/
sudo chown -R www-data:www-data /var/www/html/eval
```

Requires `php` + `libapache2-mod-php` (or php-fpm). Bootstrap and Bootstrap
Icons load from a CDN, so the server needs outbound HTTPS; to run fully
offline, download those two files into `assets/` and update the links in
`includes/header.php` / `includes/footer.php`.

Home page: `http://<host>/eval/`

## Preview locally

```bash
php -S 127.0.0.1:8747 -t html
```

Then open <http://127.0.0.1:8747/eval/> (run from the repo root, where `html/` lives).
