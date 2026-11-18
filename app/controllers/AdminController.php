<?php
// Admin sign-in and editing for settings, services, pages, and enquiries.

class AdminController
{
    public static function loginForm()
    {
        if (Auth::check()) {
            redirect('/admin');
        }
        render('admin/login', ['error' => null, 'email' => ''], 'admin/guest');
    }

    public static function login()
    {
        require_csrf();
        if (Auth::check()) {
            redirect('/admin');
        }
        $email = clean_line(isset($_POST['email']) ? $_POST['email'] : '', 190);
        $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
        $result = Auth::attempt($email, $password);
        if ($result === 'ok') {
            redirect('/admin');
        }
        $error = $result === 'throttled'
            ? 'Too many sign-in attempts. Wait 15 minutes and try again.'
            : 'Those details do not match.';
        render('admin/login', ['error' => $error, 'email' => $email], 'admin/guest');
    }

    public static function logout()
    {
        require_csrf();
        Auth::logout();
        redirect('/admin/login');
    }

    public static function dashboard()
    {
        require_admin();
        $office = head_office();
        $checks = [];
        if (setting('phone') === '') {
            $checks[] = 'Add a company phone under Settings.';
        }
        if (setting('email') === '') {
            $checks[] = 'Add a public email under Settings.';
        }
        if (setting('whatsapp') === '') {
            $checks[] = 'Add a WhatsApp number or link under Settings.';
        }
        if (setting('logo_path') === '') {
            $checks[] = 'Upload the company logo under Settings. The wordmark is used until then.';
        }
        if (!$office || trim((string) $office['hours_text']) === '') {
            $checks[] = 'Add opening hours under Settings. None are shown until you do.';
        }
        if (setting('site_url') === '') {
            $checks[] = 'Set the Site URL under Settings so canonical links stay on your domain.';
        }
        $samplePrices = (int) db()->value('SELECT COUNT(*) FROM packages WHERE is_sample = 1 AND is_published = 1');
        if ($samplePrices > 0) {
            $checks[] = $samplePrices . ' published package prices are still marked Sample. Replace them with your own figures, or unpublish them.';
        }
        $placeholders = (int) db()->value('SELECT COUNT(*) FROM gallery_items WHERE is_placeholder = 1 AND is_published = 1');
        if ($placeholders > 0) {
            $checks[] = $placeholders . ' gallery visuals are layout placeholders. Unpublish them when you add real photos.';
        }
        $sampleQuotes = (int) db()->value('SELECT COUNT(*) FROM testimonials WHERE is_sample = 1 AND is_published = 1');
        if ($sampleQuotes > 0) {
            $checks[] = 'A sample testimonial is marked published. Sample quotes stay hidden on the site until Sample is unchecked.';
        }
        render('admin/dashboard', [
            'title' => 'Dashboard',
            'nav' => 'dashboard',
            'newCount' => (int) db()->value('SELECT COUNT(*) FROM enquiries WHERE status = ?', ['new']),
            'totalCount' => (int) db()->value('SELECT COUNT(*) FROM enquiries'),
            'recent' => db()->all('SELECT id, name, phone, service_name, status, created_at FROM enquiries ORDER BY created_at DESC LIMIT 8'),
            'checks' => $checks,
            'mailOn' => setting('mail_enabled') === '1',
        ], 'admin/layout');
    }

    public static function settingsForm()
    {
        require_admin();
        self::renderSettings([], null);
    }

    public static function settingsSave()
    {
        require_admin();
        require_csrf();
        $errors = [];
        $clean = [];
        foreach (self::settingGroups() as $fields) {
            foreach ($fields as $field) {
                $key = $field['key'];
                $max = isset($field['max']) ? (int) $field['max'] : 500;
                $raw = isset($_POST[$key]) ? $_POST[$key] : '';
                $value = ($field['type'] === 'textarea') ? clean_text($raw, $max) : clean_line($raw, $max);
                if (!empty($field['required']) && $value === '') {
                    $errors[$key] = $field['label'] . ' is required.';
                }
                if (($key === 'phone' || $key === 'phone_alt') && $value !== '' && !is_nigerian_phone($value)) {
                    $errors[$key] = 'Use a Nigerian phone number, or leave it blank.';
                }
                if (($key === 'phone' || $key === 'phone_alt') && $value !== '' && is_nigerian_phone($value)) {
                    $value = format_nigerian_phone($value);
                }
                if (($key === 'email' || $key === 'mail_recipient' || $key === 'mail_from') && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$key] = 'Enter a valid email, or leave it blank.';
                }
                if (in_array($key, ['facebook', 'instagram', 'x_url', 'linkedin'], true) && !valid_optional_url($value)) {
                    $errors[$key] = 'Enter a full https link, or leave it blank.';
                }
                if ($key === 'site_url' && $value !== '' && !valid_origin($value)) {
                    $errors[$key] = 'Use only the origin, such as https://example.com, with no folder path.';
                }
                $clean[$key] = $value;
            }
        }

