<?php
// Public pages: home, services, branches, gallery, blog, contact, FAQ, and fixed pages.

class SiteController
{
    public static function home()
    {
        $faqs = db()->all(
            'SELECT question, answer FROM faqs WHERE show_on_home = 1 AND is_published = 1 ORDER BY sort_order ASC, id ASC LIMIT 4'
        );
        $testimonials = db()->all(
            'SELECT author_name, author_detail, quote FROM testimonials WHERE is_published = 1 AND is_sample = 0 ORDER BY sort_order ASC, id ASC'
        );
        $jsonLd = [];
        if ($faqs) {
            $jsonLd[] = faq_node($faqs);
        }
        render('home', [
            'seoTitle' => self::pick('home_seo_title', 'FTEE-PEE NIG LTD | Print, cleaning and auto care in Surulere'),
            'seoDescription' => self::pick('home_seo_description', 'Design and printing, cleaning, wheel balancing and alignment, and car wash in Aguda, Surulere, Lagos. Request a quote from the head office.'),
            'bodyClass' => 'page-home',
            'faqs' => $faqs,
            'testimonials' => $testimonials,
            'office' => head_office(),
            'jsonLd' => $jsonLd,
        ]);
    }

    public static function about()
    {
        self::fixedPage('about', 'page-about');
    }

    public static function privacy()
    {
        self::fixedPage('privacy', 'page-privacy');
    }

    public static function terms()
    {
        self::fixedPage('terms', 'page-terms');
    }

    public static function customPage(array $params)
    {
        $slug = slugify(isset($params['slug']) ? $params['slug'] : '');
        if ($slug === '' || reserved_slug($slug)) {
            self::notFound();
            return;
        }
        $page = db()->one('SELECT * FROM pages WHERE slug = ? AND is_published = 1', [$slug]);
        if (!$page) {
            self::notFound();
            return;
        }
        self::showPage($page, 'page-custom');
    }

    public static function services()
    {
        render('services', [
            'seoTitle' => self::pick('services_seo_title', 'Services in Surulere | FTEE-PEE NIG LTD'),
            'seoDescription' => self::pick('services_seo_description', 'Printing and branding, cleaning, wheel alignment, and car wash from our Aguda, Surulere head office in Lagos.'),
            'bodyClass' => 'page-services',
            'services' => published_services(),
            'jsonLd' => [breadcrumb_node([
                ['name' => 'Home', 'path' => '/'],
                ['name' => 'Services', 'path' => '/services'],
            ])],
        ]);
    }

    public static function service(array $params)
    {
        $slug = slugify(isset($params['slug']) ? $params['slug'] : '');
        $service = db()->one('SELECT * FROM services WHERE slug = ? AND is_published = 1', [$slug]);
        if (!$service) {
            self::notFound();
            return;
        }
        $packages = db()->all(
            'SELECT * FROM packages WHERE service_id = ? AND is_published = 1 ORDER BY sort_order ASC, id ASC',
            [(int) $service['id']]
        );
        $faqs = db()->all(
            'SELECT question, answer FROM faqs WHERE service_id = ? AND is_published = 1 ORDER BY sort_order ASC, id ASC',
            [(int) $service['id']]
        );
        $title = $service['seo_title'] !== '' ? $service['seo_title'] : $service['name'];
        $description = $service['seo_description'] !== '' ? $service['seo_description'] : $service['summary'];
        $node = [
            '@type' => 'Service',
            'name' => $service['name'],
            'description' => $service['summary'],
            'url' => canonical_url('/services/' . $service['slug']),
            'provider' => ['@id' => canonical_url('/') . '#business'],
            'areaServed' => 'Lagos',
        ];
        $offers = [];
        foreach ($packages as $package) {
            if ((int) $package['is_sample'] === 1) {
                continue;
            }
            if ($package['price_amount'] === null || $package['price_amount'] === '') {
                continue;
            }
            $offers[] = [
                '@type' => 'Offer',
                'name' => $package['name'],
                'price' => (string) (int) $package['price_amount'],
                'priceCurrency' => 'NGN',
                'url' => canonical_url('/contact'),
            ];
        }
        if ($offers) {
            $node['offers'] = $offers;
        }
        $jsonLd = [
            breadcrumb_node([
                ['name' => 'Home', 'path' => '/'],
                ['name' => 'Services', 'path' => '/services'],
                ['name' => $service['name'], 'path' => '/services/' . $service['slug']],
            ]),
            $node,
        ];
        if ($faqs) {
            $jsonLd[] = faq_node($faqs);
        }
        render('service', [
            'seoTitle' => $title,
            'seoDescription' => $description,
            'bodyClass' => 'page-service',
            'service' => $service,
            'packages' => $packages,
            'faqs' => $faqs,
            'jsonLd' => $jsonLd,
        ]);
    }

