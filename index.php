<?php
/**
 * Created by PhpStorm.
 * User: iamcaptaincode
 * Date: 10/23/2019
 * Time: 8:56 AM
 */

use Scholarship\Controllers\ScholarshipsController;
use Scholarship\Controllers\TokensController;
use Scholarship\Controllers\ApplicantsController;
use Scholarship\Models\Token as Token;
use Scholarship\Controllers\DonorsController;
use Scholarship\Http\Methods as Methods;
use Scholarship\Http\StatusCodes;
use Scholarship\Models\Token;
use Scholarship\Controllers\RatingsController;

require_once 'vendor/autoload.php';

header("Content-Type: application/json; charset=UTF-8");

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r){
    /** TOKENS CLOSURES */
    $handlePostLogin = function ($args) {
        $tokenController = new TokensController();
        //Is the data via a form?
        if (!empty($_POST['username'])) {
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $password = $_POST['password'] ?? "";
        } else {
            //Attempt to parse json input
            $json = (object) json_decode(file_get_contents('php://input'));
            if (count((array)$json) >= 2) {
                $username = filter_var($json->username, FILTER_SANITIZE_STRING);
                $password = $json->password;
            } else {
                http_response_code(StatusCodes::BAD_REQUEST);
                exit();
            }
        }
        return $tokenController->buildToken($username, $password);
    };

    $getScholarship = function( $args ) {
        if (Token::getRoleFromToken() === Token::ROLE_FACULTY || Token::getRoleFromToken() === Token::ROLE_STUDENT) {
            $ctrl = new ScholarshipsController();
            return $ctrl->getScholarship($args['scholarshipID']);
        } else {
            return http_response_code(StatusCodes::UNAUTHORIZED);
        }
    };

    $getScholarships = function() {
        if (Token::getRoleFromToken() === Token::ROLE_FACULTY || Token::getRoleFromToken() === Token::ROLE_STUDENT) {
            $ctrl = new ScholarshipsController();
            return $ctrl->getAllScholarships();
        } else {
            return http_response_code(StatusCodes::UNAUTHORIZED);
        }
    };

    $getScholarshipsByDonor = function( $args ) {
        if (Token::getRoleFromToken() === Token::ROLE_FACULTY) {
            $ctrl = new ScholarshipsController();
            return $ctrl->getScholarshipsByDonor($args['donorID']);
        } else {
            return http_response_code(StatusCodes::UNAUTHORIZED);
        }
    };

    $getScholarshipByApplication = function( $args ) {
        if (Token::getRoleFromToken() === Token::ROLE_FACULTY) {
            $ctrl = new ScholarshipsController();
            return $ctrl->getScholarshipByApplication($args['applicationID']);
        } else {
            return http_response_code(StatusCodes::UNAUTHORIZED);
        }
    };

    $handlePostScholarships = function ($args) {
        if (Token::getRoleFromToken() === Token::ROLE_FACULTY) {
            $scholarshipController = new ScholarshipsController();
            $json = (object) json_decode(file_get_contents('php://input'));

            if (count((array)$json) >= 4) {
                $name = filter_var($json->Name, FILTER_SANITIZE_STRING);
                $description = filter_var($json->Description, FILTER_SANITIZE_STRING);
                $qualifications = filter_var($json->Qualifications, FILTER_SANITIZE_STRING);
                $amount = filter_var($json->Amount, FILTER_SANITIZE_STRING);
                $questionArr = $json->Questions;

                $scholarshipController->postScholarship($name,$description,$qualifications,$amount,$questionArr);
            } else {
                http_response_code(StatusCodes::BAD_REQUEST);
                exit();
            }
        } else {
            return http_response_code(StatusCodes::UNAUTHORIZED);
        }
    };

    $updateScholarship = function( $args) {
        if (Token::getRoleFromToken() === Token::ROLE_FACULTY) {
            $scholarshipController = new ScholarshipsController();
            $scholarshipID = filter_var($args['scholarshipID'], FILTER_SANITIZE_STRING);
            $json = (object) json_decode(file_get_contents('php://input'));
            var_dump($json);
            if (count((array)$json) >= 1) {
                $scholarshipController->updateScholarship($scholarshipID, $json);
            } else {
                http_response_code(StatusCodes::BAD_REQUEST);
                exit();
            }
        } else {
            return http_response_code(StatusCodes::UNAUTHORIZED);
        }
    };

    $deleteScholarship = function ($args) {
        if (Token::getRoleFromToken() === Token::ROLE_FACULTY) {
            $ctrl = new ScholarshipsController();
            $ctrl->deleteScholarship($args['scholarshipID']);
        } else {
            return http_response_code(StatusCodes::UNAUTHORIZED);
        }
    /**
     * @param $args
     * Accesses the Ratings Controller to insert a rating,
     * by decoding a JSON object.
     */
    $insertRating = function($args){
        try {
            $ratingsController = new RatingsController();
            $json = (object) json_decode(file_get_contents('php://input'));

            return $ratingsController->insertRating($json);
        }catch(Exception $e)
        {
            echo $e;
            http_response_code(StatusCodes::BAD_REQUEST);
            exit();
        }
    };

    /**
     * @param $args
     * Accesses the Ratings Controller to
     * display UserRatings based on the Application ID
     */
    $applicationID = function($args){
        try
        {
        $ratingsController = new RatingsController();
        //$json = (object) json_decode(file_get_contents('php://input'));
        //$applicationID = filter_var($json->applicationID, FILTER_SANITIZE_STRING)
        return $ratingsController->applicationID($args);
        }catch(Exception $e)
        {
            echo $e;
            http_response_code(StatusCodes::BAD_REQUEST);
            exit();
        }
    };

    /**
     * @return array
     * Returns all the ratings from the ratings table
     * through the RatingsController and the method defined
     * there.
     */
    $getAllRatings = function(){
        try
        {
            $ratingsController = new RatingsController();
            return $ratingsController->getAllRatings();
        }catch(Exception $e)
        {
            echo $e;
            http_response_code(StatusCodes::BAD_REQUEST);
            exit();
        }
    };

    /**
     * @param $args
     * @return array|void
     * Uses the RatingsController to return
     * all the ratings inserted by a specific user
     */
    $ratingsByUser = function($args){
        try
        {
            $ratingsController = new RatingsController();
        //$json = (object) json_decode(file_get_contents('php://input'));
            return $ratingsController->ratingsByUser($args);
        }catch(Exception $e)
        {
            echo $e;
            http_response_code(StatusCodes::BAD_REQUEST);
            exit();
        }
    };

    /**
     * @param $args
     * Uses the RatingsController to accessing ratings
     * based on a scholarship ID
     */
    $ScholarshipRatings = function($args){
        try
        {
            $ratingsController = new RatingsController();
//        $fkScholarshipID = filter_var($_POST['fkScholarshipID'], FILTER_SANITIZE_STRING);
        //$json = (object) json_decode(file_get_contents('php://input'));
        //$fkScholarshipID = filter_var($json->fkScholarshipID, FILTER_SANITIZE_STRING);
            return $ratingsController->ScholarshipRatings($args);
        }catch(Exception $e)
        {
            echo $e;
            http_response_code(StatusCodes::BAD_REQUEST);
            exit();
        }
    };

    /**
     * @param $args
     * Uses the RatingsController to access the method
     * to pull the ratings for awards based on a specific
     * Application ID
     */
    $ratingsForAwardsByApplication = function($args){
        try{
            $ratingsController = new RatingsController();
            //$awardID = filter_var($_POST['awardID'], FILTER_SANITIZE_STRING);
            //$json = (object) json_decode(file_get_contents('php://input'));
            //$awardID = filter_var($json->awardID, FILTER_SANITIZE_STRING);
            return $ratingsController->ratingsForAwardsByApplication($args);
        }catch(Exception $e)
        {
            echo $e;
            http_response_code(StatusCodes::BAD_REQUEST);
            exit();
        }
    };

    /**
     * @param $args
     * @return array
     * Uses the function defined in RatingsController
     * to access the ratings by a specific rating ID
     */
    $getRatingByID = function($args){
        try{
            $ratingsController = new RatingsController();
//        $ratingID = filter_var($_POST['ratingID'], FILTER_SANITIZE_STRING);
        //$json = (object) json_decode(file_get_contents('php://input'));
        //$ratingsID = filter_var($json->ratingsID, FILTER_SANITIZE_STRING);
            return $ratingsController->getRatingByID($args);
        }catch(Exception $e)
        {
            echo $e;
            http_response_code(StatusCodes::BAD_REQUEST);
            exit();
        }
    $handlePostDonor = function ($args){
        if (Token::getRoleFromToken() == Token::ROLE_FACULTY){
            $donorController = new DonorsController();
            if (!empty($_POST['FirstName']) && !empty($_POST['LastName'])) {
                $donorID = isset($_POST['ID']) ? $_POST['ID'] : 0;
                $first = $_POST['FirstName'];
                $last = $_POST['LastName'];
                $organization = isset($_POST['Organization']) ? $_POST['Organization'] : null;
                $address = isset($_POST['Address']) ? $_POST['Address'] : null;
                $city = isset($_POST['City']) ? $_POST['City'] : null;
                $state = isset($_POST['State']) ? $_POST['State'] : null;
                $zip = isset($_POST['Zip']) ? $_POST['Zip'] : null;
                $phone = isset($_POST['Phone']) ? $_POST['Phone'] : null;
                $email = isset($_POST['Email']) ? $_POST['Email'] : null;
                $donorController->postDonor($donorID, $first, $last, $organization, $address, $city, $state, $zip, $phone, $email);
                http_response_code(StatusCodes::CREATED);
            }
        }
        else{
            http_response_code(StatusCodes::UNAUTHORIZED);
        }
    };

    $getSingleDonor = function ($arg){
        if (Token::getRoleFromToken() == Token::ROLE_FACULTY) {
            $ctrl = new DonorsController();
            return $ctrl->getDonor($arg['id']);
        }
        else{
            http_response_code(StatusCodes::UNAUTHORIZED);
        }
    };

    $getAllDonors = function (){
        if (Token::getRoleFromToken() == Token::ROLE_FACULTY) {
            $ctrl = new DonorsController();
            return $ctrl->getAll();
        }
       else {
            http_response_code(StatusCodes::UNAUTHORIZED);
        }
    };

    $getAllDonorsByScholarship = function ($arg){
        if (Token::getRoleFromToken() == Token::ROLE_FACULTY) {
            $ctrl = new DonorsController();
            return $ctrl->getAllByScholarshipId($arg);
        }
        else{
            http_response_code(StatusCodes::UNAUTHORIZED);
        }
    };
      
      
    /** Applicant Closures */
    $getAllApplicants = function($args) {
        $ctl = new \Scholarship\Controllers\ApplicantsController();
        return $ctl->getAllApplicants($args);
    };

    $createApplicant = function($args) {
        $ctl = new \Scholarship\Controllers\ApplicantsController();
        $json = (object) json_decode(file_get_contents('php://input'));
        return $ctl->createApplicant($json);
    };

    $getApplicant = function($args) {
        $ctl = new \Scholarship\Controllers\ApplicantsController();
        return $ctl->getApplicant($args);
    };

    $updateApplicant = function($args) {
        $ctl = new \Scholarship\Controllers\ApplicantsController();

        // Using an associative array such that we can grab and work with keys.
        $json = json_decode(file_get_contents('php://input'), TRUE);
        return $ctl->updateApplicant($args, $json);
    };

    $deleteApplicant = function($args) {
        $ctl = new \Scholarship\Controllers\ApplicantsController();
        return $ctl->deleteApplicant($args);
    };

    $getScholarshipApplicants = function($args) {
        $ctl = new \Scholarship\Controllers\ApplicantsController();
        return $ctl->getScholarshipApplicants($args);
    };

    /** TOKEN ROUTE */
    // accessible by anyone
    $r->addRoute(Methods::POST, 'login', $handlePostLogin);
    $r->addRoute(Methods::POST, 'login/', $handlePostLogin);

    // accessible by students and faculty
    $r->addRoute(Methods::GET,'scholarships', $getScholarships);
    $r->addRoute(Methods::GET,'scholarships/{scholarshipID:\d+}', $getScholarship);

    // accessible by faculty
    $r->addRoute(Methods::GET, 'donors/{donorID:\d+}/scholarships', $getScholarshipsByDonor);
    $r->addRoute(Methods::GET, 'applications/{applicationID:\d+}/scholarships', $getScholarshipByApplication);
    $r->addRoute(Methods::POST,'scholarships', $handlePostScholarships);
    $r->addRoute(Methods::PUT, 'scholarships/{scholarshipID:\d+}', $updateScholarship);
    $r->addRoute(Methods::DELETE,'scholarships/{scholarshipID:\d+}', $deleteScholarship);

    /** Ratings Routes */
    $r->addRoute(Methods::POST, 'rating', $insertRating);
    $r->addRoute(Methods::GET, 'rating/{id:\d+}/application', $applicationID);
    $r->addRoute(Methods::GET, 'rating', $getAllRatings);
    $r->addRoute(Methods::GET, 'rating/{id:\d+}/user', $ratingsByUser);
    $r->addRoute(Methods::GET, 'rating/{id:\d+}/scholarship', $ScholarshipRatings);
    $r->addRoute(Methods::GET, 'rating/{id:\d+}/award', $ratingsForAwardsByApplication);
    $r->addRoute(Methods::GET, 'rating/{id:\d+}/rating', $getRatingByID);

    /** Applicant Routes */
    // slash duplicates included for convenience.
    $r->addRoute(Methods::GET, 'applicants', $getAllApplicants);
    $r->addRoute(Methods::GET, 'applicants/', $getAllApplicants);
    $r->addRoute(Methods::POST, 'applicants', $createApplicant);
    $r->addRoute(Methods::POST, 'applicants/', $createApplicant);
    $r->addRoute(Methods::GET, 'applicants/{id:\d+}', $getApplicant);
    $r->addRoute(Methods::GET, 'applicants/{id:\d+}/', $getApplicant);
    $r->addRoute(Methods::PATCH, 'applicants/{id:\d+}', $updateApplicant);
    $r->addRoute(Methods::PATCH, 'applicants/{id:\d+}/', $updateApplicant);
    $r->addRoute(Methods::DELETE, 'applicants/{id:\d+}', $deleteApplicant);
    $r->addRoute(Methods::DELETE, 'applicants/{id:\d+}/', $deleteApplicant);
    $r->addRoute(Methods::GET, 'scholarships/{id:\d+}/applicants', $getScholarshipApplicants);
    $r->addRoute(Methods::GET, 'scholarships/{id:\d+}/applicants/', $getScholarshipApplicants);
    /** Donor Route */
    $r->addRoute('GET', 'donors/{id:\d+}', $getSingleDonor);
    $r->addRoute('GET', 'donors/', $getAllDonors);
    $r->addRoute('GET', 'donors', $getAllDonors);
    $r->addRoute(Methods::POST, 'donors/', $handlePostDonor);
    $r->addRoute(Methods::POST, 'donors', $handlePostDonor);
    $r->addRoute('GET', 'scholarships/{id:\d+}/donors', $getAllDonorsByScholarship);
    $r->addRoute('GET', 'scholarships/{id:\d+}/donors/', $getAllDonorsByScholarship);
});

$http_method = $_SERVER['REQUEST_METHOD'];
$uri = $_GET['request'] ?? '';
$routeInfo = $dispatcher->dispatch($http_method, $uri);

switch($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(StatusCodes::NOT_FOUND);
        //Handle 404
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(StatusCodes::METHOD_NOT_ALLOWED);
        //Handle 403
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler  = $routeInfo[1];
        $vars = $routeInfo[2];

        $response = $handler($vars);
        echo json_encode($response);
        break;
}

