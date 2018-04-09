<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\TB;

class Contacts extends PublicModel{

    function __construct(){
        parent::__construct();
    }

    function usedEmail($email, $myAdmin){
        $condition = ['email' => $email, 'admin_id' => $myAdmin];
        $count = $this->count(TB::CONTACTS, $condition);
        return $count > 0;
    }

    function validOwner($ownid, $myAdmin){
        $ownid = $this->parseId($ownid);
        $account = $this->getOne(TB::ACCOUNTS, ['_id' => $ownid]);

        if (isset($account->admin_id) && isset($account->actived)){
            $strAdmin = $myAdmin->__toString();
            $accAdmin = $account->admin_id->__toString();
            return $strAdmin == $accAdmin && $account->actived == 1;
        }

        return false;
    }

    function createContact(array $data){
        $data['created_at'] = time();
        $data = array_intersect_key($data, array_flip(TB::FIELD_CONTACTS));
        $result = null;

        if (!empty($data)) $result = $this->insert(TB::CONTACTS, $data);
        return $result;
    }

    function getContactByEmail($email, $myAdmin){
        $condition = ['email' => $email, 'admin_id' => $myAdmin];
        return $this->getOne(TB::CONTACTS, $condition);
    }

    function createTimeline(array $data){
        $data['created_at'] = time();
        $data = array_intersect_key($data, array_flip(TB::FIELD_TIMELINE_CONTACTS));
        $result = null;

        if (!empty($data)) $result = $this->insert(TB::TIMELINE_CONTACTS, $data);
        return $result;
    }

    function countAllContacts(stdClass $myAccount){
        $condition = $this->privilegeContacts($myAccount);
        return $this->count(TB::CONTACTS, $condition);
    }

    function getAllContacts(stdClass $myAccount){
        $condition = $this->privilegeContacts($myAccount);
        return $this->getMany(TB::CONTACTS, $condition);
    }

    function getPageActivity($contactid, $options = []){
        $condition = ['contact_id' => $contactid];
        $options['sort'] = ['_id' => -1];
        return $this->getMany(TB::TIMELINE_CONTACTS, $condition, [], $options);
    }

    function getAccountById($id){
        return $this->getOne(TB::ACCOUNTS, ['_id' => $id]);
    }

    function existContactId($id){
        $condition = ['_id' => $this->parseId($id)];
        $count = $this->count(TB::CONTACTS, $condition);
        return $count > 0;
    }

    function getContactById($id){
        return $this->getOne(TB::CONTACTS, ['_id' => $this->parseId($id)]);
    }

    private function privilegeContacts(stdClass $myAccount){
        $myAdmin = $myAccount->admin_id->__toString();
        $myId = $myAccount->_id->__toString();
        $condition = ['admin_id' => $myAccount->admin_id];

        if ($myAdmin != $myId){
            if ($myAccount->is_moderator == 1) $condition['group_id'] = $myAccount->group_id;
            else $condition['created_by'] = $myAccount->_id;
        }
        return $condition;
    }
}