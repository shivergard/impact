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

                $deadline =  Carbon::now()->addDays(2);

                return view('welcome' , array('deadline' => $deadline , 'identified' => true));

            }


        }

        return redirect()->action('ImpactController@fail');
        
    }


    public function ajaxResponseGet(){
        $this->rabbit = RabbitMq::makeConnection(Config::get('impact'));

        $this->interest = new Interest();

        if ($this->rabbit->getStatus()){

            $this->rabbit->getNewsSolved(
                array($this, 'prepareMessageResponse')
            );

            while (count($this->rabbit->getChannel()->callbacks) > 0) {
                $this->rabbit->getChannel()->wait();
            }

        }

        return response()->json(array('status' => 0));
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
