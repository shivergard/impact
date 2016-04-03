<?php

namespace App\Helpers;


/**

Agency :

    Agency will send to impact msg to request new task :
    Message form {'ident' : 'candidate'  , 'action' : 'create_test', 'sender' : 'agency'} 

    Agency will create task and respond in queue with ending [{queue}_response]
    Message form : { 'ident'  : 'candidate' , 'test_url' , 'reviewer' : 'agency'}

    Agency will inform about test status change 
    Message form : {'ident'  : 'candidate' , 'status' : 1 , 'reviewer' : 'agency'}


**/

use App\Applicants;

class Agency {

    const STARTED = 1;
    const FINISHED = 2;
    const EXPIRED = 3;

    private function validateApplicant($applicantModel){
        
        $return = false;

        if ($applicantModel->status == 1 || Carbon::now()->diffInHours($applicantModel->created_at) <= 48){
            $return = true;
        }

        return $return;
    }

    private function prepareTest($request){

        //check maybe we have this test

        $valid = true;

        $applicants = Applicants::where('git_url' , $request['ident']);

        if ($applicants->count() > 0){
            // validate applicant
            $applicantModel = $applicants->first();

            if (!$this->validateApplicant($applicantModel)){
                $this->statusMessage(self::EXPIRED);
                $valid = false;
            }

        }else{
            $applicantModel = new Applicants();
            $applicantModel->git_url = $applicantModel->name = $applicantModel->email = $request['ident'];
            $applicantModel->creditals = str_random(10);

            $applicantModel->save();
        }

        if (!$valid){
            return false;
        }

        $testMessage = array(
            'ident'  => $request['ident'] , 
            'test_url' => Config::get('app.url').'/tst/'.$applicantModel->creditals , 
            'reviewer' => $request['sender']
        );

        return $testMessage;


    }

}