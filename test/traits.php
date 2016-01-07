<?php

trait Hello
{
    abstract public function sendMessage();
 
    public function hello()
    {
        echo "Hello World","\n";
    }
}    
 
class Messenger
{
    use Hello;
    public function sendMessage()
    {
        echo 'I am going to say hello',"\n";
        echo $this->hello(),"\n";
    }
} 
