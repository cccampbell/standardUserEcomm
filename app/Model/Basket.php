<?php

namespace App\Model;

use App\Model\ProductData;
use App\Support\Storage\Contract\StorageInterface;
use App\Support\Exceptions\QuantityExceededException;
use App\Support\Exceptions\ProductQuantityNotAvailable;

class Basket {

    protected   $storage,
                $maxQuantity;
    
    public function __construct(StorageInterface $storage, ProductDataSet $productDS) {

        $this->storage = $storage;
        $this->productDS = $productDS;
        $this->maxQuantity = 5;

    }

    // add in postage class as dependency, can the add or change object from with in basket, means when getting total can calculate from within

    public function add(ProductData $product, $quantity) {

        if($this->has($product->getId())) {

            $quantity = $this->get($product)['quantity'] + $quantity;

        }
        // print_r($product);

        $this->update($product, $quantity);

        return $product;

    }

    public function remove($id) {

        // die(var_dump($id));
        $this->storage->unset($id);

    }

    public function update(ProductData $product, $quantity) {

        if(!$product->hasStock($quantity)) {

            throw new QuantityExceededException;

        }

        if((int)$quantity === 0) {

            $this->remove($product->getId());
            return;
        }

        $this->storage->set($product->getId(), [
            'id' => (int) $product->getId(),
            'quantity' => (int) $quantity,
        ]);

    }
    
    // check if basket has product with same colour and size
    public function has($id) {

        return $this->storage->exists($id);

    }

    public function get(ProductData $product) {

        return $this->storage->get($product->getId());

    }

    public function clear() {
         
        $this->storage->clear();
    }

    public function count() {
        return $this->storage->count();
    }

    public function all() {

        // get all ids
            // get all ids together
            //query it to get back all products asked for
        // for each product
            // add in quantity to specified product
        // return all products

        //     $productDS = new ProductDataSet();

        $ids = [];
        $items = [];


        foreach($this->storage->all() as $product) {

            $ids[] = $product['id'];
        }


        $products = $this->productDS->collectProducts($ids);


        foreach($products as $product) {

            $product->quantity = $this->get($product)['quantity'];

            $items[] = $product;
        }

        return $items;

    }

    public function countProducts() {
        return count($this->storage);
    }

    public function refresh() {

        $updateProduct = false;

        foreach($this->all() as $item) {

            // if item does not have stock
            if(!$item->hasStock($item->quantity)) {

                // die(var_dump('stock level: ' . $item->getStock()));

                $this->update($item, $item->getStock());

                $updateProduct = true;

            }

            if($item->quantity === 0) {
                $this->update($item, $item->quantity);
            }

        }

        if($updateProduct) {

            throw new ProductQuantityNotAvailable();
            return;
        }

    }

    public function getTotal() {

        $total = 0;

        foreach($this->all() as $item) {

            $total += $item->getPrice();

        }
        return $total;

    }

    public function isEmpty() {

        if($this->count() === 0) {
            
            return true;
        } else {
            return false;
        }

    }


}
?>