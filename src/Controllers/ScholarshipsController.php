<?php
namespace Scholarship\Controllers;

use PDO;
use \Scholarship\Models\Scholarship as Scholarship;
use \Scholarship\Http\StatusCodes;
use \Scholarship\Utilities\DatabaseConnection as dbo;
use \Scholarship\Models\Question as Question;


class ScholarshipsController{
    private $dbn;

    public function _construct()    {
        $this->dbn = DatabaseConnection::getInstance();
    }

    public function getScholarship(int $id)    {
        $isDeleted = false;
        $pdo = dbo::getInstance();
        $select = $pdo->prepare('SELECT * FROM `Scholarship` WHERE `ScholarshipID`=:id AND `IsDeleted`=:isDeleted');
        $select->bindParam('id',$id);
        $select->bindParam(':isDeleted', $isDeleted);
        $select->setFetchMode(PDO::FETCH_ASSOC);
        $select->execute();

        $scholarshipArr = array();
        while($row = $select->fetch()) {
            $questionArr = ScholarshipsController::buildQuestionArr($row['ScholarshipID']);
            $scholarshipArr[] = new Scholarship($row['ScholarshipID'], $row['Name'], $row['Description'], $row['Qualifications'], $row['Amount'], $questionArr);
        }
        return (!empty($scholarshipArr) ? $scholarshipArr : http_response_code(StatusCodes::NOT_FOUND));
    }

    public function getAllScholarships() {
        $isDeleted = false;
        $pdo = dbo::getInstance();
        $select = $pdo->prepare('SELECT * FROM `Scholarship` WHERE `IsDeleted` = :isDeleted');
        $select->bindParam(':isDeleted', $isDeleted);
        $select->setFetchMode(PDO::FETCH_ASSOC);
        $select->execute();

        $scholarshipArr = array();
        while($row = $select->fetch()) {
            $questionArr = ScholarshipsController::buildQuestionArr($row['ScholarshipID']);
            $scholarshipArr[] = new Scholarship($row['ScholarshipID'],$row['Name'],$row['Description'],$row['Qualifications'],$row['Amount'], $questionArr);
        }
        return (!empty($scholarshipArr) ? $scholarshipArr : http_response_code(StatusCodes::NOT_FOUND));
    }

