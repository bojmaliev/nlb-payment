<?php

namespace PaymentGatewayJson\Client\Transaction\Base;

use PaymentGatewayJson\Client\Schedule\ScheduleData;
use PaymentGatewayJson\Client\Schedule\ScheduleWithTransaction;

interface ScheduleInterface {

    /**
     * @return ScheduleData|ScheduleWithTransaction
     */
    public function getSchedule();

    /**
     * @param ScheduleData|ScheduleWithTransaction $schedule |null
     *
     * @return $this
     */
    public function setSchedule($schedule = null);
}