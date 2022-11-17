<?php

trait DatesDifferenceCalculator
{
    public function calculateDifference($date)
    {

        $difference =array();
        $date_now = date('Y-m-d h:i:s', time());
        $date1 = new DateTime($date_now);
        $date2 = new DateTime($date);
        $interval = $date1->diff($date2);
        $difference['remaining_time'] = $interval->y . " years, " . $interval->m . " months, " . $interval->d . " days ";
        $past = false;
        $invert = $interval->invert;
        if ($invert == 1) {
            $past = true;
        }
        $difference['past']=$past;
        return $difference;


    }
}