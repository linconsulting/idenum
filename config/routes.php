<?php
/**
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Action\HomePageAction::class, 'home');
 * $app->post('/album', App\Action\AlbumCreateAction::class, 'album.create');
 * $app->put('/album/:id', App\Action\AlbumUpdateAction::class, 'album.put');
 * $app->patch('/album/:id', App\Action\AlbumUpdateAction::class, 'album.patch');
 * $app->delete('/album/:id', App\Action\AlbumDeleteAction::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Action\ContactAction::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Action\ContactAction::class)->setName('contact');
 *
 */

$app->get('/', App\Action\LoginAction::class, 'root');
$app->get('/index.php', App\Action\LoginAction::class, 'home');

$app->post('/pardat', App\Action\LoginAction::class, 'pars_data');

$app->post('/ajx', App\Action\AjaxProvider::class, 'ajax_provider');

$app->get('/test', App\Action\TestAction::class, 'test');
