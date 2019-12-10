<?php

namespace Scholarship\Models;

class Question implements \JsonSerializable {

    private $questionID;
    private $scholarshipID;
    private $question;
    private $type;

    public function __construct(int $questionID, int $scholarshipID, string $question, string $type) {
        $this->questionID = $questionID;
        $this->scholarshipID = $scholarshipID;
        $this->question = $question;
        $this->type = $type;
    }

    public function getQuestionID()
    {
        return $this->questionID;
    }

    public function getScholarshipID() {
        return $this->scholarshipID;
    }

    public function setScholarshipID($scholarshipID) {
        $this->scholarshipID = $scholarshipID;
    }

    public function getQuestion() {
        return $this->question;
    }

    public function setQuestion($question) {
        $this->question = $question;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function jsonSerialize() {
        $data = array();
        $data['questionID'] = $this->questionID;
        $data['scholarshipID'] = $this->scholarshipID;
        $data['question'] = $this->question;
        $data['type'] = $this->type;
        return $data;
    }
}
