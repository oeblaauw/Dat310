<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Magazine
 *
 * @author Oyvind
 */
class Magazine {
    public $id;
    public $name;
    public $price;
    
    function __construct($id, $name, $price) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }
    
    function toString() {
        return $this->name . " - $" .$this->price;
    }
    
}