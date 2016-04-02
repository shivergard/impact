<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\Applicants;

use App\Helpers\RabbitMq;
use App\Helpers\Interest;

use Config;
use Cache;
use Cookie;

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

        if ($applicantModel->status == 1 || Carbon::now()->diffInHours($applicantModel->created_at) <= 48){
            return redirect()->action('ImpactController@applicant' , array('ident' => $applicantModel->creditals ));
        }else{
            return redirect()->action('ImpactController@fail', array('hours' => Carbon::now()->diffInHours($applicantModel->created_at)));
        }

    }

    public function fail($hours = false){
        return 'You have no rights to be here ...';
    }

    public function applicant($ident){

        $applicants = Applicants::where('creditals' , $ident); 

        if ($applicants->count() > 0){

            $applicantsModel = $applicants->first();

            if ($applicantsModel->status == 1 || Carbon::now()->diffInHours($applicantsModel->created_at) <= 48){

                $deadline =  $applicantsModel->created_at->addHours(48);

                return view('welcome' , array('deadline' => $deadline , 'identified' => true));

            }


        }

        return redirect()->action('ImpactController@fail' , array('hours' => $ident));
        
    }


    public function ajaxResponseGet(){

        $return = array(
            'status' => 0 , 
            'upd' => Cache::get('last_updated') , 
            'cookie' => Cookie::get(md5(Cache::get('last_updated'))) , 
            'key' => md5(Cache::get('last_updated'))
        );

        $last_updated = false;

        if (
            Cache::get('last_updated') && 
            !Cookie::get(md5(Cache::get('last_updated')))
            ){
            $last_updated = Cookie::make(md5(Cache::get('last_updated')), 'has', 2);
            Cookie::queue($last_updated);
            $return = json_decode(Cache::get('last_updated'));
            
        }

        $response = response();

        return $response->json($return);
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
