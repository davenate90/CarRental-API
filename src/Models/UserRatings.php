<?php


namespace Scholarship\Models;



/**
 * Class UserRatings
 * @package Scholarship\Models
 * @author Brady Blackley
 * @author Brooklyn Child
 * @author Porter Okey
 * @author Shenandoah Stubbs
 */

class UserRatings implements \JsonSerializable
{
    private $pkRatingID = '';
    private $fkScholarshipID = '';
    private $fkApplicationID = '';
    private $userID = '';
    private $ratingID = '';


    /**
     * ActualRating constructor.
     * @param string $pkRatingID
     * @param string $fkScholarshipID
     * @param string $fkApplicationID
     * @param string $ratingID
     * @param string $userID
     */
    public function __construct(string $pkRatingID, string $fkScholarshipID, string $fkApplicationID, string $ratingID,  string $userID)
    {
        $this->pkRatingID = $pkRatingID;
        $this->ratingID = $ratingID;
        $this->fkScholarshipID = $fkScholarshipID;
        $this->userID = $userID;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public
    function jsonSerialize()
    {
        $data = array();

        $data['pkRatingID'] = $this->pkRatingID;
        $data['fkScholarshipID'] = $this->fkScholarshipID;
        $data['fkApplicationID'] = $this->fkApplicationID;
        $data['ratingID'] = $this->ratingsID;
        $data['userID'] = $this->userID;
        return $data;

    }
}