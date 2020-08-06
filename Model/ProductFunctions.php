<?php
class ProductFunctions {

    public function colorsToArray($colors) {

        $product_colors = array();

        $arr_colors = explode(",", $colors);

        foreach ($arr_colors as $color) {

            $new_colors = explode(" ", $color);

            $new_colors[1] = substr($new_colors[1],1,-1);

            array_push($product_colors, $new_colors);


        }
        return $product_colors;
    }

}