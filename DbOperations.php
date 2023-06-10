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
        $stmt->bind_param("sssssss",$firstName,$lastName,$userName,$phoneNumber,$email,$gender,$password);
    
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

    public function createChama($chama_name, $description,$user_id, $contribution_period, $contribution_target,$system_flow) {
    
        // Prepare statement to insert chama into chama table
        $stmt = $this->con->prepare("INSERT INTO `chama` ( `chama_name`, `chama_description`, `chairperson_id`, `contribution_period`,`contribution_target`, `system_flow`, `created_at`) VALUES ( ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssisds", $chama_name, $description, $user_id, $contribution_period,$contribution_target, $system_flow);
    
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

    public function updateChama($chamaName, $chamaDescription, $contributionPeriod, $contributionTarget, $systemFlow,$chamaId) {

        $stmt = $this->con->prepare("UPDATE chama SET chama_name = ?, chama_description = ?, contribution_period = ?, contribution_target = ?, system_flow = ?, updated_at = NOW() WHERE chama_id = ?");
        $stmt->bind_param("sssssi", $chamaName, $chamaDescription, $contributionPeriod, $contributionTarget, $systemFlow, $chamaId);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
     
    //function to fetch a single chama 
    public function getChama($chamaId){
        $stmt = $this->con->prepare("SELECT * FROM chama WHERE chama_id = ?");
        $stmt->bind_param("i", $chamaId);
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

    public function checkSystemFlow($chamaId) {
        $stmt = $this->con->prepare("SELECT system_flow FROM chama WHERE chama_id = ?");
        $stmt->bind_param("s", $chamaId);
        $stmt->execute();
        $stmt->store_result();
    
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($systemFlow);
            $stmt->fetch();
            $stmt->close();
            
            // Return the system flow type as a string
            if ($systemFlow === 'merry-go-round') {
                return "merry-go-round";
            } elseif ($systemFlow === 'linear') {
                return "linear";
            } else {
                return "unknown";
            }
        }
    
        return "not_found";
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

    public function makeContribution($chamaId, $userId, $contributionAmount) {
        // Get the current datetime in MySQL datetime format
        $contributedAt = date('Y-m-d H:i:s');
    
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
    
        $nextContributionDate = date('Y-m-d H:i:s', strtotime("+{$contributionPeriod} days", strtotime($contributedAt))); 
    
    
        // Insert the contribution into the database
        $stmt = $this->con->prepare("INSERT INTO `contributions` (`chama_id`, `user_id`, `contribution_amount`, `contribution_date`, `next_contribution_date`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iidss", $chamaId, $userId, $contributionAmount, $contributedAt, $nextContributionDate);
    
        if ($stmt->execute()) {
            $contributedId = $stmt->insert_id;
    
            // Get the contribution details from the database
            $stmt = $this->con->prepare("SELECT * FROM `contributions` WHERE `contribution_id` = ?");
            $stmt->bind_param("i", $contributedId);
            if (!$stmt->execute()) {
                throw new Exception("Error executing the SELECT statement: " . $stmt->error);
            } else {
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    throw new Exception("No results found for contribution ID: " . $contributedId);
                } else {
                    $contribution = $result->fetch_assoc();
    
                    // Send a notification about the contribution
                    $notificationTitle="Contribution";
                    $notificationContent = "A new contribution of {$contributionAmount} has been made.";
                    $this->sendChamaNotification($chamaId, $userId, $notificationContent, $notificationTitle);
    
                    return $contribution;
                }
            }
        } else {
            return false;
        }
    }
    
    
    
    

    //function to fetch a single contribution 
    public function getMemberContribution($contributionId){
        $stmt = $this->con->prepare("SELECT * FROM contributions WHERE contribution_id = ?");
        $stmt->bind_param("i", $contributionId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    //function to fetch all contributions
    public function getAllContributions($chamaId){
        $stmt = $this->con->prepare("SELECT * FROM contributions WHERE chama_id=?");
        $stmt->bind_param("i",$chamaId);
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

    public function makeWithdrawal($chamaId, $userId , $withdrawalAmount, $withdrawalReason) {

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
    
        if ( $loggedInUserRole == 'Treasurer') {


        // Insert the withdrawals into the database
        $stmt = $this->con->prepare("INSERT INTO `withdrawals` (`chama_id`, `user_id`, `withdrawal_amount`, `withdrawal_date`, `withdrawal_reason`) VALUES (?, ? , ?, NOW() , ?)");
        $stmt->bind_param("iids", $chamaId, $userId, $withdrawalAmount, $withdrawalReason);

            if($this->getTotalChamaFunds($chamaId)< $withdrawalAmount){
                throw new Exception("You dont have Sufficient funds to make this Transaction:".$chamaId);
            }else{
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
                }
            }   
        }else{
            throw new Exception("You dont have authoriization to perfom this transaction:".$chamaId);
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
    public function getAllWithdrawals($chamaId){
        $stmt = $this->con->prepare("SELECT * FROM withdrawals WHERE chama_id=?");
        $stmt->bind_param("i",$chamaId);
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

public function chargeFine($chamaId, $userIds, $fineAmount, $fineReason) {
    $chargedFines = array();
    
    // Prepare the INSERT statement
    $stmt = $this->con->prepare("INSERT INTO `fines` (`chama_id`, `user_id`, `fine_amount`, `fine_reason`, `date_fined`, `fine_status`) VALUES (?, ?, ?, ?, NOW(), 'Not Paid')");
    
    // Iterate over the user IDs array
    foreach ($userIds as $userId) {
        // Bind the parameters for each user ID
        $stmt->bind_param("iids", $chamaId, $userId, $fineAmount, $fineReason);
        if ($stmt->execute()) {
            // Get the inserted fine ID
            $fineId = $stmt->insert_id;
            
            // Fetch the fine object from the database using the fine ID
            $fine = $this->getFine($fineId);
            if ($fine) {
                $chargedFines[] = $fine;
            }
        }
    }
    
    // Close the statement
    $stmt->close();
    
    // Return the array of charged fines
    return $chargedFines;
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
    public function getAllFines($chamaId){
        $stmt = $this->con->prepare("SELECT * FROM fines WHERE chama_id= ?");
        $stmt->bind_param("i",$chamaId);
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
        $stmt = $this->con->prepare("SELECT IFNULL(SUM(fine_amount), 0) as total_fines FROM fines WHERE chama_id = ? AND fine_status='Cleared'");
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
        $totalContributions = $this->getTotalContributions($chamaId);
        $totalWithdrawals = $this->getTotalWithdrawals($chamaId);
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

    public function hasLeadershipRole($userId,$chamaId) {
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE user_id = ? AND chama_id= ? ");
        $stmt->bind_param("ii", $userId, $chamaId);
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

    public function createMeeting($meetingDate, $meetingTime, $meetingVenue, $meetingPurpose, $chamaId,$userId) {
        try {
    
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
    
            // If the logged in user is the ChairPerson or Secretary of the chama, create the meeting
            if ($loggedInUserRole == 'Chairperson' || $loggedInUserRole == 'Secretary') {
                // Format the meeting date
                $meetingDate = date('Y-m-d', strtotime($meetingDate));
    
                // Format the meeting time
                $meetingTime = date('H:i:s', strtotime($meetingTime));
    
                $stmt = $this->con->prepare("INSERT INTO `meetings` (`meeting_date`, `meeting_time`, `meeting_venue`, `meeting_purpose`, `chama_id`, `created_by`) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssii", $meetingDate, $meetingTime, $meetingVenue, $meetingPurpose, $chamaId, $userId);
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
    public function getAllMeetings($chamaId){
        $stmt = $this->con->prepare("SELECT * FROM meetings WHERE chama_id= ?");
        $stmt->bind_param("i",$chamaId);
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

    public function requestLoan ($chamaId,$userId,$loanAmount,$loanRepayPeriod){

        // Insert the contribution into the database
        $stmt = $this->con->prepare("INSERT INTO `loans` (`user_id`,`chama_id`, `loan_amount`, `loan_repayment_period`,`requested_at`, `loan_status`) VALUES (?, ?, ?, ?, NOW() , 'pending' )");
        $stmt->bind_param("iids", $userId, $chamaId, $loanAmount,$loanRepayPeriod);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }

    }

    public function approveLoan($chamaId, $loanId, $userId)
    {
        // Get the role of the logged-in user in the specified chama
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
                if ($result->num_rows === 0) {
                    throw new Exception('No loan found with the given ID.');
                } else {
                    $row = $result->fetch_assoc();
                    $loanAmount = $row['loan_amount'];
                    $requestedAt = new DateTime($row['requested_at']);
                    $repaymentPeriod = new DateInterval('P' . $row['loan_repayment_period'] . 'D');
                    $interestRate = ($repaymentPeriod->d == 30) ? 0.01 : 0.05;
                    $repaymentDueDate = $requestedAt->add($repaymentPeriod)->format('Y-m-d H:i:s');
                }
            }
    
            $interest = $interestRate * $loanAmount;
            $amountPayable = $loanAmount + $interest;
            $stmt = $this->con->prepare("UPDATE loans SET loan_status = 'verified', verified_at = NOW() , due_at = ?, interest_rate = ?, amount_payable = ? WHERE loan_id = ?");
            $stmt->bind_param("sddi", $repaymentDueDate, $interestRate, $amountPayable, $loanId);
    
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Error updating loan status: " . $stmt->error);
            }
        } else {
            throw new Exception("User does not have the required role for loan approval.");
        }
    }
    

    public function getAllLoans($chamaId){
        $stmt = $this->con->prepare("SELECT * FROM loans WHERE chama_id= ?");
        $stmt->bind_param("i",$chamaId);
        $stmt->execute();
        $result = $stmt->get_result();
        $loans = array();
        while($loan = $result->fetch_assoc()){
           array_push($loans, $loan);
        }
        return $loans;
    }

    //function to check if loan exists in db
    public function ifLoanExist($loanId) {
        $stmt = $this->con->prepare("SELECT loan_id FROM loans WHERE loan_id = ?");
        $stmt->bind_param("i", $loanId);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function ifRequestExist($requestId) {
        $stmt = $this->con->prepare("SELECT request_id FROM joinrequests WHERE request_id = ?");
        $stmt->bind_param("i", $requestId);
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

    public function memberExists($chamaId,$userId){
        $stmt = $this->con->prepare("SELECT user_id FROM chamamembers WHERE chama_id = ? AND user_id= ? ");
        $stmt->bind_param("ii", $chamaId,$userId);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
    

    public function approveToJoinChama($chamaId, $userId, $requestId)
    {
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
            $stmt = $this->con->prepare("SELECT user_id FROM joinrequests WHERE request_id = ?");
            $stmt->bind_param("i", $requestId);
            if (!$stmt->execute()) {
                throw new Exception("Error executing the SELECT statement: " . $stmt->error);
            } else {
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    throw new Exception("No results found for request ID: " . $requestId);
                } else {
                    $newMember = $result->fetch_assoc()['user_id'];
                }
            }
    
            if ($this->memberExists($chamaId, $newMember)) {
                throw new Exception("This member already exists");
            } else {
                $stmt = $this->con->prepare("INSERT INTO `chamamembers` (`chama_id`, `user_id`, `chama_role`) VALUES (?, ?, 'Member')");
                $stmt->bind_param("ii", $chamaId, $newMember);
                if (!$stmt->execute()) {
                    throw new Exception("Error executing the INSERT statement: " . $stmt->error);
                } else {
                    $stmt = $this->con->prepare("UPDATE joinrequests SET join_status = 'Accepted', accepted_at = NOW() WHERE request_id = ?");
                    $stmt->bind_param("i", $requestId);
                    if ($stmt->execute()) {
                        return true;
                    } else {
                        throw new Exception("Error updating the join request: " . $stmt->error);
                    }
                }
            }
        }
        
        return false;
    }
    
    

    public function rejectLoan ($chamaId, $loanId,$userId){

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
        $stmt = $this->con->prepare("UPDATE loans SET loan_status = 'rejected', rejected_at = NOW() WHERE loan_id = ?");
        $stmt->bind_param("i", $loanId);
                
        if ($stmt->execute()) {
            return true;
            } else {
            return false;
            }
        }

    }

    public function rejectToJoinChama ($chamaId, $userId, $requestId){

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
        $stmt = $this->con->prepare("UPDATE joinrequests SET join_status = 'Rejected', rejected_at = NOW() WHERE request_id = ?");
        $stmt->bind_param("i", $requestId);
                
        if ($stmt->execute()) {
            return true;
            } else {
            return false;
            }
        }

    }

    public function LeadershipRole($memberId) {
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE member_id= ? ");
        $stmt->bind_param("i", $memberId);
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

    public function updateRole($chamaId, $memberId, $chamaRole,$userId)
    {
        try {
    
            // Check if the member already has a leadership role
            if ($this->LeadershipRole($memberId)) {
                throw new Exception("The member already has a leadership role in the chama.");
            }
    
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
    
            if ($loggedInUserRole == 'Chairperson' || $loggedInUserRole == 'Vice ChairPerson') {
                // Check if the role is already assigned
                $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE chama_id = ? AND chama_role = ?");
                $stmt->bind_param("is", $chamaId, $chamaRole);
                if (!$stmt->execute()) {
                    throw new Exception("Error executing the SELECT statement: " . $stmt->error);
                } else {
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        throw new Exception("The role is already assigned to another member.");
                    }
                }
    
                // Change the role
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

    public function makeChairperson($chamaId, $memberId, $userId)
    {
        try {
            // Check if the logged-in user is the chairperson
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
            
            // Check if the logged-in user is the chairperson
            if ($loggedInUserRole !== 'Chairperson') {
                throw new Exception("Only the chairperson can assign a new chairperson.");
            }
    
            // Get the current chairperson ID
            $stmt = $this->con->prepare("SELECT chairperson_id FROM chama WHERE chama_id = ?");
            $stmt->bind_param("i", $chamaId);
            if (!$stmt->execute()) {
                throw new Exception("Error executing the SELECT statement: " . $stmt->error);
            } else {
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    throw new Exception("No results found for chama ID: " . $chamaId);
                } else {
                    $currentChairpersonId = $result->fetch_assoc()['chairperson_id'];
                }
            }
            
            // Get the user_id for the new chairperson
            $stmt = $this->con->prepare("SELECT user_id FROM chamamembers WHERE member_id = ?");
            $stmt->bind_param("i", $memberId);
            if (!$stmt->execute()) {
                throw new Exception("Error executing the SELECT statement: " . $stmt->error);
            } else {
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    throw new Exception("No results found for member ID: " . $memberId);
                } else {
                    $newChairpersonUserId = $result->fetch_assoc()['user_id'];
                }
            }
            
            // Update the chairperson_id in the chama table
            $stmt = $this->con->prepare("UPDATE chama SET chairperson_id = ? WHERE chama_id = ?");
            $stmt->bind_param("ii", $newChairpersonUserId, $chamaId);
            if (!$stmt->execute()) {
                throw new Exception("Error updating the chairperson in the chama table: " . $stmt->error);
            }
            
            // Update the chama_role to 'Chairperson' for the new chairperson in the chamamembers table
            $stmt = $this->con->prepare("UPDATE chamamembers SET chama_role = 'Chairperson' WHERE member_id = ?");
            $stmt->bind_param("i", $memberId);
            if (!$stmt->execute()) {
                throw new Exception("Error updating the chama role to Chairperson: " . $stmt->error);
            }
            
            // Update the chama_role to 'Member' for the current chairperson in the chamamembers table
            $stmt = $this->con->prepare("UPDATE chamamembers SET chama_role = 'Member' WHERE member_id = ?");
            $stmt->bind_param("i", $currentChairpersonId);
            if (!$stmt->execute()) {
                throw new Exception("Error updating the chama role to Member for the current chairperson: " . $stmt->error);
            }
            
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
    

    public function demoteLeader($chamaId, $memberId, $userId)
{
    try {
        // Check if the logged-in user is the chairperson
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
        
        // Check if the logged-in user is the chairperson
        if ($loggedInUserRole !== 'Chairperson') {
            throw new Exception("Only the chairperson can demote leaders.");
        }
        
        // Check if the member to be demoted is the chairperson
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE member_id = ?");
        $stmt->bind_param("i", $memberId);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("No results found for member ID: " . $memberId);
            } else {
                $memberRole = $result->fetch_assoc()['chama_role'];
            }
        }
        
        // Check if the member to be demoted is the chairperson
        if ($memberRole === 'Chairperson') {
            throw new Exception("The chairperson cannot be demoted.");
        }
        
        // Demote the leader by setting the chama_role to 'Member'
        $stmt = $this->con->prepare("UPDATE chamamembers SET chama_role = 'Member' WHERE member_id = ?");
        $stmt->bind_param("i", $memberId);
        if (!$stmt->execute()) {
            throw new Exception("Error demoting the leader: " . $stmt->error);
        }
        
        return true;
    } catch (Exception $e) {
        throw $e;
    }
}

    

    public function performAllocation($chamaId)
    {
        // Get the chama system flow and contribution target from the chama table
        $stmt = $this->con->prepare("SELECT system_flow, contribution_target FROM chama WHERE chama_id = ?");
        $stmt->bind_param("i", $chamaId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $systemFlow = $row['system_flow'];
            $totalAllocationSum = $row['contribution_target'];
            
            // Check if the system flow is "Merry Go Round"
            if ($systemFlow === "merry-go-round") {
                if ($totalAllocationSum === 0) {
                    throw new Exception("No funds available for allocation in the chama.");
                }
                
                $members = $this->getChamaMembers($chamaId);
                shuffle($members);
                
                foreach ($members as $member) {
                    $memberId = $member['member_id'];
                    
                    // Check if the member has already been allocated in the allocations table
                    $stmt = $this->con->prepare("SELECT * FROM allocations WHERE chama_id = ? AND member_id = ?");
                    $stmt->bind_param("ii", $chamaId, $memberId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows === 0 && $totalAllocationSum > 0) {
                        
                        $stmt = $this->con->prepare("INSERT INTO `allocations` (`chama_id`, `member_id`, `allocation_amount`) VALUES (?, ?, ?)");
                        $stmt->bind_param("iid", $chamaId, $memberId, $allocationAmount);
                        $stmt->execute();
        
                        
                        return true;
                    }else{
                        throw new Exception("No funds available for allocation in the chama.");
                    }
                }
                
                // If all members have been allocated, reset the allocation
                $stmt = $this->con->prepare("DELETE FROM allocations WHERE chama_id = ?");
                $stmt->bind_param("i", $chamaId);
                $stmt->execute();
            }
        } else {
            throw new Exception("Error fetching the system flow and contribution target from the chama table.");
        }
        
        return false;
    }
    
    
    public function getMemberLoans($chamaId,$userId){
        $stmt = $this->con->prepare("SELECT * FROM loans  WHERE chama_id = ? AND user_id=?");
        $stmt->bind_param("ii", $chamaId,$userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $loans = array();
        while ($row = $result->fetch_assoc()){
            $loans[] = $row;
        }
        return $loans;
    }

    public function getMemberLoanRequests($chamaId){
        $stmt = $this->con->prepare("SELECT * FROM loans  WHERE  loan_status='pending' AND chama_id=? ");
        $stmt->bind_param("i",$chamaId);
        $stmt->execute();
        $result = $stmt->get_result();
        $loans = array();
        while ($row = $result->fetch_assoc()){
            $loans[] = $row;
        }
        return $loans;
    }

    public function getJoinRequests($chamaId){
        $stmt = $this->con->prepare("SELECT * FROM joinrequests  WHERE  join_status='Pending' AND chama_id=? ");
        $stmt->bind_param("i",$chamaId);
        $stmt->execute();
        $result = $stmt->get_result();
        $requests = array();
        while ($request = $result->fetch_assoc()){
            $requests[] = $request;
        }
        return $requests;
    }

    public function getMemberContributions($chamaId,$userId){
        $stmt = $this->con->prepare("SELECT * FROM contributions  WHERE chama_id = ? AND user_id=? ");
        $stmt->bind_param("ii", $chamaId,$userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $contributions = array();
        while ($row = $result->fetch_assoc()){
            $contributions[] = $row;
        }
        return $contributions;
    }

    public function getMemberFines($chamaId,$userId){
        $stmt = $this->con->prepare("SELECT * FROM fines  WHERE chama_id = ? AND user_id=? ");
        $stmt->bind_param("ii", $chamaId,$userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $fines = array();
        while ($row = $result->fetch_assoc()){
            $fines[] = $row;
        }
        return $fines;
    }
    
    public function isTreasurer($userId,$chamaId) {
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE user_id = ? AND chama_id= ? ");
        $stmt->bind_param("ii", $userId, $chamaId);
        $stmt->execute();
        $role = $stmt->store_result();
        $stmt->bind_result($role);

        while ($stmt->fetch()) {
            if ( $role === 'Treasurer') {
                return true;
            }
        }

        return false;
    }

    public function isSecretary($userId,$chamaId) {
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE user_id = ? AND chama_id= ? ");
        $stmt->bind_param("ii", $userId, $chamaId);
        $stmt->execute();
        $role = $stmt->store_result();
        $stmt->bind_result($role);

        while ($stmt->fetch()) {
            if ( $role === 'Secretary') {
                return true;
            }
        }

        return false;
    }

    public function isChairOrVice($userId,$chamaId) {
        $stmt = $this->con->prepare("SELECT chama_role FROM chamamembers WHERE user_id = ? AND chama_id= ? ");
        $stmt->bind_param("ii", $userId, $chamaId);
        $stmt->execute();
        $role = $stmt->store_result();
        $stmt->bind_result($role);

        while ($stmt->fetch()) {
            if ($role === 'Chairperson' || $role === 'Vice ChairPerson') {
                return true;
            }
        }

        return false;
    }
            

    public function sendChamaNotification($chamaId, $userId, $notificationContent,$notificationTitle) {
        // Generate a unique notification ID
        $notificationId = uniqid();
    
        // Insert the notification into the notifications table
        $stmt = $this->con->prepare("INSERT INTO notifications (chama_id, user_id, notification_id, notification_content, notification_title) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $chamaId, $userId, $notificationId, $notificationContent,$notificationTitle);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getAllNotifications($chamaId){
        $stmt = $this->con->prepare("SELECT * FROM notifications WHERE chama_id=?");
        $stmt->bind_param("i",$chamaId);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = array();
        while($notification = $result->fetch_assoc()){
        array_push($notifications, $notification);
        }
        return $notifications;
    }
    
    // public function leaveChama($chamaId, $userId) {
    //     // Check if the user is the chairperson
    //     if ($this->is($userId, $chamaId)) {
    //         throw new Exception("You cannot leave the chama as the chairperson.");
    //     }
        
    //     // Delete the user's membership record from the chamaMembers table
    //     $stmt = $this->con->prepare("DELETE FROM chamaMembers WHERE chama_id = ? AND user_id = ?");
    //     $stmt->bind_param("ii", $chamaId, $userId);
    //     if (!$stmt->execute()) {
    //         throw new Exception("Error executing the DELETE statement: " . $stmt->error);
    //     }
        
    //     // Perform any necessary updates or notifications
    //     $this->updateChamaMembersList();
        
    //     // Return a success message or status
    //     return "Successfully left the chama.";
    // }
        
    

}
?>