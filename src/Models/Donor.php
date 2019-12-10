<?php


namespace Scholarship\Models;


use PharIo\Manifest\Email;

class Donor
{
    private $ID;
    private $FirstName;
    private $LastName;
    private $Organization;
    private $Address;
    private $City;
    private $State;
    private $Zip;
    private $Phone;
    private $Email;

    public function __construct(int $ID, string $FirstName, string $LastName, $Organization, $Address, $City, $State, $Zip, $Phone, $Email)
    {
        $this->setID($ID);
        $this->setFirst($FirstName);
        $this->setLast($LastName);
        $this->setOrganization($Organization);
        $this->setAddress($Address);
        $this->setCity($City);
        $this->setState($State);
        $this->setZip($Zip);
        $this->setPhone($Phone);
        $this->setEmail($Email);
    }

    public function getID(){
        return $this->ID;
    }
    public function setID($id){
        $this->ID = $id;
    }

    public function getFirst()
    {
        return $this->FirstName;
    }
    public function setFirst($first){
        $this->FirstName = $first;
    }

    public function getLast(){
        return $this->LastName;
    }
    public function setLast($last){
        $this->LastName = $last;
    }

    public function getOrganization(){
        return $this->Organization;
    }
    public function setOrganization($org){
        $this->Organization = $org;
    }

    public function getAddress(){
    return $this->Address;
}
    public function setAddress($address){
        $this->Address = $address;
    }

    public function getCity(){
        return $this->City;
    }
    public function setCity($city){
        $this->City = $city;
    }

    public function getState(){
        return $this->State;
    }
    public function setState($state){
        $this->State = $state;
    }

    public function getZip(){
        return $this->Zip;
    }
    public function setZip($zip){
        $this->Zip = $zip;
    }

    public function getPhone(){
        return $this->Phone;
    }
    public function setPhone($phone){
        $this->Phone = $phone;
    }

    public function getEmail(){
        return $this->Email;
    }
    public function setEmail($email){
        $this->Email = $email;
    }
}