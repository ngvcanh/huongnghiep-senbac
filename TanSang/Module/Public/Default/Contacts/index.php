<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;
use Library\URL;

if ($action == 'Home') URL::redirect(URL::create(['contacts', 'list']));

$access_action = ['Activity', 'Create', 'List'];
if (!in_array($action, $access_action)) return;

$urlCreate = URL::create(['contacts', 'create']);
$tpl->merge($urlCreate, 'url_contacts_create');

$myAdmin = $myAccount->admin_id;
$listAccounts = $model->getListAccount($myAdmin);

$TOKEN_CREATE_NAME = 'contacts_create_token';
$COMMIT_CREATE_NAME = 'Contacts_Create';

$actionPath = $moduleDir . $action . '.php';
if (!File::isFile($actionPath)) return;
include_once $actionPath;