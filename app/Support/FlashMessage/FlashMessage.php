<?php

class FlashMessage {

    // debug - implement this into the application

    protected $flashMessages;

    protected $types = [
        'error',
        'success',
        'info'
    ];

    // store previous flash messages -> with execptions if any
    public function __construct() {

        $flashMessages = [];

    }

    public function flashMessage($type, $message) {


        // check type correct
        try {
            // checkType($type);
        } catch( Exception $e) {

            var_dump($e->getMessage('type of flash message unavailable'));

        }

        // check if message is of type exception
        if($message instanceof Exception) {

            $_SESSION['flash'][$type] = $e->getMessage();

        }
            // if - $e->getMessage()
            // else - put message in flash


    }

    // store the message
    protected function addToStorage($type, $message) {

        // $store = [
        //     'message' =>
        //     'type' =>
        //     'Exception' =>
        //     'time' =>
        // ];

    }

    // check type given against type array to see if available
    protected function checkType($type) {



    }

    // create flash message
    //  params
    //      type
    //      exception
    //      message

}