    public static function branches()
    {
        $branches = db()->all('SELECT * FROM branches WHERE is_published = 1 ORDER BY is_head_office DESC, sort_order ASC, id ASC');
        render('branches', [
            'seoTitle' => self::pick('branches_seo_title', 'Head office in Aguda, Surulere | FTEE-PEE'),
            'seoDescription' => self::pick('branches_seo_description', 'Find FTEE-PEE NIG LTD with Plus Code J9M2+P2C in Aguda, Surulere, Lagos. Further branches are added from the admin.'),
            'bodyClass' => 'page-branches',
            'branches' => $branches,
            'jsonLd' => [breadcrumb_node([
                ['name' => 'Home', 'path' => '/'],
                ['name' => 'Branches', 'path' => '/branches'],
            ])],
        ]);
    }

    public static function gallery()
    {
        $items = db()->all('SELECT * FROM gallery_items WHERE is_published = 1 ORDER BY sort_order ASC, id ASC');
        $placeholders = false;
        foreach ($items as $item) {
            if ((int) $item['is_placeholder'] === 1) {
                $placeholders = true;
                break;
            }
        }
        render('gallery', [
            'seoTitle' => self::pick('gallery_seo_title', 'Gallery | FTEE-PEE NIG LTD, Surulere'),
            'seoDescription' => self::pick('gallery_seo_description', 'Layout visuals for printing, cleaning, and auto care. Real project photos are added by the admin when they exist.'),
            'bodyClass' => 'page-gallery',
            'items' => $items,
            'showPlaceholderNote' => $placeholders,
            'jsonLd' => [breadcrumb_node([
                ['name' => 'Home', 'path' => '/'],
                ['name' => 'Gallery', 'path' => '/gallery'],
            ])],
        ]);
    }

    public static function blog()
    {
        $posts = db()->all(
            'SELECT title, slug, excerpt, published_at FROM posts WHERE is_published = 1 AND (published_at IS NULL OR published_at <= NOW()) ORDER BY published_at DESC, id DESC'
        );
        render('blog', [
            'seoTitle' => self::pick('blog_seo_title', 'Notes from Aguda | FTEE-PEE NIG LTD'),
            'seoDescription' => self::pick('blog_seo_description', 'Practical notes on print jobs, cleaning, and wheel care for customers in Surulere and the rest of Lagos.'),
            'bodyClass' => 'page-blog',
            'posts' => $posts,
            'jsonLd' => [breadcrumb_node([
                ['name' => 'Home', 'path' => '/'],
                ['name' => 'Blog', 'path' => '/blog'],
            ])],
        ]);
    }

