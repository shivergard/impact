<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;

class Applicants extends BaseModel {

    /** 
        Status Description
        1 - created
        2 - processed
        3 - post_process
        4 - finished [fcked up status]
    **/

    protected $table = 'test_applicants';

}