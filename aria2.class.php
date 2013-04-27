<?php
class aria2{
    private $server;
    private $ch;
    function __construct($server='http://127.0.0.1:6800/jsonrpc'){
        $this->server = $server;
        $this->ch = curl_init($server);
        curl_setopt_array($this->ch,array(
            CURLOPT_POST=>true,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_HEADER=>false,
        ));
    }
    function __destruct(){
        curl_close($this->ch);
    }
    private function req($data){
        curl_setopt($this->ch,CURLOPT_POSTFIELDS,$data);        
        return curl_exec($this->ch);
    }
    function __call($name,$arg){
        $data = array(
            'jsonrpc'=>'2.0',
            'id'=>'1',
            'method'=>'aria2.'.$name,
            'params'=>$arg,
        );
        $data = json_encode($data);
        return json_decode($this->req($data),1);
    }
}