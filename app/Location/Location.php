<?php

namespace App\Location;

use App\Model\DeliveryServices;
use App\Support\Exceptions\DeliveryServicesNotAvailable;

class Location {

    protected   $country_code,
                $country_name,
                $country_id,
                $timezone,
                $curreny_code,
                $curreny_symbol,
                $curreny_converter,
                $delivery_available;


    public function setLocation($data) {

        $this->fill($data);

    } 

    public function isEmpty() {

        return empty($this->country_code);

    }

    protected function fill($data) {

        $this->country_code = $data['country_code'];
        $this->country_name = $data['country_name'];
        $this->timezone = $data['timezone'];
        $this->curreny_code = $data['curreny_code'];
        $this->curreny_symbol = $data['curreny_symbol'];
        $this->curreny_converter = $data['curreny_converter'];

        // with info check if delivery service available
        $delivery = DeliveryServices::getWithCountryCode($this->country_code);

        if($delivery !== NULL ) {

            $this->delivery_available = $delivery['delivery_available'];
            $this->country_id = $delivery['country_id'];

        }

    }

    public function isDeliveryAvailable() {

        if(empty($this->delivery_available)) {

            throw new DeliveryServicesNotAvailable();

        } else {

            return $this->delivery_available;

        }

    }

    public function getCountryName() {

        return $this->country_name;

    }


}