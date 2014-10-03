<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Subscription
 *
 * @author Øyvind
 */
class Subscription {
    public $subs = array(
        1 => array(),
        2 => array(),
        3 => array(),
        4 => array(),
        5 => array()
    );

    public static function listMagazines($magazines) {
        foreach ($magazines as $mag) {
            $output = "<input type='checkbox' name='mag" . $mag->id . "' value='" . $mag->name . "'>"
                    . $mag->toString() . "<br/>";
            echo $output;
        }
    }

    public function subscribe($magazine_id, $email) {
        array_push($this->subs[$magazine_id], $email);
    }
}
