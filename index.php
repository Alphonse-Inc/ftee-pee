<?php
// Front controller. Clean URLs for the public site and the admin.

require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/controllers/SiteController.php';
require __DIR__ . '/app/controllers/AdminController.php';

$method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper((string) $_SERVER['REQUEST_METHOD']) : 'GET';
if (!in_array($method, ['GET', 'POST', 'HEAD'], true)) {
    http_response_code(405);
    header('Allow: GET, POST, HEAD');
    echo 'Method not allowed.';
    exit;
}

if ($method === 'GET' || $method === 'HEAD') {
    $uriPath = parse_url(isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
    if (is_string($uriPath) && strlen($uriPath) > 1 && substr($uriPath, -1) === '/' && request_path() !== '/') {
        $target = rtrim($uriPath, '/');
        $qs = isset($_SERVER['QUERY_STRING']) ? (string) $_SERVER['QUERY_STRING'] : '';
        if ($qs !== '') {
            $target .= '?' . $qs;
        }
        header('Location: ' . $target, true, 301);
        exit;
    }
}

$router = new Router();
$router->get('/', ['SiteController', 'home']);
$router->get('/about', ['SiteController', 'about']);
$router->get('/services', ['SiteController', 'services']);
$router->get('/services/{slug}', ['SiteController', 'service']);
$router->get('/branches', ['SiteController', 'branches']);
$router->get('/gallery', ['SiteController', 'gallery']);
$router->get('/blog', ['SiteController', 'blog']);
$router->get('/blog/{slug}', ['SiteController', 'post']);
$router->get('/contact', ['SiteController', 'contact']);
$router->post('/contact', ['SiteController', 'contactSubmit']);
$router->get('/quote', ['SiteController', 'quoteRedirect']);
$router->get('/faq', ['SiteController', 'faq']);
$router->get('/privacy', ['SiteController', 'privacy']);
$router->get('/terms', ['SiteController', 'terms']);
$router->get('/sitemap.xml', ['SiteController', 'sitemap']);
$router->get('/robots.txt', ['SiteController', 'robots']);

$router->get('/admin/login', ['AdminController', 'loginForm']);
$router->post('/admin/login', ['AdminController', 'login']);
$router->post('/admin/logout', ['AdminController', 'logout']);
$router->get('/admin', ['AdminController', 'dashboard']);
$router->get('/admin/settings', ['AdminController', 'settingsForm']);
$router->post('/admin/settings', ['AdminController', 'settingsSave']);

$resources = [
    'services' => 'service',
    'packages' => 'package',
    'branches' => 'branch',
    'gallery' => 'gallery',
    'faqs' => 'faq',
    'posts' => 'post',
    'pages' => 'page',
    'users' => 'user',
    'testimonials' => 'testimonial',
];
foreach ($resources as $plural => $single) {
    $router->get('/admin/' . $plural, ['AdminController', $plural]);
    $router->get('/admin/' . $plural . '/new', ['AdminController', $single . 'Form']);
    $router->post('/admin/' . $plural . '/new', ['AdminController', $single . 'Save']);
    $router->get('/admin/' . $plural . '/{id}/edit', ['AdminController', $single . 'Form']);
    $router->post('/admin/' . $plural . '/{id}/edit', ['AdminController', $single . 'Save']);
    $router->post('/admin/' . $plural . '/{id}/delete', ['AdminController', $single . 'Delete']);
}

$router->get('/admin/enquiries', ['AdminController', 'enquiries']);
$router->get('/admin/enquiries/{id}', ['AdminController', 'enquiry']);
$router->post('/admin/enquiries/{id}/status', ['AdminController', 'enquiryStatus']);
$router->post('/admin/enquiries/{id}/delete', ['AdminController', 'enquiryDelete']);
$router->get('/{slug}', ['SiteController', 'customPage']);

$router->dispatch($method, request_path());
