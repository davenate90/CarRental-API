<?php


namespace Scholarship\Controllers;
use PDO;
use \Scholarship\Models\Rating;
use Scholarship\Models\Token;
use Scholarship\Models\UserRatings;
use Scholarship\Utilities\DatabaseConnection;

/**
 * Class RatingsController
 * @package Scholarship\Models
 * @author Brady Blackley
 * @author Brooklyn Child
 * @author Porter Okey
 * @author Shenandoah Stubbs
 */
class RatingsController
{

    private $dbh;

    /**
     * RatingsController constructor.
     */
    public function __construct()
    {
        $this->dbh= DatabaseConnection::getInstance();
    }

    /**
     * @param $json
     * Inserts rating into the rating and userRating tables via a
     * JSON object and prepared statements
     */
    public function insertRating($json)
    {
        //print_r($json);
        $Rname = $json->name ?? "Hi";
        $rating = $json->ratingScore ?? "1";
        $fkScholarshipID = $json->scholarshipid ?? "2";
        $applicationID = $json->app ?? "2";
        $userID = $json->userid ?? "1";


        $insert = $this->dbh->prepare('INSERT INTO `Ratings` VALUES(NULL, :Rname, :rating, :fkScholarshipID, :ApplicationID)');
        $insert->bindParam(':Rname',$Rname);
        $insert->bindParam(':rating',$rating);
        $insert->bindParam(':fkScholarshipID',$fkScholarshipID);
        $insert->bindParam(':ApplicationID',$applicationID);
        $insert->execute();
        $ratingID = $this->dbh->lastInsertId();

        $insert = $this->dbh->prepare('INSERT INTO `UserRatings` VALUES(NULL, :fkScholarshipID, :userID, :ratingID, :ApplicationID)');
        $insert->bindParam(':ratingID',$ratingID);
        $insert->bindParam(':userID',$userID);
        $insert->bindParam(':fkScholarshipID',$fkScholarshipID);
        $insert->bindParam(':ApplicationID',$applicationID);
        $insert->execute();
    }

