<?php

namespace Scholarship\Controllers;

use Scholarship\Http\StatusCodes;
use \Scholarship\Models\Donor as Donor;
use \Scholarship\Utilities\DatabaseConnection as dbo;

class DonorsController
{
    public function postDonor(int $donorID = 0, string $first, string $last, $organization, $address, $city,
                    $state, $zip, $phone, $email){
        $pdo = dbo::getInstance();
        if ($donorID == 0){
            $insert = $pdo->prepare('INSERT INTO `Donor` (`FirstName`, `LastName`, `Organization`, `Address`, `City`, `State`,
                    `Zip`, `Phone`, `Email`) VALUES (:first, :last, :organization, :address, :city, :state, :zip, :phone, :email)');
        }
        else{
            $insert = $pdo->prepare('UPDATE `Donor` SET `FirstName` =:first, `LastName` =:last, `Organization` =:organization,
                            `Address` =:address, `City` =:city, `State` =:state, `Zip` =:zip, `Phone` =:phone, `Email` =:email WHERE ID =:donor');
            $insert->bindParam(':donor', $donorID);
        }
        $insert->bindParam(':first', $first);
        $insert->bindParam(':last', $last);
        $insert->bindParam(':organization', $organization);
        $insert->bindParam(':address', $address);
        $insert->bindParam(':city', $city);
        $insert->bindParam(':state', $state);
        $insert->bindParam(':zip', $zip);
        $insert->bindParam(':phone', $phone);
        $insert->bindParam(':email', $email);
        $insert->execute();
    }

    public function getDonor(int $id)
    {
        $pdo = dbo::getInstance();
        $select = $pdo->prepare('SELECT * FROM `Donor` WHERE `ID`=:id');
        $select->bindParam(':id', $id);
        $select->execute();
        $row = $select->fetch();
        if (empty($row)) {
            return StatusCodes::NOT_FOUND;
        }
        else {
            $donor = new Donor($row['ID'], $row['FirstName'], $row['LastName'], $row['Organization'], $row['Address'],
                $row['City'], $row['State'], $row['Zip'], $row['Phone'], $row['Email']);
            return $donor;
        }
    }

    public function getAll()
    {
        $pdo = dbo::getInstance();
        $select = $pdo->prepare('SELECT * FROM `Donor`');
        $select->execute();
        $donorArray = array();

        while ($row = $select->fetch()){
            $donorArray[] = new Donor($row['ID'], $row['FirstName'], $row['LastName'], $row['Organization'], $row['Address'],
            $row['City'], $row['State'], $row['Zip'], $row['Phone'], $row['Email']);
        }
        //return $select->fetchAll();
        //return $donorArray[2]->getFirst();
        return $donorArray;
    }

    public function getAllByScholarshipId(int $id){
        $pdo = dbo::getInstance();
        $select = $pdo->prepare('SELECT Donor.ID, Donor.FirstName, Donor.LastName FROM `Donor` INNER JOIN 
            `DonorsAndScholarships` ON `DonorsAndScholarships.donorID` = `Donor.ID` WHERE `DonorsAndScholarships.scholarshipID` =:id');
        $select->bindParam(':id', $id);
        $select->execute();
        $donorArray = array();

        while ($row = $select->fetch()){
            $donorArray[] = new Donor($row['ID'], $row['FirstName'], $row['LastName'], $row['Organization'], $row['Address'],
                $row['City'], $row['State'], $row['Zip'], $row['Phone'], $row['Email']);
        }
        //return $select->fetchAll();
        return $donorArray;
    }
}
