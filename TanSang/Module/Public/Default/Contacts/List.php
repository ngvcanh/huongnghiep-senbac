<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\Session;
use Library\URL;

$myid = $myAccount->_id->__toString();

foreach($listAccounts as $account){
    $accid = $account->_id->__toString();
    if ($accid == $myid) $account->selected = 'selected';
    
    $account->id = $accid;
    $tpl->assign($account, 'listaccounts');
}

$limit = 20;
$totalContacts = $model->countAllContacts($myAccount);
$totalPage = ceil($totalContacts / $limit);

$listContacts = $model->getAllContacts($myAccount);
foreach($listContacts as $contact){
    $contact->url_activity = URL::create(['contacts', 'activity', $contact->_id->__toString()]);
    $tpl->assign($contact, 'listcontacts');
}

$strToken = $token->generate(32);
Session::add($TOKEN_CREATE_NAME, $strToken);
$tpl->merge($strToken, $TOKEN_CREATE_NAME);
$tpl->merge($COMMIT_CREATE_NAME, 'contacts_create_commit');

$tpl->setFile([
    'content' => 'contacts/list',
    'right_sidebar' => 'contacts/right-sidebar.create',
    'sub_css' => 'contacts/list/css',
    'inc_script' => 'contacts/list/inc',
    'sub_script' => 'contacts/list/script'
]);