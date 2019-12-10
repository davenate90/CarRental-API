<?php


namespace Scholarship\Models;
use PDO;
use Scholarship\Utilities\DatabaseConnection;


/**
 * Class Rating
 * @package Scholarship\Models
 * @author Brady Blackley
 * @author Brooklyn Child
 * @author Porter Okey
 * @author Shenandoah Stubbs
 */
class Rating implements \JsonSerializable
{

    public $ratingsID = '';
    public $Rname = '';
    public $rating = '';
    public $fkScholarshipID = '' ;
    public $applicationID = '' ;


    private $dbh;

    /**
     * Rating constructor.
     * @param string $Rname
     * @param string $rating
     * @param string $ratingsID
     * @param string $fkScholarshipID
     * @param string $applicationID
     */
    public function __construct($Rname, $rating, $ratingsID, $fkScholarshipID, $applicationID)
    {
        $this->dbh= DatabaseConnection::getInstance();
        $this->Rname = $Rname;
        $this->rating = $rating;
        $this->ratingsID = $ratingsID;
        $this->fkScholarshipID = $fkScholarshipID;
        $this->applicationID = $applicationID;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $data = array();

        $data['ratingID'] = $this->ratingsID;
        $data['name'] = $this->Rname;
        $data['rating'] = $this->rating;
        $data['fkScholarshipID'] = $this->fkScholarshipID;
        return $data;

    }
}