    public static function post(array $params)
    {
        $slug = slugify(isset($params['slug']) ? $params['slug'] : '');
        $post = db()->one(
            'SELECT * FROM posts WHERE slug = ? AND is_published = 1 AND (published_at IS NULL OR published_at <= NOW())',
            [$slug]
        );
        if (!$post) {
            self::notFound();
            return;
        }
        $title = $post['seo_title'] !== '' ? $post['seo_title'] : $post['title'];
        $description = $post['seo_description'] !== '' ? $post['seo_description'] : $post['excerpt'];
        $published = $post['published_at'] ? $post['published_at'] : $post['created_at'];
        render('post', [
            'seoTitle' => $title,
            'seoDescription' => $description,
            'bodyClass' => 'page-post',
            'post' => $post,
            'ogType' => 'article',
            'jsonLd' => [
                breadcrumb_node([
                    ['name' => 'Home', 'path' => '/'],
                    ['name' => 'Blog', 'path' => '/blog'],
                    ['name' => $post['title'], 'path' => '/blog/' . $post['slug']],
                ]),
                [
                    '@type' => 'Article',
                    'headline' => $post['title'],
                    'description' => $description,
                    'datePublished' => date('c', strtotime((string) $published)),
                    'dateModified' => date('c', strtotime((string) $post['updated_at'])),
                    'author' => [
                        '@type' => 'Organization',
                        'name' => setting('business_name', 'FTEE-PEE NIG LTD'),
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => setting('business_name', 'FTEE-PEE NIG LTD'),
                    ],
                    'mainEntityOfPage' => canonical_url('/blog/' . $post['slug']),
                ],
            ],
        ]);
    }

    public static function contact()
    {
        self::renderContact([], null);
    }

    public static function contactSubmit()
    {
        require_csrf();
        $honeypot = trim(isset($_POST['company_website']) ? (string) $_POST['company_website'] : '');
        if ($honeypot !== '') {
            flash('success', 'We have received your message and will reply on the phone or email you gave us.');
            redirect('/contact');
        }
        $errors = [];
        $name = clean_line(isset($_POST['name']) ? $_POST['name'] : '', 120);
        $phoneRaw = clean_line(isset($_POST['phone']) ? $_POST['phone'] : '', 40);
        $email = strtolower(clean_line(isset($_POST['email']) ? $_POST['email'] : '', 190));
        $message = clean_text(isset($_POST['message']) ? $_POST['message'] : '', 5000);
        $serviceRaw = isset($_POST['service_id']) ? (string) $_POST['service_id'] : '';
        $branchRaw = isset($_POST['branch_id']) ? (string) $_POST['branch_id'] : '';

        if (strlen($name) < 2) {
            $errors['name'] = 'Enter your name.';
        }
        if (!is_nigerian_phone($phoneRaw)) {
            $errors['phone'] = 'Enter a Nigerian phone number, such as 0803 123 4567 or +234 803 123 4567.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }
        if (strlen(trim($message)) < 10) {
            $errors['message'] = 'Tell us a little more about the job, at least a sentence.';
        }

        $service = null;
        $serviceName = 'Not sure yet';
        if ($serviceRaw === '' ) {
            $errors['service_id'] = 'Choose a service, or Not sure yet.';
        } elseif ($serviceRaw !== 'none') {
            $service = db()->one('SELECT id, name FROM services WHERE id = ? AND is_published = 1', [(int) $serviceRaw]);
            if (!$service) {
                $errors['service_id'] = 'Choose a service from the list.';
            } else {
                $serviceName = $service['name'];
            }
        }

        $branch = null;
        $branchName = '';
        $branchCount = (int) db()->value('SELECT COUNT(*) FROM branches WHERE is_published = 1');
        if ($branchCount > 0 && $branchRaw === '') {
            $errors['branch_id'] = 'Choose a branch, or Not sure yet.';
        } elseif ($branchRaw !== '' && $branchRaw !== 'none') {
            $branch = db()->one('SELECT id, name FROM branches WHERE id = ? AND is_published = 1', [(int) $branchRaw]);
            if (!$branch) {
                $errors['branch_id'] = 'Choose a branch from the list.';
            } else {
                $branchName = $branch['name'];
            }
        }

        $ip = client_ip();
        $recent = (int) db()->value(
            'SELECT COUNT(*) FROM enquiries WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)',
            [$ip]
        );
        if ($recent >= 8) {
            $errors['form'] = 'Too many messages from this network in the last hour. Please wait before sending another.';
        }

        if ($errors) {
            self::renderContact($errors, $_POST);
            return;
        }

        $id = db()->insert(
            'INSERT INTO enquiries (name, phone, email, message, service_id, service_name, branch_id, branch_name, status, mail_status, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $name,
                format_nigerian_phone($phoneRaw),
                $email,
                $message,
                $service ? (int) $service['id'] : null,
                $serviceName,
                $branch ? (int) $branch['id'] : null,
                $branchName,
                'new',
                'skipped',
                $ip,
            ]
        );
        $enquiry = db()->one('SELECT * FROM enquiries WHERE id = ?', [$id]);
        $mailStatus = 'failed';
        if ($enquiry) {
            try {
                $mailStatus = Mailer::sendEnquiry($enquiry);
            } catch (Exception $e) {
                error_log('FTEE-PEE mail: ' . $e->getMessage());
                $mailStatus = 'failed';
            }
            db()->run('UPDATE enquiries SET mail_status = ? WHERE id = ?', [$mailStatus, $id]);
        }
        flash('success', 'We have received your message and will reply on the phone or email you gave us.');
        redirect('/contact');
    }

