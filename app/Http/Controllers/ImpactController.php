<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\Applicants;

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

                $deadline =  (new Carbon('first day of December 2008'))->addDays(2);

                return view('welcome' , array('deadline' => $deadline , 'identified' => true));

            }


        }

        return redirect()->action('ImpactController@fail');
        
    }

    public function impact(){

        $return = Input::all();

        return response()->json($return);
    }
}
