<?php

namespace PaymentGatewayJson\Client\Transaction\Base;

use PaymentGatewayJson\Client\Schedule\ScheduleData;
use PaymentGatewayJson\Client\Schedule\ScheduleWithTransaction;

/**
 * Trait ScheduleTrait
 *
 * @package PaymentGatewayJson\Client\Transaction\Base
 */
trait ScheduleTrait {

    /**
     * @var ScheduleWithTransaction
     */
    protected $schedule;

    /**
     * ScheduleResultData for backward compatibility
     *
     * @return ScheduleData|ScheduleWithTransaction
     */
    public function getSchedule() {
        return $this->schedule;
    }

    /**
     * ScheduleResultData for backward compatibility
     *
     * @param ScheduleData|ScheduleWithTransaction $schedule
     *
     * @return $this
     */
    public function setSchedule($schedule = null) {
        $this->schedule = $schedule;

        return $this;
    }

}