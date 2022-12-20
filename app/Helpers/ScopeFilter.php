<?php

namespace App\Helpers;

use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ScopeFilter extends Builder
{
    public function filter(array $filters): self
    {
        return $this->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            });
        })
            ->when(isset($filters['fromDate']) && ! isset($filters['untilDate']), function ($query) use ($filters) {


                $startDate = $filters['fromDate'];
                $endDate = date('Y-m-d');
                return $this->filterDates($query->get(),$startDate, $endDate);

            });
    }


    public function filterDates($result, $startDate, $endDate)
    {
        $response = array();
        if ($endDate == $startDate) {
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

                return new Collection($response);



        }else {
            $begin = new DateTime();
            $end = new DateTime(date('Y-m-d'));

            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($begin, $interval, $end);
            /* foreach ($period as $dt) {
                 dd($dt->format("m"));
                 //dd( $dt->format("l Y-m-d H:i:s\n"));
             }*/
        }
    }
}
