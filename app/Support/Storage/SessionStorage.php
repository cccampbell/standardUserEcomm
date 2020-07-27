<?php 

namespace App\Support\Storage;

use Countable;
use App\Support\Storage\Contract\StorageInterface;


class SessionStorage implements StorageInterface, Countable {

protected $bucket;

 public function __construct($bucket = 'default') {

   if(!isset($_SESSION[$bucket])) {
      $_SESSION[$bucket] = [];
   }

   $this->bucket = $bucket;

 }

 public function set($id, $value) {

   $_SESSION[$this->bucket][$id] = $value;


 }

 public function exists($id) {

   return isset($_SESSION[$this->bucket][$id]);

 }

 public function get($id) {

   if(!$this->exists($id)) {
      return null;
   }

   return $_SESSION[$this->bucket][$id];

 }

 public function all() {

   return $_SESSION[$this->bucket];

 }

 public function unset($id) {

   if($this->exists($id)) {

      unset( $_SESSION[$this->bucket][ (int) $id ] );

   }

 }

 public function has($id) {

   return isset($_SESSION[$this->bucket[$id]]);
 }

 public function clear() {

   unset($_SESSION[$this->bucket]);

 }

 public function count() {

   return count($this->all());
     
 }

}