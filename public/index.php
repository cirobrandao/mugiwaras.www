<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Core\Router;
use App\Core\Request;
use App\Core\Auth;
use App\Core\Response;

$debug = (bool)config('app.debug', false);
if ($debug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

$security = config('security');

session_name((string)$security['session_cookie']);
session_set_cookie_params([
    'path' => base_path('/'),
    'secure' => (bool)$security['session_secure'],
    'httponly' => true,
    'samesite' => (string)$security['session_samesite'],
]);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

foreach ($security['headers'] as $k => $v) {
    header($k . ': ' . $v);
}

$request = new Request();
Auth::checkRemember($request);

$router = new Router();

$router->get('/assets/bootstrap.min.css', function (): void {
    $path = dirname(__DIR__) . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: text/css; charset=utf-8');
    readfile($path);
});

$router->get('/assets/bootstrap.bundle.min.js', function (): void {
    $path = dirname(__DIR__) . '/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: application/javascript; charset=utf-8');
    readfile($path);
});

$router->get('/', [new App\Controllers\AuthController(), 'loginForm']);
$router->post('/login', [new App\Controllers\AuthController(), 'login']);
$router->get('/register', [new App\Controllers\AuthController(), 'registerForm']);
$router->post('/register/accept', [new App\Controllers\AuthController(), 'acceptTerms']);
$router->post('/register', [new App\Controllers\AuthController(), 'register']);
$router->get('/logout', [new App\Controllers\AuthController(), 'logout']);

$router->get('/support', [new App\Controllers\SupportController(), 'form']);
$router->post('/support', [new App\Controllers\SupportController(), 'submit']);
$router->get('/support/track/{token}', [new App\Controllers\SupportController(), 'track']);
$router->post('/support/track/{token}/reply', [new App\Controllers\SupportController(), 'replyGuest']);
$router->get('/support/{id}', [new App\Controllers\SupportController(), 'show']);
$router->post('/support/{id}/reply', [new App\Controllers\SupportController(), 'reply']);

$router->get('/dashboard', [new App\Controllers\DashboardController(), 'index'], [App\Core\Auth::requireRole(['user','admin','equipe','superadmin']), App\Core\Middleware::requireActiveAccess()]);

$router->get('/loja', [new App\Controllers\PaymentController(), 'packages']);
$router->get('/loja/checkout/{id}', [new App\Controllers\PaymentController(), 'checkout']);
$router->post('/loja/request', [new App\Controllers\PaymentController(), 'requestPayment']);
$router->post('/loja/voucher', [new App\Controllers\PaymentController(), 'redeemVoucher']);
$router->get('/loja/history', [new App\Controllers\PaymentController(), 'history']);
$router->post('/loja/proof', [new App\Controllers\PaymentController(), 'uploadProof']);

$router->get('/libraries', [new App\Controllers\LibraryController(), 'index'], [App\Core\Middleware::requireActiveAccess()]);
$router->get('/libraries/search', [new App\Controllers\LibraryController(), 'search'], [App\Core\Middleware::requireActiveAccess()]);
$router->get('/libraries/{category}', [new App\Controllers\LibraryController(), 'category'], [App\Core\Middleware::requireActiveAccess()]);
$router->get('/libraries/{category}/{series}', [new App\Controllers\LibraryController(), 'series'], [App\Core\Middleware::requireActiveAccess()]);

$router->get('/upload', [new App\Controllers\UploadController(), 'form'], [App\Core\Auth::requireUploadAccess(), App\Core\Middleware::requireActiveAccess()]);
$router->post('/upload', [new App\Controllers\UploadController(), 'submit'], [App\Core\Auth::requireUploadAccess(), App\Core\Middleware::requireActiveAccess()]);
$router->post('/upload/process-pending', [new App\Controllers\UploadController(), 'processPending'], [App\Core\Auth::requireUploadAccess(), App\Core\Middleware::requireActiveAccess()]);


