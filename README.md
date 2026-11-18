# FTEE-PEE NIG LTD

Corporate website and admin for FTEE-PEE NIG LTD, Aguda, Surulere, Lagos. It is plain PHP for cPanel shared hosting: no Node, Composer, Docker, or build step.

The public site covers design and printing, cleaning, wheel balancing and alignment, and car wash and detailing. The admin edits prices, copy, branches, gallery, FAQs, blog posts, pages, and enquiries.

## Requirements

- PHP 8.0 or newer. The code avoids PHP 8.1-only syntax so a host still on 8.0 can run it.
- MySQL 5.7+ or MariaDB 10.3+, with the `pdo_mysql` extension.
- Apache with `mod_rewrite` and `.htaccess` allowed (`AllowOverride`).
- GD is optional. Uploads work without it; images are resized only when GD is present.
- `mail()` is optional and often unreliable. The site does not depend on it.

## The one place that sets the folder

On the install form, **Folder path** is the only path you set.

- Leave it empty when the site is the domain root (`https://example.com/`).
- Use `/fteepee` (no trailing slash) when the site lives in a subdirectory.

The installer writes that value in both places Apache and PHP need:

- `config/config.php` → `base_url` (links, redirects, and the session cookie path)
- `.htaccess` → `RewriteBase`

If you move the site later, change those two lines so they still match. Do not put the domain name in `base_url`. The public domain (`https://example.com`, with no folder) is **Admin → Settings → Site URL**.

## Install on cPanel

1. In cPanel, create a MySQL database and a user. Add the user to the database with all privileges. Note the host (often `localhost`), database name, user, and password. cPanel usually prefixes both names with your account name.
2. Upload this project into `public_html`, or into a subdirectory such as `public_html/fteepee`. Upload the project root itself. There is no separate `public` folder to point at.
3. During install, `config/` and `.htaccess` must be writable, and `uploads/` must be writable (`755` or `775`). You can remove write permission from `config/` after install. Leave `uploads/` writable.
4. Visit `https://your-domain/install`, or `https://your-domain/fteepee/install` if you used a folder.
5. Enter the database details, the folder path, the site URL, and the first admin. The installer creates the tables, seeds editable content, writes the config, and then locks itself.
6. Sign in at `/admin`. Before you announce the site, add the phone, email, WhatsApp link, and opening hours. None of those were supplied, so the public site hides them until you do. Upload the logo when you have it. The wordmark is used until then.
7. Replace or unpublish sample prices. They are labelled **Sample** on the service pages.
8. Delete `install.php` if you want it gone. Leaving it is safe: it refuses to run again while `config/installed.lock` exists.

If the host returns a 500 error as soon as the files are uploaded, comment out the `Options -Indexes -MultiViews` line in `.htaccess`. Some hosts disallow `Options` in per-directory files.

## Enquiries and email

Every quote is stored in the database first. **Admin → Settings** can turn on email and set a recipient. `mail()` runs only then. If the function is missing, or the host rejects the message, the enquiry is still saved and the admin screen says the email failed. Do not treat email as the only inbox. This is normal on shared hosting.

## What the starter content is

Read [ASSUMPTIONS.md](ASSUMPTIONS.md). In short: package figures are sample Lagos market ranges, not a FTEE-PEE tariff. The gallery ships with drawings labelled placeholder, not client photographs. A sample testimonial exists in the database and is hidden on the public site. No client counts, awards, or certifications are invented.

## Day to day

Admins change services, packages, branches, gallery, FAQs, blog posts, pages (about, privacy, terms), testimonials, users, and enquiries. Homepage text and search titles are under Settings. Enquiry status is new, read, or closed. Opening a new enquiry marks it read.

## Local preview

Apache on cPanel uses `.htaccess`. For a quick local look, from this directory:

```bash
php -S 127.0.0.1:8080 dev-router.php
```

Then open `/install`. `dev-router.php` is blocked from the web by `.htaccess`.

## Layout

`index.php` is the front controller. Application code is in `app/`, templates in `templates/`, and the schema in `database/schema.sql`. Direct HTTP access to `app/`, `config/`, `database/`, and `templates/` is denied. Uploaded images go in `uploads/` under random names; PHP files are rejected and blocked by `uploads/.htaccess`.
