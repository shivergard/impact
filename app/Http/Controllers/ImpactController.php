<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\Applicants;

use App\Helpers\RabbitMq;
use App\Helpers\Interest;

use Config;

class ImpactController extends Controller
{

    public function startImpact(){
        $return = Input::all();

        $applicants = Applicants::where('git_url' , $return['github']);

        if ($applicants->count() > 0){
            $applicantModel = $applicants->first();
        }else{
            $applicantModel = new Applicants();

            $applicantModel->name = $return['name'];
            $applicantModel->email = $return['email'];
            $applicantModel->git_url = $return['github'];

            $applicantModel->creditals = str_random(10);

            $applicantModel->save();            
        }

        if (!($applicantModel->status == 1 || Carbon::now()->diffInHours($applicantModel->created_at) <= 48)){
            return redirect()->action('ImpactController@applicant' , array('ident' , $applicantModel->creditals ));
        }else{
            return redirect()->action('ImpactController@fail');
        }

    }

    public function fail(){
        return 'You have no rights to be here ...';
    }

    public function applicant($ident){

        $applicants = Applicants::where('creditals' , $ident); 

        if ($applicants->count() > 0){

            $applicantsModel = $applicants->first();

            if ($applicantsModel->status == 1 || Carbon::now()->diffInHours($applicantsModel->created_at) <= 48){

                $deadline =  $applicantsModel->created_at->addDays(2);

                return view('welcome' , array('deadline' => $deadline , 'identified' => true));

            }


        }

        return redirect()->action('ImpactController@fail');
        
    }


    public function ajaxResponseGet(){

        $return = array('status' => 0);

        if (Cache::get('last_updated') &&  Cookie::get(md5(Cache::get('last_updated')))){
            Cookie::make(md5(Cache::get('last_updated')), 'has', 10);
            $return = json_decode(Cache::get('last_updated'));
            
        }

        return response()->json($return);
    }

    public function prepareMessageResponse($message = false){

        $return = false;

        if ($message){
            
            $return = json_decode($message->body, true);

            $responsed = false;

            if (json_last_error() == JSON_ERROR_NONE && is_array($return)){

                if ($this->interest->exec($return)){
                    $return['status'] = 1;
                    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']); 
                }else{
                    $return['status'] = 2;
                }

            }else{
                $return = array('status' => 3);
            }

            
        }

        die(json_encode($return));
    }


    public function impact(){

        $return = Input::all();

        return response()->json($return);
    }
}