        $officeInput = self::officeInput();
        if ($officeInput['phone'] !== '' && !is_nigerian_phone($officeInput['phone'])) {
            $errors['ho_phone'] = 'Use a Nigerian number for the head office phone, or leave it blank.';
        } elseif ($officeInput['phone'] !== '') {
            $officeInput['phone'] = format_nigerian_phone($officeInput['phone']);
        }
        if ($officeInput['email'] !== '' && !filter_var($officeInput['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['ho_email'] = 'Enter a valid head office email, or leave it blank.';
        }
        if ($officeInput['name'] === '') {
            $officeInput['name'] = 'Head office';
        }

        $upload = null;
        if (isset($_FILES['logo']) && (int) $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = Uploader::store($_FILES['logo'], 'logo');
            if (!$upload['ok']) {
                $errors['logo'] = $upload['error'];
            }
        }

        if ($errors) {
            if ($upload && !empty($upload['ok'])) {
                Uploader::delete($upload['path']);
            }
            self::renderSettings($errors, $_POST);
            return;
        }

        foreach ($clean as $key => $value) {
            self::saveSetting($key, $value);
        }
        self::saveSetting('mail_enabled', isset($_POST['mail_enabled']) ? '1' : '0');

        $logoPath = setting('logo_path');
        if (!empty($_POST['remove_logo']) && $upload === null) {
            Uploader::delete($logoPath);
            $logoPath = '';
        }
        if ($upload && !empty($upload['ok'])) {
            Uploader::delete($logoPath);
            $logoPath = $upload['path'];
        }
        self::saveSetting('logo_path', $logoPath);

        $office = db()->one('SELECT id FROM branches WHERE is_head_office = 1 ORDER BY id ASC LIMIT 1');
        if ($office) {
            db()->run(
                'UPDATE branches SET name = ?, address_line = ?, area = ?, city = ?, state = ?, postal_code = ?, country = ?, plus_code = ?, phone = ?, email = ?, hours_text = ?, notes = ? WHERE id = ?',
                [
                    $officeInput['name'], $officeInput['address_line'], $officeInput['area'], $officeInput['city'],
                    $officeInput['state'], $officeInput['postal_code'], $officeInput['country'], $officeInput['plus_code'],
                    $officeInput['phone'], $officeInput['email'], $officeInput['hours_text'], $officeInput['notes'],
                    (int) $office['id'],
                ]
            );
        } else {
            db()->insert(
                'INSERT INTO branches (name, is_head_office, address_line, area, city, state, postal_code, country, plus_code, phone, email, hours_text, notes, sort_order, is_published) VALUES (?, 1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1)',
                [
                    $officeInput['name'], $officeInput['address_line'], $officeInput['area'], $officeInput['city'],
                    $officeInput['state'], $officeInput['postal_code'], $officeInput['country'], $officeInput['plus_code'],
                    $officeInput['phone'], $officeInput['email'], $officeInput['hours_text'], $officeInput['notes'],
                ]
            );
        }
        forget_settings();
        forget_head_office();
        flash('success', 'Settings saved. The head office branch was updated from this form.');
        redirect('/admin/settings');
    }

    public static function services()
    {
        require_admin();
        render('admin/services', [
            'title' => 'Services',
            'nav' => 'services',
            'rows' => db()->all('SELECT id, name, slug, sort_order, is_published FROM services ORDER BY sort_order ASC, id ASC'),
        ], 'admin/layout');
    }

    public static function serviceForm(array $params)
    {
        require_admin();
        self::renderServiceForm(self::id($params), [], null);
    }

    public static function serviceSave(array $params)
    {
        require_admin();
        require_csrf();
        $id = self::id($params);
        $existing = self::mustRow('services', $id);
        $errors = [];
        $name = clean_line(isset($_POST['name']) ? $_POST['name'] : '', 160);
        $summary = clean_line(isset($_POST['summary']) ? $_POST['summary'] : '', 400);
        $body = clean_text(isset($_POST['body']) ? $_POST['body'] : '', 20000);
        $process = clean_text(isset($_POST['process']) ? $_POST['process'] : '', 8000);
        $seoTitle = clean_line(isset($_POST['seo_title']) ? $_POST['seo_title'] : '', 180);
        $seoDescription = clean_line(isset($_POST['seo_description']) ? $_POST['seo_description'] : '', 320);
        $icon = isset($_POST['icon_key']) ? (string) $_POST['icon_key'] : 'print';
        if (!isset(icon_keys()[$icon])) {
            $icon = 'print';
        }
        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }
        if ($summary === '') {
            $errors['summary'] = 'Summary is required.';
        }
        if ($errors) {
            self::renderServiceForm($id, $errors, $_POST);
            return;
        }
        $slug = unique_slug('services', $name, $id);
        $sort = self::sortValue();
        $published = self::flag('is_published');
        if ($existing) {
            db()->run(
                'UPDATE services SET name = ?, slug = ?, summary = ?, body = ?, process = ?, icon_key = ?, sort_order = ?, is_published = ?, seo_title = ?, seo_description = ? WHERE id = ?',
                [$name, $slug, $summary, $body, $process, $icon, $sort, $published, $seoTitle, $seoDescription, $id]
            );
        } else {
            db()->insert(
                'INSERT INTO services (name, slug, summary, body, process, icon_key, sort_order, is_published, seo_title, seo_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$name, $slug, $summary, $body, $process, $icon, $sort, $published, $seoTitle, $seoDescription]
            );
        }
        unset($GLOBALS['ftee_services']);
        flash('success', 'Service saved.');
        redirect('/admin/services');
    }

    public static function serviceDelete(array $params)
    {
        self::simpleDelete('services', $params, '/admin/services', 'Service deleted. Its packages were removed with it.');
        unset($GLOBALS['ftee_services']);
    }

    public static function packages()
    {
        require_admin();
        $serviceId = isset($_GET['service']) ? (int) $_GET['service'] : 0;
        if ($serviceId > 0) {
            $rows = db()->all(
                'SELECT p.*, s.name AS service_name FROM packages p INNER JOIN services s ON s.id = p.service_id WHERE p.service_id = ? ORDER BY p.sort_order ASC, p.id ASC',
                [$serviceId]
            );
        } else {
            $rows = db()->all(
                'SELECT p.*, s.name AS service_name FROM packages p INNER JOIN services s ON s.id = p.service_id ORDER BY s.sort_order ASC, p.sort_order ASC, p.id ASC'
            );
        }
        render('admin/packages', [
            'title' => 'Packages',
            'nav' => 'packages',
            'rows' => $rows,
            'services' => db()->all('SELECT id, name FROM services ORDER BY sort_order ASC, id ASC'),
            'serviceId' => $serviceId,
        ], 'admin/layout');
    }

    public static function packageForm(array $params)
    {
        require_admin();
        self::renderPackageForm(self::id($params), [], null);
    }

