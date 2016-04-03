<?php

namespace App\Helpers;


/**

Agency :

    Agency will send to impact msg to request new task :
    Message form {'ident' : 'candidate'  , 'sender' : 'agency'}

    Agency will create task and respond in queue with ending [{queue}_response]
    Message form : { 'ident'  : 'candidate' , 'test_url' , 'reviewer' : 'agency'}

    Agency will inform about test status change 
    Message form : {'ident'  : 'candidate' , 'status' : 1 , 'reviewer' : 'agency'}


**/


class Agency {


}