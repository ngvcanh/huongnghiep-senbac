<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\Session;

$id = NULL;
if (isset($dirURL[2])){
    $temp = $dirURL[2];
    
    if ($model->validId($temp) && $model->existContactId($temp)){
        $id = $temp;
    }
}

$tpl->setFile([
    'content' => 'error',
    //'right_sidebar' => 'contacts/right-sidebar.create',
    //'sub_css' => 'contacts/list.css',
    //'inc_script' => 'contacts/list.inc',
    //'sub_script' => 'contacts/list.script'
]);
$tpl->merge('This contact is not exist.', 'error_content');

if ($id !== NULL){
    $tpl->merge('You have not permission to view this contact.', 'error_content');
    $contact = $model->getContactById($id);

    $myStrAdmin = $myAccount->admin_id->__toString();
    $contactStrAdmin = $contact->admin_id->__toString();

    if ($contactStrAdmin == $myStrAdmin){
        $myStrId = $myAccount->_id->__toString();
        $isAdmin = ($myStrAdmin == $contactStrAdmin);

        $groupStrContact = NULL;
        $hasGroup = ($contact->group_id != 0);
        if ($hasGroup) $groupStrContact = $contact->group_id->__toString();

        $myGroup = NULL;
        if ($myAccount->group_id != 0) $myGroup = $myAccount->group_id->__toString();
        $isMod = ($myAccount->is_moderator == 1 && $hasGroup && $myGroup == $groupStrContact);
        
        $createdStr = $contact->created_by->__toString();
        $myContact = ($myStrId == $createdStr);

        if ($isAdmin || $isMod || $myContact){
            $listActivity = $model->getPageActivity($contact->_id);

            foreach($listActivity as $activity){
                $createdBy = $model->getAccountById($activity->created_by);
                $activity->created_name = "{$createdBy->firstname} {$createdBy->lastname}";

                if (isset($createdBy->avatar)) $activity->created_avatar = $createdBy->avatar;
                $activity->created_date = date('d-m-Y H:i', $activity->created_at);

                switch($activity->type){
                    case 0:
                        $activity->isCreate = true;
                        break;
                    case 1:
                        $activity->isAddOwner = true;
                        break;
                    case 2:
                    case 3:
                }

                $tpl->assign($activity, 'activities');
            }

            $tpl->setFile([
                'content'           => 'contacts/activity',
                'activity.left'     => 'contacts/activity/left',
                'activity.right'    => 'contacts/activity/right',
                'sub_css'           => 'contacts/activity/css',
                'inc_script'        => 'contacts/activity/inc',
                'sub_script'        => 'contacts/activity/script',
                'acti_create'       => 'contacts/activity/timeline/create',
                'acti_addowner'     => 'contacts/activity/timeline/addowner'
            ]);
            $tpl->merge($contact, 'contact');

            foreach($listAccounts as $account){
                $accid = $account->_id->__toString();
                if ($accid == $createdStr) $account->selected = 'selected';
                
                $account->id = $accid;
                $tpl->assign($account, 'listaccounts');
            }
        }
    }
}