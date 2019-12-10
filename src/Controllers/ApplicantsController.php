<?php

namespace Scholarship\Controllers;

use PDO;
use Scholarship\Models\Applicant as Applicant;
use Scholarship\Http\StatusCodes;
use Scholarship\Models\Token as Token;
use Scholarship\Utilities\DatabaseConnection;
use Exception;

class ApplicantsController {
    /**
     * Database connection variable.
     * @var PDO
     */
    private $dbc;

    const ACCESS_FACULTY = 0;
    const ACCESS_USER = 1;
    const ACCESS_NONE = 2;

    // These only matter if student role.
    private $userApplicantID;
    private $tokenUserID;

    /**
     * ApplicantsController constructor.
     */
    public function __construct()
    {
        $this->dbc = DatabaseConnection::getInstance();
        $this->userApplicantID = null;
    }

    /**
     * Gets all applicants available, depending on permissions.
     * @param $args
     * @return array|int|mixed
     */
    public function getAllApplicants($args) {
        // faculty: return all applicants in db
        $role = $this->getAccessRole();
        if ($role == $this::ACCESS_FACULTY) {
            $query = $this->dbc->prepare('
                SELECT *
                FROM `Applicants`;
                ');

            $applicantArray = array();
            $result = $query->execute();

            while ($row = $query->fetch()) {
                $applicantArray[] = new Applicant($row);
            }

            return $applicantArray;
        } elseif ($role == $this::ACCESS_USER) {
            $query = $this->dbc->prepare('
                SELECT *
                FROM Applicants
                WHERE applicantID = :applicantID;
            ');
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $query->bindParam(':applicantID', $this->userApplicantID);

            try {
                $query->execute();
            } catch (Exception $e) {
                return http_response_code(StatusCodes::BAD_REQUEST);
            }

            $applicantInfo = $query->fetch();

            return $applicantInfo;
        } elseif ($role == $this::ACCESS_NONE) {
            return http_response_code(StatusCodes::METHOD_NOT_ALLOWED);
        }

        // if we get here, something went wrong.
        return http_response_code(StatusCodes::INTERNAL_SERVER_ERROR);
    }

    /**
     * Inserts an applicant into the database, and returns what fields were set.
     * @param $json
     * @return array|int
     */
    public function createApplicant($json) {
        // faculty: create an applicant with a new UserID
        // student: create an applicant with student's UserID

        // basically, we're going to abuse null coalescing
        // to get arguments in a format we want.
        $applicantInfo = array();

        // Only faculty can decide UserID, automatic if student.
        // As always, kick em out if no role.
        $role = $this->getAccessRole();
        if ($role == $this::ACCESS_FACULTY) {
            $applicantInfo['userID'] = $json->userID ?? null; // inherits from User
        } elseif ($role == $this::ACCESS_USER) {
            $applicantInfo['userID'] = $this->tokenUserID;
        } else {
            return http_response_code(StatusCodes::FORBIDDEN);
        }

        $applicantInfo['firstName'] = $json->firstName ?? null; // inherits from User
        $applicantInfo['lastName'] = $json->lastName ?? null; // inherits from User
        $applicantInfo['middleInitial'] = $json->middleInitial ?? null; // string
        $applicantInfo['prefix'] = $json->prefix ?? null; // string
        $applicantInfo['birthDate'] = $json->birthDate ?? null; // DateTime
        $applicantInfo['gender'] = $json->gender ?? null; // GENDER
        $applicantInfo['address'] = $json->address ?? null; // string
        $applicantInfo['addressLine2'] = $json->addressLine2 ?? null;
        $applicantInfo['city'] = $json->city ?? null; // string
        $applicantInfo['state'] = $json->state ?? null; // string, should be two-character e.g. 'UT'
        $applicantInfo['zip'] = $json->zip ?? null; // string
        $applicantInfo['maritalStatus'] = $json->maritalStatus ?? null; // MARITAL_STATUS
        $applicantInfo['primaryPhone'] = $json->primaryPhone ?? null; // string
        $applicantInfo['secondaryPhone'] = $json->secondaryPhone ?? null; // string
        $applicantInfo['primaryPhoneType'] = $json->primaryPhoneType ?? null; // PHONE_TYPE
        $applicantInfo['secondaryPhoneType'] = $json->secondaryPhoneType ?? null; // PHONE_TYPE

        $applicantInfo['overallGPA'] = $json->overallGPA ?? null; // double
        $applicantInfo['majorGPA'] = $json->majorGPA ?? null; // double
        $applicantInfo['actScore'] = $json->actScore ?? null; // int

        $applicantInfo['currentMajor'] = $json->currentMajor ?? null; // MAJOR
        $applicantInfo['futureMajor'] = $json->futureMajor ?? null; // MAJOR
        $applicantInfo['currentAcademicLevel'] = $json->currentAcademicLevel ?? null; // ACADEMIC_LEVEL
        $applicantInfo['degreeGoal'] = $json->degreeGoal ?? null; // DEGREE_GOAL
        $applicantInfo['highSchool'] = $json->highSchool ?? null; // string
        $applicantInfo['lastUniversity'] = $json->lastUniversity ?? null; // string
        $applicantInfo['firstSemester'] = $json->firstSemester ?? null; // SEMESTER
        $applicantInfo['firstYear'] = $json->firstYear ?? null; // int
        $applicantInfo['currentScheduleStatus'] = $json->currentScheduleStatus ?? null; // SCHEDULE_STATUS

        $applicantInfo['pastCSCourses'] = $json->pastCSCourses ?? null; // array
        $applicantInfo['apTestsPassed'] = $json->apTestsPassed ?? null; // array
        $applicantInfo['ceCourses'] = $json->ceCourses ?? null; // array

        $applicantInfo['clubs'] = $json->clubs ?? null; // string
        $applicantInfo['honors'] = $json->honors ?? null; // string
        $applicantInfo['csTopics'] = $json->csTopics ?? null; // string
        $applicantInfo['pastScholarships'] = $json->pastScholarships ?? null; // string
        $applicantInfo['achievements'] = $json->achievements ?? null; // string


        $query = $this->dbc->prepare('
            INSERT INTO Applicants (userID, firstName, lastName, middleInitial,
            prefix, birthDate, gender, address, addressLine2, city,
            state, zip, maritalStatus, primaryPhone, secondaryPhone,
            primaryPhoneType, secondaryPhoneType, overallGPA, majorGPA,
            actScore, currentMajor, futureMajor, currentAcademicLevel,
            degreeGoal, highSchool, lastUniversity, firstSemester,
            firstYear, currentScheduleStatus, pastCSCourses,
            apTestsPassed, ceCourses, clubs, honors, csTopics,
            pastScholarships, achievements)
            VALUES (:userID,:firstName,:lastName,:middleInitial,
            :prefix,:birthDate,:gender,:address,:addressLine2,:city,
            :state,:zip,:maritalStatus,:primaryPhone,:secondaryPhone,
            :primaryPhoneType,:secondaryPhoneType,:overallGPA,:majorGPA,
            :actScore,:currentMajor,:futureMajor,:currentAcademicLevel,
            :degreeGoal,:highSchool,:lastUniversity,:firstSemester,
            :firstYear,:currentScheduleStatus,:pastCSCourses,
            :apTestsPassed,:ceCourses,:clubs,:honors,:csTopics,
            :pastScholarships,:achievements);
        ');

        // get ready for some obnoxious bindParams()!
        $query->bindParam(':userID', $applicantInfo['userID']); // inherits from User

        $query->bindParam(':firstName', $applicantInfo['firstName']); // inherits from User
        $query->bindParam(':lastName', $applicantInfo['lastName']); // inherits from User
        $query->bindParam(':middleInitial', $applicantInfo['middleInitial']); // string
        $query->bindParam(':prefix', $applicantInfo['prefix']); // string
        $query->bindParam(':birthDate', $applicantInfo['birthDate']); // DateTime
        $query->bindParam(':gender', $applicantInfo['gender']); // GENDER
        $query->bindParam(':address', $applicantInfo['address']); // string
        $query->bindParam(':addressLine2', $applicantInfo['addressLine2']);
        $query->bindParam(':city', $applicantInfo['city']); // string
        $query->bindParam(':state', $applicantInfo['state']); // string, should be two-character e.g. 'UT'
        $query->bindParam(':zip', $applicantInfo['zip']); // string
        $query->bindParam(':maritalStatus', $applicantInfo['maritalStatus']); // MARITAL_STATUS
        $query->bindParam(':primaryPhone', $applicantInfo['primaryPhone']); // string
        $query->bindParam(':secondaryPhone', $applicantInfo['secondaryPhone']); // string
        $query->bindParam(':primaryPhoneType', $applicantInfo['primaryPhoneType']); // PHONE_TYPE
        $query->bindParam(':secondaryPhoneType', $applicantInfo['secondaryPhoneType']); // PHONE_TYPE

        $query->bindParam(':overallGPA', $applicantInfo['overallGPA']); // double
        $query->bindParam(':majorGPA', $applicantInfo['majorGPA']); // double
        $query->bindParam(':actScore', $applicantInfo['actScore']); // int

        $query->bindParam(':currentMajor', $applicantInfo['currentMajor']); // MAJOR
        $query->bindParam(':futureMajor', $applicantInfo['futureMajor']); // MAJOR
        $query->bindParam(':currentAcademicLevel', $applicantInfo['currentAcademicLevel']); // ACADEMIC_LEVEL
        $query->bindParam(':degreeGoal', $applicantInfo['degreeGoal']); // DEGREE_GOAL
        $query->bindParam(':highSchool', $applicantInfo['highSchool']); // string
        $query->bindParam(':lastUniversity', $applicantInfo['lastUniversity']); // string
        $query->bindParam(':firstSemester', $applicantInfo['firstSemester']); // SEMESTER
        $query->bindParam(':firstYear', $applicantInfo['firstYear']); // int
        $query->bindParam(':currentScheduleStatus', $applicantInfo['currentScheduleStatus']); // SCHEDULE_STATUS

        $query->bindParam(':pastCSCourses', $applicantInfo['pastCSCourses']); // array
        $query->bindParam(':apTestsPassed', $applicantInfo['apTestsPassed']); // array
        $query->bindParam(':ceCourses', $applicantInfo['ceCourses']); // array

        $query->bindParam(':clubs', $applicantInfo['clubs']); // string
        $query->bindParam(':honors', $applicantInfo['honors']); // string
        $query->bindParam(':csTopics', $applicantInfo['csTopics']); // string
        $query->bindParam(':pastScholarships', $applicantInfo['pastScholarships']); // string
        $query->bindParam(':achievements', $applicantInfo['achievements']); // string

        // Bad request if insert fails
        try {
            $result = $query->execute();
        } catch (Exception $e) {
            return http_response_code(StatusCodes::BAD_REQUEST);
        }

        if ($query->rowCount() < 1) {
            return http_response_code(StatusCodes::BAD_REQUEST);
        }

        return $applicantInfo;
    }

    /**
     * Checks permissions and returns one applicant.
     * @param $args
     * @return int|Applicant
     */
    public function getApplicant($args) {
        // faculty: get an applicant with a given ApplicantID
        // student: ONLY get this applicant if student UserID = applicant UserID else 403

        $tempAID = filter_var($args['id'], FILTER_SANITIZE_STRING);

        $role = $this->getAccessRole();
        if ($role == self::ACCESS_FACULTY) {
            $applicantID = $tempAID;
        } elseif ($role == self::ACCESS_USER) {
            // check that user is this applicant.
            // if not, kick em out.
            if ($this->userApplicantID == $tempAID) {
                $applicantID = $tempAID;
            } else {
                return http_response_code(StatusCodes::FORBIDDEN);
            }
        } elseif ($role == self::ACCESS_NONE) {
            return http_response_code(StatusCodes::FORBIDDEN);
        }

        $query = $this->dbc->prepare('
            SELECT *
            FROM Applicants
            WHERE applicantID = :applicantID;
        ');

        $query->bindParam(':applicantID', $applicantID);
        $result = $query->execute();

        $row = $query->fetch();

        // make sure we're not returning nothing.
        if (empty($row)) {
            return http_response_code(StatusCodes::NOT_FOUND);
        }

        $applicant = new Applicant($row);

        return $applicant;
    }

    /**
     * Updates specified fields for an applicant. Returns only the fields updated.
     * @param $args
     * @param $json
     * @return array|int
     */
    public function updateApplicant($args, $json) {
        // faculty: update an applicant with a given ApplicantID
        // student: update an applicant if student UserID = applicant UserID else 403
        // todo student user can orphan their applicantID?

        // check roles
        $role = $this->getAccessRole(); // same code as Delete
        $tempAID = filter_var($args['id'], FILTER_SANITIZE_STRING);

        if ($role == $this::ACCESS_FACULTY) {
            $applicantID = $tempAID; // has permission
        } elseif ($role == $this::ACCESS_USER) {
            // make sure requested delete id matches user's.
            if ($tempAID == $this->userApplicantID) {
                $applicantID = $tempAID;
            } else {
                return http_response_code(StatusCodes::FORBIDDEN);
            }
        } else {
            return http_response_code(StatusCodes::FORBIDDEN);
        }

        // first, make sure ApplicantID exists...
        $queryinit = $this->dbc->prepare('
            SELECT *
            FROM Applicants
            WHERE applicantID = :applicantID;
        ');

        $queryinit->bindParam(':applicantID', $applicantID);
        $queryinit->setFetchMode(PDO::FETCH_ASSOC);

        $result = $queryinit->execute();

        $row = $queryinit->fetch();

        // make sure we're not returning nothing.
        if (empty($row)) {
            return http_response_code(StatusCodes::NOT_FOUND);
        }

        $applicantInfo = array();

        // Extract keys from JSON, and only try to update
        // if a field already exists in the row.
        $newkeys = array_keys($json);
        $oldkeys = array_keys($row);

        // If a new key matches an old key, we need that value
        // such that we can update it.
        foreach ($newkeys as $newkey) {
            if (in_array($newkey, $oldkeys)) {
                $applicantInfo[$newkey] = $json[$newkey];
            }
        }

        // For each key in the new ApplicantInfo, execute Insert.
        foreach ($applicantInfo as $column => $newdata) {
            // We have to manually sanitize the column, since PDO cannot bind column names!!
            $sanitizedColumn = filter_var($column, FILTER_SANITIZE_STRING);
            $queryinsert = $this->dbc->prepare("
                UPDATE Applicants
                SET $sanitizedColumn = :newdata
                WHERE applicantID = :applicantID; 
            ");

            $queryinsert->bindParam(':applicantID', $applicantID);
            $queryinsert->bindParam(':newdata', $newdata);

            // If they tried to insert the wrong datatype...
            try {
                $result = $queryinsert->execute();
            } catch (Exception $e) {
                return http_response_code(StatusCodes::BAD_REQUEST);
            }
        }

        return $applicantInfo;
    }

    /**
     * Checks permissions, then deletes an applicant.
     * @param $args
     * @return int
     */
    public function deleteApplicant($args) {
        // Attempt to delete.
        // If it doesn't work, return a fail.

        // check that user has priveleges to do this.
        $role = $this->getAccessRole();
        $tempAID = filter_var($args['id'], FILTER_SANITIZE_STRING);

        if ($role == $this::ACCESS_FACULTY) {
            $applicantID = $tempAID;
        } elseif ($role == $this::ACCESS_USER) {
            // make sure requested delete id matches user's.
            if ($tempAID == $this->userApplicantID) {
                $applicantID = $tempAID;
            } else {
                return http_response_code(StatusCodes::FORBIDDEN);
            }
        } else {
            return http_response_code(StatusCodes::FORBIDDEN);
        }

        try {
            $query = $this->dbc->prepare('
                DELETE
                FROM Applicants
                WHERE applicantID = :applicantID;
            ');
            $query->bindParam(':applicantID', $applicantID);

            $query->execute();
        } catch (Exception $e) {
            return http_response_code(StatusCodes::BAD_REQUEST);
        }

        if ($query->rowCount() < 1) {
            return http_response_code(StatusCodes::BAD_REQUEST);
        } else {
            return http_response_code(StatusCodes::OK);
        }

    }

    /**
     * Returns all applicants that have applications for a given scholarship ID.
     * @param $args
     * @return array|int
     */
    public function getScholarshipApplicants($args) {
        // faculty: get all applicants for a given ScholarshipID
        // student: do nothing 403
        $role = $this->getAccessRole();
        if ($role != $this::ACCESS_FACULTY) {
            return http_response_code(StatusCodes::FORBIDDEN);
        }

        // 'id' here represents ScholarshipID
        $scholarshipID = filter_var($args['id'], FILTER_SANITIZE_STRING);

        // get an array of matching applicantIDs
        $applicantIDs = array();
        $queryMatch = $this->dbc->prepare('
            SELECT at.applicantID as ApplicantID
            FROM Scholarship s INNER JOIN Applications an
                ON s.ScholarshipID = an.ScholarshipID
                INNER JOIN Applicants at ON an.ApplicantID = at.applicantID;
            WHERE s.ScholarshipID = :scholarshipID;
        ');
        $queryMatch->bindParam(':scholarshipID', $scholarshipID);
        $queryMatch->setFetchMode(PDO::FETCH_OBJ);

        // Not sure what could go wrong here, but...
        try {
            $result = $queryMatch->execute();
        } catch (Exception $e) {
            return http_response_code(StatusCodes::INTERNAL_SERVER_ERROR);
        }

        // if we didn't get any results from that ScholarshipID
        if ($queryMatch->rowCount() < 1) {
            return http_response_code(StatusCodes::NOT_FOUND);
        }

        // save all returned ApplicantIDs
        while ($row = $queryMatch->fetch()) {
            array_push($applicantIDs, $row->ApplicantID);
        }

        $queryMatch->closeCursor();

        $applicantArray = array();

        foreach ($applicantIDs as $applicantID) {
            $applicantArray[] = $this->getOneApplicant($applicantID);
        }

        return $applicantArray;

    }

    /**
     * Gets the token role while also setting Student variables,
     * if user is a student.
     * @return int
     */
    private function getAccessRole(): int {
        // We're doing this because we can get the
        // UserID->ApplicantID while checking the role.
        $tokenRole = Token::getRoleFromToken();

        if ($tokenRole === Token::ROLE_FACULTY) {
            return $this::ACCESS_FACULTY;
        }
        elseif ($tokenRole === Token::ROLE_STUDENT) {
            $tokenUsername = Token::getUsernameFromToken();
            $queryUserNameToID = $this->dbc->prepare('
                SELECT *
                FROM Users
                WHERE username = :username;
            ');

            $queryUserNameToID->bindParam(':username', $tokenUsername);
            $queryUserNameToID->setFetchMode(PDO::FETCH_ASSOC);

            try {
                $queryUserNameToID->execute();
            } catch (Exception $e) {
                $this->userApplicantID = null;
                return $this::ACCESS_USER;
            }

            // Let's assume there's one username to ID...
            // extract the userID
            $row = $queryUserNameToID->fetch();
            $this->tokenUserID = $row['userID'];

            // Now we can find the associated applicant, if exists.
            $queryAID = $this->dbc->prepare('
                SELECT applicantID, userID
                FROM Applicants
                WHERE userID = :userID;
            ');

            $queryAID->setFetchMode(PDO::FETCH_ASSOC);
            $queryAID->bindParam(':userID', $this->tokenUserID);

            try {
                $queryAID->execute();
            } catch (Exception $e) {
                $this->userApplicantID = null;
                return $this::ACCESS_USER;
            }

            $aidResult = $queryAID->fetch();
            $this->userApplicantID = $aidResult['applicantID'] ?? null;

            return $this::ACCESS_USER;
        } else {
            return $this::ACCESS_NONE;
        }
    }

    /**
     * Does not check permissions and gets one applicant.
     * @param int $applicantID
     * @return Applicant
     */
    private function getOneApplicant(int $applicantID): Applicant {
        $applicantInfo = array();

        $query = $this->dbc->prepare('
            SELECT *
            FROM Applicants
            WHERE applicantID = :applicantID;
        ');

        $query->bindParam(':applicantID', $applicantID);
        $query->setFetchMode(PDO::FETCH_ASSOC);

        try {
            $result = $query->execute();
        } catch (Exception $e) {
            return http_response_code(StatusCodes::INTERNAL_SERVER_ERROR);
        }
        $row = $query->fetch();
        $query->closeCursor();

        $applicant = new Applicant($row);

        return $applicant;
    }
}