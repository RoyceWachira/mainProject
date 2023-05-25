<?php


  class DbOperation{
   
    private $con;
    //constuctor which aids in db connection
    function __construct()
    {
        require_once  dirname(__FILE__).'/DbConnect.php';

        $db = new DbConnect();

        $this->con = $db->connect();
    }
    
    //function for creating a user
    public function createUser($firstName,$lastName,$userName,$phoneNumber,$email,$gender,$pass){

        $password=password_hash($pass, PASSWORD_DEFAULT);

        $stmt= $this->con->prepare("INSERT INTO `user` (`first_name`, `last_name`, `username`, `phone_number`, `email`, `gender`, `password`, `role`, `created_at`) VALUES( ?, ?, ?, ?, ?, ?, ? , 'user', NOW());");
        $stmt->bind_param("ssssssss",$firstName,$lastName,$userName,$phoneNumber,$email,$gender,$password);
    
         if($stmt->execute()){
            return true;
         }
         else{
            return false;
         }
    }
    

    public function updateUser($userId, $firstName, $lastName, $userName, $phoneNumber, $email, $gender) {

        $stmt = $this->con->prepare("UPDATE user SET first_name = ?, last_name = ?, username = ?, phone_number = ?, email = ?, gender = ?, updated_at = NOW() WHERE user_id = ?");
        $stmt->bind_param("ssssssi", $firstName, $lastName, $userName, $phoneNumber, $email, $gender, $userId);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    

    //creating default admin
    public function createAdmin(){
        $password=password_hash('admin123', PASSWORD_DEFAULT);
        $stmt= $this->con->prepare("INSERT INTO `user` (`user_id`,`username`,`email`,`password`,`role`) VALUES(NULL,'admin1','admin1@gmail.com','$password','admin');");
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }

    //checking if admin exists in db
    public function createDefaultAdmin(){
      $stmt =  $this->con->prepare("SELECT role from user WHERE role= ?");
      $role='admin';
      $stmt-> bind_param("s",$role);
      $stmt->execute();
      $stmt->store_result();
      if($stmt->num_rows == 0){
          $this->createAdmin();
      }
  
    }
  
    //function for user login 
    public function userLogin($username, $pass){
        $stmt = $this->con->prepare("SELECT * from user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if($result->num_rows > 0){
            $user = $result->fetch_assoc();
            $cpass = $user['password'];
    
            if(password_verify($pass, $cpass)){
                $crole = $user['role'];
    
                if($crole == 'admin'){
                    return 2;
                }else{
                    return 1;
                }
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    

    //function to fetch user 
    public function getUser($userId){
        $stmt =  $this->con->prepare("SELECT * from user WHERE user_id= ?");
        $stmt-> bind_param("s",$userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUsersession($username){
        $stmt =  $this->con->prepare("SELECT * from user WHERE username= ?");
        $stmt-> bind_param("s",$username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    //function to get all users 
    public function getAllUsers(){
        $stmt = $this->con->prepare("SELECT * FROM user");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = array();
        while($user = $result->fetch_assoc()){
            $users[] = $user;
        }
        return $users;
    }

    public function getTotalUserCount() {
        $stmt = $this->con->prepare("SELECT COUNT(*) AS total_count FROM user");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $totalCount = $row['total_count'];
        return $totalCount;
    }

    public function getTotalMembersCount($chamaId) {
        $stmt = $this->con->prepare("SELECT COUNT(*) AS total_members FROM chamamembers WHERE chama_id=?");
        $stmt->bind_param("i",$chamaId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $totalMembers = $row['total_members'];
        return $totalMembers;
    }
    
    

    //function to check if user exists in db
    public function isUserExist($email) {
        $stmt = $this->con->prepare("SELECT user_id FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    //function for logout
    public function logout(){
        session_start();
        unset($_SESSION['username']);
        session_destroy();
        return true;
    }

    //CRUD for chamas

    public function createChama($chama_name, $description,$user_id, $contribution_period, $system_flow) {
    
        // Prepare statement to insert chama into chama table
        $stmt = $this->con->prepare("INSERT INTO `chama` ( `chama_name`, `chama_description`, `chairperson_id`, `contribution_period`, `system_flow`, `created_at`) VALUES ( ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssiss", $chama_name, $description, $user_id, $contribution_period, $system_flow);
    
        if ($stmt->execute()) {
            // Get the last inserted chama_id
            $chama_id = $this->con->insert_id;
            // Insert the user who created the chama into the chama_user table as admin
            $stmt = $this->con->prepare("INSERT INTO `chamamembers` (`chama_id`, `user_id`, `chama_role`) VALUES (?, ?, 'ChairPerson')");
            $stmt->bind_param("ss", $chama_id, $user_id);
    
            if ($stmt->execute()) {
                return true;
            }
        }
    
        return false;
    }
     
    //function to fetch a single chama 
    public function getChama($chama_id){
        $stmt = $this->con->prepare("SELECT * FROM chama WHERE chama_id = ?");
        $stmt->bind_param("i", $chama_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    //function to fetch all chamas
    public function getAllChamas() {
        $stmt = $this->con->prepare("SELECT * FROM chama");
        $stmt->execute();
        $result = $stmt->get_result();
        $chamas = array();
        while ($chama = $result->fetch_assoc()) {
            $chamas[] = $chama;
        }
        return $chamas;
    }



    public function getTotalChamaCount() {
        $stmt = $this->con->prepare("SELECT COUNT(*) AS total_count FROM chama");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $totalCount = $row['total_count'];
        return $totalCount;
    }

    public function updateChama($id, $chama_name, $description) {
        $stmt = $this->con->prepare("UPDATE chama SET chama_name = ?, description = ? WHERE chama_id = ?");
        $stmt->bind_param("ssi", $chama_name, $description, $id);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function addUserToChama($chama_id, $user_id){
        try {
            $admin=$_SESSION['userId'];
            // Get the role of the logged in user in the specified chama
            $stmt = $this->con->prepare("SELECT chairperson_id FROM chama WHERE chama_id = ?");
            $stmt->bind_param("i", $chama_id);
            if (!$stmt->execute()) {
                echo $stmt->error;
                throw new Exception("Error executing the SELECT statement: " . $stmt->error);
            }else{
            $result = $stmt->get_result();
            $loggedInUserRole = $result->fetch_assoc()['chairperson_id'];
            }
    
            // If the logged in user is the ChairPerson of the chama, add the specified user to the chama
            if($loggedInUserRole == $admin){

                $stmt = $this->con->prepare("INSERT INTO `chamamembers` (`chama_id`, `user_id`, `chama_role`) VALUES (?, ?, 'Member')");
                $stmt->bind_param("ii", $chama_id, $user_id);
                if (!$stmt->execute()) {
                    throw new Exception("Error executing the INSERT statement: " . $stmt->error);
                }else{
                return true;
                }
            }
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    //function to check if user exists in db
    public function ifMemberExist($user_id) {
        $stmt = $this->con->prepare("SELECT user_id FROM chamamembers WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function isJoined($userId, $chamaId) {
        $stmt = $this->con->prepare("SELECT user_id, chama_id FROM chamamembers WHERE user_id = ? AND chama_id = ?");
        $stmt->bind_param("ii", $userId, $chamaId);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0){
            return true;
        }else{
            return false;
        }
    }

    public function getChamasUserNotJoined($userId) {
        $stmt = $this->con->prepare("SELECT chama_id FROM chamamembers WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $joinedChamas = array();
        while ($chama = $result->fetch_assoc()) {
            $joinedChamas[] = $chama['chama_id'];
        }
    
        if (empty($joinedChamas)) {
            // If the user has not joined any chama, return all chamas
            $stmt = $this->con->prepare("SELECT * FROM chama");
        } else {
            // If the user has joined some chamas, retrieve chamas they have not joined
            $stmt = $this->con->prepare("SELECT * FROM chama WHERE chama_id NOT IN (".implode(',', $joinedChamas).")");
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
        $chamasNotJoined = array();
        while ($row = $result->fetch_assoc()) {
            $chamasNotJoined[] = $row;
        }
    
        return $chamasNotJoined;
    }
    



    
    public function chamaExists($chama_name) {
        $stmt = $this->con->prepare("SELECT chama_id FROM chama WHERE chama_name = ?");
        $stmt->bind_param("s", $chama_name);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function getUserJoinedChamas($userId) {
        $stmt = $this->con->prepare("SELECT chama.* FROM chama INNER JOIN chamamembers ON chama.chama_id = chamamembers.chama_id WHERE chamamembers.user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $chamas = array();
        while ($chama = $result->fetch_assoc()) {
            $chamas[] = $chama;
        }
        
        return $chamas;
    }

    public function makeContribution($chamaId, $contributionAmount) {

        // Get the current datetime in MySQL datetime format
        $contributedAt = date('Y-m-d H:i:s');
    
        //get user_id from currently logged in user
        $userId = $_SESSION['userId'];
    
        // Get the contribution period for the specified chama
        $stmt = $this->con->prepare("SELECT contribution_period FROM chama WHERE chama_id = ?");
        $stmt->bind_param("i", $chamaId);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("No results found for chama ID: " . $chamaId);
            } else {
                $contributionPeriod = $result->fetch_assoc()['contribution_period'];
            }
        }
    
        // Calculate the next contribution date by adding the contribution period to the current date and time
        $nextContributionDate = date('Y-m-d H:i:s', strtotime($contributedAt . ' + ' . $contributionPeriod));
    
        // Insert the contribution into the database
        $stmt = $this->con->prepare("INSERT INTO `contributions` (`chama_id`, `user_id`, `contribution_amount`, `contribution_date`, `next_contribution_date`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iidss", $chamaId, $userId, $contributionAmount, $contributedAt, $nextContributionDate);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    

    //function to fetch a single contribution 
    public function getMemberContribution($userId,$chamaId){
        $stmt = $this->con->prepare("SELECT * FROM contributions WHERE user_id = ? AND chama_id=? ");
        $stmt->bind_param("ii", $userId,$chamaId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    //function to fetch all contributions
    public function getAllContributions(){
        $stmt = $this->con->prepare("SELECT * FROM contributions");
        $stmt->execute();
        $result = $stmt->get_result();
        $contributions = array();
        while($contribution = $result->fetch_assoc()){
        array_push($contributions, $contribution);
        }
        return $contributions;
    }
    
    public function updateContribution($contributionId, $contributionAmount) {
        $stmt = $this->con->prepare("UPDATE contributions SET contribution_amount = ? WHERE contribution_id = ?");
        $stmt->bind_param("di", $contributionAmount, $contributionId);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function makeWithdrawal($chamaId, $withdrwalAmount, $withdrwalReason) {
        // Get the current datetime in MySQL datetime format
        $withdrewAt = date('Y-m-d H:i:s');


        //get user_id from currently logged in user
        $userId = $_SESSION['userId'];

        // Get the role of the logged in user in the specified chama
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE chama_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $chamaId, $userId);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("No results found for chama ID: " . $chamaId);
            } else {
                $loggedInUserRole = $result->fetch_assoc()['chama_role'];
            }
        }
    
        if ($loggedInUserRole == 'Chairperson' || $loggedInUserRole == 'Treasurer') {

        // Insert the withdrawals into the database
        $stmt = $this->con->prepare("INSERT INTO `withdrawals` (`chama_id`, `user_id`, `withdrawal_amount`, `withdrawal_date`, `withdrawal_reason`) VALUES (?, ? , ?, ?, ?)");
        $stmt->bind_param("iidss", $chamaId, $userId, $withdrwalAmount, $withdrewAt, $withdrwalReason);
    
        if ($stmt->execute()) {
            return true;
         } else {
            return false;
            }
        }
    }
    
    //function to fetch a single withdrawal 
    public function getWithdrawal($withdrawalId){
        $stmt = $this->con->prepare("SELECT * FROM withdrawals WHERE withdrawal_id = ?");
        $stmt->bind_param("i", $withdrawalId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
        
    //function to fetch all withdrawals
    public function getAllWithdrawals(){
        $stmt = $this->con->prepare("SELECT * FROM withdrawals");
        $stmt->execute();
        $result = $stmt->get_result();
        $withdrawals = array();
        while($withdrawal = $result->fetch_assoc()){
           array_push($withdrawals, $withdrawal);
        }
        return $withdrawals;
    }
        
    public function updateWithdrawal($withdrawalId, $withdrwalAmount, $withdrwalReason,$chamaId) {

        //get user_id from currently logged in user
        $userId = $_SESSION['userId'];

        // Get the role of the logged in user in the specified chama
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE chama_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $chamaId, $userId);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("No results found for chama ID: " . $chamaId);
            } else {
                $loggedInUserRole = $result->fetch_assoc()['chama_role'];
            }
        }
    
        if ($loggedInUserRole == 'Chairperson' || $loggedInUserRole == 'Treasurer') {
        $stmt = $this->con->prepare("UPDATE withdrawals SET withdrawal_amount = ?, SET withdrawal_reason = ? WHERE withdrawal_id = ?");
        $stmt->bind_param("dsi", $withdrwalAmount, $withdrwalReason , $withdrawalId);
            
        if ($stmt->execute()) {
            return true;
        } else {
        return false;
            }
        }
    }

    public function chargeFine($chamaId, $userId, $fineAmount, $fineReason) {
        
        // Insert the fines into the database
        $stmt = $this->con->prepare("INSERT INTO `fines` (`chama_id`, `user_id`, `fine_amount`, `fine_reason`, `date_fined`, `fine_status`) VALUES (?, ?, ?, ?, NOW(), 'Not Paid')");
        $stmt->bind_param("iids", $chamaId, $userId, $fineAmount, $fineReason);
    
        if ($stmt->execute()) {
            return true;
         } else {
            return false;
        }
    }
    
    //function to fetch a single fine 
    public function getFine($fineId){
        $stmt = $this->con->prepare("SELECT * FROM fines WHERE fine_id = ?");
        $stmt->bind_param("i", $fineId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
        
    //function to fetch all fines
    public function getAllFines(){
        $stmt = $this->con->prepare("SELECT * FROM fines");
        $stmt->execute();
        $result = $stmt->get_result();
        $fines = array();
        while($fine = $result->fetch_assoc()){
           array_push($fines, $fine);
        }
        return $fines;
    }
        
    public function updateFine($fineId, $fineAmount, $fineReason) {
        $stmt = $this->con->prepare("UPDATE fines SET fine_amount = ?, SET fine_reason = ? WHERE fine_id = ?");
        $stmt->bind_param("dsi", $fineAmount, $fineReason, $fineId);
            
        if ($stmt->execute()) {
            return true;
        } else {
        return false;
        }
    }

    public function getLastError() {
        return $this->con->error;
    }

    public function getChamaMembers($chamaId){
        $stmt = $this->con->prepare("SELECT user.*, chamamembers.* FROM user INNER JOIN chamamembers ON chamamembers.user_id = user.user_id WHERE chamamembers.chama_id = ?");
        $stmt->bind_param("i", $chamaId);
        $stmt->execute();
        $result = $stmt->get_result();
        $members = array();
        while ($row = $result->fetch_assoc()){
            $members[] = $row;
        }
        return $members;
    }
    

    public function getTotalContributions($chamaId) {
        $stmt = $this->con->prepare("SELECT IFNULL(SUM(contribution_amount), 0) as total_contributions FROM contributions WHERE chama_id = ?");
        $stmt->bind_param("i", $chamaId);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_contributions = $row['total_contributions'];
        return $total_contributions;
    }
    
    public function getTotalWithdrawals($chamaId) {
        $stmt = $this->con->prepare("SELECT IFNULL(SUM(withdrawal_amount), 0) as total_withdrawals FROM withdrawals WHERE chama_id = ?");
        $stmt->bind_param("i", $chamaId);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_withdrawals = $row['total_withdrawals'];
        return $total_withdrawals;
    }

    public function getTotalFines($chamaId) {
        $stmt = $this->con->prepare("SELECT IFNULL(SUM(fine_amount), 0) as total_fines FROM fines WHERE chama_id = ?");
        $stmt->bind_param("i", $chamaId);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_fines = $row['total_fines'];
        return $total_fines;
    }

    public function getTotalChamaFunds($chamaId) {
        $totalContributions = $this->getTotalWithdrawals($chamaId);
        $totalWithdrawals = $this->getTotalContributions($chamaId);
        $totalFines = $this->getTotalFines($chamaId);
    
        // Calculate the total funds in the chama
        $totalFunds = ($totalContributions+ $totalFines)-$totalWithdrawals;
    
        return $totalFunds;
    }

    public function getTotalFinesForMemberInChama($chamaId, $userId) {
        $stmt = $this->con->prepare("SELECT IFNULL(COUNT(*),0) AS total_member_fines FROM fines WHERE chama_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $chamaId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_member_fines = $row['total_member_fines'];
        return $total_member_fines;
    }


    public function getTotalLoansForMemberInChama($chamaId, $userId) {
        $stmt = $this->con->prepare("SELECT IFNULL(COUNT(*),0) AS total_member_loans FROM loans WHERE chama_id = ? AND user_id = ? AND loan_status= 'verified' ");
        $stmt->bind_param("ii", $chamaId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_member_loans = $row['total_member_loans'];
        return $total_member_loans;
    }

    public function getTotalContributionsForMemberInChama($chamaId, $userId) {
        $stmt = $this->con->prepare("SELECT IFNULL(COUNT(*),0) AS total_member_contributions FROM contributions WHERE chama_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $chamaId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_member_contributions = $row['total_member_contributions'];
        return $total_member_contributions;
    }

    public function hasLeadershipRole($userId) {
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $role = $stmt->store_result();
        $stmt->bind_result($role);

        while ($stmt->fetch()) {
            if ($role === 'Chairperson' || $role === 'Vice Chairperson' || $role === 'Secretary' || $role === 'Treasurer') {
                return true;
            }
        }

        return false;
    }

    public function createMeeting($meetingDate, $meetingTime, $meetingVenue, $meetingPurpose,$chama_id) {
        try {
            $user_id = $_SESSION['userId'];
    
            // Get the role of the logged in user in the specified chama
            $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE chama_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $chama_id,$user_id);
            if (!$stmt->execute()) {
                throw new Exception("Error executing the SELECT statement: " . $stmt->error);
            } else {

                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    throw new Exception("No results found for chama ID: " . $chama_id);
                }else{
                $loggedInUserRole = $result->fetch_assoc()['chama_role'];
                }
            }
    
            // If the logged in user is the ChairPerson or Secretary of the chama, create the meeting
            if ($loggedInUserRole == 'Chairperson' || $loggedInUserRole == 'Secretary') {
                $stmt = $this->con->prepare("INSERT INTO `meetings` (`meeting_date`, `meeting_time`, `meeting_venue`, `meeting_purpose`, `chama_id`, `created_by`) VALUES (?, ?, ?, ?, ?, $user_id)");
                $stmt->bind_param("ssssi", $meetingDate, $meetingTime, $meetingVenue, $meetingPurpose, $chama_id);
                if (!$stmt->execute()) {
                    throw new Exception("Error executing the INSERT statement: " . $stmt->error);
                } else {
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    //function to fetch a single Meeting 
    public function getMeeting($meetingId){
        $stmt = $this->con->prepare("SELECT * FROM meetings WHERE meeting_id = ?");
        $stmt->bind_param("i", $meetingId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
        
    //function to fetch all meetings
    public function getAllMeetings(){
        $stmt = $this->con->prepare("SELECT * FROM meetings");
        $stmt->execute();
        $result = $stmt->get_result();
        $meetings = array();
        while($meeting = $result->fetch_assoc()){
           array_push($meetings, $meeting);
        }
        return $meetings;
    }
        
    public function updateMeeting($meetingId,$meetingDate, $meetingTime, $meetingVenue, $meetingPurpose,$chamaId) {

        $userId = $_SESSION['userId'];
    
        // Get the role of the logged in user in the specified chama
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE chama_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $chamaId,$userId);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {

            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("No results found for chama ID: " . $chamaId);
            }else{
            $loggedInUserRole = $result->fetch_assoc()['chama_role'];
            }
        }

        if ($loggedInUserRole == 'Chairperson' || $loggedInUserRole == 'Secretary') {
        $stmt = $this->con->prepare("UPDATE meetings SET meeting_date = ?, SET meeting_time = ? , SET meeting_venue= ?, SET meeting_purpose = ? WHERE meeting_id = ?");
        $stmt->bind_param("ssssi", $meetingDate, $meetingTime , $meetingVenue, $meetingPurpose,$meetingId);
            
        if ($stmt->execute()) {
            return true;
        } else {
        return false;
            }
        }
    }

    public function requestLoan ($chamaId,$loanAmount,$loanRepayPeriod){

        // Get the current datetime in MySQL datetime format
        $date = date('Y-m-d H:i:s');

        //get user_id from currently logged in user
        $userId = $_SESSION['userId'];

        // Insert the contribution into the database
        $stmt = $this->con->prepare("INSERT INTO `loans` (`user_id`,`chama_id`, `loan_amount`, `loan_repayment_period`,`requested_at`, `loan_status`) VALUES (?, ?, ?, ?, ?, 'pending' )");
        $stmt->bind_param("iidss", $userId, $chamaId, $loanAmount,$loanRepayPeriod,$date);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }

    }

    public function approveLoan($chamaId, $loanId)
    {
        // Get the current datetime in MySQL datetime format
        $verifiedAt = date('Y-m-d H:i:s');
        
        $userId = $_SESSION['userId'];
        
        // Get the role of the logged in user in the specified chama
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE chama_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $chamaId, $userId);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("No results found for chama ID: " . $chamaId);
            } else {
                $loggedInUserRole = $result->fetch_assoc()['chama_role'];
            }
        }
    
        if ($loggedInUserRole == 'Chairperson' || $loggedInUserRole == 'Treasurer') {
            $stmt = $this->con->prepare("SELECT loan_amount, requested_at, loan_repayment_period FROM loans WHERE loan_id = ?");
            $stmt->bind_param("i", $loanId);
            if (!$stmt->execute()) {
                throw new Exception("Error executing the SELECT statement: " . $stmt->error);
            } else {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $loanAmount = $row['loan_amount'];
                    $requestedAt = new DateTime($row['requested_at']);
                    $repaymentPeriod = new DateInterval('P' . $row['loan_repayment_period'] . 'D');
                    if ($repaymentPeriod->d == 30) {
                        $interestRate = 0.01;
                    } else {
                        $interestRate = 0.05;
                    }
                    $repaymentDueDate = $requestedAt->add($repaymentPeriod)->format('Y-m-d H:i:s');
                } else {
                    echo 'No loan found with the given ID.';
                }
            }
    
            $interest = $interestRate * $loanAmount;
            $amountPayable = $loanAmount + $interest;
            $stmt = $this->con->prepare("UPDATE loans SET loan_status = 'verified', verified_at = ?, due_at = ?, interest_rate = ?, amount_payable = ? WHERE loan_id = ?");
            $stmt->bind_param("ssddi", $verifiedAt, $repaymentDueDate, $interestRate, $amountPayable, $loanId);
                    
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    //function to check if loan exists in db
    public function ifLoanExist($loanId) {
        $stmt = $this->con->prepare("SELECT loan_id FROM loans WHERE loan_id = ?");
        $stmt->bind_param("i", $loanId);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function requestToJoinChama($chamaId,$userId){

        // Insert the request into the database
        $stmt = $this->con->prepare("INSERT INTO `joinrequests` (`user_id`,`chama_id`, `requested_at`, `join_status`) VALUES (?, ?, NOW() , 'Pending' )");
        $stmt->bind_param("ii", $userId, $chamaId);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }

    }

    public function checkJoinRequestStatus($chamaId, $userId) {
        // Prepare the query to check the join request status
        $stmt = $this->con->prepare("SELECT join_status FROM joinrequests WHERE chama_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $chamaId, $userId);
        $stmt->execute();
        $stmt->store_result();
    
        // Check if a join request exists for the specified chama and user
        if ($stmt->num_rows > 0) {
            // Fetch the join status from the result
            $stmt->bind_result($joinStatus);
            $stmt->fetch();

            if ($joinStatus == 'Pending') {
                return true;
            } else{
                return false;
            }
        }
    }
    

    public function approveToJoinChama($chamaId, $userId, $requestId)
    {
        // Get the current datetime in MySQL datetime format
        $acceptedAt = date('Y-m-d H:i:s');
        
        $admin = $_SESSION['userId'];
        
        // Get the role of the logged in user in the specified chama
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE chama_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $chamaId, $admin);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("No results found for chama ID: " . $chamaId);
            } else {
                $loggedInUserRole = $result->fetch_assoc()['chama_role'];
            }
        }
    
        if ($loggedInUserRole == 'Chairperson' || $loggedInUserRole == 'Treasurer' || $loggedInUserRole == 'Vice Chairperson' || $loggedInUserRole == 'Secretary') {

            $stmt = $this->con->prepare("INSERT INTO `chamamembers` (`chama_id`, `user_id`, `chama_role`) VALUES (?, ?, 'Member')");
            $stmt->bind_param("ii", $chamaId, $userId);
            if (!$stmt->execute()) {
                throw new Exception("Error executing the INSERT statement: " . $stmt->error);
            }else{
            return true;
            }

            $stmt = $this->con->prepare("UPDATE joinrequests SET join_status = 'Accepted', accepted_at = ? WHERE request_id = ?");
            $stmt->bind_param("si", $acceptedAt, $requestId);
                    
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }

        }
    }

    public function rejectLoan ($chamaId, $loanId){

        // Get the current datetime in MySQL datetime format
        $rejectedAt = date('Y-m-d H:i:s');

        //get user_id from currently logged in user
        $userId = $_SESSION['userId'];

        // Get the role of the logged in user in the specified chama
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE chama_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $chamaId, $userId);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("No results found for chama ID: " . $chamaId);
            } else {
                $loggedInUserRole = $result->fetch_assoc()['chama_role'];
            }
        }
    
        if ($loggedInUserRole == 'Chairperson' || $loggedInUserRole == 'Treasurer') {

        // update status
        $stmt = $this->con->prepare("UPDATE loans SET loan_status = 'rejected', rejected_at = ? WHERE loan_id = ?");
        $stmt->bind_param("si", $rejectedAt, $loanId);
                
        if ($stmt->execute()) {
            return true;
            } else {
            return false;
            }
        }

    }

    public function rejectToJoinChama ($chamaId, $requestId){

        // Get the current datetime in MySQL datetime format
        $rejectedAt = date('Y-m-d H:i:s');

        //get user_id from currently logged in user
        $userId = $_SESSION['userId'];

        // Get the role of the logged in user in the specified chama
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE chama_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $chamaId, $userId);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("No results found for chama ID: " . $chamaId);
            } else {
                $loggedInUserRole = $result->fetch_assoc()['chama_role'];
            }
        }
    
        if ($loggedInUserRole == 'Chairperson' || $loggedInUserRole == 'Treasurer' || $loggedInUserRole == 'Vice Chairperson' || $loggedInUserRole == 'Secretary') {

        // update status 
        $stmt = $this->con->prepare("UPDATE joinrequests SET join_status = 'Rejected', rejected_at = ? WHERE request_id = ?");
        $stmt->bind_param("si", $rejectedAt, $requestId);
                
        if ($stmt->execute()) {
            return true;
            } else {
            return false;
            }
        }

    }

    public function updateRole ($chamaId, $memberId, $chamaRole){
        try {
        //get user_id from currently logged in user
        $userId = $_SESSION['userId'];

        // Get the role of the logged in user in the specified chama
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE chama_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $chamaId, $userId);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("No results found for chama ID: " . $chamaId);
            } else {
                $loggedInUserRole = $result->fetch_assoc()['chama_role'];
            }
        }
    
        if ($loggedInUserRole == 'Chairperson') {

                //change role
                $stmt = $this->con->prepare("UPDATE chamamembers SET chama_role = ? WHERE member_id = ?");
                $stmt->bind_param("si", $chamaRole, $memberId);
                        
                if ($stmt->execute()) {
                    return true;
                } else {
                    return false;
                }
            }
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    
        
    

}
?>