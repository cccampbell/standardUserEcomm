<?php
namespace App\Util;

class Util {

    /**
     * @param String $url url string
     * 
     */
    public function getArrayFromUrl(String $url) {

        $product_filters = [];

        $types = explode('&', $url);

        foreach ($types as $v) {

            $slice = explode('=', $v);

            $filter = $slice[0];

            $slugs = explode('+',$slice[1]);

            $product_filters[$filter] = $slugs;

        }
        return $product_filters;

    }


}