    public function getScholarshipsByDonor(int $id) {
        $pdo = dbo::getInstance();
        $select = $pdo->prepare( 'SELECT * FROM `Scholarship` s
                                            INNER JOIN `DonorsAndScholarships` ds ON s.`ScholarshipID` = ds.`scholarshipID`
                                            WHERE `donorID` = :id');
        $select->bindParam(':id',$id);
        $select->setFetchMode(PDO::FETCH_ASSOC);
        $select->execute();
        while($row = $select->fetch()) {
            $questionArr = ScholarshipsController::buildQuestionArr($row['ScholarshipID']);
            $array[] = new Scholarship($row['ScholarshipID'], $row['Name'], $row['Description'], $row['Qualifications'], $row['Amount'], $questionArr);
        }
        return (!empty($array) ? $array : http_response_code(StatusCodes::NOT_FOUND));
    }

    public function getScholarshipByApplication(int $id) {
        $pdo = dbo::getInstance();
        $select = $pdo->prepare( 'SELECT * FROM `Scholarship` s
                                            INNER JOIN `Applications` a ON s.ScholarshipID = a.ScholarshipID
                                            WHERE `ApplicationID` = :id');
        $select->bindParam(':id',$id);
        $select->setFetchMode(PDO::FETCH_ASSOC);
        $select->execute();

        while($row = $select->fetch()) {
            $questionArr = ScholarshipsController::buildQuestionArr($row['ScholarshipID']);
            $array[] = new Scholarship($row['ScholarshipID'], $row['Name'], $row['Description'], $row['Qualifications'], $row['Amount'], $questionArr);
        }
        return (!empty($array) ? $array : http_response_code(StatusCodes::NOT_FOUND));
    }

    public function postScholarship(string $name, string $description, string $qualifications, string $amount, array $questionArr) {
        $pdo = dbo::getInstance();
        $insert = $pdo->prepare('INSERT INTO `Scholarship` (`Name`,`Description`,`Qualifications`,`Amount`) VALUES (:name, :description, :qualifications, :amount)');
        $insert->bindParam(':name', $name);
        $insert->bindParam(':description', $description);
        $insert->bindParam(':qualifications', $qualifications);
        $insert->bindParam(':amount', $amount);
        $insert->execute();

        $scholarshipID = $pdo->lastInsertId();
        $this->addQuestions($scholarshipID, $questionArr);
    }

    public function updateScholarship($scholarshipID, $json) {
        if (!empty($json->Name)) {
            $this->updateScholarshipDBValue($scholarshipID, 'Name', $json->Name);
        }
        if (!empty($json->Description)) {
            $this->updateScholarshipDBValue($scholarshipID, 'Description', $json->Description);
        }
        if (!empty($json->Qualifications)) {
            $this->updateScholarshipDBValue($scholarshipID, 'Qualifications', $json->Qualifications);
        }
        if (!empty($json->Amount)) {
            $this->updateScholarshipDBValue($scholarshipID, 'Amount', $json->Amount);
        }
        // TODO: Update for questions
        if (!empty($json->Question)) {
            $this->updateQuestionDBValue($scholarshipID, $json->Question);
        }
    }

    public function updateScholarshipDBValue($scholarshipID, $column, $value) {
        $pdo = dbo::getInstance();
        $insert = $pdo->prepare("UPDATE `Scholarship` SET $column = :value WHERE `ScholarshipID` = :scholarshipID");
        $insert->bindParam(':scholarshipID', $scholarshipID);
        $insert->bindParam(':value', $value);
        $insert->execute();
    }

    // Work in progress
    public function updateQuestionDBValue($scholarshipID, $question) {
        $pdo = dbo::getInstance();
        $insert = $pdo->prepare("UPDATE `Questions` SET `Question` = :question, `Type` = :type WHERE `ScholarshipID` = :scholarshipID AND `QuestionID` = :questionID");
        $insert->bindParam(':scholarshipID', $scholarshipID);
        $insert->bindParam(':questionID', $question->questionID);
        $insert->bindParam(':question', $question->question);
        $insert->bindParam(':type', $question->type);
        $insert->execute();
    }


    public function deleteScholarship(int $scholarshipID) {
        // Soft delete from Scholarship table
        $isDeleted = true;
        $pdo = dbo::getInstance();
        $select = $pdo->prepare('UPDATE `Scholarship` SET `IsDeleted`=:isDeleted WHERE `ScholarshipID`=:id');
        $select->bindParam(':id',$scholarshipID);
        $select->bindParam(':isDeleted', $isDeleted);
        $select->execute();

        // Soft delete from Questions table
        $isDeleted = true;
        $pdo = dbo::getInstance();
        $select = $pdo->prepare('UPDATE `Questions` SET `IsDeleted`=:isDeleted WHERE `ScholarshipID`=:id');
        $select->bindParam(':id',$scholarshipID);
        $select->bindParam(':isDeleted', $isDeleted);
        $select->execute();
    }

    public function addQuestions(int $scholarshipID, $questionArr) {
        foreach($questionArr as $question) {
            $newQuestion = filter_var($question->question, FILTER_SANITIZE_STRING);
            $newType = filter_var($question->type, FILTER_SANITIZE_STRING);

            $pdo = dbo::getInstance();
            $insert = $pdo->prepare('INSERT INTO `Questions` (`ScholarshipID`, `Question`, `Type`) 
                                               VALUES (:scholarshipID, :question, :type)');
            $insert->bindParam(':scholarshipID', $scholarshipID);
            $insert->bindParam(':question', $newQuestion);
            $insert->bindParam(':type', $newType);
            $insert->execute();
        }
    }

    public function buildQuestionArr(int $scholarshipID) {
        $isDeleted = false;
        $pdo = dbo::getInstance();
        $select = $pdo->prepare('SELECT * FROM `Questions` WHERE `ScholarshipID`=:scholarshipID AND `IsDeleted`=:isDeleted');
        $select->bindParam('scholarshipID',$scholarshipID);
        $select->bindParam(':isDeleted', $isDeleted);
        $select->setFetchMode(PDO::FETCH_ASSOC);
        $select->execute();

        $questionArr = array();
        while ($row = $select->fetch()) {
            $questionArr[] = new Question($row['QuestionID'], $row['ScholarshipID'], $row['Question'], $row['Type']);
        }
        return $questionArr;
    }

    // TODO: Remove this method?
    public function getQuestionsForScholarship(int $scholarshipID) {
        $pdo = dbo::getInstance();
        $query = $pdo->prepare('SELECT * FROM `Question` WHERE `ScholarshipID`=:scholarshipID');
        $query->bindParam(':scholarshipID',$scholarshipID);
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $query->execute();
        while($row = $query->fetch()) {
            $arr[] = new Scholarship($row['QuestionID'], $row['ScholarshipID'], $row['Question'], $row['Type']);
        }
        return $arr;
    }

    /*  //WG: stretch goal
    public function removeQuestion(int $questionID) {
        $isDeleted = true;
        $pdo = dbo::getInstance();  //connect to database
        $remove = $pdo->prepare('UPDATE `Questions`
                                            SET `IsDeleted` = :isDeleted WHERE `QuestionID` = $questionID');
        $remove->bindParam('ScholarshipID', $scholarshipID);
        $remove->bindParam('Question', $question);
        $remove->bindParam('Type', $type);
        $remove->bindParam('isDeleted', $isDeleted);
        $remove->execute();
        return "Removing Question"; //WG: remove after everything works
    } */
}
