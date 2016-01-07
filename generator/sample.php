<?php

namespace Stark;

use Tully\Edmure;
use Tully\Edmure as Mayon;


class Books 
{

    /* Member variables */
    var $price;
    var $title;

    /* Member functions */
    function setPrice($par){
        $this->price = $par;
    }

    function getPrice(){
        echo $this->price ."<br/>";
    }
    
    function setTitle($par){
        $this->title = $par;
    }

    function getTitle(){
        echo $this->title ." <br/>";
    }

}
?>
