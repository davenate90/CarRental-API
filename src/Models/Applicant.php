<?php

namespace Scholarship\Models;
use PDO;
use Scholarship\Utilities\DatabaseConnection;

class Applicant implements \JsonSerializable
{
    /*
     * A lot of data to hold!
     * All of this is sourced from the original website,
     * with a few tweaks for convenience here and there.
     *
     * todo can we use type hints?
     * todo more efficient enum style?
     */
    private $userID; // string
    private $applicantID; // int

    // "Contact Info" tab
    private $firstName; // string
    private $lastName; // string
    private $middleInitial; // string
    private $prefix; // string
    private $birthDate; // DateTime
    private $gender; // GENDER
    const GENDER_MALE = 'm';
    const GENDER_FEMALE = 'f';
    const GENDER_OTHER = 'o';
    private $address; // string
    private $addressLine2; // string
    private $city; // string
    private $state; // string, should be two-character e.g. 'UT'
    private $zip; // string
    private $maritalStatus; // MARITAL_STATUS
    const MARITAL_STATUS_SINGLE = 'single';
    const MARITAL_STATUS_MARRIED = 'married';
    const MARITAL_STATUS_DIVORCED = 'divorced';
    const MARITAL_STATUS_WIDOWED = 'widowed';
    private $primaryPhone; // string
    private $secondaryPhone; // string
    private $primaryPhoneType; // PHONE_TYPE
    private $secondaryPhoneType; // PHONE_TYPE
    const PHONE_TYPE_HOME = 'home';
    const PHONE_TYPE_CELL = 'cell';
    const PHONE_TYPE_WORK = 'work';

    // "Academic Info" tab
    private $overallGPA; // double
    private $majorGPA; // double
    private $actScore; // int

    // "Educational Info" tab
    private $currentMajor; // MAJOR
    private $futureMajor; // MAJOR
    const MAJOR_CS = 'cs';
    const MAJOR_WEB = 'web';
    const MAJOR_NTM = 'ntm';
    const MAJOR_OTHER = 'other';
    const MAJOR_UNDECLARED = 'undeclared';
    private $currentAcademicLevel; // ACADEMIC_LEVEL
    const ACADEMIC_LEVEL_FRESHMAN = 1;
    const ACADEMIC_LEVEL_SOPHOMORE = 2;
    const ACADEMIC_LEVEL_JUNIOR = 3;
    const ACADEMIC_LEVEL_SENIOR = 4;
    private $degreeGoal; // DEGREE_GOAL
    const DEGREE_GOAL_CERTIFICATE = 'certificate';
    const DEGREE_GOAL_ASSOCIATE = 'associate';
    const DEGREE_GOAL_BACHELOR = 'bachelor';
    const DEGREE_GOAL_MASTER = 'master';
    const DEGREE_GOAL_PHD = 'phd';
    private $highSchool; // string
    private $lastUniversity; // string
    private $firstSemester; // SEMESTER
    const SEMESTER_SPRING = 'spring';
    const SEMESTER_SUMMER = 'summer';
    const SEMESTER_FALL = 'fall';
    private $firstYear; // int
    //private $startDate; // todo this would be nice to have
    private $currentScheduleStatus; // SCHEDULE_STATUS
    const SCHEDULE_STATUS_FULLTIME = 'full-time';
    const SCHEDULE_STATUS_PARTTIME = 'part-time';
    const SCHEDULE_STATUS_UNREGISTERED = 'unregistered';

    // "Courses Taken" tab
    private $pastCSCourses; // array
    private $apTestsPassed; // array
    private $ceCourses; // array

    // "Extracurricular Info" tab
    private $clubs; // string
    private $honors; // string
    private $csTopics; // string
    private $pastScholarships; // string
    private $achievements; // string

