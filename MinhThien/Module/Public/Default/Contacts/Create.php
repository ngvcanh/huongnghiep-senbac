<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\URL;
use Library\Validate;
use Library\Session;
use Application\Response;
use MongoDB\Driver\WriteResult;

if (validate_token_commit($post, $TOKEN_CREATE_NAME, $COMMIT_CREATE_NAME)){
    $response = ['message' => 'Data invalid.', 'status' => 'error'];

    $rules = [
        'email' => ['type' => 'email', 'min' => 7, 'max' => 100],
        'firstname' => ['type' => 'string', 'min' => 1, 'max' => 100],
        'lastname' => ['type' => 'string', 'min' => 1, 'max' => 100],
        'owner_id' => ['type' => 'string', 'base' => '/^0|([a-f\d]{24})$/'],
        'job_title' => ['type' => 'string', 'min' => 0, 'max' => 200]
    ];
    $columns = array_keys($rules);

    $validate = Validate::getInstance($rules, $columns)->setSource($post);
    $data = $validate->run();

    if ($validate->isFullValid()){
        $response['message'] = 'Email already used.';
        $email = $data['email'];
        $myAdmin = $myAccount->admin_id;

        if (!$model->usedEmail($email, $myAdmin)){
            $response['message'] = 'Owner not exist.';
            $ownid = $data['owner_id'];

            if ($ownid == 0 || $model->validOwner($ownid, $myAdmin)){
                $data['admin_id'] = $myAdmin;
                $data['created_by'] = $myAccount->_id;
                $data['group_id'] = 0;

                if ($myAdmin->__toString() != $myAccount->_id->__toString()){
                    $data['group_id'] = $myAccount->group_id;
                }

                $result = $model->createContact($data);
                $response['message'] = 'Cannot create contact. Please try again.';
                
                if ($model->isDBResult($result) && $result->getInsertedCount() == 1){
                    $contact = $model->getContactByEmail($email, $myAdmin);
                    $data = [
                        'contact_id' => $contact->_id,
                        'type' => 0,
                        'created_by' => $myAccount->_id,
                        'source' => 0,
                        'admin_id' => $myAdmin
                    ];
                    $model->createTimeline($data);

                    $data = [
                        'contact_id' => $contact->_id,
                        'type' => 1,
                        'created_by' => $myAccount->_id,
                        'source' => 0,
                        'admin_id' => $myAdmin
                    ];
                    $model->createTimeline($data);
                    
                    Session::cancel($TOKEN_CREATE_NAME);
                    $response = ['message' => 'Create contact successfully.', 'status' => 'success'];
                }
            }
        }
    }

    $this->response = $response;
    new Response('Content-Type: application/json', function(){
        return $this->response;
    });
}