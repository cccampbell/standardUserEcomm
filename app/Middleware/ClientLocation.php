<?php

namespace App\Middleware;


use App\Middleware\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Support\Exceptions\DeliveryServicesNotAvailable;

class ClientLocation extends Middleware {


                            // req, req handler
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler) {

        $response = $handler->handle($request);

        // if location not set on application
        if($this->container->get('location')->isEmpty()) {

            // get info on location
            // cross match with db to check if can be delivered
                // debug - set back up when not in dev mode
                    // as in dev mode use this ip

                $dev_uk_ip = '143.159.155.120';
                $dev_us_ip = '72.229.28.185';

                // $ip = $_SERVER['REMOTE_ADDR'];
                
                $location_data = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $dev_uk_ip));

                    // key names
                        // geoplugin_countryCode
                        // geoplugin_countyName
                        // geoplugin_euVATrate
                        // geoplugin_timezone
                        // geoplugin_currencyCode
                        // geoplugin_currencySymbol
                        // geoplugin_currencyConverter


                $this->container->get('location')->setLocation([
                    'country_code' => $location_data['geoplugin_countryCode'],
                    'country_name' => $location_data['geoplugin_countryName'],
                    'timezone' => $location_data['geoplugin_timezone'],
                    'curreny_code' => $location_data['geoplugin_currencyCode'],
                    'curreny_symbol' => $location_data['geoplugin_currencySymbol'],
                    'curreny_converter' => $location_data['geoplugin_currencyConverter'],
                ]);

        }

        // check if delivery_service available
        try {

            $this->container->get('location')->isDeliveryAvailable();

        } catch (DeliveryServicesNotAvailable $e) {

            // automate flash every two minutes
            $_SESSION['flash']['failed'] = $e->getMessage() . ' in the ' . $this->container->get('location')->getCountryName();

        }

        return $response;

    }

}