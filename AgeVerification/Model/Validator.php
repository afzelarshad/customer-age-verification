<?php

namespace Customer\AgeVerification\Model;

use Magento\Framework\Stdlib\DateTime\DateTime;

class Validator
{
    /**
     * @var DateTime
     */
    private $dateTime;
    private Config $config;

    /**
     * Validator constructor.
     *
     * @param DateTime $dateTime
     * @param Config $config
     */
    public function __construct(
        DateTime $dateTime,
        Config $config
    ) {
        $this->dateTime = $dateTime;
        $this->config = $config;
    }

    /**
     * @param string $dob
     * @return bool
     */
    public function validate($dob)
    {
        $ageLimit = $this->config->getAgeLimit();
        if ($dob) {
            if ($this->config->isEnabled()) {

//            $dob = $this->dateTime->date('Y-m-d', $dob);
                //explode the date to get month, day and year
//            $birthDate = explode("-", $dob);
                //get age from date or birthdate

                $age = (date('Y') - date('Y', strtotime($dob)));
                $now = $this->dateTime->date('Y-m-d');
                if ($age > 100) {
                    return false;
                }
                if ($now < $dob) {
                    return false;
                }
                if ($age <= $ageLimit) {
                    return false;
                }
                return true;
            } else {
                return true;
            }
        }
        return false;
    }
}
