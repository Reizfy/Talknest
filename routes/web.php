<?php

// Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Security\RolePermission;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuperAdminUser;
use App\Http\Controllers\SuperAdminNest;
use App\Http\Controllers\SuperAdminPost;
use App\Http\Controllers\SuperAdminComment;
use App\Http\Controllers\NestController;
use Illuminate\Support\Facades\Artisan;
// Packages
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__.'/auth.php';

Route::get('/storage', function () {
    Artisan::call('storage:link');
});

//UI Pages Routs

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::redirect('/home', '/');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/documentation', [HomeController::class, 'uisheet'])->name('uisheet');

    // Permission Module
    Route::get('/role-permission',[RolePermission::class, 'index'])->name('role.permission.list');
    Route::resource('permission',PermissionController::class);
    Route::resource('role', RoleController::class);

    // Users Module
    Route::resource('users', UserController::class);
});

//App Details Page => 'Dashboard'], function() {
Route::group(['prefix' => 'menu-style'], function() {
    //MenuStyle Page Routs
    Route::get('horizontal', [HomeController::class, 'horizontal'])->name('menu-style.horizontal');
    Route::get('dual-horizontal', [HomeController::class, 'dualhorizontal'])->name('menu-style.dualhorizontal');
    Route::get('dual-compact', [HomeController::class, 'dualcompact'])->name('menu-style.dualcompact');
    Route::get('boxed', [HomeController::class, 'boxed'])->name('menu-style.boxed');
    Route::get('boxed-fancy', [HomeController::class, 'boxedfancy'])->name('menu-style.boxedfancy');
});

//App Details Page => 'special-pages'], function() {
Route::group(['prefix' => 'special-pages'], function() {
    //Example Page Routs
    Route::get('billing', [HomeController::class, 'billing'])->name('special-pages.billing');
    Route::get('calender', [HomeController::class, 'calender'])->name('special-pages.calender');
    Route::get('kanban', [HomeController::class, 'kanban'])->name('special-pages.kanban');
    Route::get('pricing', [HomeController::class, 'pricing'])->name('special-pages.pricing');
    Route::get('rtl-support', [HomeController::class, 'rtlsupport'])->name('special-pages.rtlsupport');
    Route::get('timeline', [HomeController::class, 'timeline'])->name('special-pages.timeline');
});

//Widget Routs
Route::group(['prefix' => 'widget'], function() {
    Route::get('widget-basic', [HomeController::class, 'widgetbasic'])->name('widget.widgetbasic');
    Route::get('widget-chart', [HomeController::class, 'widgetchart'])->name('widget.widgetchart');
    Route::get('widget-card', [HomeController::class, 'widgetcard'])->name('widget.widgetcard');
});

//Maps Routs
Route::group(['prefix' => 'maps'], function() {
    Route::get('google', [HomeController::class, 'google'])->name('maps.google');
    Route::get('vector', [HomeController::class, 'vector'])->name('maps.vector');
});

//Auth pages Routs
Route::group(['prefix' => 'auth'], function() {
    Route::get('signin', [HomeController::class, 'signin'])->name('auth.signin');
    Route::get('signup', [HomeController::class, 'signup'])->name('auth.signup');
    Route::get('confirmmail', [HomeController::class, 'confirmmail'])->name('auth.confirmmail');
    Route::get('lockscreen', [HomeController::class, 'lockscreen'])->name('auth.lockscreen');
    Route::get('recoverpw', [HomeController::class, 'recoverpw'])->name('auth.recoverpw');
    Route::get('userprivacysetting', [HomeController::class, 'userprivacysetting'])->name('auth.userprivacysetting');
});

// Nests interactive routes: redirect guests to register
Route::get('/nests/create', function() {
    return auth()->check() ? app(App\Http\Controllers\NestController::class)->create() : redirect()->route('register');
})->name('nests.create');

// Nests routes
Route::get('/nests/{name}', [App\Http\Controllers\NestController::class, 'index'])->name('nests.index');

Route::get('/nests/{name}/posts', [App\Http\Controllers\NestController::class, 'posts'])->name('nests.posts');

Route::post('/nests/{nest}/create-posts', [App\Http\Controllers\NestController::class, 'storePost'])->name('posts.store');

Route::get('/nests/{name}/comments/{post_id}', [App\Http\Controllers\NestController::class, 'comments'])->name('nests.comments');

Route::post('/posts/{id}/vote', function($id) {
    return auth()->check() ? app(App\Http\Controllers\NestController::class)->vote(request(), $id) : redirect()->route('register');
})->name('posts.vote');