    public static function packageSave(array $params)
    {
        require_admin();
        require_csrf();
        $id = self::id($params);
        self::mustRow('packages', $id);
        $errors = [];
        $serviceId = isset($_POST['service_id']) ? (int) $_POST['service_id'] : 0;
        $service = db()->one('SELECT id FROM services WHERE id = ?', [$serviceId]);
        if (!$service) {
            $errors['service_id'] = 'Choose a service.';
        }
        $name = clean_line(isset($_POST['name']) ? $_POST['name'] : '', 160);
        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }
        $amountRaw = trim(isset($_POST['price_amount']) ? (string) $_POST['price_amount'] : '');
        $amount = null;
        if ($amountRaw !== '') {
            if (!preg_match('/^\d{1,8}$/', $amountRaw)) {
                $errors['price_amount'] = 'Enter the naira amount as digits, or leave it blank.';
            } else {
                $amount = (int) $amountRaw;
            }
        }
        if ($errors) {
            self::renderPackageForm($id, $errors, $_POST);
            return;
        }
        $data = [
            $serviceId,
            $name,
            clean_line(isset($_POST['summary']) ? $_POST['summary'] : '', 400),
            clean_line(isset($_POST['price_label']) ? $_POST['price_label'] : '', 160),
            $amount,
            clean_line(isset($_POST['price_note']) ? $_POST['price_note'] : '', 400),
            clean_text(isset($_POST['features']) ? $_POST['features'] : '', 5000),
            self::sortValue(),
            self::flag('is_sample'),
            self::flag('is_published'),
        ];
        if ($id) {
            $data[] = $id;
            db()->run(
                'UPDATE packages SET service_id = ?, name = ?, summary = ?, price_label = ?, price_amount = ?, price_note = ?, features = ?, sort_order = ?, is_sample = ?, is_published = ? WHERE id = ?',
                $data
            );
        } else {
            db()->insert(
                'INSERT INTO packages (service_id, name, summary, price_label, price_amount, price_note, features, sort_order, is_sample, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                $data
            );
        }
        flash('success', 'Package saved.');
        redirect('/admin/packages');
    }

    public static function packageDelete(array $params)
    {
        self::simpleDelete('packages', $params, '/admin/packages', 'Package deleted.');
    }

    public static function branches()
    {
        require_admin();
        render('admin/branches', [
            'title' => 'Branches',
            'nav' => 'branches',
            'rows' => db()->all('SELECT id, name, area, city, is_head_office, is_published, sort_order FROM branches ORDER BY is_head_office DESC, sort_order ASC, id ASC'),
        ], 'admin/layout');
    }

    public static function branchForm(array $params)
    {
        require_admin();
        self::renderBranchForm(self::id($params), [], null);
    }

    public static function branchSave(array $params)
    {
        require_admin();
        require_csrf();
        $id = self::id($params);
        self::mustRow('branches', $id);
        $errors = [];
        $name = clean_line(isset($_POST['name']) ? $_POST['name'] : '', 160);
        $area = clean_line(isset($_POST['area']) ? $_POST['area'] : '', 120);
        $address = clean_line(isset($_POST['address_line']) ? $_POST['address_line'] : '', 255);
        $plus = clean_line(isset($_POST['plus_code']) ? $_POST['plus_code'] : '', 40);
        $published = self::flag('is_published');
        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }
        if ($published && $area === '' && $address === '' && $plus === '') {
            $errors['area'] = 'Add an area, address, or Plus Code before publishing.';
        }
        $phone = clean_line(isset($_POST['phone']) ? $_POST['phone'] : '', 40);
        if ($phone !== '' && !is_nigerian_phone($phone)) {
            $errors['phone'] = 'Use a Nigerian phone number, or leave it blank.';
        } elseif ($phone !== '') {
            $phone = format_nigerian_phone($phone);
        }
        $email = strtolower(clean_line(isset($_POST['email']) ? $_POST['email'] : '', 190));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email, or leave it blank.';
        }
        $map = clean_line(isset($_POST['map_url']) ? $_POST['map_url'] : '', 500);
        if (!valid_optional_url($map)) {
            $errors['map_url'] = 'Enter a full https map link, or leave it blank to build one from the Plus Code.';
        }
        if ($errors) {
            self::renderBranchForm($id, $errors, $_POST);
            return;
        }
        $isHead = self::flag('is_head_office');
        if ($isHead) {
            db()->run('UPDATE branches SET is_head_office = 0');
        }
        $data = [
            $name, $isHead, $address, $area,
            clean_line(isset($_POST['city']) ? $_POST['city'] : '', 120),
            clean_line(isset($_POST['state']) ? $_POST['state'] : '', 120),
            clean_line(isset($_POST['postal_code']) ? $_POST['postal_code'] : '', 20),
            clean_line(isset($_POST['country']) ? $_POST['country'] : 'Nigeria', 80) ?: 'Nigeria',
            $plus, $phone, $email,
            clean_line(isset($_POST['hours_text']) ? $_POST['hours_text'] : '', 255),
            $map,
            clean_text(isset($_POST['notes']) ? $_POST['notes'] : '', 4000),
            self::sortValue(),
            $published,
        ];
        if ($id) {
            $data[] = $id;
            db()->run(
                'UPDATE branches SET name = ?, is_head_office = ?, address_line = ?, area = ?, city = ?, state = ?, postal_code = ?, country = ?, plus_code = ?, phone = ?, email = ?, hours_text = ?, map_url = ?, notes = ?, sort_order = ?, is_published = ? WHERE id = ?',
                $data
            );
        } else {
            db()->insert(
                'INSERT INTO branches (name, is_head_office, address_line, area, city, state, postal_code, country, plus_code, phone, email, hours_text, map_url, notes, sort_order, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                $data
            );
        }
        forget_head_office();
        flash('success', 'Branch saved.');
        redirect('/admin/branches');
    }

    public static function branchDelete(array $params)
    {
        self::simpleDelete('branches', $params, '/admin/branches', 'Branch deleted.');
        forget_head_office();
    }

    public static function gallery()
    {
        require_admin();
        render('admin/gallery', [
            'title' => 'Gallery',
            'nav' => 'gallery',
            'rows' => db()->all('SELECT id, title, is_placeholder, is_published, sort_order, image_path FROM gallery_items ORDER BY sort_order ASC, id ASC'),
        ], 'admin/layout');
    }

    public static function galleryForm(array $params)
    {
        require_admin();
        self::renderGalleryForm(self::id($params), [], null);
    }

    public static function gallerySave(array $params)
    {
        require_admin();
        require_csrf();
        $id = self::id($params);
        $existing = self::mustRow('gallery_items', $id);
        $errors = [];
        $title = clean_line(isset($_POST['title']) ? $_POST['title'] : '', 160);
        if ($title === '') {
            $errors['title'] = 'Title is required.';
        }
        $upload = null;
        if (isset($_FILES['image']) && (int) $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = Uploader::store($_FILES['image'], 'gallery');
            if (!$upload['ok']) {
                $errors['image'] = $upload['error'];
            }
        } elseif (!$existing) {
            $errors['image'] = 'Choose an image to upload.';
        }
        if ($errors) {
            if ($upload && !empty($upload['ok'])) {
                Uploader::delete($upload['path']);
            }
            self::renderGalleryForm($id, $errors, $_POST);
            return;
        }
        $alt = clean_line(isset($_POST['alt_text']) ? $_POST['alt_text'] : '', 255);
        if ($alt === '') {
            $alt = $title;
        }
        $caption = clean_line(isset($_POST['caption']) ? $_POST['caption'] : '', 400);
        $sort = self::sortValue();
        $published = self::flag('is_published');
        $placeholder = self::flag('is_placeholder');
        $path = $existing ? $existing['image_path'] : '';
        $width = $existing ? (int) $existing['image_width'] : 1200;
        $height = $existing ? (int) $existing['image_height'] : 800;
        if ($upload && !empty($upload['ok'])) {
            if ($existing) {
                Uploader::delete($existing['image_path']);
            }
            $path = $upload['path'];
            $width = (int) $upload['width'];
            $height = (int) $upload['height'];
            $placeholder = 0;
        }
        if ($existing) {
            db()->run(
                'UPDATE gallery_items SET title = ?, caption = ?, image_path = ?, alt_text = ?, image_width = ?, image_height = ?, is_placeholder = ?, sort_order = ?, is_published = ? WHERE id = ?',
                [$title, $caption, $path, $alt, $width, $height, $placeholder, $sort, $published, $id]
            );
        } else {
            db()->insert(
                'INSERT INTO gallery_items (title, caption, image_path, alt_text, image_width, image_height, is_placeholder, sort_order, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$title, $caption, $path, $alt, $width, $height, $placeholder, $sort, $published]
            );
        }
        flash('success', 'Gallery item saved.');
        redirect('/admin/gallery');
    }

    public static function galleryDelete(array $params)
    {
        require_admin();
        require_csrf();
        $row = self::mustRow('gallery_items', self::id($params));
        if ($row) {
            Uploader::delete($row['image_path']);
            db()->run('DELETE FROM gallery_items WHERE id = ?', [(int) $row['id']]);
            flash('success', 'Gallery item deleted.');
        }
        redirect('/admin/gallery');
    }

    public static function faqs()
    {
        require_admin();
        render('admin/faqs', [
            'title' => 'FAQs',
            'nav' => 'faqs',
            'rows' => db()->all(
                'SELECT f.id, f.question, f.show_on_home, f.is_published, f.sort_order, s.name AS service_name
                 FROM faqs f LEFT JOIN services s ON s.id = f.service_id
                 ORDER BY f.sort_order ASC, f.id ASC'
            ),
        ], 'admin/layout');
    }

    public static function faqForm(array $params)
    {
        require_admin();
        self::renderFaqForm(self::id($params), [], null);
    }

    public static function faqSave(array $params)
    {
        require_admin();
        require_csrf();
        $id = self::id($params);
        self::mustRow('faqs', $id);
        $errors = [];
        $question = clean_line(isset($_POST['question']) ? $_POST['question'] : '', 255);
        $answer = clean_text(isset($_POST['answer']) ? $_POST['answer'] : '', 5000);
        if ($question === '') {
            $errors['question'] = 'Question is required.';
        }
        if ($answer === '') {
            $errors['answer'] = 'Answer is required.';
        }
        $serviceId = isset($_POST['service_id']) ? (int) $_POST['service_id'] : 0;
        if ($serviceId > 0 && !db()->one('SELECT id FROM services WHERE id = ?', [$serviceId])) {
            $errors['service_id'] = 'Choose a valid service, or leave it as general.';
        }
        if ($errors) {
            self::renderFaqForm($id, $errors, $_POST);
            return;
        }
        $serviceValue = $serviceId > 0 ? $serviceId : null;
        $data = [$question, $answer, $serviceValue, self::flag('show_on_home'), self::sortValue(), self::flag('is_published')];
        if ($id) {
            $data[] = $id;
            db()->run(
                'UPDATE faqs SET question = ?, answer = ?, service_id = ?, show_on_home = ?, sort_order = ?, is_published = ? WHERE id = ?',
                $data
            );
        } else {
            db()->insert(
                'INSERT INTO faqs (question, answer, service_id, show_on_home, sort_order, is_published) VALUES (?, ?, ?, ?, ?, ?)',
                $data
            );
        }
        flash('success', 'FAQ saved.');
        redirect('/admin/faqs');
    }

    public static function faqDelete(array $params)
    {
        self::simpleDelete('faqs', $params, '/admin/faqs', 'FAQ deleted.');
    }

    public static function posts()
    {
        require_admin();
        render('admin/posts', [
            'title' => 'Blog',
            'nav' => 'posts',
            'rows' => db()->all('SELECT id, title, slug, is_published, published_at FROM posts ORDER BY published_at DESC, id DESC'),
        ], 'admin/layout');
    }

    public static function postForm(array $params)
    {
        require_admin();
        self::renderPostForm(self::id($params), [], null);
    }

    public static function postSave(array $params)
    {
        require_admin();
        require_csrf();
        $id = self::id($params);
        $existing = self::mustRow('posts', $id);
        $errors = [];
        $title = clean_line(isset($_POST['title']) ? $_POST['title'] : '', 200);
        $body = clean_text(isset($_POST['body']) ? $_POST['body'] : '', 40000);
        $excerpt = clean_line(isset($_POST['excerpt']) ? $_POST['excerpt'] : '', 400);
        if ($title === '') {
            $errors['title'] = 'Title is required.';
        }
        if (strlen($body) < 20) {
            $errors['body'] = 'Write the article. At least a short paragraph.';
        }
        $dateRaw = trim(isset($_POST['published_at']) ? (string) $_POST['published_at'] : '');
        $publishedAt = null;
        if ($dateRaw !== '') {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateRaw)) {
                $errors['published_at'] = 'Use a date as YYYY-MM-DD.';
            } else {
                $publishedAt = $dateRaw . ' 09:00:00';
            }
        }
        if ($errors) {
            self::renderPostForm($id, $errors, $_POST);
            return;
        }
        if ($excerpt === '') {
            $flat = trim(preg_replace('/\s+/', ' ', $body));
            $excerpt = str_cut(is_string($flat) ? $flat : '', 180);
        }
        $published = self::flag('is_published');
        if ($published && $publishedAt === null) {
            $publishedAt = ($existing && !empty($existing['published_at'])) ? $existing['published_at'] : date('Y-m-d H:i:s');
        }
        if (!$published && $publishedAt === null && $existing && !empty($existing['published_at'])) {
            $publishedAt = $existing['published_at'];
        }
        $slug = unique_slug('posts', $title, $id);
        $seoTitle = clean_line(isset($_POST['seo_title']) ? $_POST['seo_title'] : '', 180);
        $seoDescription = clean_line(isset($_POST['seo_description']) ? $_POST['seo_description'] : '', 320);
        if ($existing) {
            db()->run(
                'UPDATE posts SET title = ?, slug = ?, excerpt = ?, body = ?, seo_title = ?, seo_description = ?, is_published = ?, published_at = ? WHERE id = ?',
                [$title, $slug, $excerpt, $body, $seoTitle, $seoDescription, $published, $publishedAt, $id]
            );
        } else {
            db()->insert(
                'INSERT INTO posts (title, slug, excerpt, body, seo_title, seo_description, is_published, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [$title, $slug, $excerpt, $body, $seoTitle, $seoDescription, $published, $publishedAt]
            );
        }
        flash('success', 'Article saved.');
        redirect('/admin/posts');
    }

    public static function postDelete(array $params)
    {
        self::simpleDelete('posts', $params, '/admin/posts', 'Article deleted.');
    }

    public static function pages()
    {
        require_admin();
        render('admin/pages', [
            'title' => 'Pages',
            'nav' => 'pages',
            'rows' => db()->all('SELECT id, title, slug, is_published, sort_order FROM pages ORDER BY sort_order ASC, title ASC'),
        ], 'admin/layout');
    }

    public static function pageForm(array $params)
    {
        require_admin();
        self::renderPageForm(self::id($params), [], null);
    }

    public static function pageSave(array $params)
    {
        require_admin();
        require_csrf();
        $id = self::id($params);
        $existing = self::mustRow('pages', $id);
        $errors = [];
        $title = clean_line(isset($_POST['title']) ? $_POST['title'] : '', 200);
        $body = clean_text(isset($_POST['body']) ? $_POST['body'] : '', 40000);
        if ($title === '') {
            $errors['title'] = 'Title is required.';
        }
        if (strlen($body) < 20) {
            $errors['body'] = 'Write the page. At least a short paragraph.';
        }
        $slug = $existing ? $existing['slug'] : '';
        if ($existing && is_core_page($existing['slug'])) {
            $slug = $existing['slug'];
        } else {
            $wanted = trim(isset($_POST['slug']) ? (string) $_POST['slug'] : '');
            $base = slugify($wanted !== '' ? $wanted : $title);
            if (reserved_slug($base)) {
                $errors['slug'] = 'That web address is reserved. Choose another title or slug.';
            } else {
                $slug = unique_slug('pages', $base, $id);
            }
        }
        if ($errors) {
            self::renderPageForm($id, $errors, $_POST);
            return;
        }
        $data = [
            $title, $slug, $body,
            clean_line(isset($_POST['seo_title']) ? $_POST['seo_title'] : '', 180),
            clean_line(isset($_POST['seo_description']) ? $_POST['seo_description'] : '', 320),
            self::flag('is_published'),
            self::sortValue(),
        ];
        if ($existing) {
            $data[] = $id;
            db()->run(
                'UPDATE pages SET title = ?, slug = ?, body = ?, seo_title = ?, seo_description = ?, is_published = ?, sort_order = ? WHERE id = ?',
                $data
            );
        } else {
            db()->insert(
                'INSERT INTO pages (title, slug, body, seo_title, seo_description, is_published, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)',
                $data
            );
        }
        flash('success', 'Page saved.');
        redirect('/admin/pages');
    }

    public static function pageDelete(array $params)
    {
        require_admin();
        require_csrf();
        $row = self::mustRow('pages', self::id($params));
        if ($row && is_core_page($row['slug'])) {
            flash('error', 'About, privacy, and terms stay on the site. Edit them instead of deleting them.');
            redirect('/admin/pages');
        }
        if ($row) {
            db()->run('DELETE FROM pages WHERE id = ?', [(int) $row['id']]);
            flash('success', 'Page deleted.');
        }
        redirect('/admin/pages');
    }

    public static function users()
    {
        require_admin();
        render('admin/users', [
            'title' => 'Users',
            'nav' => 'users',
            'rows' => db()->all('SELECT id, name, email, created_at FROM users ORDER BY id ASC'),
        ], 'admin/layout');
    }

    public static function userForm(array $params)
    {
        require_admin();
        self::renderUserForm(self::id($params), [], null);
    }

    public static function userSave(array $params)
    {
        require_admin();
        require_csrf();
        $id = self::id($params);
        $existing = self::mustRow('users', $id);
        $errors = [];
        $name = clean_line(isset($_POST['name']) ? $_POST['name'] : '', 120);
        $email = strtolower(clean_line(isset($_POST['email']) ? $_POST['email'] : '', 190));
        $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
        $confirm = isset($_POST['password_confirm']) ? (string) $_POST['password_confirm'] : '';
        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        } else {
            $dup = db()->one('SELECT id FROM users WHERE email = ? AND id <> ?', [$email, $id]);
            if ($dup) {
                $errors['email'] = 'That email is already an admin.';
            }
        }
        if (!$existing && strlen($password) < 8) {
            $errors['password'] = 'Use at least 8 characters.';
        }
        if ($password !== '' && strlen($password) < 8) {
            $errors['password'] = 'Use at least 8 characters.';
        }
        if (strlen($password) > 200) {
            $errors['password'] = 'That password is too long.';
        }
        if ($password !== $confirm) {
            $errors['password_confirm'] = 'The two passwords do not match.';
        }
        if ($errors) {
            self::renderUserForm($id, $errors, $_POST);
            return;
        }
        if ($existing) {
            if ($password !== '') {
                db()->run(
                    'UPDATE users SET name = ?, email = ?, password_hash = ? WHERE id = ?',
                    [$name, $email, password_hash($password, PASSWORD_DEFAULT), $id]
                );
            } else {
                db()->run('UPDATE users SET name = ?, email = ? WHERE id = ?', [$name, $email, $id]);
            }
        } else {
            db()->insert(
                'INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)',
                [$name, $email, password_hash($password, PASSWORD_DEFAULT), 'admin']
            );
        }
        unset($GLOBALS['ftee_user']);
        flash('success', 'Admin saved.');
        redirect('/admin/users');
    }

    public static function userDelete(array $params)
    {
        require_admin();
        require_csrf();
        $row = self::mustRow('users', self::id($params));
        if (!$row) {
            redirect('/admin/users');
        }
        if ((int) $row['id'] === Auth::id()) {
            flash('error', 'You cannot delete the account you are signed in with.');
            redirect('/admin/users');
        }
        $count = (int) db()->value('SELECT COUNT(*) FROM users');
        if ($count <= 1) {
            flash('error', 'Keep at least one admin.');
            redirect('/admin/users');
        }
        db()->run('DELETE FROM users WHERE id = ?', [(int) $row['id']]);
        flash('success', 'Admin deleted.');
        redirect('/admin/users');
    }

    public static function testimonials()
    {
        require_admin();
        render('admin/testimonials', [
            'title' => 'Testimonials',
            'nav' => 'testimonials',
            'rows' => db()->all('SELECT id, author_name, is_sample, is_published, sort_order FROM testimonials ORDER BY sort_order ASC, id ASC'),
        ], 'admin/layout');
    }

    public static function testimonialForm(array $params)
    {
        require_admin();
        self::renderTestimonialForm(self::id($params), [], null);
    }

    public static function testimonialSave(array $params)
    {
        require_admin();
        require_csrf();
        $id = self::id($params);
        self::mustRow('testimonials', $id);
        $errors = [];
        $author = clean_line(isset($_POST['author_name']) ? $_POST['author_name'] : '', 120);
        $quote = clean_text(isset($_POST['quote']) ? $_POST['quote'] : '', 2000);
        if ($author === '') {
            $errors['author_name'] = 'Name is required.';
        }
        if (strlen(trim($quote)) < 10) {
            $errors['quote'] = 'Enter the quote.';
        }
        if ($errors) {
            self::renderTestimonialForm($id, $errors, $_POST);
            return;
        }
        $data = [
            $author,
            clean_line(isset($_POST['author_detail']) ? $_POST['author_detail'] : '', 160),
            $quote,
            self::flag('is_sample'),
            self::flag('is_published'),
            self::sortValue(),
        ];
        if ($id) {
            $data[] = $id;
            db()->run(
                'UPDATE testimonials SET author_name = ?, author_detail = ?, quote = ?, is_sample = ?, is_published = ?, sort_order = ? WHERE id = ?',
                $data
            );
        } else {
            db()->insert(
                'INSERT INTO testimonials (author_name, author_detail, quote, is_sample, is_published, sort_order) VALUES (?, ?, ?, ?, ?, ?)',
                $data
            );
        }
        flash('success', 'Testimonial saved. Sample quotes never appear on the public site.');
        redirect('/admin/testimonials');
    }

    public static function testimonialDelete(array $params)
    {
        self::simpleDelete('testimonials', $params, '/admin/testimonials', 'Testimonial deleted.');
    }

    public static function enquiries()
    {
        require_admin();
        $status = isset($_GET['status']) ? (string) $_GET['status'] : '';
        if (!in_array($status, ['new', 'read', 'closed'], true)) {
            $status = '';
        }
        if ($status === '') {
            $rows = db()->all('SELECT id, name, phone, email, service_name, status, mail_status, created_at FROM enquiries ORDER BY created_at DESC LIMIT 100');
        } else {
            $rows = db()->all(
                'SELECT id, name, phone, email, service_name, status, mail_status, created_at FROM enquiries WHERE status = ? ORDER BY created_at DESC LIMIT 100',
                [$status]
            );
        }
        render('admin/enquiries', [
            'title' => 'Enquiries',
            'nav' => 'enquiries',
            'rows' => $rows,
            'status' => $status,
            'total' => (int) db()->value('SELECT COUNT(*) FROM enquiries'),
        ], 'admin/layout');
    }

    public static function enquiry(array $params)
    {
        require_admin();
        $row = self::mustRow('enquiries', self::id($params));
        if (!$row) {
            return;
        }
        if ($row['status'] === 'new') {
            db()->run('UPDATE enquiries SET status = ? WHERE id = ?', ['read', (int) $row['id']]);
            $row['status'] = 'read';
        }
        render('admin/enquiry', [
            'title' => 'Enquiry',
            'nav' => 'enquiries',
            'row' => $row,
        ], 'admin/layout');
    }

    public static function enquiryStatus(array $params)
    {
        require_admin();
        require_csrf();
        $row = self::mustRow('enquiries', self::id($params));
        if (!$row) {
            return;
        }
        $status = isset($_POST['status']) ? (string) $_POST['status'] : '';
        if (!in_array($status, ['new', 'read', 'closed'], true)) {
            flash('error', 'Choose a valid status.');
        } else {
            db()->run('UPDATE enquiries SET status = ? WHERE id = ?', [$status, (int) $row['id']]);
            flash('success', 'Status updated.');
        }
        redirect('/admin/enquiries/' . (int) $row['id']);
    }

    public static function enquiryDelete(array $params)
    {
        self::simpleDelete('enquiries', $params, '/admin/enquiries', 'Enquiry deleted.');
    }

    private static function id(array $params)
    {
        return isset($params['id']) ? (int) $params['id'] : 0;
    }

    private static function flag($key)
    {
        return isset($_POST[$key]) ? 1 : 0;
    }

    private static function sortValue()
    {
        $n = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;
        if ($n < -999) {
            return -999;
        }
        if ($n > 9999) {
            return 9999;
        }
        return $n;
    }

    private static function mustRow($table, $id)
    {
        $allowed = [
            'services' => true, 'packages' => true, 'branches' => true, 'gallery_items' => true,
            'faqs' => true, 'posts' => true, 'pages' => true, 'users' => true,
            'testimonials' => true, 'enquiries' => true,
        ];
        if (!isset($allowed[$table])) {
            throw new InvalidArgumentException('Unknown table');
        }
        if ($id < 1) {
            return null;
        }
        $row = db()->one('SELECT * FROM ' . $table . ' WHERE id = ?', [$id]);
        if (!$row) {
            SiteController::notFound();
            exit;
        }
        return $row;
    }

    private static function simpleDelete($table, array $params, $back, $message)
    {
        require_admin();
        require_csrf();
        $row = self::mustRow($table, self::id($params));
        if ($row) {
            db()->run('DELETE FROM ' . $table . ' WHERE id = ?', [(int) $row['id']]);
            flash('success', $message);
        }
        redirect($back);
    }

    private static function saveSetting($key, $value)
    {
        db()->run(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?',
            [$key, $value, $value]
        );
    }

    private static function officeInput()
    {
        $country = clean_line(isset($_POST['ho_country']) ? $_POST['ho_country'] : 'Nigeria', 80);
        if ($country === '') {
            $country = 'Nigeria';
        }
        return [
            'name' => clean_line(isset($_POST['ho_name']) ? $_POST['ho_name'] : 'Head office', 160),
            'address_line' => clean_line(isset($_POST['ho_address_line']) ? $_POST['ho_address_line'] : '', 255),
            'area' => clean_line(isset($_POST['ho_area']) ? $_POST['ho_area'] : '', 120),
            'city' => clean_line(isset($_POST['ho_city']) ? $_POST['ho_city'] : '', 120),
            'state' => clean_line(isset($_POST['ho_state']) ? $_POST['ho_state'] : '', 120),
            'postal_code' => clean_line(isset($_POST['ho_postal_code']) ? $_POST['ho_postal_code'] : '', 20),
            'country' => $country,
            'plus_code' => clean_line(isset($_POST['ho_plus_code']) ? $_POST['ho_plus_code'] : '', 40),
            'phone' => clean_line(isset($_POST['ho_phone']) ? $_POST['ho_phone'] : '', 40),
            'email' => strtolower(clean_line(isset($_POST['ho_email']) ? $_POST['ho_email'] : '', 190)),
            'hours_text' => clean_line(isset($_POST['ho_hours_text']) ? $_POST['ho_hours_text'] : '', 255),
            'notes' => clean_text(isset($_POST['ho_notes']) ? $_POST['ho_notes'] : '', 4000),
        ];
    }

    private static function renderSettings(array $errors, $posted)
    {
        $all = settings_all();
        $values = [];
        foreach (self::settingGroups() as $fields) {
            foreach ($fields as $field) {
                $key = $field['key'];
                if (is_array($posted) && array_key_exists($key, $posted)) {
                    $values[$key] = $posted[$key];
                } else {
                    $values[$key] = isset($all[$key]) ? $all[$key] : '';
                }
            }
        }
        $office = head_office();
        render('admin/settings', [
            'title' => 'Settings',
            'nav' => 'settings',
            'groups' => self::settingGroups(),
            'values' => $values,
            'errors' => $errors,
            'posted' => $posted,
            'office' => $office,
            'mailOn' => is_array($posted) ? isset($posted['mail_enabled']) : setting('mail_enabled') === '1',
            'logoPath' => isset($all['logo_path']) ? $all['logo_path'] : '',
        ], 'admin/layout');
    }

    private static function renderServiceForm($id, array $errors, $posted)
    {
        $row = $id ? db()->one('SELECT * FROM services WHERE id = ?', [$id]) : null;
        if ($id && !$row) {
            SiteController::notFound();
            return;
        }
        render('admin/service_form', [
            'title' => $id ? 'Edit service' : 'New service',
            'nav' => 'services',
            'row' => $row,
            'errors' => $errors,
            'posted' => $posted,
            'id' => $id,
        ], 'admin/layout');
    }

    private static function renderPackageForm($id, array $errors, $posted)
    {
        $row = $id ? db()->one('SELECT * FROM packages WHERE id = ?', [$id]) : null;
        if ($id && !$row) {
            SiteController::notFound();
            return;
        }
        $preselect = isset($_GET['service']) ? (int) $_GET['service'] : 0;
        render('admin/package_form', [
            'title' => $id ? 'Edit package' : 'New package',
            'nav' => 'packages',
            'row' => $row,
            'errors' => $errors,
            'posted' => $posted,
            'id' => $id,
            'services' => db()->all('SELECT id, name FROM services ORDER BY sort_order ASC, id ASC'),
            'preselect' => $preselect,
        ], 'admin/layout');
    }

    private static function renderBranchForm($id, array $errors, $posted)
    {
        $row = $id ? db()->one('SELECT * FROM branches WHERE id = ?', [$id]) : null;
        if ($id && !$row) {
            SiteController::notFound();
            return;
        }
        render('admin/branch_form', [
            'title' => $id ? 'Edit branch' : 'New branch',
            'nav' => 'branches',
            'row' => $row,
            'errors' => $errors,
            'posted' => $posted,
            'id' => $id,
        ], 'admin/layout');
    }

    private static function renderGalleryForm($id, array $errors, $posted)
    {
        $row = $id ? db()->one('SELECT * FROM gallery_items WHERE id = ?', [$id]) : null;
        if ($id && !$row) {
            SiteController::notFound();
            return;
        }
        render('admin/gallery_form', [
            'title' => $id ? 'Edit gallery item' : 'New gallery item',
            'nav' => 'gallery',
            'row' => $row,
            'errors' => $errors,
            'posted' => $posted,
            'id' => $id,
        ], 'admin/layout');
    }

    private static function renderFaqForm($id, array $errors, $posted)
    {
        $row = $id ? db()->one('SELECT * FROM faqs WHERE id = ?', [$id]) : null;
        if ($id && !$row) {
            SiteController::notFound();
            return;
        }
        render('admin/faq_form', [
            'title' => $id ? 'Edit FAQ' : 'New FAQ',
            'nav' => 'faqs',
            'row' => $row,
            'errors' => $errors,
            'posted' => $posted,
            'id' => $id,
            'services' => db()->all('SELECT id, name FROM services ORDER BY sort_order ASC, id ASC'),
        ], 'admin/layout');
    }

    private static function renderPostForm($id, array $errors, $posted)
    {
        $row = $id ? db()->one('SELECT * FROM posts WHERE id = ?', [$id]) : null;
        if ($id && !$row) {
            SiteController::notFound();
            return;
        }
        render('admin/post_form', [
            'title' => $id ? 'Edit article' : 'New article',
            'nav' => 'posts',
            'row' => $row,
            'errors' => $errors,
            'posted' => $posted,
            'id' => $id,
        ], 'admin/layout');
    }

    private static function renderPageForm($id, array $errors, $posted)
    {
        $row = $id ? db()->one('SELECT * FROM pages WHERE id = ?', [$id]) : null;
        if ($id && !$row) {
            SiteController::notFound();
            return;
        }
        render('admin/page_form', [
            'title' => $id ? 'Edit page' : 'New page',
            'nav' => 'pages',
            'row' => $row,
            'errors' => $errors,
            'posted' => $posted,
            'id' => $id,
            'core' => $row && is_core_page($row['slug']),
        ], 'admin/layout');
    }

    private static function renderUserForm($id, array $errors, $posted)
    {
        $row = $id ? db()->one('SELECT id, name, email FROM users WHERE id = ?', [$id]) : null;
        if ($id && !$row) {
            SiteController::notFound();
            return;
        }
        render('admin/user_form', [
            'title' => $id ? 'Edit admin' : 'New admin',
            'nav' => 'users',
            'row' => $row,
            'errors' => $errors,
            'posted' => $posted,
            'id' => $id,
        ], 'admin/layout');
    }

    private static function renderTestimonialForm($id, array $errors, $posted)
    {
        $row = $id ? db()->one('SELECT * FROM testimonials WHERE id = ?', [$id]) : null;
        if ($id && !$row) {
            SiteController::notFound();
            return;
        }
        render('admin/testimonial_form', [
            'title' => $id ? 'Edit testimonial' : 'New testimonial',
            'nav' => 'testimonials',
            'row' => $row,
            'errors' => $errors,
            'posted' => $posted,
            'id' => $id,
        ], 'admin/layout');
    }

    private static function settingGroups()
    {
        return [
            'Business' => [
                ['key' => 'business_name', 'label' => 'Business name', 'type' => 'text', 'max' => 160, 'required' => true],
                ['key' => 'tagline', 'label' => 'Tagline', 'type' => 'text', 'max' => 180, 'help' => 'Shown under the name in the header.'],
            ],
            'Contact' => [
                ['key' => 'phone', 'label' => 'Company phone', 'type' => 'text', 'max' => 40, 'help' => 'Shown in the header and footer. Leave blank until you have the number.'],
                ['key' => 'phone_alt', 'label' => 'Second phone', 'type' => 'text', 'max' => 40],
                ['key' => 'email', 'label' => 'Public email', 'type' => 'email', 'max' => 190],
                ['key' => 'whatsapp', 'label' => 'WhatsApp number or link', 'type' => 'text', 'max' => 190, 'help' => 'A number such as 0803 000 0000, or a full https://wa.me link.'],
            ],
            'Social links' => [
                ['key' => 'facebook', 'label' => 'Facebook URL', 'type' => 'url', 'max' => 300],
                ['key' => 'instagram', 'label' => 'Instagram URL', 'type' => 'url', 'max' => 300],
                ['key' => 'x_url', 'label' => 'X or Twitter URL', 'type' => 'url', 'max' => 300],
                ['key' => 'linkedin', 'label' => 'LinkedIn URL', 'type' => 'url', 'max' => 300],
            ],
            'Homepage' => [
                ['key' => 'home_kicker', 'label' => 'Hero label', 'type' => 'text', 'max' => 120],
                ['key' => 'home_title', 'label' => 'Hero title', 'type' => 'textarea', 'max' => 200, 'rows' => 2],
                ['key' => 'home_lead', 'label' => 'Hero introduction', 'type' => 'textarea', 'max' => 800, 'rows' => 4],
                ['key' => 'home_services_title', 'label' => 'Services heading', 'type' => 'text', 'max' => 160],
                ['key' => 'home_steps_title', 'label' => 'Steps heading', 'type' => 'text', 'max' => 160],
                ['key' => 'step_1_title', 'label' => 'Step 1 title', 'type' => 'text', 'max' => 120],
                ['key' => 'step_1_body', 'label' => 'Step 1 text', 'type' => 'textarea', 'max' => 400, 'rows' => 3],
                ['key' => 'step_2_title', 'label' => 'Step 2 title', 'type' => 'text', 'max' => 120],
                ['key' => 'step_2_body', 'label' => 'Step 2 text', 'type' => 'textarea', 'max' => 400, 'rows' => 3],
                ['key' => 'step_3_title', 'label' => 'Step 3 title', 'type' => 'text', 'max' => 120],
                ['key' => 'step_3_body', 'label' => 'Step 3 text', 'type' => 'textarea', 'max' => 400, 'rows' => 3],
                ['key' => 'home_office_title', 'label' => 'Office heading', 'type' => 'text', 'max' => 160],
                ['key' => 'home_faq_title', 'label' => 'FAQ heading', 'type' => 'text', 'max' => 160],
                ['key' => 'home_closing', 'label' => 'Closing line', 'type' => 'textarea', 'max' => 400, 'rows' => 3],
                ['key' => 'footer_blurb', 'label' => 'Footer blurb', 'type' => 'textarea', 'max' => 400, 'rows' => 3],
            ],
            'Page introductions' => [
                ['key' => 'services_intro', 'label' => 'Services page', 'type' => 'textarea', 'max' => 800, 'rows' => 3],
                ['key' => 'branches_intro', 'label' => 'Branches page', 'type' => 'textarea', 'max' => 800, 'rows' => 3],
                ['key' => 'gallery_intro', 'label' => 'Gallery page', 'type' => 'textarea', 'max' => 800, 'rows' => 3],
                ['key' => 'blog_intro', 'label' => 'Blog page', 'type' => 'textarea', 'max' => 800, 'rows' => 3],
                ['key' => 'contact_intro', 'label' => 'Contact page', 'type' => 'textarea', 'max' => 800, 'rows' => 3],
                ['key' => 'faq_intro', 'label' => 'FAQ page', 'type' => 'textarea', 'max' => 800, 'rows' => 3],
            ],
            'Search' => [
                ['key' => 'site_url', 'label' => 'Site URL', 'type' => 'url', 'max' => 190, 'help' => 'Origin only, for example https://example.com. The folder path is base_url in config/config.php, not here.'],
                ['key' => 'seo_title', 'label' => 'Default SEO title', 'type' => 'text', 'max' => 180],
                ['key' => 'seo_description', 'label' => 'Default meta description', 'type' => 'textarea', 'max' => 320, 'rows' => 3],
                ['key' => 'home_seo_title', 'label' => 'Home title', 'type' => 'text', 'max' => 180],
                ['key' => 'home_seo_description', 'label' => 'Home description', 'type' => 'textarea', 'max' => 320, 'rows' => 2],
                ['key' => 'services_seo_title', 'label' => 'Services title', 'type' => 'text', 'max' => 180],
                ['key' => 'services_seo_description', 'label' => 'Services description', 'type' => 'textarea', 'max' => 320, 'rows' => 2],
                ['key' => 'branches_seo_title', 'label' => 'Branches title', 'type' => 'text', 'max' => 180],
                ['key' => 'branches_seo_description', 'label' => 'Branches description', 'type' => 'textarea', 'max' => 320, 'rows' => 2],
                ['key' => 'gallery_seo_title', 'label' => 'Gallery title', 'type' => 'text', 'max' => 180],
                ['key' => 'gallery_seo_description', 'label' => 'Gallery description', 'type' => 'textarea', 'max' => 320, 'rows' => 2],
                ['key' => 'blog_seo_title', 'label' => 'Blog title', 'type' => 'text', 'max' => 180],
                ['key' => 'blog_seo_description', 'label' => 'Blog description', 'type' => 'textarea', 'max' => 320, 'rows' => 2],
                ['key' => 'contact_seo_title', 'label' => 'Contact title', 'type' => 'text', 'max' => 180],
                ['key' => 'contact_seo_description', 'label' => 'Contact description', 'type' => 'textarea', 'max' => 320, 'rows' => 2],
                ['key' => 'faq_seo_title', 'label' => 'FAQ title', 'type' => 'text', 'max' => 180],
                ['key' => 'faq_seo_description', 'label' => 'FAQ description', 'type' => 'textarea', 'max' => 320, 'rows' => 2],
            ],
            'Email delivery' => [
                ['key' => 'mail_recipient', 'label' => 'Send enquiries to', 'type' => 'email', 'max' => 190, 'help' => 'Used only when email delivery is turned on. The enquiry is saved either way.'],
                ['key' => 'mail_from', 'label' => 'From address', 'type' => 'email', 'max' => 190, 'help' => 'Some hosts only allow mail() from an address on this domain. Leave blank to use the recipient address.'],
            ],
        ];
    }
}
