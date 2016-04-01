<?php

namespace App\Helpers;


/**

Interest formula

    Interest is calculated based on sum and days fields
    Interest is calculated per day as a percentage from the original amount
    If day is...
        divisible by three, the interest is: 1%
        divisible by five, the interest is: 2%
        divisible by both three and five, the interest is: 3%
        not divisible by either three or five, interest is: 4%
    Each day interest amount is rounded to two digits [ok]
    Final interest is a sum of all days interests [ok]
    Total sum is the sum of original amount and total interest [ok]


**/


class Interest {

    private function typeDetect($day , $num){
        return (($day / $num) == intval($day / $num) ? true : false);
    }


    private function getInterestPercent($day){

        $percent = 4;

        $type3 = $this->typeDetect($day , 3);
        $type5 = $this->typeDetect($day , 5);

        if ($type3 && $type5)
            $percent = 3;
        else if ($type3)
            $percent = 1;
        else if ($type5)
            $percent = 2;

        return $percent;
    }

    private function getInterestSum($sum , $percent){
        //summ absolute
        $summ = ($sum / 100) * $percent;
        //summ rounded
        return round($summ, 2);
    }




    public function exec($data){

        //Interest is calculated per day as a percentage from the original amount 
        $days = $data['days'];

        $interestTotal = 0;

        //$data['interest_amount'] = array();

        while ($days > 0) {

            $interestPercent = $this->getInterestPercent($days);
            $interestSum = $this->getInterestSum($data['sum'] , $interestPercent);

            /**
            $data['interest_amount'][$days] = array(
                'percent' => $interestPercent ,
                'sum' => $interestSum
            );
            **/

            //Total sum is the sum of original amount and total interest 
            $interestTotal = $interestTotal + $interestSum;

            $days = $days - 1; 
        }


        $data['interest'] = $interestTotal;
        $data['totalSum'] = $data['sum'] + $data['interest'];


        return $data;
    }
}