// Comment and reply route
Route::post('/posts/{post}/comment', [App\Http\Controllers\CommentController::class, 'store'])->name('posts.comment');

Route::post('/nests-store', function() {
    return auth()->check() ? app(App\Http\Controllers\NestController::class)->store(request()) : redirect()->route('register');
})->name('nests.store');

//Explicit update route for nests
Route::put('nests/{nest}', [App\Http\Controllers\NestController::class, 'update'])->name('nests.update');

// Edit nest route
Route::get('nests/{nest}/edit', [App\Http\Controllers\NestController::class, 'edit'])->name('nests.edit');


// Promote member to moderator
Route::post('/nests/{nest}/{user}/promote', [App\Http\Controllers\NestController::class, 'promote'])->name('nests.promote');
// Kick member from nest
Route::delete('/nests/{nest}/{user}/kick', [App\Http\Controllers\NestController::class, 'kick'])->name('nests.kick');
Route::delete('/nests/{name}', [App\Http\Controllers\NestController::class, 'destroy'])->name('nests.destroy');


//Error Page Route
Route::group(['prefix' => 'errors'], function() {
    Route::get('error404', [HomeController::class, 'error404'])->name('errors.error404');
    Route::get('error500', [HomeController::class, 'error500'])->name('errors.error500');
    Route::get('maintenance', [HomeController::class, 'maintenance'])->name('errors.maintenance');
});


//Forms Pages Routs
Route::group(['prefix' => 'forms'], function() {
    Route::get('element', [HomeController::class, 'element'])->name('forms.element');
    Route::get('wizard', [HomeController::class, 'wizard'])->name('forms.wizard');
    Route::get('validation', [HomeController::class, 'validation'])->name('forms.validation');
});


//Table Page Routs
Route::group(['prefix' => 'table'], function() {
    Route::get('bootstraptable', [HomeController::class, 'bootstraptable'])->name('table.bootstraptable');
    Route::get('datatable', [HomeController::class, 'datatable'])->name('table.datatable');
});

//Icons Page Routs
Route::group(['prefix' => 'icons'], function() {
    Route::get('solid', [HomeController::class, 'solid'])->name('icons.solid');
    Route::get('outline', [HomeController::class, 'outline'])->name('icons.outline');
    Route::get('dualtone', [HomeController::class, 'dualtone'])->name('icons.dualtone');
    Route::get('colored', [HomeController::class, 'colored'])->name('icons.colored');
});

// Superadmin (admin-only) routes
Route::middleware(['auth', 'admin'])->prefix('superadmin')->group(function() {
    Route::get('users', [SuperAdminUser::class, "index"])->name('superadmin.users');
    Route::get('users/{id}/edit', [SuperAdminUser::class, "edit"])->name('superadmin.users.edit');
    Route::put('users/{id}', [SuperAdminUser::class, "update"])->name('superadmin.users.update');
    Route::delete('users/{id}', [SuperAdminUser::class, "destroy"])->name('superadmin.users.destroy');

    Route::get('nests', [SuperAdminNest::class, "index"])->name('superadmin.nests');
    Route::get('nests/{id}/edit', [SuperAdminNest::class, "edit"])->name('superadmin.nests.edit');
    Route::put('nests/{id}', [SuperAdminNest::class, "update"])->name('superadmin.nests.update');
    Route::delete('nests/{id}', [SuperAdminNest::class, "destroy"])->name('superadmin.nests.destroy');

    Route::get('posts', [SuperAdminPost::class, "index"])->name('superadmin.posts');
    Route::get('posts/{id}/edit', [SuperAdminPost::class, "edit"])->name('superadmin.posts.edit');
    Route::put('posts/{id}', [SuperAdminPost::class, "update"])->name('superadmin.posts.update');
    Route::delete('posts/{id}', [SuperAdminPost::class, "destroy"])->name('superadmin.posts.destroy');

    Route::get('comments', [SuperAdminComment::class, "index"])->name('superadmin.comments');
    Route::get('comments/{id}/edit', [SuperAdminComment::class, "edit"])->name('superadmin.comments.edit');
    Route::put('comments/{id}', [SuperAdminComment::class, "update"])->name('superadmin.comments.update');
    Route::delete('comments/{id}', [SuperAdminComment::class, "destroy"])->name('superadmin.comments.destroy');
    
    Route::view('logs', 'superadmin.logs')->name('superadmin.logs');
});

//Extra Page Routs
Route::get('privacy-policy', [HomeController::class, 'privacypolicy'])->name('pages.privacy-policy');
Route::get('terms-of-use', [HomeController::class, 'termsofuse'])->name('pages.term-of-use');
