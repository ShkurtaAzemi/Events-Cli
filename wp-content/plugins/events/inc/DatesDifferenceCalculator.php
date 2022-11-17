<?php

//calculate remaining time of event and whether has passed or not
trait DatesDifferenceCalculator
{
    public function calculateDifference($date)
    {

        $difference = array();
        $date_now = date('Y-m-d h:i:s', time());
        $date1 = new DateTime($date_now);
        $date2 = new DateTime($date);
        $interval = $date1->diff($date2);

        $difference['remaining_time'] = '';
        if ($interval->y > 0) {
            $difference['remaining_time'] .= $interval->y . " years, ";
        }
        if ($interval->m > 0) {
            $difference['remaining_time'] .= $interval->m . " months, ";
        }

        if ($interval->d > 0) {
            $difference['remaining_time'] .= $interval->d . " days, ";
        }
        if ($interval->h > 0) {
            $difference['remaining_time'] .= $interval->h . " hours, ";
        }
        if ($interval->i > 0) {
            $difference['remaining_time'] .= $interval->i . " minutes. ";
        }

        //invert=1 means the event has passed, invert=0 means that event is in the future

        $difference['past'] = $interval->invert == 1;
        return $difference;


    }
}