    public function __construct(array $applicantInfo) {

        // Semi-auto-generated code.
        $this->userID = $applicantInfo['userID']; // string
        $this->applicantID = $applicantInfo['applicantID']; // int

        // "Contact Info" tab
        $this->firstName = $applicantInfo['firstName']; // string
        $this->lastName = $applicantInfo['lastName']; // string
        $this->middleInitial = $applicantInfo['middleInitial']; // string
        $this->prefix = $applicantInfo['prefix']; // string
        $this->birthDate = $applicantInfo['birthDate']; // DateTime
        $this->gender = $applicantInfo['gender']; // GENDER
        $this->address = $applicantInfo['address']; // string
        $this->addressLine2 = $applicantInfo['addressLine2'];
        $this->city = $applicantInfo['city']; // string
        $this->state = $applicantInfo['state']; // string, should be two-character e.g. 'UT'
        $this->zip = $applicantInfo['zip']; // string
        $this->maritalStatus = $applicantInfo['maritalStatus']; // MARITAL_STATUS
        $this->primaryPhone = $applicantInfo['primaryPhone']; // string
        $this->secondaryPhone = $applicantInfo['secondaryPhone']; // string
        $this->primaryPhoneType = $applicantInfo['primaryPhoneType']; // PHONE_TYPE
        $this->secondaryPhoneType = $applicantInfo['secondaryPhoneType']; // PHONE_TYPE

        // "Academic Info" tab
        $this->overallGPA = $applicantInfo['overallGPA']; // double
        $this->majorGPA = $applicantInfo['majorGPA']; // double
        $this->actScore = $applicantInfo['actScore']; // int

        // "Educational Info" tab
        $this->currentMajor = $applicantInfo['currentMajor']; // MAJOR
        $this->futureMajor = $applicantInfo['futureMajor']; // MAJOR
        $this->currentAcademicLevel = $applicantInfo['currentAcademicLevel']; // ACADEMIC_LEVEL
        $this->degreeGoal = $applicantInfo['degreeGoal']; // DEGREE_GOAL
        $this->highSchool = $applicantInfo['highSchool']; // string
        $this->lastUniversity = $applicantInfo['lastUniversity']; // string
        $this->firstSemester = $applicantInfo['firstSemester']; // SEMESTER
        $this->firstYear = $applicantInfo['firstYear']; // int
        $this->currentScheduleStatus = $applicantInfo['currentScheduleStatus']; // SCHEDULE_STATUS

        // "Courses Taken" tab
        // todo we should find a way to make this a list
        $this->pastCSCourses = $applicantInfo['pastCSCourses']; // array
        $this->apTestsPassed = $applicantInfo['apTestsPassed']; // array
        $this->ceCourses = $applicantInfo['ceCourses']; // array

        // "Extracurricular Info" tab
        $this->clubs = $applicantInfo['clubs']; // string
        $this->honors = $applicantInfo['honors']; // string
        $this->csTopics = $applicantInfo['csTopics']; // string
        $this->pastScholarships = $applicantInfo['pastScholarships']; // string
        $this->achievements = $applicantInfo['achievements']; // string
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @param mixed $userID
     */
    public function setUserID($userID): void
    {
        $this->userID = $userID;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getMiddleInitial()
    {
        return $this->middleInitial;
    }

    /**
     * @param mixed $middleInitial
     */
    public function setMiddleInitial($middleInitial): void
    {
        $this->middleInitial = $middleInitial;
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param mixed $prefix
     */
    public function setPrefix($prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @return mixed
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param mixed $birthDate
     */
    public function setBirthDate($birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getAddressLine2()
    {
        return $this->addressLine2;
    }

    /**
     * @param mixed $addressLine2
     */
    public function setAddressLine2($addressLine2): void
    {
        $this->addressLine2 = $addressLine2;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip): void
    {
        $this->zip = $zip;
    }

    /**
     * @return mixed
     */
    public function getMaritalStatus()
    {
        return $this->maritalStatus;
    }

    /**
     * @param mixed $maritalStatus
     */
    public function setMaritalStatus($maritalStatus): void
    {
        $this->maritalStatus = $maritalStatus;
    }

    /**
     * @return mixed
     */
    public function getPrimaryPhone()
    {
        return $this->primaryPhone;
    }

    /**
     * @param mixed $primaryPhone
     */
    public function setPrimaryPhone($primaryPhone): void
    {
        $this->primaryPhone = $primaryPhone;
    }

    /**
     * @return mixed
     */
    public function getSecondaryPhone()
    {
        return $this->secondaryPhone;
    }

    /**
     * @param mixed $secondaryPhone
     */
    public function setSecondaryPhone($secondaryPhone): void
    {
        $this->secondaryPhone = $secondaryPhone;
    }

    /**
     * @return mixed
     */
    public function getPrimaryPhoneType()
    {
        return $this->primaryPhoneType;
    }

    /**
     * @param mixed $primaryPhoneType
     */
    public function setPrimaryPhoneType($primaryPhoneType): void
    {
        $this->primaryPhoneType = $primaryPhoneType;
    }

    /**
     * @return mixed
     */
    public function getSecondaryPhoneType()
    {
        return $this->secondaryPhoneType;
    }

    /**
     * @param mixed $secondaryPhoneType
     */
    public function setSecondaryPhoneType($secondaryPhoneType): void
    {
        $this->secondaryPhoneType = $secondaryPhoneType;
    }

    /**
     * @return mixed
     */
    public function getOverallGPA()
    {
        return $this->overallGPA;
    }

    /**
     * @param mixed $overallGPA
     */
    public function setOverallGPA($overallGPA): void
    {
        $this->overallGPA = $overallGPA;
    }

    /**
     * @return mixed
     */
    public function getMajorGPA()
    {
        return $this->majorGPA;
    }

    /**
     * @param mixed $majorGPA
     */
    public function setMajorGPA($majorGPA): void
    {
        $this->majorGPA = $majorGPA;
    }

    /**
     * @return mixed
     */
    public function getActScore()
    {
        return $this->actScore;
    }

    /**
     * @param mixed $actScore
     */
    public function setActScore($actScore): void
    {
        $this->actScore = $actScore;
    }

    /**
     * @return mixed
     */
    public function getCurrentMajor()
    {
        return $this->currentMajor;
    }

    /**
     * @param mixed $currentMajor
     */
    public function setCurrentMajor($currentMajor): void
    {
        $this->currentMajor = $currentMajor;
    }

    /**
     * @return mixed
     */
    public function getFutureMajor()
    {
        return $this->futureMajor;
    }

    /**
     * @param mixed $futureMajor
     */
    public function setFutureMajor($futureMajor): void
    {
        $this->futureMajor = $futureMajor;
    }

    /**
     * @return mixed
     */
    public function getCurrentAcademicLevel()
    {
        return $this->currentAcademicLevel;
    }

    /**
     * @param mixed $currentAcademicLevel
     */
    public function setCurrentAcademicLevel($currentAcademicLevel): void
    {
        $this->currentAcademicLevel = $currentAcademicLevel;
    }

    /**
     * @return mixed
     */
    public function getDegreeGoal()
    {
        return $this->degreeGoal;
    }

    /**
     * @param mixed $degreeGoal
     */
    public function setDegreeGoal($degreeGoal): void
    {
        $this->degreeGoal = $degreeGoal;
    }

    /**
     * @return mixed
     */
    public function getHighSchool()
    {
        return $this->highSchool;
    }

    /**
     * @param mixed $highSchool
     */
    public function setHighSchool($highSchool): void
    {
        $this->highSchool = $highSchool;
    }

    /**
     * @return mixed
     */
    public function getLastUniversity()
    {
        return $this->lastUniversity;
    }

    /**
     * @param mixed $lastUniversity
     */
    public function setLastUniversity($lastUniversity): void
    {
        $this->lastUniversity = $lastUniversity;
    }

    /**
     * @return mixed
     */
    public function getFirstSemester()
    {
        return $this->firstSemester;
    }

    /**
     * @param mixed $firstSemester
     */
    public function setFirstSemester($firstSemester): void
    {
        $this->firstSemester = $firstSemester;
    }

    /**
     * @return mixed
     */
    public function getFirstYear()
    {
        return $this->firstYear;
    }

    /**
     * @param mixed $firstYear
     */
    public function setFirstYear($firstYear): void
    {
        $this->firstYear = $firstYear;
    }

    /**
     * @return mixed
     */
    public function getCurrentScheduleStatus()
    {
        return $this->currentScheduleStatus;
    }

    /**
     * @param mixed $currentScheduleStatus
     */
    public function setCurrentScheduleStatus($currentScheduleStatus): void
    {
        $this->currentScheduleStatus = $currentScheduleStatus;
    }

    /**
     * @return mixed
     */
    public function getPastCSCourses()
    {
        return $this->pastCSCourses;
    }

    /**
     * @param mixed $pastCSCourses
     */
    public function setPastCSCourses($pastCSCourses): void
    {
        $this->pastCSCourses = $pastCSCourses;
    }

    /**
     * @return mixed
     */
    public function getApTestsPassed()
    {
        return $this->apTestsPassed;
    }

    /**
     * @param mixed $apTestsPassed
     */
    public function setApTestsPassed($apTestsPassed): void
    {
        $this->apTestsPassed = $apTestsPassed;
    }

    /**
     * @return mixed
     */
    public function getCeCourses()
    {
        return $this->ceCourses;
    }

    /**
     * @param mixed $ceCourses
     */
    public function setCeCourses($ceCourses): void
    {
        $this->ceCourses = $ceCourses;
    }

    /**
     * @return mixed
     */
    public function getClubs()
    {
        return $this->clubs;
    }

    /**
     * @param mixed $clubs
     */
    public function setClubs($clubs): void
    {
        $this->clubs = $clubs;
    }

    /**
     * @return mixed
     */
    public function getHonors()
    {
        return $this->honors;
    }

    /**
     * @param mixed $honors
     */
    public function setHonors($honors): void
    {
        $this->honors = $honors;
    }

    /**
     * @return mixed
     */
    public function getCsTopics()
    {
        return $this->csTopics;
    }

    /**
     * @param mixed $csTopics
     */
    public function setCsTopics($csTopics): void
    {
        $this->csTopics = $csTopics;
    }

    /**
     * @return mixed
     */
    public function getPastScholarships()
    {
        return $this->pastScholarships;
    }

    /**
     * @param mixed $pastScholarships
     */
    public function setPastScholarships($pastScholarships): void
    {
        $this->pastScholarships = $pastScholarships;
    }

    /**
     * @return mixed
     */
    public function getAchievements()
    {
        return $this->achievements;
    }

    /**
     * @param mixed $achievements
     */
    public function setAchievements($achievements): void
    {
        $this->achievements = $achievements;
    }

    public function jsonSerialize()
    {

        $data = array();
        // excluding UserID as nobody but the user needs to know,
        // and they have other ways of getting it.
        $data['userID'] = $this->userID; // string
        $data['applicantID'] = $this->applicantID; // int

        // "Contact Info" tab
        $data['firstName'] = $this->firstName; // string
        $data['lastName'] = $this->lastName; // string
        $data['middleInitial'] = $this->middleInitial; // string
        $data['prefix'] = $this->prefix; // string
        $data['birthDate'] = $this->birthDate; // DateTime
        $data['gender'] = $this->gender; // GENDER
        $data['address'] = $this->address; // string
        $data['addressLine2'] = $this->addressLine2;
        $data['city'] = $this->city; // string
        $data['state'] = $this->state; // string, should be two-character e.g. 'UT'
        $data['zip'] = $this->zip; // string
        $data['maritalStatus'] = $this->maritalStatus; // MARITAL_STATUS
        $data['primaryPhone'] = $this->primaryPhone; // string
        $data['secondaryPhone'] = $this->secondaryPhone; // string
        $data['primaryPhoneType'] = $this->primaryPhoneType; // PHONE_TYPE
        $data['secondaryPhoneType'] = $this->secondaryPhoneType; // PHONE_TYPE

        // "Academic Info" tab
        $data['overallGPA'] = $this->overallGPA; // double
        $data['majorGPA'] = $this->majorGPA; // double
        $data['actScore'] = $this->actScore; // int

        // "Educational Info" tab
        $data['currentMajor'] = $this->currentMajor; // MAJOR
        $data['futureMajor'] = $this->futureMajor; // MAJOR
        $data['currentAcademicLevel'] = $this->currentAcademicLevel; // ACADEMIC_LEVEL
        $data['degreeGoal'] = $this->degreeGoal; // DEGREE_GOAL
        $data['highSchool'] = $this->highSchool; // string
        $data['lastUniversity'] = $this->lastUniversity; // string
        $data['firstSemester'] = $this->firstSemester; // SEMESTER
        $data['firstYear'] = $this->firstYear; // int
        //$data['startDate'] = $this->;
        $data['currentScheduleStatus'] = $this->currentScheduleStatus; // SCHEDULE_STATUS

        // "Courses Taken" tab
        $data['pastCSCourses'] = $this->pastCSCourses; // array
        $data['apTestsPassed'] = $this->apTestsPassed; // array
        $data['ceCourses'] = $this->ceCourses; // array

        // "Extracurricular Info" tab
        $data['clubs'] = $this->clubs; // string
        $data['honors'] = $this->honors; // string
        $data['csTopics'] = $this->csTopics; // string
        $data['pastScholarships'] = $this->pastScholarships; // string
        $data['achievements'] = $this->achievements; // string

        return $data;
    }
}