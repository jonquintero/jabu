<?php

namespace App\Helpers;

use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Response;

trait SearchTaskHelper
{
    /**
     * @param $result
     * @return array|mixed|void|null
     * @throws \Exception
     */
    public function search($result)
    {

        $request = Request::only('search',  'fromDate', 'untilDate', 'groupBy');

        if (isset($request['fromDate'])){
            $startDate = $request['fromDate'];
            $endDate = $request['untilDate'] ?? date('Y-m-d');

           abort_if($startDate > $endDate, Response::HTTP_BAD_REQUEST, 'fromDate must not be major to untilDate');

            if ($endDate == $startDate) {
                return $this->currentDate($result);

            }else {

                $begin = new DateTime($startDate);
                $end = new DateTime($endDate);


                return $this->intervalDates($begin, $end, $result);
            }

        }
        if(isset($request['groupBy']))
        {
            return $this->groupBy($result);
        }

        return $result->paginate(15);

    }

    /**
     * @param $result
     * @return mixed|void|null
     */
    public function groupBy($result)
    {
        $request = Request::only('search',  'fromDate', 'untilDate', 'groupBy');

        if (isset($request['groupBy'])){

            switch ($request['groupBy']){
                case 'today': return $this->today($result);
                case 'tomorrow': return $this->tomorrow($result);
                case 'next week': return $this->nextWeek($result);
                case 'next': return $this->next($result);
            }
        }
    }


    public function today($result)
    {
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d');

        if ($endDate == $startDate) {
            $response = $this->currentDate($result);

            return collect(array_unique($response, SORT_REGULAR))->paginate(20);
        }
    }

    /**
     * @param $result
     * @return mixed
     * @throws \Exception
     */
    public function nextWeek($result): mixed
    {
        $startDate = date('Y-m-d', strtotime('next week'));
        $endDate = date('Y-m-d', strtotime('+6 days', strtotime('+7 day', strtotime(date('o-\WW-1')))));

        $begin = new DateTime($startDate);
        $end = new DateTime($endDate);

        return $this->intervalDates($begin, $end, $result);
    }

    /**
     * @param $result
     * @return mixed
     */
    public function tomorrow($result): mixed
    {

        foreach ($result as $item => $value) {
            if ($value->frequency_id == 1) {
                $response[] = $value;
            }

            if ($value->frequency_id == 2 && date('l', strtotime('+1 days')) == "Monday") {
                $response[] = $value;
            }

            if ($value->frequency_id == 3 && date('l', strtotime('+1 days')) == "Wednesday") {
                $response[] = $value;
            }
            if ($value->frequency_id == 4 && date('l', strtotime('+1 days')) == "Friday") {
                $response[] = $value;
            }
            if ($value->frequency_id == 5 && date('5', strtotime('+1 days')) == 5) {
                $response[] = $value;
            }
        }

        return collect(array_unique($response, SORT_REGULAR))->paginate(20);

    }

    public function next($result): mixed
    {
        $startDate = date('Y-m-d', strtotime('+7 days', strtotime('next week', strtotime(date('o-\WW-1')))));
        $endDate = date('Y-m-d', strtotime('+6 days', strtotime($startDate, strtotime(date('o-\WW-1')))));

        $begin = new DateTime($startDate);
        $end = new DateTime($endDate);


        return $this->intervalDates($begin, $end, $result);
    }

    /**
     * @param $result
     * @return array
     */
    public function currentDate($result): array
    {
        foreach ($result as $item => $value) {
            if ($value->frequency_id == 1) {
                $response[] = $value;
            }

            if ($value->frequency_id == 2 && date('l') == "Monday") {
                $response[] = $value;
            }

            if ($value->frequency_id == 3 && date('l') == "Wednesday") {
                $response[] = $value;
            }
            if ($value->frequency_id == 3 && date('l') == "Friday") {
                $response[] = $value;
            }
            if ($value->frequency_id == 4 && date('d') == 5) {
                $response[] = $value;
            }
        }
        return $response;
    }

    /**
     * @param DateTime $begin
     * @param DateTime $end
     * @param $result
     * @return mixed
     */
    public function intervalDates(DateTime $begin, DateTime $end, $result)
    {
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            foreach ($result as $item => $value) {
                if ($value->frequency_id == 1) {
                    $response[] = $value;
                }

                if ($value->frequency_id == 2 && $dt->format('l') == "Monday") {
                    $response[] = $value;
                }

                if ($value->frequency_id == 3 && $dt->format('l') == "Wednesday") {
                    $response[] = $value;
                }
                if ($value->frequency_id == 3 && $dt->format('l') == "Friday") {
                    $response[] = $value;
                }
                if ($value->frequency_id == 4 && $dt->format('d') == 5) {
                    $response[] = $value;
                }
            }
        }
        return collect(array_unique($response, SORT_REGULAR))->paginate(20);
    }
}
