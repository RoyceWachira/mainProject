<?php

  class DbConnect{
    private $con;

    function __construct()
    {
        
    }
    

    //function for db connection
    function connect(){
        include_once dirname(__FILE__).'/constants.php';
        $this->con = new mysqli(DB_Host,DB_User,DB_Pass,DB_Name);

        if(mysqli_connect_errno()){
        
            echo "Failed to connect with database".mysqli_connect_error();

        }
        return $this->con;
    }
  }
?>