    /**
     * @param $args
     * @return void
     * Displays UserRatings based on the Application ID
     * Done through prepared statements
     */
    public function applicationID($args)
    {
        $myArray = array();
        if(Token::getRoleFromToken() === Token::ROLE_FACULTY) {
        $applicationID = filter_var($args["id"], FILTER_SANITIZE_STRING);
        $myArray = array();
        $query = $this->dbh->prepare('SELECT * FROM `Ratings` 
                INNER JOIN `UserRatings` 
                ON Ratings.ratingID = UserRatings.ratingID 
                AND Ratings.ApplicationID = UserRatings.fkApplicationID 
                WHERE `fkApplicationID` = :applicationID');
        $query->bindParam(':applicationID', $applicationID);
        $query->setFetchMode(PDO::FETCH_OBJ);
        $result = $query->execute();
        while($row = $query->fetch())
        {
            $myArray[] = new Rating($row->name, $row->rating, $row->ratingID, $row->fkScholarshipID, $row->ApplicationID);
        }

        return $myArray;
        }
        return $myArray;
    }

    /**
     * @return array
     * Returns all ratings from the Ratings table
     *
     */
    public function getAllRatings()
    {
        $myArray = array();
        if(Token::getRoleFromToken() === Token::ROLE_FACULTY) {

            $query = $this->dbh->prepare('SELECT * FROM `Ratings`');
            $query->setFetchMode(PDO::FETCH_OBJ);
            $result = $query->execute();
            while ($row = $query->fetch()) {
                $myArray[] = new Rating($row->name, $row->rating, $row->ratingID, $row->fkScholarshipID, $row->ApplicationID);
            }
        }
        return $myArray;
    }

    /**
     * @param $args
     * @return array|void
     * Returns UserRatings based on the user who assigned them.
     * This is achieved using prepared statements
     */
    public function ratingsByUser($args)
    {
        $myArray = array();
        if(Token::getRoleFromToken() === Token::ROLE_FACULTY) {
            $userId = filter_var($args["id"], FILTER_SANITIZE_STRING);
            $query = $this->dbh->prepare('SELECT * FROM `Ratings` 
                INNER JOIN `UserRatings` 
                ON Ratings.ratingID = UserRatings.ratingID 
                AND Ratings.ApplicationID = UserRatings.fkApplicationID 
                WHERE `userID` = :userID');
            $query->bindParam(':userID', $userId);
            $query->setFetchMode(PDO::FETCH_OBJ);
            $result = $query->execute();
            while ($row = $query->fetch()) {
                $myArray[] = new Rating($row->name, $row->rating, $row->ratingID, $row->fkScholarshipID, $row->ApplicationID);
            }
        }
        return $myArray;

    }

    /**
     * @param $args
     * @return void
     * Returns ratings based on a Scholarship ID.
     * Achieved using prepared statements.
     */
    public function ScholarshipRatings($args)
    {
        $myArray = array();
        if(Token::getRoleFromToken() === Token::ROLE_FACULTY) {
            $fkScholarshipID = filter_var($args["id"], FILTER_SANITIZE_STRING);
            $query = $this->dbh->prepare('SELECT * FROM `Ratings` 
                INNER JOIN `UserRatings` 
                ON Ratings.ratingID = UserRatings.ratingID 
                AND Ratings.ApplicationID = UserRatings.fkApplicationID 
                WHERE UserRatings.fkScholarshipID = :fkScholarshipID');
            $query->bindParam(':fkScholarshipID', $fkScholarshipID);
            $query->setFetchMode(PDO::FETCH_OBJ);
            $result = $query->execute();
            while ($row = $query->fetch()) {
                $myArray[] = new Rating($row->name, $row->rating, $row->ratingID, $row->fkScholarshipID, $row->ApplicationID);
            }
        return $myArray;

        }
    }

    /**
     * @param $args
     * @return void
     * Returns rating given on specific awards based on the Application ID.
     * This is done using prepared statements
     */
    public function ratingsForAwardsByApplication($args)//TODO
    {
        $myArray = array();
        if (Token::getRoleFromToken() === Token::ROLE_FACULTY) {
        $awardID = filter_var($args["id"], FILTER_SANITIZE_STRING);
        $query = $this->dbh->prepare('SELECT * FROM `UserRatings`
               INNER JOIN `Awards`
               ON UserRatings.fkApplicationID = Awards.ApplicationID
               WHERE `awardID` = :awardID');
        $query->bindParam(':awardID', $awardID);
        $query->setFetchMode(PDO::FETCH_OBJ);
        $result = $query->execute();
        while ($row = $query->fetch()) {
            echo "$row->ratingID<br>";
            $myArray[] = new UserRatings($row->name, $row->rating, $row->ratingID, $row->fkScholarshipID, $row->ApplicationID);
        }
    }
        return $myArray;
    }

    /**
     * @param $args
     * @return array
     * Returns ratings by rating ID
     * Using prepared statements
     */
    public function getRatingByID($args)
    {
        $myArray = array();
        if (Token::getRoleFromToken() === Token::ROLE_FACULTY) {
            $ratingID = filter_var($args["id"], FILTER_SANITIZE_STRING);

            $query = $this->dbh->prepare('SELECT * FROM `Ratings` 
                INNER JOIN `UserRatings` 
                ON Ratings.ratingID = UserRatings.ratingID 
                AND Ratings.ApplicationID = UserRatings.fkApplicationID 
                WHERE UserRatings.ratingID = :ratingID');
            $query->bindParam(':ratingID', $ratingID);
            $query->setFetchMode(PDO::FETCH_OBJ);
            $result = $query->execute();
            while ($row = $query->fetch()) {
                $myArray[] = new Rating($row->name, $row->rating, $row->ratingID, $row->fkScholarshipID, $row->ApplicationID);
            }
            return $myArray;
        }
    }



}