<?php

namespace Scholarship\Models;

//use \Scholarship\Models\Question as Question;

class Scholarship implements \JsonSerializable {
    private $scholarshipID;
    private $scholarshipName;
    private $scholarshipDescription;
    private $scholarshipQualification;
    private $scholarshipAmount;
    private $questionArr;

    public function __construct(int $scholarshipID,  string $scholarshipName, string $scholarshipDescription, string $scholarshipQualification, float $scholarshipAmount, $questionArr = null) {
        $this->scholarshipID = $scholarshipID;
        $this->scholarshipName = $scholarshipName;
        $this->scholarshipDescription = $scholarshipDescription;
        $this->scholarshipQualification = $scholarshipQualification;
        $this->scholarshipAmount = $scholarshipAmount;
        $this->questionArr = $questionArr;
    }

    public function getID() {
        return $this->scholarshipID;
    }

    public function setID($scholarshipID) {
        $this->scholarshipID = $scholarshipID;
    }

    public function getName() {
        return $this->scholarshipName;
    }

    public function setName($scholarshipName) {
        $this->scholarshipName = $scholarshipName;
    }

    public function getDescription() {
        return $this->scholarshipDescription;
    }

    public function setDescription($scholarshipDescription){
        $this->scholarshipDescription = $scholarshipDescription;
    }

    public function getQualification() {
        return $this->scholarshipQualification;
    }

    public function setQualification($scholarshipQualification) {
        $this->scholarshipQualification = $scholarshipQualification;
    }

    public function getAmount() {
        return $this->scholarshipAmount;
    }

    public function setAmount($scholarshipAmount) {
        $this->scholarshipAmount = $scholarshipAmount;
    }

    /**
     * @return string
     */
    public function getScholarshipQualification(): string {
        return $this->scholarshipQualification;
    }

    /**
     * @param string $scholarshipQualification
     */
    public function setScholarshipQualification(string $scholarshipQualification): void {
        $this->scholarshipQualification = $scholarshipQualification;
    }

    /**
     * @return float
     */
    public function getScholarshipAmount(): float {
        return $this->scholarshipAmount;
    }

    /**
     * @param float $scholarshipAmount
     */
    public function setScholarshipAmount(float $scholarshipAmount): void {
        $this->scholarshipAmount = $scholarshipAmount;
    }

    /**
     * @return null
     */
    public function getQuestionArr() {
        return $this->questionArr;
    }

    /**
     * @param null $questionArr
     */
    public function setQuestionArr($questionArr): void {
        $this->questionArr = $questionArr;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize() {
        $data = array();
        $data['ScholarshipID'] = $this->scholarshipID;
        $data['Name'] = $this->scholarshipName;
        $data['Description'] = $this->scholarshipDescription;
        $data['Qualifications'] = $this->scholarshipQualification;
        $data['Amount'] = $this->scholarshipAmount;
        $data['Questions'] = $this->questionArr;
        return $data;
    }
}