    public static function quoteRedirect()
    {
        redirect('/contact');
    }

    public static function faq()
    {
        $rows = db()->all(
            'SELECT f.question, f.answer, f.service_id, s.name AS service_name, s.sort_order AS service_sort
             FROM faqs f
             LEFT JOIN services s ON s.id = f.service_id
             WHERE f.is_published = 1
             ORDER BY f.sort_order ASC, f.id ASC'
        );
        $grouped = [];
        foreach ($rows as $row) {
            $label = $row['service_name'] ? $row['service_name'] : 'General';
            if (!isset($grouped[$label])) {
                $grouped[$label] = [];
            }
            $grouped[$label][] = $row;
        }
        if (isset($grouped['General'])) {
            $general = ['General' => $grouped['General']];
            unset($grouped['General']);
            $grouped = $general + $grouped;
        }
        $jsonLd = [breadcrumb_node([
            ['name' => 'Home', 'path' => '/'],
            ['name' => 'FAQ', 'path' => '/faq'],
        ])];
        if ($rows) {
            $jsonLd[] = faq_node($rows);
        }
        render('faq', [
            'seoTitle' => self::pick('faq_seo_title', 'Questions | FTEE-PEE NIG LTD, Lagos'),
            'seoDescription' => self::pick('faq_seo_description', 'Answers about quotes, printing, cleaning, wheel alignment, and car wash at our Aguda, Surulere office.'),
            'bodyClass' => 'page-faq',
            'grouped' => $grouped,
            'jsonLd' => $jsonLd,
        ]);
    }

    public static function sitemap()
    {
        header('Content-Type: application/xml; charset=UTF-8');
        $urls = [];
        $add = function ($path, $lastmod) use (&$urls) {
            $urls[] = ['loc' => canonical_url($path), 'lastmod' => $lastmod];
        };
        $add('/', null);
        $pages = db()->all('SELECT slug, updated_at FROM pages WHERE is_published = 1');
        $pageMap = [];
        foreach ($pages as $page) {
            $pageMap[$page['slug']] = substr((string) $page['updated_at'], 0, 10);
        }
        foreach (['about', 'privacy', 'terms'] as $slug) {
            if (isset($pageMap[$slug])) {
                $add('/' . $slug, $pageMap[$slug]);
                unset($pageMap[$slug]);
            }
        }
        $add('/services', null);
        $add('/branches', null);
        $add('/gallery', null);
        $add('/blog', null);
        $add('/contact', null);
        $add('/faq', null);
        foreach ($pageMap as $slug => $day) {
            if (!reserved_slug($slug)) {
                $add('/' . $slug, $day);
            }
        }
        $services = db()->all('SELECT slug, updated_at FROM services WHERE is_published = 1');
        foreach ($services as $row) {
            $add('/services/' . $row['slug'], substr((string) $row['updated_at'], 0, 10));
        }
        $posts = db()->all('SELECT slug, updated_at FROM posts WHERE is_published = 1');
        foreach ($posts as $row) {
            $add('/blog/' . $row['slug'], substr((string) $row['updated_at'], 0, 10));
        }
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($urls as $url) {
            echo '  <url><loc>' . e($url['loc']) . '</loc>';
            if (!empty($url['lastmod'])) {
                echo '<lastmod>' . e($url['lastmod']) . '</lastmod>';
            }
            echo "</url>\n";
        }
        echo "</urlset>\n";
        exit;
    }