$router->get('/libraries', [new App\Controllers\LibraryController(), 'index'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->get('/libraries/{category}', [new App\Controllers\LibraryController(), 'category'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->get('/libraries/{category}/{series}', [new App\Controllers\LibraryController(), 'series'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->post('/libraries/content/update', [new App\Controllers\LibraryController(), 'updateContent'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->post('/libraries/content/order', [new App\Controllers\LibraryController(), 'updateContentOrder'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->post('/libraries/content/delete', [new App\Controllers\LibraryController(), 'deleteContent'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->post('/libraries/favorite', [new App\Controllers\LibraryController(), 'toggleFavorite'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->post('/libraries/read', [new App\Controllers\LibraryController(), 'toggleRead'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->post('/libraries/progress', [new App\Controllers\LibraryController(), 'updateProgress'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->post('/libraries/series/favorite', [new App\Controllers\LibraryController(), 'toggleSeriesFavorite'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->post('/libraries/series/update', [new App\Controllers\LibraryController(), 'updateSeries'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->post('/libraries/series/pin', [new App\Controllers\LibraryController(), 'pinSeries'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->post('/libraries/series/adult', [new App\Controllers\LibraryController(), 'updateSeriesAdult'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->post('/libraries/series/delete', [new App\Controllers\LibraryController(), 'deleteSeries'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);

$router->get('/reader/{id}', [new App\Controllers\ReaderController(), 'open'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->get('/pdf/{id}', [new App\Controllers\ReaderController(), 'pdfOpen'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->get('/reader/{id}/page/{page}', [new App\Controllers\ReaderController(), 'page'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->get('/download/{id}', [new App\Controllers\ReaderController(), 'download'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->get('/reader/file', [new App\Controllers\ReaderController(), 'openFile'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->get('/reader/file/page/{page}', [new App\Controllers\ReaderController(), 'pageFile'], [App\Core\Middleware::requireAuth(), App\Core\Middleware::requireActiveAccess()]);
$router->get('/reader/file', [new App\Controllers\ReaderController(), 'openFile'], [App\Core\Middleware::requireActiveAccess()]);
$router->get('/reader/file/page/{page}', [new App\Controllers\ReaderController(), 'pageFile'], [App\Core\Middleware::requireActiveAccess()]);

$router->get('/admin', [new App\Controllers\Admin\DashboardController(), 'index'], [App\Core\Auth::requireTeamAccess()]);
$router->get('/admin/users', [new App\Controllers\Admin\UsersController(), 'index'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/users/update', [new App\Controllers\Admin\UsersController(), 'update'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/users/restrict', [new App\Controllers\Admin\UsersController(), 'restrict'], [App\Core\Auth::requireAdmin()]);
$router->get('/admin/team', [new App\Controllers\Admin\UsersController(), 'team'], [App\Core\Auth::requireTeamAccess()]);
$router->post('/admin/team/update', [new App\Controllers\Admin\UsersController(), 'teamUpdate'], [App\Core\Auth::requireTeamAccess()]);
$router->post('/admin/users/lock', [new App\Controllers\Admin\UsersController(), 'lock'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/users/unlock', [new App\Controllers\Admin\UsersController(), 'unlock'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/users/reset', [new App\Controllers\Admin\UsersController(), 'reset'], [App\Core\Auth::requireAdmin()]);
$router->get('/admin/packages', [new App\Controllers\Admin\PackagesController(), 'index'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/packages/create', [new App\Controllers\Admin\PackagesController(), 'create'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/packages/update', [new App\Controllers\Admin\PackagesController(), 'update'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/packages/delete', [new App\Controllers\Admin\PackagesController(), 'delete'], [App\Core\Auth::requireAdmin()]);
$router->get('/admin/payments', [new App\Controllers\Admin\PaymentsController(), 'index'], [App\Core\Auth::requireAdmin()]);
$router->get('/admin/payments/proof/{id}', [new App\Controllers\Admin\PaymentsController(), 'proof'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/payments/approve', [new App\Controllers\Admin\PaymentsController(), 'approve'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/payments/reject', [new App\Controllers\Admin\PaymentsController(), 'reject'], [App\Core\Auth::requireAdmin()]);
$router->get('/admin/support', [new App\Controllers\Admin\SupportController(), 'index'], [App\Core\Auth::requireSupportStaff()]);
$router->get('/admin/support/{id}', [new App\Controllers\Admin\SupportController(), 'show'], [App\Core\Auth::requireSupportStaff()]);
$router->get('/admin/uploads', [new App\Controllers\Admin\UploadsController(), 'index'], [App\Core\Auth::requireTeamAccess()]);
$router->post('/admin/uploads/update', [new App\Controllers\Admin\UploadsController(), 'update'], [App\Core\Auth::requireTeamAccess()]);
$router->post('/admin/uploads/approve', [new App\Controllers\Admin\UploadsController(), 'approve'], [App\Core\Auth::requireTeamAccess()]);
$router->post('/admin/uploads/approve-multiple', [new App\Controllers\Admin\UploadsController(), 'approveMultiple'], [App\Core\Auth::requireTeamAccess()]);
$router->post('/admin/uploads/delete-multiple', [new App\Controllers\Admin\UploadsController(), 'deleteMultiple'], [App\Core\Auth::requireTeamAccess()]);
$router->post('/admin/uploads/delete', [new App\Controllers\Admin\UploadsController(), 'delete'], [App\Core\Auth::requireTeamAccess()]);
$router->post('/admin/support/status', [new App\Controllers\Admin\SupportController(), 'status'], [App\Core\Auth::requireSupportStaff()]);
$router->post('/admin/support/note', [new App\Controllers\Admin\SupportController(), 'note'], [App\Core\Auth::requireSupportStaff()]);
$router->post('/admin/support/{id}/reply', [new App\Controllers\Admin\SupportController(), 'reply'], [App\Core\Auth::requireSupportStaff()]);
$router->get('/admin/security/email-blocklist', [new App\Controllers\Admin\SecurityController(), 'emailBlocklist'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/security/email-blocklist/add', [new App\Controllers\Admin\SecurityController(), 'emailAdd'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/security/email-blocklist/remove', [new App\Controllers\Admin\SecurityController(), 'emailRemove'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/security/user-blocklist/add', [new App\Controllers\Admin\SecurityController(), 'userBlockAdd'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/security/user-blocklist/remove', [new App\Controllers\Admin\SecurityController(), 'userBlockRemove'], [App\Core\Auth::requireAdmin()]);
$router->get('/admin/settings', [new App\Controllers\Admin\SettingsController(), 'index'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/settings/save', [new App\Controllers\Admin\SettingsController(), 'save'], [App\Core\Auth::requireAdmin()]);

$router->get('/admin/vouchers', [new App\Controllers\Admin\VouchersController(), 'index'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/vouchers/save', [new App\Controllers\Admin\VouchersController(), 'save'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/vouchers/remove', [new App\Controllers\Admin\VouchersController(), 'remove'], [App\Core\Auth::requireAdmin()]);

$router->get('/admin/news', [new App\Controllers\Admin\NewsController(), 'index'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/news/create', [new App\Controllers\Admin\NewsController(), 'create'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/news/update', [new App\Controllers\Admin\NewsController(), 'update'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/news/delete', [new App\Controllers\Admin\NewsController(), 'delete'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/news/category/create', [new App\Controllers\Admin\NewsController(), 'createCategory'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/news/category/update', [new App\Controllers\Admin\NewsController(), 'updateCategory'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/news/category/delete', [new App\Controllers\Admin\NewsController(), 'deleteCategory'], [App\Core\Auth::requireAdmin()]);

$router->get('/admin/categories', [new App\Controllers\Admin\CategoriesController(), 'index'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/categories/create', [new App\Controllers\Admin\CategoriesController(), 'create'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/categories/update', [new App\Controllers\Admin\CategoriesController(), 'update'], [App\Core\Auth::requireAdmin()]);
$router->post('/admin/categories/delete', [new App\Controllers\Admin\CategoriesController(), 'delete'], [App\Core\Auth::requireAdmin()]);

$router->get('/reset', [new App\Controllers\AuthController(), 'resetForm']);
$router->post('/reset', [new App\Controllers\AuthController(), 'resetSubmit']);

$router->get('/perfil', [new App\Controllers\ProfileController(), 'show'], [App\Core\Middleware::requireAuth()]);
$router->get('/perfil/editar', [new App\Controllers\ProfileController(), 'editForm'], [App\Core\Middleware::requireAuth()]);
$router->get('/perfil/senha', [new App\Controllers\ProfileController(), 'passwordForm'], [App\Core\Middleware::requireAuth()]);

$router->dispatch($request);