    public static function robots()
    {
        header('Content-Type: text/plain; charset=UTF-8');
        $prefix = base_url();
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo 'Disallow: ' . $prefix . "/admin\n";
        echo 'Disallow: ' . $prefix . "/install\n";
        echo 'Disallow: ' . $prefix . "/install.php\n";
        echo "\nSitemap: " . canonical_url('/sitemap.xml') . "\n";
        exit;
    }

    public static function notFound()
    {
        http_response_code(404);
        if (Auth::check() && strpos(request_path(), '/admin') === 0) {
            render('admin/message', [
                'title' => 'Not found',
                'nav' => '',
                'message' => 'That admin page does not exist.',
            ], 'admin/layout');
            return;
        }
        render('404', [
            'seoTitle' => 'Page not found',
            'seoDescription' => 'That page is not on the FTEE-PEE website.',
            'robots' => 'noindex, nofollow',
            'bodyClass' => 'page-404',
        ]);
    }

    private static function fixedPage($slug, $bodyClass)
    {
        $page = db()->one('SELECT * FROM pages WHERE slug = ? AND is_published = 1', [$slug]);
        if (!$page) {
            self::notFound();
            return;
        }
        self::showPage($page, $bodyClass);
    }

    private static function showPage(array $page, $bodyClass)
    {
        $title = $page['seo_title'] !== '' ? $page['seo_title'] : $page['title'];
        $description = $page['seo_description'] !== '' ? $page['seo_description'] : setting('seo_description');
        render('page', [
            'seoTitle' => $title,
            'seoDescription' => $description,
            'bodyClass' => $bodyClass,
            'page' => $page,
            'jsonLd' => [breadcrumb_node([
                ['name' => 'Home', 'path' => '/'],
                ['name' => $page['title'], 'path' => '/' . $page['slug']],
            ])],
        ]);
    }

    private static function renderContact(array $errors, $posted)
    {
        $services = published_services();
        $branches = db()->all('SELECT id, name, is_head_office FROM branches WHERE is_published = 1 ORDER BY is_head_office DESC, sort_order ASC, id ASC');
        $selectedService = '';
        $selectedBranch = '';
        if (is_array($posted)) {
            $selectedService = isset($posted['service_id']) ? (string) $posted['service_id'] : '';
            $selectedBranch = isset($posted['branch_id']) ? (string) $posted['branch_id'] : '';
        } else {
            if (isset($_GET['service'])) {
                $slug = slugify((string) $_GET['service']);
                $found = db()->one('SELECT id FROM services WHERE slug = ? AND is_published = 1', [$slug]);
                if ($found) {
                    $selectedService = (string) $found['id'];
                }
            }
            if (isset($_GET['branch'])) {
                $branchId = (int) $_GET['branch'];
                foreach ($branches as $branch) {
                    if ((int) $branch['id'] === $branchId) {
                        $selectedBranch = (string) $branchId;
                        break;
                    }
                }
            }
            if ($selectedBranch === '' && count($branches) === 1) {
                $selectedBranch = (string) $branches[0]['id'];
            }
        }
        render('contact', [
            'seoTitle' => self::pick('contact_seo_title', 'Request a quote | FTEE-PEE NIG LTD'),
            'seoDescription' => self::pick('contact_seo_description', 'Ask FTEE-PEE for a quote on printing, cleaning, wheel alignment, or car wash in Aguda, Surulere, Lagos.'),
            'bodyClass' => 'page-contact',
            'errors' => $errors,
            'posted' => $posted,
            'services' => $services,
            'branches' => $branches,
            'selectedService' => $selectedService,
            'selectedBranch' => $selectedBranch,
            'office' => head_office(),
            'jsonLd' => [breadcrumb_node([
                ['name' => 'Home', 'path' => '/'],
                ['name' => 'Contact', 'path' => '/contact'],
            ])],
        ]);
    }

    private static function pick($key, $fallback)
    {
        $value = setting($key, '');
        return $value !== '' ? $value : $fallback;
    }
}
