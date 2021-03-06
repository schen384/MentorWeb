<?php
	include_once('db_helper.php');
	header("Access-Control-Allow-Origin: *");

	function welcome() {
		global $_USER;
		
		//$userid = array('username' => $_USER['uid']);
		//echo var_dump($_USER);
		$userInfo = array('username' => $_USER['uid']);
		$userType = "None";
		
		$dbQuery = sprintf("SELECT first_name, last_name FROM USER WHERE username = '%s'",
												$_USER['uid']);
		$result = getDBResultsArray($dbQuery);
		if (!empty($result)) {
			$userType = "User";	
			$userInfo["firstName"] = $result["0"]["first_name"];
			$userInfo["lastName"] = $result["0"]["last_name"];
		}

		if (!empty($result)) {
			$dbQuery = sprintf("SELECT username FROM Mentee WHERE username = '%s'",
													$_USER['uid']);
			$result = getDBResultsArray($dbQuery);
			if (!empty($result)){$userType = "Mentee";}
		}

		if (empty($result)) {
			$dbQuery = sprintf("SELECT username FROM Mentor WHERE username = '%s'",
													$_USER['uid']);
			$result = getDBResultsArray($dbQuery);
			if (!empty($result)){$userType = "Mentor";}
		}

		if (empty($result) && userIsAdmin()) {
			$userType = "Admin";
		}

		// echo $userType;
		// array_push($result, $userType);
		 $userInfo["userType"] = $userType;
		// echo var_dump($_User);
		// echo $result["0"]["UserType"];

	 	 $GLOBALS["_PLATFORM"]->sandboxHeader("Content-type: application/json");
	 	 //echo var_dump($userinfo);
	 	 echo json_encode($userInfo);
	}//end welcome

	//function reset() {
		//global $_USER;

		// $dbQuery = sprintf("SELECT first_name, last_name FROM User WHERE username = '%s'",
		// 										$_USER['uid']);
		// $result = getDBResultsArray($dbQuery
		// $dbQuery = sprintf("SELECT first_name, last_name FROM User WHERE username = '%s'",
		// 										$_USER['uid']);
		// $result = getDBResultsArray($dbQuery
		// $dbQuery = sprintf("DELETE FROM Mentee WHERE username = '%s'",
		// 										$_USER['uid']);
		// $result = getDBResultsArray($dbQuery);
		// $dbQuery = sprintf("DELETE FROM User WHERE username = '%s'",
		// 										$_USER['uid']);
		// $result = getDBResultsArray($dbQuery);
	//}

	function resetUser() {
		global $_USER;
		$dbQuery = sprintf("DELETE FROM USER WHERE username = '%s'",
												$_USER['uid']);
		$result = getDBResultsArray($dbQuery);

		$userInfo = array('username' => $_USER['uid']);
		$userInfo['complete'] = "true";
		echo json_encode($userInfo);
	}

	function getUserType() {
		// echo "in getUserType: \n";
		global $_USER;
		$user = $_USER['uid'];

		$userInfo = array("Admin" => 0, "Mentor" => 0, "Mentee" => 0, "Name" => '',"Mentor" => '');
		$checkAdmin = sprintf("SELECT first_name FROM USER, Admin WHERE USER.username = '%s' AND Admin.username = '%s'", $user, $user);
		$isAdmin = getDBResultsArray($checkAdmin);
		// echo "isAdmin: " . $isAdmin . "\n";
		if (!empty($isAdmin)) {
			$userInfo["Admin"] = 1;
			$userInfo["Name"] = $isAdmin[0]["first_name"];
		}
		$checkMentor = sprintf("SELECT USER.username, first_name FROM USER, Mentor WHERE USER.username = '%s' AND Mentor.username = '%s'", $user, $user);
		$isMentor = getDBResultsArray($checkMentor);
		if (!empty($isMentor)) {
			$userInfo["Mentor"] = 1;
			$userInfo["Username"] = $isMentor[0]["username"];
			$userInfo["Name"] = $isMentor[0]["first_name"];
		}
		$checkMentee = sprintf("SELECT USER.username, first_name, mentor_user FROM USER, Mentee WHERE USER.username = '%s' AND Mentee.username = '%s'", $user, $user);
		$isMentee = getDBResultsArray($checkMentee);
		if (!empty($isMentee)) {
			$userInfo["Mentee"] = 1;
			$userInfo["Username"] = $isMentee[0]["username"];
			$userInfo["Name"] = $isMentee[0]["first_name"];
			$userInfo["Mentor"] = $isMentee[0]["mentor_user"];
		}
		header("Content-type: application/json");
		echo json_encode($userInfo);
	}

	function submitRegForm($form) {
		global $_USER;

		$dbQuery = sprintf("INSERT INTO USER (username, last_name, first_name, phone_num, email, pref_communication)
												VALUES ('%s', '%s', '%s', '%u', '%s', '%s')",
												$_USER['uid'], $form['firstName'], $form['lastName'], 
												$form['phoneNumber'], $form['email'], $form['commMethod']);
		$result = getDBRegInserted($dbQuery);

		header("Content-type: application/json");
		echo json_encode($result);
	}//end submitRegForm

	function listMentor(){
		echo "list Mentor";
	}

	function listMentee(){
		echo "list Mentee";
	}

	function addMentee() {
		global $_USER;	
		$user = $_USER['uid'];
		$fname = mysql_real_escape_string($_POST['fname']);
		$lname = mysql_real_escape_string($_POST['lname']);
		$phone = mysql_real_escape_string($_POST['phone']);
		$email = mysql_real_escape_string($_POST['email']);
		$pref_communication = mysql_real_escape_string($_POST['pref_comm']);
		$depth_focus = mysql_real_escape_string($_POST['dfocus']);
		$depth_focus_other = mysql_real_escape_string($_POST['dfocusother']); //don't need escape string for pre-defined vals
		$first_gen_college_student = $_POST['first_gen_college_student'];
		$transfer_from_outside = $_POST['transfer_from_outside'];
		$institution_name = mysql_real_escape_string($_POST['institution_name']);
		$transfer_from_within = $_POST['transfer_from_within'];
		$prev_major = mysql_real_escape_string($_POST['prev_major']);
		$international_student = $_POST['international_student'];
		$expec_graduation = mysql_real_escape_string($_POST['expec_graduation']);
		$other_major =  mysql_real_escape_string($_POST['other_major']);
		$undergrad_research =  mysql_real_escape_string($_POST['undergrad_research']);

		$bme_org1 = null;
		$bme_org2 = null;
		$bme_org3 = null;
		$bme_org4 = null;
		$bme_org5 = null;
		$bme_org6 = null;
		$bme_org7 = null;
		$bmeOrgs = $_POST['bme_organization'];
		for ($i=1; $i <= count($bmeOrgs); $i++) {
			${"bme_org" . $i}  = $bmeOrgs[$i-1]['name']; //Json of all the organizations $_POST['bme_organization']
		}

		$tutor_teacher_program1 = null;
		$tutor_teacher_program2 = null;
		$tutor_teacher_program3 = null;
		$tutor_teacher_program4 = null;
		$tutor_teacher_program5 = null;
		$tutor_teacher_program6 = null;
		$ttProg = $_POST['tutor_teacher_program'];
		for ($i=1; $i <= count($ttProg); $i++) {
			${"tutor_teacher_program" . $i}  = $ttProg[$i-1]['name']; 
		}

		$bme_academ_exp1 = null;
		$bme_academ_exp2 = null;
		$bme_academ_exp3 = null;
		$bme_academ_exp4 = null;
		$bmeExp = $_POST['bme_academ_exp'];
		for ($i=1; $i <= count($bmeExp); $i++) {
			${"bme_academ_exp" . $i}  = $bmeExp[$i-1]['name']; 
		}

		$international_experience1 = null;
		$international_experience2 = null;
		$international_experience3 = null;
		$international_experience4 = null;
		$international_experience5 = null;
		$internatExp = $_POST['international_experience'];
		for ($i=1; $i <= count($internatExp); $i++) {
			${"international_experience" . $i}  = $internatExp[$i-1]['name']; 
		}

		$career_dev_program1 = null;
		$career_dev_program2 = null;
		$career_dev_program3 = null;
		$carDevProg = $_POST['career_dev_program']; 
		for ($i=1; $i <= count($carDevProg); $i++) {
			${"career_dev_program" . $i}  = $carDevProg[$i-1]['name']; 
		}

		$post_grad_plan = mysql_real_escape_string($_POST['post_grad_plan']);
		$post_grad_plan_desc = mysql_real_escape_string($_POST['post_grad_plan_desc']);
		$personal_hobby = mysql_real_escape_string($_POST['personal_hobby']);

		$userQuery = sprintf("INSERT INTO USER (username, last_name, first_name, phone_num, email, pref_communication)
					VALUES ('%s', '%s', '%s', '%s','%s','%s')", $user, $lname, $fname,$phone,$email,$pref_communication);
		$uResult = getDBRegInserted($userQuery);

		$menteeQuery = sprintf("INSERT INTO Mentee (username, depth_focus, depth_focus_other, post_grad_plan, post_grad_plan_desc, 
			freshman, transfer_from_outside, institution_name,
			transfer_from_within, prev_major, international_student, first_gen_college_student, expec_graduation, 
			undergrad_research,  personal_hobby) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%u', '%u', '%s', '%u', '%s', '%u', '%u', '%s', '%u', '%s')", 
			$user, $depth_focus, $depth_focus_other, $post_grad_plan, $post_grad_plan_desc, 
			$freshman, $transfer_from_outside, $institution_name,
			$transfer_from_within, $prev_major, $international_student, $first_gen_college_student, $expec_graduation, 
			$undergrad_research, $personal_hobby);
		$mResult = getDBRegInserted($menteeQuery);

		$bTrack = $_POST['breadth_track'];
		foreach ($bTrack as $key => $value) {
			$breadth_track = $value['name'];
			$breadth_track_desc = $value['desc'];
			$btrackQuery = sprintf("INSERT INTO Mentee_Breadth_Track(username, breadth_track, breadth_track_desc) VALUES ('%s', '%s', '%s')",
			$user, $breadth_track, $breadth_track_desc);
			$btrackResult = getDBRegInserted($btrackQuery);
		}

		if ($_POST['bme_organization']) {
			$bmeOrgQuery = sprintf("INSERT INTO Mentee_BME_Organization(username, bme_org1, bme_org2, bme_org3, 
				bme_org4, bme_org5, bme_org6, bme_org7) VALUES ('%s', '%s', '%s' , '%s', '%s', '%s', '%s', '%s')",
				$user, $bme_org1, $bme_org2, $bme_org3, $bme_org4, $bme_org5, $bme_org6, $bme_org7);
			$bmeOrgResult = getDBRegInserted($bmeOrgQuery);
		}

		if ($_POST['bme_academ_exp']) {
			$bmeQuery = sprintf("INSERT INTO Mentee_BME_Academic_Experience(username, bme_academ_exp1, bme_academ_exp2,
				bme_academ_exp3, bme_academ_exp4) VALUES ('%s', '%s', '%s', '%s', '%s')",
			$user, $bme_academ_exp1, $bme_academ_exp2, $bme_academ_exp3, $bme_academ_exp4);
			$bmeResult = getDBRegInserted($bmeQuery);
		}

		if ($_POST['international_experience']) {
			$interQuery = sprintf("INSERT INTO Mentee_International_Experience(username, international_experience1, international_experience2, 
				international_experience3, international_experience4, international_experience5)
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s')", $user, $international_experience1, $international_experience2,
			$international_experience3, $international_experience4, $international_experience5);
			$interResult = getDBRegInserted($interQuery);
		}

		if ($_POST['career_dev_program']) {
			$careerQuery = sprintf("INSERT INTO Mentee_Career_Dev_Program(username, career_dev_program1,
				career_dev_program2, career_dev_program3) VALUES ('%s', '%s', '%s','%s')",
				$user, $career_dev_program1, $career_dev_program2, $career_dev_program3);
			$careerResult = getDBRegInserted($careerQuery);
		}

		if ($_POST['tutor_teacher_program']) {
			$ttProgQuery = sprintf("INSERT INTO Mentee_Tutor_Teacher_Program(username, tutor_teacher_program1, tutor_teacher_program2,
				tutor_teacher_program3, tutor_teacher_program4, tutor_teacher_program5, tutor_teacher_program6) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s',' %s')", $user, $tutor_teacher_program1, $tutor_teacher_program2, $tutor_teacher_program3, 
			$tutor_teacher_program4, $tutor_teacher_program5, $tutor_teacher_program6);
			$ttProgResult = getDBRegInserted($ttProgQuery);
		}

		updateMaxMenteesPerMentor();

		header("Content-type: application/json");
		echo json_encode($uresult);
		// echo json_encode($mresult);
	}

	function updateMaxMenteesPerMentor() {
		include_once("mentor_maximum.php");

		$currentValue = retrieveMaxMenteesPerMentor();
		$minValue = calcMinMaxMenteesPerMentor();

		if ($currentValue < $minValue) {
			setMaxMenteesPerMentor($minValue);
		}
	}

	function getMenteeMatch() {
		global $_USER;
		$dbQuery = sprintf("SELECT mentor_user FROM Matches WHERE mentee_user = '%s'", $_USER['uid']); // breadth_track, student_year, career_dev_program, future_plans, Mentor_BME_Academic_Experience,
		$result = getDBResultsArray($dbQuery);
		echo json_encode($result);
	}

	function getMentorMatches() {
		global $_USER;
		$dbQuery = sprintf("SELECT *
							FROM USER 
							LEFT JOIN Mentee_Breadth_Track ON USER.username = Mentee_Breadth_Track.username
							LEFT JOIN Mentee_BME_Organization ON USER.username = Mentee_BME_Organization.username
							LEFT JOIN Mentee_Tutor_Teacher_Program ON USER.username = Mentee_Tutor_Teacher_Program.username
							LEFT JOIN Mentee_BME_Academic_Experience ON USER.username = Mentee_BME_Academic_Experience.username
							LEFT JOIN Mentee_International_Experience ON USER.username = Mentee_International_Experience.username
							LEFT JOIN Mentee_Career_Dev_Program ON USER.username = Mentee_Career_Dev_Program.username
							LEFT JOIN Ethnicity ON USER.username = Ethnicity.username
							LEFT JOIN Matches ON USER.username = Matches.mentee_user
							LEFT JOIN Other_Organization ON USER.username = Other_Organization.username
							LEFT JOIN Mentee ON USER.username = Mentee.username 
							WHERE Matches.mentor_user = '%s'", $_USER['uid']);
		$result = getDBResultsArray($dbQuery);

		echo json_encode($result);
	}

	function chooseMentor() {
		
		global $_USER;
		// $dbQuery = sprintf("INSERT INTO Matches FROM Mentee WHERE username = '%s'",
		// 										$_USER['uid']);
		$dbQuery = sprintf("INSERT INTO Matches (mentee_user, mentor_user)
					VALUES ('%s', '%s')", $_USER['uid'], $_POST['mentor']);
		
		$result = getDBRegInserted($dbQuery);
		echo json_encode($_POST);
	}



	 function getMentor($mentor) {
		$user = $mentor;

		$dbQuery = sprintf("SELECT *,GROUP_CONCAT(Mentor_Breadth_Track.breadth_track) as `breadth_tracks`
									,GROUP_CONCAT(Mentor_Breadth_Track.breadth_track_desc) as `breadth_track_descs`
							FROM USER 
							LEFT JOIN Mentor_Breadth_Track ON USER.username = Mentor_Breadth_Track.username
							LEFT JOIN Mentor_BME_Organization ON USER.username = Mentor_BME_Organization.username
							LEFT JOIN Mentor_Tutor_Teacher_Program ON USER.username = Mentor_Tutor_Teacher_Program.username
							LEFT JOIN Mentor_BME_Academic_Experience ON USER.username = Mentor_BME_Academic_Experience.username
							LEFT JOIN Mentor_International_Experience ON USER.username = Mentor_International_Experience.username
							LEFT JOIN Mentor_Career_Dev_Program ON USER.username = Mentor_Career_Dev_Program.username
							LEFT JOIN Mentor_Honors_Program ON USER.username = Mentor_Honors_Program.username
							LEFT JOIN Ethnicity ON USER.username = Ethnicity.username
							LEFT JOIN Mentee_Mentor_Organization ON USER.username = Mentee_Mentor_Organization.username
							LEFT JOIN Matches ON USER.username = Matches.mentor_user
							LEFT JOIN Other_Organization ON USER.username = Other_Organization.username
							LEFT JOIN Mentor ON USER.username = Mentor.username 
							WHERE USER.username = '%s'", $user); // breadth_track, student_year, career_dev_program, future_plans, Mentor_BME_Academic_Experience,
		
	


		$result = getDBResultsArray($dbQuery);
		// $checkMentee = sprintf("SELECT first_name, id, mentor_user FROM User, Mentee WHERE User.username = '%s' AND Mentee.username = '%s'", $user, $user);
		// $isMentee = getDBResultsArray($checkMentee);
		echo json_encode($result);
	 }

	 function getMentee($mentee) {
	 	$user = $mentee;

	 	$dbQuery = sprintf("SELECT *,GROUP_CONCAT(Mentee_Breadth_Track.breadth_track) as `breadth_tracks`
									,GROUP_CONCAT(Mentee_Breadth_Track.breadth_track_desc) as `breadth_track_descs`
							FROM USER 
							LEFT JOIN Mentee_Breadth_Track ON USER.username = Mentee_Breadth_Track.username
							LEFT JOIN Mentee_BME_Organization ON USER.username = Mentee_BME_Organization.username
							LEFT JOIN Mentee_Tutor_Teacher_Program ON USER.username = Mentee_Tutor_Teacher_Program.username
							LEFT JOIN Mentee_BME_Academic_Experience ON USER.username = Mentee_BME_Academic_Experience.username
							LEFT JOIN Mentee_International_Experience ON USER.username = Mentee_International_Experience.username
							LEFT JOIN Mentee_Career_Dev_Program ON USER.username = Mentee_Career_Dev_Program.username
							LEFT JOIN Ethnicity ON USER.username = Ethnicity.username
							LEFT JOIN Mentee_Mentor_Organization ON USER.username = Mentee_Mentor_Organization.username
							LEFT JOIN Matches ON USER.username = Matches.mentee_user
							LEFT JOIN Other_Organization ON USER.username = Other_Organization.username
							LEFT JOIN Mentee ON USER.username = Mentee.username 
							WHERE USER.username = '%s'", $user); // breadth_track, student_year, career_dev_program, future_plans, Mentor_BME_Academic_Experience,
		
		$result = getDBResultsArray($dbQuery);
		echo json_encode($result);
	 }

	 function listMentors() {
		$dbQuery = "SELECT * FROM USER
				LEFT JOIN Mentor_Breadth_Track ON Mentor_Breadth_Track.username = USER.username
				LEFT JOIN Mentor_BME_Organization ON Mentor_BME_Organization.username = USER.username
				LEFT JOIN Mentor_Tutor_Teacher_Program ON Mentor_Tutor_Teacher_Program.username = USER.username
				LEFT JOIN Mentor_BME_Academic_Experience ON Mentor_BME_Academic_Experience.username = USER.username
				LEFT JOIN Mentor_International_Experience ON Mentor_International_Experience.username = USER.username
				LEFT JOIN Mentor_Career_Dev_Program ON Mentor_Career_Dev_Program.username = USER.username
				LEFT JOIN Mentor ON USER.username = USER.username
				WHERE Mentor.username = USER.username
					AND (SELECT COUNT(*) FROM Matches
					WHERE Mentor.username = mentor_user) < (SELECT settingValue FROM GlobalSettings where settingName = 'MaxMenteesPerMentor')"; // breadth_track, student_year, career_dev_program, future_plans, Mentor_BME_Academic_Experience,
		$result = getDBResultsArray($dbQuery);
		echo json_encode($result);
	}

	 function listUnapprovedMentors() {
		$dbQuery = "SELECT * FROM USER
													
													LEFT JOIN Mentor_Breadth_Track
														ON  USER.username = Mentor_Breadth_Track.username 
													LEFT JOIN Mentor_BME_Organization
														ON Mentor_BME_Organization.username = USER.username
													LEFT JOIN Mentor_Tutor_Teacher_Program
														ON Mentor_Tutor_Teacher_Program.username = USER.username
													LEFT JOIN Mentor_BME_Academic_Experience
														ON Mentor_BME_Academic_Experience.username = USER.username
													LEFT JOIN Mentor_International_Experience
														ON Mentor_International_Experience.username = USER.username
													LEFT JOIN Mentor_Career_Dev_Program
														ON Mentor_Career_Dev_Program.username = USER.username

													LEFT JOIN Mentor
														ON  USER.username = Mentor.username

													WHERE Mentor.approved = 0"; // breadth_track, student_year, career_dev_program, future_plans, Mentor_BME_Academic_Experience,
		$result = getDBResultsArray($dbQuery);
		echo json_encode($result);
	}

	 function listApprovedMentors() {
		$dbQuery = "SELECT * FROM USER
													
													LEFT JOIN Mentor_Breadth_Track
														ON Mentor_Breadth_Track.username = USER.username
													LEFT JOIN Mentor_BME_Organization
														ON Mentor_BME_Organization.username = USER.username
													LEFT JOIN Mentor_Tutor_Teacher_Program
														ON Mentor_Tutor_Teacher_Program.username = USER.username
													LEFT JOIN Mentor_BME_Academic_Experience
														ON Mentor_BME_Academic_Experience.username = USER.username
													LEFT JOIN Mentor_International_Experience
														ON Mentor_International_Experience.username = USER.username
													LEFT JOIN Mentor_Career_Dev_Program
														ON Mentor_Career_Dev_Program.username = USER.username
													LEFT JOIN Mentor
														ON  USER.username = Mentor.username
													WHERE Mentor.approved = 1"; // breadth_track, student_year, career_dev_program, future_plans, Mentor_BME_Academic_Experience,
		$result = getDBResultsArray($dbQuery);
		echo json_encode($result);
	}

	function approveMentor($mentors) {
		foreach ($mentors as $mentor) {
			$mentor = mysql_real_escape_string($mentor);
			$dbQuery = sprintf("UPDATE Mentor SET approved = 1 WHERE Mentor.username = '%s'", $mentor);
			$result = getDBResultsArray($dbQuery);
			echo json_encode($result);
		}
	}

	function familyRequest() {
		global $_USER;	
		$user = $_USER['uid'];
		$members = $_POST['members'];
		foreach ($members as $member) {
			$member = mysql_real_escape_string($member);
			$frQuery = sprintf("INSERT INTO Family_Request (requester, requested)
					VALUES ('%s', '%s')", $user, $member);
			$fqResult = getDBRegInserted($frQuery);
			echo json_encode($fqResult);
		}
	}

	function listUnapprovedFamily() {
		$reqPairs = array();
		$reqQuery = sprintf("SELECT * FROM Family_Request");
		$requests = getDBResultsArray($reqQuery);
		foreach ($requests as $req) {
			// $reqPair['requested'] = [];
			$reqPair = [];
			$reqInfo = sprintf("SELECT u.username, u.house_belongs,u.first_name,u.last_name,u.email,
							case when u.family_belongs is not null 
								then u.family_belongs
								else 'Wait to be assigned'
							end as family_belongs,
       						case when GROUP_CONCAT(Mentor_Breadth_Track.breadth_track) is not null 
       							then GROUP_CONCAT(Mentor_Breadth_Track.breadth_track)
       							else NULL
       						end as `mentor_breadth_tracks`
							FROM USER u 
							left join Mentor on u.username = Mentor.username
							left join Mentor_Breadth_Track on u.username = Mentor_Breadth_Track.username
							WHERE u.username = '%s'",$req['requester']);
			$reqInfo = getDBResultsArray($reqInfo);
			$reqeeQuery = sprintf("SELECT requested FROM Family_Request WHERE requester = '%s'",$req['requester']);
			$reqees = getDBResultsArray($reqeeQuery);
			$reqPair['requester'] = $reqInfo[0];
			$reqeeInfo = sprintf("SELECT u.username, u.house_belongs,u.first_name,u.last_name,u.email,
							case when u.family_belongs is not null 
								then u.family_belongs
								else 'Wait to be assigned'
							end as family_belongs,
       						case when GROUP_CONCAT(Mentee_Breadth_Track.breadth_track) is not null 
       							then GROUP_CONCAT(Mentee_Breadth_Track.breadth_track)
       							else NULL
       						end as `mentee_breadth_tracks`
							FROM USER u 
							left join Mentee on u.username = Mentee.username
							left join Mentee_Breadth_Track on u.username = Mentee_Breadth_Track.username
							WHERE u.username = '%s'",$req['requested']);
			$reqeeInfo = getDBResultsArray($reqeeInfo);
			$reqPair['requested'] = $reqeeInfo[0];
			$reqPair['id'] = $req['_id'];
			array_push($reqPairs, $reqPair);
		}
		echo json_encode($reqPairs);
	}

	function leaveFamily($reason) {
		global $_USER;	
		$user = $_USER['uid'];
		$dbQuery = sprintf("INSERT INTO Leave_Request (username,house_belongs,family_belongs,leave_reason) 
							VALUES ('%s',(SELECT house_belongs FROM USER u WHERE u.username='%s'),
										 (SELECT family_belongs FROM USER u WHERE u.username='%s'),
										 '%s')",$user,$user,$user,$reason);
		$dbResult = getDBRegInserted($dbQuery);
	}

	function getLeaveRequests() {
		$dbQuery = sprintf("SELECT u.username, u.first_name,u.last_name,lr.house_belongs,lr.family_belongs,lr.leave_reason
							FROM Leave_Request lr INNER JOIN USER u ON lr.username = u.username");
		$dbResult = getDBResultsArray($dbQuery);
		echo json_encode($dbResult);
	}

	function approveFamilyRequest($reqs) {

		foreach ($reqs as $req) {
			$requester = mysql_real_escape_string($req['requester']);
			$requested = mysql_real_escape_string($req['requested']);
			$house = mysql_real_escape_string($req['house']);
			
			if ($req['new_family'] == 1) {
				$checkFamily = sprintf("SELECT family_belongs FROM USER WHERE username='%s'",$requester);
				$checkResult = getDBResultsArray($checkFamily);
				if(!$checkResult[0]['family_belongs']) {
					createFamily($requester,$house);
				}
			}
			//requested mentor
			$familyQuery = sprintf("UPDATE USER,
									(SELECT family_belongs FROM USER WHERE username='%s') u2
									SET USER.family_belongs = u2.family_belongs
									WHERE USER.username='%s'",$requester,$requested);
			$familyResult = getDBResultInserted($familyQuery);
			//mentee of requested mentor
			$familyQuery = sprintf("UPDATE USER,
									(SELECT family_belongs FROM USER WHERE username='%s') u2
									SET USER.family_belongs = u2.family_belongs
									WHERE USER.username=(SELECT mentee_user FROM Matches 
												WHERE mentor_user='%s')",$requested,$requested);
			$familyResult = getDBResultInserted($familyQuery);

			$delReqQuery = sprintf("DELETE FROM Family_Request WHERE requester='%s' AND requested='%s'
								   ",$requester,$requested);
			$delResult = getDBResultsArray($delReqQuery);
			$delReqQuery = sprintf("DELETE FROM Family_Request WHERE requested='%s'
								   ",$requested);
			$delResult = getDBResultsArray($delReqQuery);			
		}
	}

	function approveLeaverequest($reqs) {
		foreach ($reqs as $req) {
			$requester = mysql_real_escape_string($req);
			$dbQuery = sprintf("UPDATE USER SET family_belongs=null WHERE username='%s'",$requester);
			$dbResult = getDBRegInserted($dbQuery);

			$dbQuery = sprintf("UPDATE USER SET family_belongs=null 
								WHERE username=(SELECT mentee_user FROM Matches 
												WHERE mentor_user='%s')"
								,$requester);
			$dbResult = getDBRegInserted($dbQuery);

			$delReqQuery = sprintf("DELETE FROM Leave_Request WHERE username='%s'
								   ",$requester);
			$delResult = getDBResultsArray($delReqQuery);
			echo json_encode($dbResult);	
		}

	}

	function createFamily($member,$house) {
		$dbQuery = sprintf("INSERT INTO Family (family_number,house_name) 
							VALUES ((SELECT COALESCE(((SELECT f2.family_number FROM Family f2 
									 WHERE f2.house_name = '%s' ORDER BY f2.family_number DESC LIMIT 1)+1),1)),
							'%s')",$house,$house);
		$dbResult = getDBResultInserted($dbQuery);
		$dbQuery = sprintf("UPDATE USER SET family_belongs = 
							(SELECT family_number FROM Family
							WHERE house_name = '%s'
							ORDER BY family_number DESC
							LIMIT 1) WHERE username='%s'",$house, $member);		
		$dbResult = getDBResultInserted($dbQuery);
	}

	function addMentorLoop($mentor) {
		echo "addMEntor in PHP \n";
		global $_USER;	
		$user = $mentor['username'];
		$fname = mysql_real_escape_string($mentor['first_name']);//$data->fname);
		$lname = mysql_real_escape_string($mentor['last_name']);
		$alias = mysql_real_escape_string($mentor['alias']);
		$phone = mysql_real_escape_string($mentor['phone']);
		$email = mysql_real_escape_string($mentor['email']);
		$pref_communication = mysql_real_escape_string($mentor['pref_communication']);
		$gender = mysql_real_escape_string($mentor['gender']);
		$depth_focus = mysql_real_escape_string($mentor['depth_focus']);
		$depth_focus_other = mysql_real_escape_string($mentor['depth_focus_other']); 
		$live_before_tech = mysql_real_escape_string($mentor['live_before_tech']);
		$live_on_campus = $mentor['live_on_campus']; //is number 0 or 1 posting?
		$first_gen_college_student = $mentor['first_gen_college_student'];
		$transfer_from_outside = $mentor['transfer_from_outside'];
		$institution_name = mysql_real_escape_string($mentor['institution_name']);
		$transfer_from_within = $mentor['transfer_from_within'];
		$prev_major = mysql_real_escape_string($mentor['prev_major']);
		$international_student = $mentor['international_student'];
		$home_country =  mysql_real_escape_string($mentor['home_country']);
		$expec_graduation = mysql_real_escape_string($mentor['expec_graduation']);
		$other_major =  mysql_real_escape_string($mentor['other_major']);
	
		$ethnicity1 = null;
		$ethnicity2 = null; 
		$ethnicity3 = null;
		$ethnicity4 = null;
		$ethnicity5 - null;
		$ethnicity = $mentor['ethnicity'];
		for ($i=1; $i <= count($ethnicity) ; $i++) {
			${"ethnicity" . $i}  = $ethnicity[$i-1]['name'];
		}

		$honor_program1 = null;
		$honor_program2 = null;
		$honor_program3 = null;
		$hProgs = $mentor['honor_program'];
		for ($i=1; $i <= count($hProgs); $i++) {
			${"honor_program" . $i}  = $hProgs[$i-1]['name']; 
		}		

		$undergrad_research = $mentor['undergrad_research'];
		if ($mentor['undergrad_research']) {
			$undergrad_research_desc = $mentor['undergrad_research_desc'];
		} else {
			$undergrad_research_desc = null;
		}

		if ($mentor['other_organization1']) {
			$other_organization1 = $mentor['other_organization1'];
		} else {
			$other_organization1 = null;
		}
		if ($mentor['other_organization2']) {
			$other_organization2 = $mentor['other_organization2'];
		} else {
			$other_organization2 = null;
		}
		if ($mentor['other_organization3']) {
			$other_organization3 = $mentor['other_organization3'];
		} else {
			$other_organization3 = null;
		}

		$bme_org1 = null;
		$bme_org2 = null;
		$bme_org3 = null;
		$bme_org4 = null;
		$bme_org5 = null;
		$bme_org6 = null;
		$bme_org7 = null;
		//echo var_dump($mentor['bme_organization']);
		$bmeOrgs = $mentor['bme_organization'];
		for ($i=1; $i <= count($bmeOrgs); $i++) {
			//echo $bmeOrgs[$i-1]['name'];
			${"bme_org" . $i}  = $bmeOrgs[$i-1]['name']; //Json of all the organizations $mentor['bme_organization']
		}
		if ($mentor['bme_org_other']) {
			$bme_org_other = mysql_real_escape_string($mentor['bme_org_other']);
		} else {
			$bme_org_other = null;
		}

		$mm_org1 = null;
		$mm_org2 = null;
		$mm_org3 = null;
		$mm_org4 = null;
		$mm_org5 = null;
		$mmOrgs = $mentor['mm_org'];
		for ($i=1; $i <= count($mmOrgs); $i++) {
			${"mm_org" . $i}  = $mmOrgs[$i-1]['name']; //Json of all the organizations $mentor['bme_organization']
		} 
		if ($mentor['mm_org_other']) {
			$mm_org_other = mysql_real_escape_string($mentor['mm_org_other']);
		} else {
			$mm_org_other = null;
		}

		$tutor_teacher_program1 = null;
		$tutor_teacher_program2 = null;
		$tutor_teacher_program3 = null;
		$tutor_teacher_program4 = null;
		$tutor_teacher_program5 = null;
		$tutor_teacher_program6 = null;
		$ttProg = $mentor['tutor_teacher_program'];
		for ($i=1; $i <= count($ttProg); $i++) {
			${"tutor_teacher_program" . $i}  = $ttProg[$i-1]['name']; 
		}
		if ($mentor['tutor_teacher_program_other']) {
			$tutor_teacher_program_other = mysql_real_escape_string($mentor['tutor_teacher_program_other']);
		} else {
			$tutor_teacher_program_other = null;
		}

		$bme_academ_exp1 = null;
		$bme_academ_exp2 = null;
		$bme_academ_exp3 = null;
		$bme_academ_exp4 = null;
		$bmeExp = $mentor['bme_academ_exp'];
		for ($i=1; $i <= count($bmeExp); $i++) {
			${"bme_academ_exp" . $i}  = $bmeExp[$i-1]['name']; 
		}
		if ($mentor['bme_academ_exp_other']) {
			$bme_academ_exp_other = mysql_real_escape_string($mentor['bme_academ_exp_other']);
		} else {
			$bme_academ_exp_other = null;
		}

		$international_experience1 = null;
		$international_experience2 = null;
		$international_experience3 = null;
		$international_experience4 = null;
		$international_experience5 = null;
		$internatExp = $mentor['international_experience'];
		for ($i=1; $i <= count($internatExp); $i++) {
			${"international_experience" . $i}  = $internatExp[$i-1]['name']; 
		}
		if ($mentor['international_experience_other']) {
			$international_experience_other = mysql_real_escape_string($mentor['international_experience_other']);
		} else {
			$international_experience_other = null;
		}

		$career_dev_program1 = null;
		$career_dev_program2 = null;
		$career_dev_program3 = null;
		$carDevProg = $mentor['career_dev_program']; 
		for ($i=1; $i <= count($carDevProg); $i++) {
			${"career_dev_program" . $i}  = $carDevProg[$i-1]['name']; 
		}
		if ($mentor['career_dev_program_other']) {
			$career_dev_program_other = mysql_real_escape_string($mentor['career_dev_program_other']);
		} else {
			$career_dev_program_other = null;
		}

		$post_grad_plan = mysql_real_escape_string($mentor['post_grad_plan']);
		if ($mentor['post_grad_plan_desc']) {
			$post_grad_plan_desc = mysql_real_escape_string($mentor['post_grad_plan_desc']);
		} else {
			$post_grad_plan_desc = null;
		}

		$personal_hobby = mysql_real_escape_string($mentor['personal_hobby']);

		$userQuery = sprintf("INSERT INTO USER (username, last_name, first_name, phone_num, email, pref_communication)
					VALUES ('%s', '%s', '%s','%s','%s','%s')", $user, $lname, $fname, $phone, $email, $pref_communication);
		$uResult = getDBRegInserted($userQuery);


		$mentorQuery = sprintf("INSERT INTO Mentor (username, alias, gender, depth_focus, depth_focus_other,
			live_before_tech, live_on_campus, first_gen_college_student, transfer_from_outside, institution_name,
			transfer_from_within, prev_major, international_student, home_country, expec_graduation, other_major, 
			undergrad_research, undergrad_research_desc, post_grad_plan, post_grad_plan_desc, personal_hobby) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%u', '%u', '%u', '%u', '%s', '%u', '%s', '%u', '%s', '%s', '%s', 
				'%u', '%s', '%s', '%s', '%s')", 
			$user, $alias, $gender, $depth_focus, $depth_focus_other,
			$live_before_tech, $live_on_campus, $first_gen_college_student, $transfer_from_outside, $institution_name, 
			$transfer_from_within, $prev_major, $international_student, $home_country, $expec_graduation, $other_major,
			$undergrad_research, $undergrad_research_desc, $post_grad_plan, $post_grad_plan_desc, $personal_hobby);
		$mResult = getDBRegInserted($mentorQuery);

		$bTrack = $mentor['breadth_track'];
		//echo var_dump($bTrack);
		foreach ($bTrack as $key => $value) {
			$breadth_track = $value['name'];
			//echo $value['name'];
			$breadth_track_desc = $value['desc'];
			$bTrackQuery = sprintf("INSERT INTO Mentor_Breadth_Track(username, breadth_track, breadth_track_desc) VALUES ('%s', '%s', '%s')",
			$user, $breadth_track, $breadth_track_desc);
			$bTrackResult = getDBRegInserted($bTrackQuery);
		}

		if ($mentor['ethnicity']) {
		$ethQuery = sprintf("INSERT INTO Ethnicity(username, ethnicity1, ethnicity2, ethnicity3, ethnicity4, ethnicity5) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s')", $user, $ethnicity1, $ethnicity2, $ethnicity3, $ethnicity4, $ethnicity5);
		$eResult = getDBRegInserted($ethQuery);
		}

		if ($mentor['honor_program']) {
		$honorProgQuery = sprintf("INSERT INTO Mentor_Honors_Program(username, program1, program2, program3) 
			VALUES ('%s', '%s', '%s', '%s')", $user, $honor_program1, $honor_program2, $honor_program3);
		$hpResult = getDBRegInserted($honorProgQuery);
		}

		if ($mentor['other_organization1']) {
			$otherOrgQuery = sprintf("INSERT INTO Other_Organization(username, organization1, organization2, organization3) 
				VALUES ('%s', '%s', '%s', '%s')", $user, $other_organization1, $other_organization2, $other_organization3);
			$otherOrgResult = getDBRegInserted($otherOrgQuery);
		}

		if ($mentor['bme_organization']) {
		$bmeOrgQuery = sprintf("INSERT INTO Mentor_BME_Organization(username, bme_org1, bme_org2, bme_org3,
			bme_org4, bme_org5, bme_org6, bme_org7, bme_org_other) VALUES ('%s', '%s', '%s' , '%s', '%s', '%s', '%s', '%s', '%s')",
			$user, $bme_org1, $bme_org2, $bme_org3, $bme_org4, $bme_org5, $bme_org6, $bme_org7, $bme_org_other);
		$bmeOrgResult = getDBRegInserted($bmeOrgQuery);
		}

		if ($mentor['mm_org']) {
		$mmOrgQuery = sprintf("INSERT INTO Mentee_Mentor_Organization(username, mm_org1, mm_org2, mm_org3, mm_org4, mm_org5, mm_org_other) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')", $user, $mm_org1, $mm_org2, $mm_org3, $mm_org4, $mm_org5, $mm_org_other);
		$mmResult = getDBRegInserted($mmOrgQuery);
		}

		if ($mentor['tutor_teacher_program']) {
		$ttProgQuery = sprintf("INSERT INTO Mentor_Tutor_Teacher_Program(username, tutor_teacher_program1, tutor_teacher_program2, 
			tutor_teacher_program3, tutor_teacher_program4, tutor_teacher_program5, tutor_teacher_program6, tutor_teacher_program_other)
		 VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $user, $tutor_teacher_program1, $tutor_teacher_program2, 
		 $tutor_teacher_program3, $tutor_teacher_program4, $tutor_teacher_program5, $tutor_teacher_program6, $tutor_teacher_program_other);
		$ttProgResult = getDBRegInserted($ttProgQuery);
		}

		if ($mentor['bme_academ_exp']) {
		$bmeQuery = sprintf("INSERT INTO Mentor_BME_Academic_Experience(username, bme_academ_exp1, bme_academ_exp2,
			bme_academ_exp3, bme_academ_exp4, bme_academ_exp_other) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
		$user, $bme_academ_exp1, $bme_academ_exp2, $bme_academ_exp3, $bme_academ_exp4, $bme_academ_exp_other);
		$bmeResult = getDBRegInserted($bmeQuery);
		}

		if ($mentor['international_experience']) {
		$interQuery = sprintf("INSERT INTO Mentor_International_Experience(username, international_experience1, international_experience2, 
			international_experience3, international_experience4, international_experience5, international_experience_other)
		VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')", $user, $international_experience1, $international_experience2,
		$international_experience3, $international_experience4, $international_experience5, $international_experience_other);
		$interResults = getDBRegInserted($interQuery);
		}

		if ($mentor['career_dev_program']) {
		$careerQuery = sprintf("INSERT INTO Mentor_Career_Dev_Program(username, career_dev_program1,
			career_dev_program2, career_dev_program3, career_dev_program_other) VALUES ('%s', '%s', '%s','%s', '%s')",
			 $user, $career_dev_program1, $career_dev_program2, $career_dev_program3, $career_dev_program_other);
		$careerResults = getDBRegInserted($careerQuery);
		}

		// //header("Content-type: application/json");
		// // print_r($json);
		// echo json_encode($uresult+$mresult);
		
	}//end addMentor

	function addMentor() {
		echo "addMEntor in PHP \n";
		global $_USER;	
		$user = $_USER['uid'];
		$fname = mysql_real_escape_string($_POST['fname']);//$data->fname);
		$lname = mysql_real_escape_string($_POST['lname']);
		$phone = mysql_real_escape_string($_POST['phone']);
		$email = mysql_real_escape_string($_POST['email']);
		$pref_communication = mysql_real_escape_string($_POST['pref_communication']);
		$gender = mysql_real_escape_string($_POST['gender']);
		$depth_focus = mysql_real_escape_string($_POST['dfocus']);
		$depth_focus_other = mysql_real_escape_string($_POST['dfocusother']); 
		$live_before_tech = mysql_real_escape_string($_POST['live_before_tech']);
		$live_on_campus = $_POST['live_on_campus']; //is number 0 or 1 posting?
		$first_gen_college_student = $_POST['first_gen_college_student'];
		$transfer_from_outside = $_POST['transfer_from_outside'];
		$institution_name = mysql_real_escape_string($_POST['institution_name']);
		$transfer_from_within = $_POST['transfer_from_within'];
		$prev_major = mysql_real_escape_string($_POST['prev_major']);
		$international_student = $_POST['international_student'];
		$home_country =  mysql_real_escape_string($_POST['home_country']);
		$expec_graduation = mysql_real_escape_string($_POST['expec_graduation']);
		$other_major =  mysql_real_escape_string($_POST['other_major']);
	
		$ethnicity1 = null;
		$ethnicity2 = null; 
		$ethnicity3 = null;
		$ethnicity4 = null;
		$ethnicity5 = null;
		$ethnicity = $_POST['ethnicity'];
		for ($i=1; $i <= count($ethnicity) ; $i++) {
			${"ethnicity" . $i}  = $ethnicity[$i-1]['name'];
		}

		$honor_program1 = null;
		$honor_program2 = null;
		$honor_program3 = null;
		$hProgs = $_POST['honor_program'];
		for ($i=1; $i <= count($hProgs); $i++) {
			${"honor_program" . $i}  = $hProgs[$i-1]['name']; 
		}		

		$undergrad_research = $_POST['undergrad_research'];
		if ($_POST['undergrad_research']) {
			$undergrad_research_desc = $_POST['undergrad_research_desc'];
		} else {
			$undergrad_research_desc = null;
		}

		if ($_POST['other_organization1']) {
			$other_organization1 = $_POST['other_organization1'];
		} else {
			$other_organization1 = null;
		}
		if ($_POST['other_organization2']) {
			$other_organization2 = $_POST['other_organization2'];
		} else {
			$other_organization2 = null;
		}
		if ($_POST['other_organization3']) {
			$other_organization3 = $_POST['other_organization3'];
		} else {
			$other_organization3 = null;
		}

		$bme_org1 = null;
		$bme_org2 = null;
		$bme_org3 = null;
		$bme_org4 = null;
		$bme_org5 = null;
		$bme_org6 = null;
		$bme_org7 = null;
		$bmeOrgs = $_POST['bme_organization'];
		for ($i=1; $i <= count($bmeOrgs); $i++) {
			${"bme_org" . $i}  = $bmeOrgs[$i-1]['name']; //Json of all the organizations $_POST['bme_organization']
		}
		if ($_POST['bme_org_other']) {
			$bme_org_other = mysql_real_escape_string($_POST['bme_org_other']);
		} else {
			$bme_org_other = null;
		}

		$mm_org1 = null;
		$mm_org2 = null;
		$mm_org3 = null;
		$mm_org4 = null;
		$mm_org5 = null;
		$mmOrgs = $_POST['mm_org'];
		for ($i=1; $i <= count($mmOrgs); $i++) {
			${"mm_org" . $i}  = $mmOrgs[$i-1]['name']; //Json of all the organizations $_POST['bme_organization']
		} 
		if ($_POST['mm_org_other']) {
			$mm_org_other = mysql_real_escape_string($_POST['mm_org_other']);
		} else {
			$mm_org_other = null;
		}

		$tutor_teacher_program1 = null;
		$tutor_teacher_program2 = null;
		$tutor_teacher_program3 = null;
		$tutor_teacher_program4 = null;
		$tutor_teacher_program5 = null;
		$tutor_teacher_program6 = null;
		$ttProg = $_POST['tutor_teacher_program'];
		for ($i=1; $i <= count($ttProg); $i++) {
			${"tutor_teacher_program" . $i}  = $ttProg[$i-1]['name']; 
		}
		if ($_POST['tutor_teacher_program_other']) {
			$tutor_teacher_program_other = mysql_real_escape_string($_POST['tutor_teacher_program_other']);
		} else {
			$tutor_teacher_program_other = null;
		}

		$bme_academ_exp1 = null;
		$bme_academ_exp2 = null;
		$bme_academ_exp3 = null;
		$bme_academ_exp4 = null;
		$bmeExp = $_POST['bme_academ_exp'];
		for ($i=1; $i <= count($bmeExp); $i++) {
			${"bme_academ_exp" . $i}  = $bmeExp[$i-1]['name']; 
		}
		if ($_POST['bme_academ_exp_other']) {
			$bme_academ_exp_other = mysql_real_escape_string($_POST['bme_academ_exp_other']);
		} else {
			$bme_academ_exp_other = null;
		}

		$international_experience1 = null;
		$international_experience2 = null;
		$international_experience3 = null;
		$international_experience4 = null;
		$international_experience5 = null;
		$internatExp = $_POST['international_experience'];
		for ($i=1; $i <= count($internatExp); $i++) {
			${"international_experience" . $i}  = $internatExp[$i-1]['name']; 
		}
		if ($_POST['international_experience_other']) {
			$international_experience_other = mysql_real_escape_string($_POST['international_experience_other']);
		} else {
			$international_experience_other = null;
		}

		$career_dev_program1 = null;
		$career_dev_program2 = null;
		$career_dev_program3 = null;
		$carDevProg = $_POST['career_dev_program']; 
		for ($i=1; $i <= count($carDevProg); $i++) {
			${"career_dev_program" . $i}  = $carDevProg[$i-1]['name']; 
		}
		if ($_POST['career_dev_program_other']) {
			$career_dev_program_other = mysql_real_escape_string($_POST['career_dev_program_other']);
		} else {
			$career_dev_program_other = null;
		}

		$post_grad_plan = mysql_real_escape_string($_POST['post_grad_plan']);
		if ($_POST['post_grad_plan_desc']) {
			$post_grad_plan_desc = mysql_real_escape_string($_POST['post_grad_plan_desc']);
		} else {
			$post_grad_plan_desc = null;
		}

		$personal_hobby = mysql_real_escape_string($_POST['personal_hobby']);

		$userQuery = sprintf("INSERT INTO USER (username, last_name, first_name, phone_num, email, pref_communication)
					VALUES ('%s', '%s', '%s','%s','%s','%s')", $user, $lname, $fname, $phone, $email, $pref_communication);
		$uResult = getDBRegInserted($userQuery);


		$mentorQuery = sprintf("INSERT INTO Mentor (username, gender, depth_focus, depth_focus_other,
			live_before_tech, live_on_campus, first_gen_college_student, transfer_from_outside, institution_name,
			transfer_from_within, prev_major, international_student, home_country, expec_graduation, other_major, 
			undergrad_research, undergrad_research_desc, post_grad_plan, post_grad_plan_desc, personal_hobby) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%u', '%u', '%u', '%s', '%u', '%s', '%u', '%s', '%s', '%s', 
				'%u', '%s', '%s', '%s', '%s')", 
			$user, $gender, $depth_focus, $depth_focus_other,
			$live_before_tech, $live_on_campus, $first_gen_college_student, $transfer_from_outside, $institution_name, 
			$transfer_from_within, $prev_major, $international_student, $home_country, $expec_graduation, $other_major,
			$undergrad_research, $undergrad_research_desc, $post_grad_plan, $post_grad_plan_desc, $personal_hobby);
		$mResult = getDBRegInserted($mentorQuery);

		$bTrack = $_POST['breadth_track'];
		foreach ($bTrack as $key => $value) {
			$breadth_track = $value['name'];
			$breadth_track_desc = $value['desc'];
			$bTrackQuery = sprintf("INSERT INTO Mentor_Breadth_Track(username, breadth_track, breadth_track_desc) VALUES ('%s', '%s', '%s')",
			$user, $breadth_track, $breadth_track_desc);
			$bTrackResult = getDBRegInserted($bTrackQuery);
		}

		if ($_POST['ethnicity']) {
		$ethQuery = sprintf("INSERT INTO Ethnicity(username, ethnicity1, ethnicity2, ethnicity3, ethnicity4, ethnicity5) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s')", $user, $ethnicity1, $ethnicity2, $ethnicity3, $ethnicity4, $ethnicity5);
		$eResult = getDBRegInserted($ethQuery);
		}

		if ($_POST['honor_program']) {
		$honorProgQuery = sprintf("INSERT INTO Mentor_Honors_Program(username, program1, program2, program3) 
			VALUES ('%s', '%s', '%s', '%s')", $user, $honor_program1, $honor_program2, $honor_program3);
		$hpResult = getDBRegInserted($honorProgQuery);
		}

		if ($_POST['other_organization1']) {
			$otherOrgQuery = sprintf("INSERT INTO Other_Organization(username, organization1, organization2, organization3) 
				VALUES ('%s', '%s', '%s', '%s')", $user, $other_organization1, $other_organization2, $other_organization3);
			$otherOrgResult = getDBRegInserted($otherOrgQuery);
		}

		if ($_POST['bme_organization']) {
		$bmeOrgQuery = sprintf("INSERT INTO Mentor_BME_Organization(username, bme_org1, bme_org2, bme_org3,
			bme_org4, bme_org5, bme_org6, bme_org7, bme_org_other) VALUES ('%s', '%s', '%s' , '%s', '%s', '%s', '%s', '%s', '%s')",
			$user, $bme_org1, $bme_org2, $bme_org3, $bme_org4, $bme_org5, $bme_org6, $bme_org7, $bme_org_other);
		$bmeOrgResult = getDBRegInserted($bmeOrgQuery);
		}

		if ($_POST['mm_org']) {
		$mmOrgQuery = sprintf("INSERT INTO Mentee_Mentor_Organization(username, mm_org1, mm_org2, mm_org3, mm_org4, mm_org5, mm_org_other) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')", $user, $mm_org1, $mm_org2, $mm_org3, $mm_org4, $mm_org5, $mm_org_other);
		$mmResult = getDBRegInserted($mmOrgQuery);
		}

		if ($_POST['tutor_teacher_program']) {
		$ttProgQuery = sprintf("INSERT INTO Mentor_Tutor_Teacher_Program(username, tutor_teacher_program1, tutor_teacher_program2, 
			tutor_teacher_program3, tutor_teacher_program4, tutor_teacher_program5, tutor_teacher_program6, tutor_teacher_program_other)
		 VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $user, $tutor_teacher_program1, $tutor_teacher_program2, 
		 $tutor_teacher_program3, $tutor_teacher_program4, $tutor_teacher_program5, $tutor_teacher_program6, $tutor_teacher_program_other);
		$ttProgResult = getDBRegInserted($ttProgQuery);
		}

		if ($_POST['bme_academ_exp']) {
		$bmeQuery = sprintf("INSERT INTO Mentor_BME_Academic_Experience(username, bme_academ_exp1, bme_academ_exp2,
			bme_academ_exp3, bme_academ_exp4, bme_academ_exp_other) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
		$user, $bme_academ_exp1, $bme_academ_exp2, $bme_academ_exp3, $bme_academ_exp4, $bme_academ_exp_other);
		$bmeResult = getDBRegInserted($bmeQuery);
		}

		if ($_POST['international_experience']) {
		$interQuery = sprintf("INSERT INTO Mentor_International_Experience(username, international_experience1, international_experience2, 
			international_experience3, international_experience4, international_experience5, international_experience_other)
		VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')", $user, $international_experience1, $international_experience2,
		$international_experience3, $international_experience4, $international_experience5, $international_experience_other);
		$interResults = getDBRegInserted($interQuery);
		}

		if ($_POST['career_dev_program']) {
		$careerQuery = sprintf("INSERT INTO Mentor_Career_Dev_Program(username, career_dev_program1,
			career_dev_program2, career_dev_program3, career_dev_program_other) VALUES ('%s', '%s', '%s','%s', '%s')",
			 $user, $career_dev_program1, $career_dev_program2, $career_dev_program3, $career_dev_program_other);
		$careerResults = getDBRegInserted($careerQuery);
		}

		// //header("Content-type: application/json");
		// // print_r($json);
		// echo json_encode($uresult+$mresult);
		
	}//end addMentor

	//begin edit mentor profile
	function updateMentorProfile() {
		
		echo "Update Mentor Profile in PHP \n";
		global $_USER;	
		$user = $_USER['uid'];
		echo $user;
		$fname = mysql_real_escape_string($_POST['fname']);//$data->fname);
		echo $fname;
		$lname = mysql_real_escape_string($_POST['lname']);
		$phone = mysql_real_escape_string($_POST['phone']);
		$email = mysql_real_escape_string($_POST['email']);
		$pref_communication = mysql_real_escape_string($_POST['pref_communication']);
		$gender = mysql_real_escape_string($_POST['gender']);
		$depth_focus = mysql_real_escape_string($_POST['dfocus']);
		$depth_focus_other = mysql_real_escape_string($_POST['dfocusother']); 
		$live_before_tech = mysql_real_escape_string($_POST['live_before_tech']);
		$live_on_campus = $_POST['live_on_campus']; //is number 0 or 1 posting?
		$first_gen_college_student = $_POST['first_gen_college_student'];
		$transfer_from_outside = $_POST['transfer_from_outside'];
		$institution_name = mysql_real_escape_string($_POST['institution_name']);
		$transfer_from_within = $_POST['transfer_from_within'];
		$prev_major = mysql_real_escape_string($_POST['prev_major']);
		$international_student = $_POST['international_student'];
		$home_country =  mysql_real_escape_string($_POST['home_country']);
		$expec_graduation = mysql_real_escape_string($_POST['expec_graduation']);
		$other_major =  mysql_real_escape_string($_POST['other_major']);
	
		$ethnicity1 = null;
		$ethnicity2 = null; 
		$ethnicity3 = null;
		$ethnicity4 = null;
		$ethnicity5 = null;
		$ethnicity = $_POST['ethnicity'];
		for ($i=1; $i <= count($ethnicity) ; $i++) {
			${"ethnicity" . $i}  = $ethnicity[$i-1];
		}

		$honor_program1 = null;
		$honor_program2 = null;
		$honor_program3 = null;
		$hProgs = $_POST['honor_program'];
		for ($i=1; $i <= count($hProgs); $i++) {
			${"honor_program" . $i}  = $hProgs[$i-1]; 
		}		

		$undergrad_research = $_POST['undergrad_research'];
		if ($_POST['undergrad_research']) {
			$undergrad_research_desc = $_POST['undergrad_research_desc'];
		} else {
			$undergrad_research_desc = null;
		}

		if ($_POST['other_organization1']) {
			$other_organization1 = $_POST['other_organization1'];
		} else {
			$other_organization1 = null;
		}
		if ($_POST['other_organization2']) {
			$other_organization2 = $_POST['other_organization2'];
		} else {
			$other_organization2 = null;
		}
		if ($_POST['other_organization3']) {
			$other_organization3 = $_POST['other_organization3'];
		} else {
			$other_organization3 = null;
		}

		$bme_org1 = null;
		$bme_org2 = null;
		$bme_org3 = null;
		$bme_org4 = null;
		$bme_org5 = null;
		$bme_org6 = null;
		$bme_org7 = null;
		$bmeOrgs = $_POST['bme_organization'];
		for ($i=1; $i <= count($bmeOrgs); $i++) {
			${"bme_org" . $i}  = $bmeOrgs[$i-1]; //Json of all the organizations $_POST['bme_organization']
		}
		if ($_POST['bme_org_other']) {
			$bme_org_other = mysql_real_escape_string($_POST['bme_org_other']);
		} else {
			$bme_org_other = null;
		}

		$mm_org1 = null;
		$mm_org2 = null;
		$mm_org3 = null;
		$mm_org4 = null;
		$mm_org5 = null;
		$mmOrgs = $_POST['mm_org'];
		for ($i=1; $i <= count($mmOrgs); $i++) {
			${"mm_org" . $i}  = $mmOrgs[$i-1]; //Json of all the organizations $_POST['bme_organization']
		} 
		if ($_POST['mm_org_other']) {
			$mm_org_other = mysql_real_escape_string($_POST['mm_org_other']);
		} else {
			$mm_org_other = null;
		}

		$tutor_teacher_program1 = null;
		$tutor_teacher_program2 = null;
		$tutor_teacher_program3 = null;
		$tutor_teacher_program4 = null;
		$tutor_teacher_program5 = null;
		$tutor_teacher_program6 = null;
		$ttProg = $_POST['tutor_teacher_program'];
		for ($i=1; $i <= count($ttProg); $i++) {
			${"tutor_teacher_program" . $i}  = $ttProg[$i-1]; 
		}
		if ($_POST['tutor_teacher_program_other']) {
			$tutor_teacher_program_other = mysql_real_escape_string($_POST['tutor_teacher_program_other']);
		} else {
			$tutor_teacher_program_other = null;
		}

		$bme_academ_exp1 = null;
		$bme_academ_exp2 = null;
		$bme_academ_exp3 = null;
		$bme_academ_exp4 = null;
		$bmeExp = $_POST['bme_academ_exp'];
		for ($i=1; $i <= count($bmeExp); $i++) {
			${"bme_academ_exp" . $i}  = $bmeExp[$i-1]; 
		}
		if ($_POST['bme_academ_exp_other']) {
			$bme_academ_exp_other = mysql_real_escape_string($_POST['bme_academ_exp_other']);
		} else {
			$bme_academ_exp_other = null;
		}

		$international_experience1 = null;
		$international_experience2 = null;
		$international_experience3 = null;
		$international_experience4 = null;
		$international_experience5 = null;
		$internatExp = $_POST['international_experience'];
		for ($i=1; $i <= count($internatExp); $i++) {
			${"international_experience" . $i}  = $internatExp[$i-1]; 
		}
		if ($_POST['international_experience_other']) {
			$international_experience_other = mysql_real_escape_string($_POST['international_experience_other']);
		} else {
			$international_experience_other = null;
		}

		$career_dev_program1 = null;
		$career_dev_program2 = null;
		$career_dev_program3 = null;
		$carDevProg = $_POST['career_dev_program']; 
		for ($i=1; $i <= count($carDevProg); $i++) {
			${"career_dev_program" . $i}  = $carDevProg[$i-1]; 
		}
		if ($_POST['career_dev_program_other']) {
			$career_dev_program_other = mysql_real_escape_string($_POST['career_dev_program_other']);
		} else {
			$career_dev_program_other = null;
		}

		$post_grad_plan = mysql_real_escape_string($_POST['post_grad_plan']);
		if ($_POST['post_grad_plan_desc']) {
			$post_grad_plan_desc = mysql_real_escape_string($_POST['post_grad_plan_desc']);
		} else {
			$post_grad_plan_desc = null;
		}

		$personal_hobby = mysql_real_escape_string($_POST['personal_hobby']);

		$userQuery = sprintf("UPDATE USER SET last_name='%s', first_name='%s', phone_num='%s', email='%s', pref_communication='%s'
							  WHERE username='%s'", $lname, $fname, $phone, $email, $pref_communication, $user);
		$uResult = getDBRegInserted($userQuery);


		$mentorQuery = sprintf("UPDATE Mentor SET gender='%s', depth_focus='%s', depth_focus_other='%s',
			live_before_tech='%s', live_on_campus='%u', first_gen_college_student='%u', transfer_from_outside='%u', institution_name='%s',
			transfer_from_within='%u', prev_major='%s', international_student='%u', home_country='%s', expec_graduation='%s', other_major='%s', 
			undergrad_research='%u', undergrad_research_desc='%s', post_grad_plan='%s', post_grad_plan_desc='%s', personal_hobby='%s'
			WHERE username='%s'", 
			$gender, $depth_focus, $depth_focus_other,
			$live_before_tech, $live_on_campus, $first_gen_college_student, $transfer_from_outside, $institution_name, 
			$transfer_from_within, $prev_major, $international_student, $home_country, $expec_graduation, $other_major,
			$undergrad_research, $undergrad_research_desc, $post_grad_plan, $post_grad_plan_desc, $personal_hobby, $user);
		$mResult = getDBRegInserted($mentorQuery);

		$bTrack = $_POST['breadth_track'];
		//delete first
		$bTrackDelQuery = sprintf("DELETE FROM Mentor_Breadth_Track WHERE username='%s'",$user);
		$bTrackDelResult = getDBRegInserted($bTrackDelQuery);
		foreach ($bTrack as $key => $value) {
			$breadth_track = $value['name'];
			$breadth_track_desc = $value['desc'];
			$bTrackQuery = sprintf("INSERT INTO Mentor_Breadth_Track(username, breadth_track, breadth_track_desc) VALUES ('%s', '%s', '%s')",
			$user, $breadth_track, $breadth_track_desc);
			$bTrackResult = getDBRegInserted($bTrackQuery);
		}



		if ($_POST['ethnicity']) {
			$ethCheckQuery = sprintf("SELECT count(*) FROM Ethnicity WHERE username='%s'",$user);
			$ethCheckResult = getDBResultRecord($ethCheckQuery);
			$ethQuery = sprintf("");
			if($ethCheckResult['count(*)'] >= 1) {
				$ethQuery = sprintf("UPDATE Ethnicity SET ethnicity1='%s', ethnicity2='%s', ethnicity3='%s', ethnicity4='%s', ethnicity5='%s' WHERE username='%s'", 
					$ethnicity1, $ethnicity2, $ethnicity3, $ethnicity4, $ethnicity5, $user);
				
			} else {
				$ethQuery = sprintf("INSERT INTO Ethnicity(username, ethnicity1, ethnicity2, ethnicity3, ethnicity4, ethnicity5) 
					VALUES ('%s', '%s', '%s', '%s', '%s', '%s')", $user, $ethnicity1, $ethnicity2, $ethnicity3, $ethnicity4, $ethnicity5);
			}
			$eResult = getDBRegInserted($ethQuery);	
			
		}


		if ($_POST['honor_program']) {
			$hpCheckQuery = sprintf("SELECT count(*) FROM Mentor_Honors_Program WHERE username='%s'",$user);
			$hpCheckResult = getDBResultRecord($hpCheckQuery);
			$honorProgQuery = sprintf("");
			if($hpCheckResult['count(*)'] >= 1) {
				$honorProgQuery = sprintf("UPDATE Mentor_Honors_Program SET program1='%s', program2='%s', program3='%s' WHERE username='%s'",
					$honor_program1, $honor_program2, $honor_program3, $user);	
			} else {
				$honorProgQuery = sprintf("INSERT INTO Mentor_Honors_Program(username, program1, program2, program3) 
					VALUES ('%s', '%s', '%s', '%s')", $user, $honor_program1, $honor_program2, $honor_program3);
			}
			$hpResult = getDBRegInserted($honorProgQuery);
		}

		if ($_POST['other_organization1']) {
			$otherOrgCheckQuery = sprintf("SELECT count(*) FROM Other_Organization WHERE username='%s'",$user);
			$otherOrgCheckResult = getDBResultRecord($otherOrgCheckQuery);
			$otherOrgQuery = sprintf("");
			if($otherOrgCheckResult['count(*)'] >= 1) {
				$otherOrgQuery = sprintf("UPDATE Other_Organization SET organization1='%s', organization2='%s', organization3='%s' WHERE username='%s'", 
					$other_organization1, $other_organization2, $other_organization3, $user);
			} else {
				$otherOrgQuery = sprintf("INSERT INTO Other_Organization(username, organization1, organization2, organization3) 
					VALUES ('%s', '%s', '%s', '%s')", $user, $other_organization1, $other_organization2, $other_organization3);
			}
			$otherOrgResult = getDBRegInserted($otherOrgQuery);
		}

		if ($_POST['bme_organization']) {
			$bmeOrgCheckQuery = sprintf("SELECT count(*) FROM Mentor_BME_Organization WHERE username='%s'",$user);
			$bmeOrgCheckResult = getDBResultRecord($bmeOrgCheckQuery);
			$bmeOrgQuery = sprintf("");
			if($bmeOrgCheckResult['count(*)'] >= 1) {
				$bmeOrgQuery = sprintf("UPDATE Mentor_BME_Organization SET bme_org1='%s', bme_org2='%s', bme_org3='%s',
					bme_org4='%s', bme_org5='%s', bme_org6='%s', bme_org7='%s', bme_org_other='%s'  WHERE username='%s'",
					$bme_org1, $bme_org2, $bme_org3, $bme_org4, $bme_org5, $bme_org6, $bme_org7, $bme_org_other, $user);
			} else {
				$bmeOrgQuery = sprintf("INSERT INTO Mentor_BME_Organization(username, bme_org1, bme_org2, bme_org3,
					bme_org4, bme_org5, bme_org6, bme_org7, bme_org_other) VALUES ('%s', '%s', '%s' , '%s', '%s', '%s', '%s', '%s', '%s')",
					$user, $bme_org1, $bme_org2, $bme_org3, $bme_org4, $bme_org5, $bme_org6, $bme_org7, $bme_org_other);
			}			
			$bmeOrgResult = getDBRegInserted($bmeOrgQuery);
		}

		if ($_POST['mm_org']) {
			$mmOrgCheckQuery = sprintf("SELECT count(*) FROM Mentee_Mentor_Organization WHERE username='%s'",$user);
			$mmOrgCheckResult = getDBResultRecord($mmOrgCheckQuery);
			$mmOrgQuery = sprintf("");
			if($mmOrgCheckResult['count(*)'] >= 1) {
				$mmOrgQuery = sprintf("UPDATE Mentee_Mentor_Organization SET mm_org1='%s', mm_org2='%s', mm_org3='%s', mm_org4='%s', mm_org5='%s', mm_org_other='%s' WHERE username='%s'",
				 	$mm_org1, $mm_org2, $mm_org3, $mm_org4, $mm_org5, $mm_org_other, $user);
			} else {
				$mmOrgQuery = sprintf("INSERT INTO Mentee_Mentor_Organization(username, mm_org1, mm_org2, mm_org3, mm_org4, mm_org5, mm_org_other) 
					VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')", $user, $mm_org1, $mm_org2, $mm_org3, $mm_org4, $mm_org5, $mm_org_other);
			}
			$mmResult = getDBRegInserted($mmOrgQuery);
		}

		if ($_POST['tutor_teacher_program']) {
			$ttProgCheckQuery = sprintf("SELECT count(*) FROM Mentor_Tutor_Teacher_Program WHERE username='%s'",$user);
			$ttProgCheckResult = getDBResultRecord($ttProgCheckQuery);
			$ttProgQuery = sprintf("");
			if($ttProgCheckResult['count(*)'] >= 1) {
				$ttProgQuery = sprintf("UPDATE Mentor_Tutor_Teacher_Program SET tutor_teacher_program1='%s', tutor_teacher_program2='%s', 
					tutor_teacher_program3='%s', tutor_teacher_program4='%s', tutor_teacher_program5='%s', tutor_teacher_program6='%s', tutor_teacher_program_other='%s' WHERE username='%s'", 
					$tutor_teacher_program1, $tutor_teacher_program2, $tutor_teacher_program3, $tutor_teacher_program4, $tutor_teacher_program5, $tutor_teacher_program6, $tutor_teacher_program_other, $user);
			} else {
				$ttProgQuery = sprintf("INSERT INTO Mentor_Tutor_Teacher_Program(username, tutor_teacher_program1, tutor_teacher_program2, 
						tutor_teacher_program3, tutor_teacher_program4, tutor_teacher_program5, tutor_teacher_program6, tutor_teacher_program_other)
					 VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $user, $tutor_teacher_program1, $tutor_teacher_program2, 
					 $tutor_teacher_program3, $tutor_teacher_program4, $tutor_teacher_program5, $tutor_teacher_program6, $tutor_teacher_program_other);
			}	
			$ttProgResult = getDBRegInserted($ttProgQuery);
		}

		if ($_POST['bme_academ_exp']) {
			$bmeCheckQuery = sprintf("SELECT count(*) FROM Mentor_BME_Academic_Experience WHERE username='%s'",$user);
			$bmeCheckResult = getDBResultRecord($bmeCheckQuery);
			$bmeQuery = sprintf("");
			if($bmeCheckResult['count(*)'] >= 1) {
				$bmeQuery = sprintf("UPDATE Mentor_BME_Academic_Experience SET bme_academ_exp1='%s', bme_academ_exp2='%s',
					bme_academ_exp3='%s', bme_academ_exp4='%s', bme_academ_exp_other='%s' WHERE username='%s'",
					$bme_academ_exp1, $bme_academ_exp2, $bme_academ_exp3, $bme_academ_exp4, $bme_academ_exp_other, $user);
			} else {
				$bmeQuery = sprintf("INSERT INTO Mentor_BME_Academic_Experience(username, bme_academ_exp1, bme_academ_exp2,
					bme_academ_exp3, bme_academ_exp4, bme_academ_exp_other) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
					$user, $bme_academ_exp1, $bme_academ_exp2, $bme_academ_exp3, $bme_academ_exp4, $bme_academ_exp_other);
			}
			$bmeResult = getDBRegInserted($bmeQuery);
		}

		if ($_POST['international_experience']) {
			$interCheckQuery = sprintf("SELECT count(*) FROM Mentor_International_Experience WHERE username='%s'",$user);
			$interCheckResult = getDBResultRecord($interCheckQuery);
			$interQuery = sprintf("");
			if($interCheckResult['count(*)'] >= 1) {
				$interQuery = sprintf("UPDATE Mentor_International_Experience SET international_experience1='%s', international_experience2='%s', 
					international_experience3='%s', international_experience4='%s', international_experience5='%s', international_experience_other='%s' WHERE username='%s'", 
					$international_experience1, $international_experience2,
					$international_experience3, $international_experience4, $international_experience5, $international_experience_other, $user);
			} else {
				$interQuery = sprintf("INSERT INTO Mentor_International_Experience(username, international_experience1, international_experience2, 
						international_experience3, international_experience4, international_experience5, international_experience_other)
					VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')", $user, $international_experience1, $international_experience2,
					$international_experience3, $international_experience4, $international_experience5, $international_experience_other);
			}
			$interResults = getDBRegInserted($interQuery);
		}

		if ($_POST['career_dev_program']) {
			$careerCheckQuery = sprintf("SELECT count(*) FROM Mentor_Career_Dev_Program WHERE username='%s'",$user);
			$careerCheckResult = getDBResultRecord($careerCheckQuery);
			$careerQuery = sprintf("");
			if($careerCheckResult['count(*)'] >= 1) {
				$careerQuery = sprintf("UPDATE Mentor_Career_Dev_Program SET career_dev_program1='%s',
					career_dev_program2='%s', career_dev_program3='%s', career_dev_program_other='%s' WHERE username='%s'",
					 $career_dev_program1, $career_dev_program2, $career_dev_program3, $career_dev_program_other, $user);
			} else {
				$careerQuery = sprintf("INSERT INTO Mentor_Career_Dev_Program(username, career_dev_program1,
					career_dev_program2, career_dev_program3, career_dev_program_other) VALUES ('%s', '%s', '%s','%s', '%s')",
					$user, $career_dev_program1, $career_dev_program2, $career_dev_program3, $career_dev_program_other);
			}
			$careerResults = getDBRegInserted($careerQuery);
		}


		

		// //header("Content-type: application/json");
		// // print_r($json);
		// echo json_encode($uresult+$mresult);
		
	}

//begin edit mentee profile
	function updateMenteeProfile() {
		
		echo "Update Mentee Profile in PHP \n";
		global $_USER;	
		$user = $_USER['uid'];
		echo $user;
		$fname = mysql_real_escape_string($_POST['fname']);//$data->fname);
		echo $fname;
		$lname = mysql_real_escape_string($_POST['lname']);
		$phone = mysql_real_escape_string($_POST['phone']);
		$email = mysql_real_escape_string($_POST['email']);
		$pref_communication = mysql_real_escape_string($_POST['pref_communication']);
		$gender = mysql_real_escape_string($_POST['gender']);
		$depth_focus = mysql_real_escape_string($_POST['dfocus']);
		$depth_focus_other = mysql_real_escape_string($_POST['dfocusother']); 
		$live_before_tech = mysql_real_escape_string($_POST['live_before_tech']);
		$live_on_campus = $_POST['live_on_campus']; //is number 0 or 1 posting?
		$first_gen_college_student = $_POST['first_gen_college_student'];
		$transfer_from_outside = $_POST['transfer_from_outside'];
		$institution_name = mysql_real_escape_string($_POST['institution_name']);
		$transfer_from_within = $_POST['transfer_from_within'];
		$prev_major = mysql_real_escape_string($_POST['prev_major']);
		$international_student = $_POST['international_student'];
		$home_country =  mysql_real_escape_string($_POST['home_country']);
		$expec_graduation = mysql_real_escape_string($_POST['expec_graduation']);
		$other_major =  mysql_real_escape_string($_POST['other_major']);
	

	

		$undergrad_research = $_POST['undergrad_research'];
		if ($_POST['undergrad_research']) {
			$undergrad_research = 1;
			$undergrad_research_desc = $_POST['undergrad_research_desc'];
		} else {
			$undergrad_research = 0;
			$undergrad_research_desc = null;
		}


		$bme_org1 = null;
		$bme_org2 = null;
		$bme_org3 = null;
		$bme_org4 = null;
		$bme_org5 = null;
		$bme_org6 = null;
		$bme_org7 = null;
		$bmeOrgs = $_POST['bme_organization'];
		for ($i=1; $i <= count($bmeOrgs); $i++) {
			${"bme_org" . $i}  = $bmeOrgs[$i-1]; //Json of all the organizations $_POST['bme_organization']
		}
		if ($_POST['bme_org_other']) {
			$bme_org_other = mysql_real_escape_string($_POST['bme_org_other']);
		} else {
			$bme_org_other = null;
		}


		$tutor_teacher_program1 = null;
		$tutor_teacher_program2 = null;
		$tutor_teacher_program3 = null;
		$tutor_teacher_program4 = null;
		$tutor_teacher_program5 = null;
		$tutor_teacher_program6 = null;
		$ttProg = $_POST['tutor_teacher_program'];
		for ($i=1; $i <= count($ttProg); $i++) {
			${"tutor_teacher_program" . $i}  = $ttProg[$i-1]; 
		}
		if ($_POST['tutor_teacher_program_other']) {
			$tutor_teacher_program_other = mysql_real_escape_string($_POST['tutor_teacher_program_other']);
		} else {
			$tutor_teacher_program_other = null;
		}

		$bme_academ_exp1 = null;
		$bme_academ_exp2 = null;
		$bme_academ_exp3 = null;
		$bme_academ_exp4 = null;
		$bmeExp = $_POST['bme_academ_exp'];
		for ($i=1; $i <= count($bmeExp); $i++) {
			${"bme_academ_exp" . $i}  = $bmeExp[$i-1]; 
		}
		if ($_POST['bme_academ_exp_other']) {
			$bme_academ_exp_other = mysql_real_escape_string($_POST['bme_academ_exp_other']);
		} else {
			$bme_academ_exp_other = null;
		}

		$international_experience1 = null;
		$international_experience2 = null;
		$international_experience3 = null;
		$international_experience4 = null;
		$international_experience5 = null;
		$internatExp = $_POST['international_experience'];
		for ($i=1; $i <= count($internatExp); $i++) {
			${"international_experience" . $i}  = $internatExp[$i-1]; 
		}
		if ($_POST['international_experience_other']) {
			$international_experience_other = mysql_real_escape_string($_POST['international_experience_other']);
		} else {
			$international_experience_other = null;
		}

		$career_dev_program1 = null;
		$career_dev_program2 = null;
		$career_dev_program3 = null;
		$carDevProg = $_POST['career_dev_program']; 
		for ($i=1; $i <= count($carDevProg); $i++) {
			${"career_dev_program" . $i}  = $carDevProg[$i-1]; 
		}
		if ($_POST['career_dev_program_other']) {
			$career_dev_program_other = mysql_real_escape_string($_POST['career_dev_program_other']);
		} else {
			$career_dev_program_other = null;
		}

		$post_grad_plan = mysql_real_escape_string($_POST['post_grad_plan']);
		if ($_POST['post_grad_plan_desc']) {
			$post_grad_plan_desc = mysql_real_escape_string($_POST['post_grad_plan_desc']);
		} else {
			$post_grad_plan_desc = null;
		}

		$personal_hobby = mysql_real_escape_string($_POST['personal_hobby']);

		$userQuery = sprintf("UPDATE USER SET last_name='%s', first_name='%s', phone_num='%s', email='%s', pref_communication='%s'
							  WHERE username='%s'", $lname, $fname, $phone, $email, $pref_communication, $user);
		$uResult = getDBRegInserted($userQuery);


		$menteeQuery = sprintf("UPDATE Mentee SET  depth_focus='%s', depth_focus_other='%s',
			first_gen_college_student='%u', transfer_from_outside='%u', institution_name='%s',
			transfer_from_within='%u', prev_major='%s', international_student='%u', expec_graduation='%s', other_major='%s', 
			undergrad_research='%u', undergrad_research_desc='%s', post_grad_plan='%s', post_grad_plan_desc='%s', personal_hobby='%s'
			WHERE username='%s'", 
			$depth_focus, $depth_focus_other,
			$first_gen_college_student, $transfer_from_outside, $institution_name, 
			$transfer_from_within, $prev_major, $international_student, $expec_graduation, $other_major,
			$undergrad_research, $undergrad_research_desc, $post_grad_plan, $post_grad_plan_desc, $personal_hobby, $user);
		$mResult = getDBRegInserted($menteeQuery);

		$bTrack = $_POST['breadth_track'];
		//delete first
		$bTrackDelQuery = sprintf("DELETE FROM Mentee_Breadth_Track WHERE username='%s'",$user);
		$bTrackDelResult = getDBRegInserted($bTrackDelQuery);
		foreach ($bTrack as $key => $value) {
			$breadth_track = $value['name'];
			$breadth_track_desc = $value['desc'];
			$bTrackQuery = sprintf("INSERT INTO Mentee_Breadth_Track(username, breadth_track, breadth_track_desc) VALUES ('%s', '%s', '%s')",
			$user, $breadth_track, $breadth_track_desc);
			$bTrackResult = getDBRegInserted($bTrackQuery);
		}

		if ($_POST['bme_organization']) {
			$bmeOrgCheckQuery = sprintf("SELECT count(*) FROM Mentee_BME_Organization WHERE username='%s'",$user);
			$bmeOrgCheckResult = getDBResultRecord($bmeOrgCheckQuery);
			$bmeOrgQuery = sprintf("");
			if($bmeOrgCheckResult['count(*)'] >= 1) {
				$bmeOrgQuery = sprintf("UPDATE Mentee_BME_Organization SET bme_org1='%s', bme_org2='%s', bme_org3='%s',
					bme_org4='%s', bme_org5='%s', bme_org6='%s', bme_org7='%s', bme_org_other='%s'  WHERE username='%s'",
					$bme_org1, $bme_org2, $bme_org3, $bme_org4, $bme_org5, $bme_org6, $bme_org7, $bme_org_other, $user);
			} else {
				$bmeOrgQuery = sprintf("INSERT INTO Mentee_BME_Organization(username, bme_org1, bme_org2, bme_org3,
					bme_org4, bme_org5, bme_org6, bme_org7, bme_org_other) VALUES ('%s', '%s', '%s' , '%s', '%s', '%s', '%s', '%s', '%s')",
					$user, $bme_org1, $bme_org2, $bme_org3, $bme_org4, $bme_org5, $bme_org6, $bme_org7, $bme_org_other);
			}			
			$bmeOrgResult = getDBRegInserted($bmeOrgQuery);
		}


		if ($_POST['tutor_teacher_program']) {
			$ttProgCheckQuery = sprintf("SELECT count(*) FROM Mentee_Tutor_Teacher_Program WHERE username='%s'",$user);
			$ttProgCheckResult = getDBResultRecord($ttProgCheckQuery);
			$ttProgQuery = sprintf("");
			if($ttProgCheckResult['count(*)'] >= 1) {
				$ttProgQuery = sprintf("UPDATE Mentee_Tutor_Teacher_Program SET tutor_teacher_program1='%s', tutor_teacher_program2='%s', 
					tutor_teacher_program3='%s', tutor_teacher_program4='%s', tutor_teacher_program5='%s', tutor_teacher_program6='%s', tutor_teacher_program_other='%s' WHERE username='%s'", 
					$tutor_teacher_program1, $tutor_teacher_program2, $tutor_teacher_program3, $tutor_teacher_program4, $tutor_teacher_program5, $tutor_teacher_program6, $tutor_teacher_program_other, $user);
			} else {
				$ttProgQuery = sprintf("INSERT INTO Mentee_Tutor_Teacher_Program(username, tutor_teacher_program1, tutor_teacher_program2, 
						tutor_teacher_program3, tutor_teacher_program4, tutor_teacher_program5, tutor_teacher_program6, tutor_teacher_program_other)
					 VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $user, $tutor_teacher_program1, $tutor_teacher_program2, 
					 $tutor_teacher_program3, $tutor_teacher_program4, $tutor_teacher_program5, $tutor_teacher_program6, $tutor_teacher_program_other);
			}	
			$ttProgResult = getDBRegInserted($ttProgQuery);
		}

		if ($_POST['bme_academ_exp']) {
			$bmeCheckQuery = sprintf("SELECT count(*) FROM Mentee_BME_Academic_Experience WHERE username='%s'",$user);
			$bmeCheckResult = getDBResultRecord($bmeCheckQuery);
			$bmeQuery = sprintf("");
			if($bmeCheckResult['count(*)'] >= 1) {
				$bmeQuery = sprintf("UPDATE Mentee_BME_Academic_Experience SET bme_academ_exp1='%s', bme_academ_exp2='%s',
					bme_academ_exp3='%s', bme_academ_exp4='%s', bme_academ_exp_other='%s' WHERE username='%s'",
					$bme_academ_exp1, $bme_academ_exp2, $bme_academ_exp3, $bme_academ_exp4, $bme_academ_exp_other, $user);
			} else {
				$bmeQuery = sprintf("INSERT INTO Mentee_BME_Academic_Experience(username, bme_academ_exp1, bme_academ_exp2,
					bme_academ_exp3, bme_academ_exp4, bme_academ_exp_other) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
					$user, $bme_academ_exp1, $bme_academ_exp2, $bme_academ_exp3, $bme_academ_exp4, $bme_academ_exp_other);
			}
			$bmeResult = getDBRegInserted($bmeQuery);
		}

		if ($_POST['international_experience']) {
			$interCheckQuery = sprintf("SELECT count(*) FROM Mentee_International_Experience WHERE username='%s'",$user);
			$interCheckResult = getDBResultRecord($interCheckQuery);
			$interQuery = sprintf("");
			if($interCheckResult['count(*)'] >= 1) {
				$interQuery = sprintf("UPDATE Mentee_International_Experience SET international_experience1='%s', international_experience2='%s', 
					international_experience3='%s', international_experience4='%s', international_experience5='%s', international_experience_other='%s' WHERE username='%s'", 
					$international_experience1, $international_experience2,
					$international_experience3, $international_experience4, $international_experience5, $international_experience_other, $user);
			} else {
				$interQuery = sprintf("INSERT INTO Mentee_International_Experience(username, international_experience1, international_experience2, 
						international_experience3, international_experience4, international_experience5, international_experience_other)
					VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')", $user, $international_experience1, $international_experience2,
					$international_experience3, $international_experience4, $international_experience5, $international_experience_other);
			}
			$interResults = getDBRegInserted($interQuery);
		}

		if ($_POST['career_dev_program']) {
			$careerCheckQuery = sprintf("SELECT count(*) FROM Mentee_Career_Dev_Program WHERE username='%s'",$user);
			$careerCheckResult = getDBResultRecord($careerCheckQuery);
			$careerQuery = sprintf("");
			if($careerCheckResult['count(*)'] >= 1) {
				$careerQuery = sprintf("UPDATE Mentee_Career_Dev_Program SET career_dev_program1='%s',
					career_dev_program2='%s', career_dev_program3='%s', career_dev_program_other='%s' WHERE username='%s'",
					 $career_dev_program1, $career_dev_program2, $career_dev_program3, $career_dev_program_other, $user);
			} else {
				$careerQuery = sprintf("INSERT INTO Mentee_Career_Dev_Program(username, career_dev_program1,
					career_dev_program2, career_dev_program3, career_dev_program_other) VALUES ('%s', '%s', '%s','%s', '%s')",
					$user, $career_dev_program1, $career_dev_program2, $career_dev_program3, $career_dev_program_other);
			}
			$careerResults = getDBRegInserted($careerQuery);
		}


		

		// //header("Content-type: application/json");
		// // print_r($json);
		// echo json_encode($uresult+$mresult);
		
	}

	function submitTask() {
		global $_USER;	
		$user = $_USER['uid'];
		echo $user;		
		$task_type = mysql_real_escape_string($_POST['task_type']);
		$task_point = mysql_real_escape_string($_POST['task_point']);
		$task_date = mysql_real_escape_string($_POST['task_date']);
		$task_desc = mysql_real_escape_string($_POST['task_desc']);

		$taskQuery = sprintf("INSERT INTO Task ( task_type, task_point, task_description, owned_by, finish_date)
							  VALUES ( '%s', '%u', '%s', '%s', '%s')",
							  $task_type,$task_point,$task_desc,$user,$task_date);
		$huQuery = sprintf("UPDATE House SET total_point = total_point + '%u' WHERE house_name = (SELECT house_belongs FROM USER 
							WHERE username = '%s')",$task_point,$user);
		$tResult = getDBRegInserted($taskQuery);
		$huResult = getDBRegInserted($huQuery);
		echo json_encode($tResult);
	}

	function getTaskHistory() {
		global $_USER;	
		$user = $_USER['uid'];
		$thQuery = sprintf("SELECT * FROM Task WHERE owned_by='%s'",$user);
		$thResult = getDBResultsArray($thQuery);
		echo json_encode($thResult);
	}


	function getHouses() {
		$housesQuery = sprintf("SELECT * FROM House");
		$houseResult = getDBResultsArray($housesQuery);
		echo json_encode($houseResult);
	}

	function getHouseMembers() {
		global $_USER;
		$user = $_USER['uid'];
		$hmQuery = sprintf("SELECT u.username, u.family_belongs, u.house_belongs,u.first_name,u.last_name,u.email,
							case when Mentee.username is not null
            					then 'Mentee'
            					else NULL
       						end as is_mentee,
							case when Mentor.username is not null
            					then 'Mentor'
            					else NULL 
       						end as is_mentor,
       						case when GROUP_CONCAT(Mentor_Breadth_Track.breadth_track) is not null 
       							then GROUP_CONCAT(Mentor_Breadth_Track.breadth_track)
       							else NULL
       						end as `mentor_breadth_tracks`,
                            case when GROUP_CONCAT(Mentee_Breadth_Track.breadth_track) is not null
                                then GROUP_CONCAT(Mentee_Breadth_Track.breadth_track)
								else NULL
                            end as 'mentee_breadth_tracks'
							FROM USER u 
							left join Mentee on u.username = Mentee.username 
							left join Mentor on u.username = Mentor.username
							left join Mentor_Breadth_Track on u.username = Mentor_Breadth_Track.username
							left join Mentee_Breadth_Track on u.username = Mentee_Breadth_Track.username
							WHERE u.house_belongs = (SELECT house_belongs FROM USER WHERE username='%s')
							group by u.username",$user);
		$hmResult = getDBResultsArray($hmQuery);
		echo json_encode($hmResult);
	}

	function getFamilyMembers() {
		global $_USER;
		$user = $_USER['uid'];
		$fmQuery = sprintf("SELECT u.house_belongs, u.family_belongs, u.first_name,u.last_name,u.email,
							case when Mentee.username is not null
            					then 'Mentee'
            					else NULL
       						end as is_mentee,
							case when Mentor.username is not null
            					then 'Mentor'
            					else NULL 
       						end as is_mentor,
       						case when GROUP_CONCAT(Mentor_Breadth_Track.breadth_track) is not null 
       							then GROUP_CONCAT(Mentor_Breadth_Track.breadth_track)
       							else NULL
       						end as `mentor_breadth_tracks`,
                            case when GROUP_CONCAT(Mentee_Breadth_Track.breadth_track) is not null
                                then GROUP_CONCAT(Mentee_Breadth_Track.breadth_track)
								else NULL
                            end as 'mentee_breadth_tracks'
							FROM USER u 
							left join Mentee on u.username = Mentee.username 
							left join Mentor on u.username = Mentor.username
							left join Mentor_Breadth_Track on u.username = Mentor_Breadth_Track.username
							left join Mentee_Breadth_Track on u.username = Mentee_Breadth_Track.username
							WHERE u.house_belongs = (SELECT house_belongs FROM USER WHERE username='%s')
							AND   u.family_belongs = (SELECT family_belongs FROM USER WHERE username='%s')
							group by u.username",$user,$user);
		$fmResult = getDBResultsArray($fmQuery);
		echo json_encode($fmResult);
	}

	function listAliasNames($alias) {
		$countHasName = sprintf("SELECT username FROM Mentor WHERE Mentor.alias = '%s'", $alias);
		$nameResult = getDBResultRecord($countHasName);
		if ($nameResult) {
			echo "name already exists";
		} else {
			echo "name does not already exist, ok to show this alias";
		}
		header("Content-type: application/json");
		echo json_encode($nameResult);
	}

	function inputAliasName($aliasName) {
		global $_USER;
		$user = $_USER['uid'];

		$query = sprintf("UPDATE Mentor SET alias = '%s' WHERE username = '%s'", $aliasName, $user);
		$queryRestult = getDBResultInserted($query);
		header("Content-type: application/json");
		echo json_encode($queryResult);
	}

	function genFauxUsers($form) {
		global $_USER;

		//$form = json_decode($form);
		$count = 0;
		foreach($form as $currentUser) {
			$count++;
			$dbQuery = sprintf("INSERT INTO USER (username, last_name, first_name, phone_num, email, pref_communication)
													VALUES ('%s', '%s', '%s', '%u', '%s', '%s')",
													$currentUser['uid'], $currentUser['firstName'], $currentUser['lastName'], 
													$currentUser['phoneNumber'], $currentUser['email'], $currentUser['commMethod']);
			$result = getDBRegInserted($dbQuery);
		}

		// $dbQuery = sprintf("first_name = $form['firstName'],
		//  last_name=$form['lastName'], phone_num=$form['phoneNumber'], email=$form['email'],
		//   pref_communication=$form['commMethod']");

		header("Content-type: application/json");
		//echo $form;
		echo json_encode($count);
	}//end genFauxUsers

	function deleteMentors() {
		global $_USER;

		$dbQueryMentor = sprintf("DELETE FROM USER WHERE username IN (SELECT username FROM Mentor)");

		$result = deleteDBEntries($dbQueryMentor);
		//header("Content-type: application/json");
		echo "deleted";
		//echo json_encode();
	}

	function genFauxMentors() {

		//$form = json_decode($form);
		$count = 0;
		foreach($_POST['mentors'] as $cu) {
			//echo var_dump($cu);
			addMentorLoop($cu);
		}
		//echo var_dump($_POST);
		header("Content-type: application/json");
		//echo json_encode($_POST);
	}

	// function genFauxMentors() {
	// 	global $_USER;

	// 	//$form = json_decode($form);
	// 	$count = 0;
	// 	foreach($_POST['mentors'] as $cu) {
	// 		$count++;
	// 		$dbQuery = sprintf("INSERT INTO USER (username, last_name, first_name, phone_num, email, pref_communication)
	// 												VALUES ('%s', '%s', '%s', '%u', '%s', '%s')",
	// 												$cu['username'], $cu['first_name'], $cu['last_name'], 
	// 												$cu['phone_number'], $cu['email'], $cu['pref_communication']);
	// 		$result = getDBRegInserted($dbQuery);

	// 		$dbQuery = sprintf("INSERT INTO Mentor (username, alias, opt_in, depth_focus, post_grad_plan, expec_graduation,
	// 																					 	transfer_from_within, transfer_from_outside, international_student,
	// 																					 	first_gen_college_student, live_before_tech, live_on_campus,
	// 																					 	undergrad_research, home_country, gender)
	// 												VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
	// 												$cu['username'], $cu['alias'], $cu['opt_in'], $cu['depth_focus'], $cu['post_grad_plan'], 
	// 												$cu['expec_graduation'], $cu['transfer_from_within,'], $cu['transfer_from_outside'],
	// 												$cu['international_student'], $cu['first_gen_college_student'], $cu['live_before_tech'], 
	// 												$cu['live_on_campus'], $cu['undergrad_research'], $cu['home_country'], $cu['gender']);
	// 		$result = getDBRegInserted($dbQuery);
	// 	}
	// 	//echo var_dump($_POST);
	// 	header("Content-type: application/json");
	// 	echo json_encode($_POST);
	// }

	//**********************************
	// RequestPeriod Code
	//**********************************
	/**
	 * Function that determines whether or not the given request period is currently open
	 */
	function getRequestPeriodStatus($requestPeriod){
		$dbQuery = sprintf("SELECT isOpen FROM RequestPeriods WHERE RequestPeriod = '%s'",
			mysql_real_escape_string($requestPeriod));
		$result=getDBResultsArray($dbQuery)[0];
		header("Content-type: application/json");
		echo json_encode($result);
	}
	
	/**
	 * Function that determines whether or not the default request period is currently open
	 */
	function getDefaultPeriodStatus(){
		$defaultPeriod = "DefaultRequestPeriod";
		getRequestPeriodStatus($defaultPeriod);
	}
	
	/**
	 * Opens a given request period
	 */
	function openRequestPeriod($requestPeriod){
		$dbQuery = sprintf("UPDATE RequestPeriods SET isOpen = 1 WHERE RequestPeriod = '%s'",
			mysql_real_escape_string($requestPeriod));
		$result=getDBRegInserted($dbQuery);
		header("Content-type: application/json");
		echo json_encode($result);
	}
	
	/**
	 * Closes a given request period
	 */
	function closeRequestPeriod($requestPeriod){
		$dbQuery = sprintf("UPDATE RequestPeriods SET isOpen = 0 WHERE RequestPeriod = '%s'",
			mysql_real_escape_string($requestPeriod));
		$result=getDBRegInserted($dbQuery);
		header("Content-type: application/json");
		echo json_encode($result);
	}

	function putDefaultPeriodStatus($newStatus) {
		if ($newStatus == 0) {
			closeDefaultRequestPeriod();
		} else {
			openDefaultRequestPeriod();
		}
	}
	
	/**
	 * Opens the default request period
	 */
	function openDefaultRequestPeriod(){
		$defaultPeriod = "DefaultRequestPeriod";
		openRequestPeriod($defaultPeriod);
	}
	
	/**
	 * Closes the default request period
	 */
	function closeDefaultRequestPeriod(){
		$defaultPeriod = "DefaultRequestPeriod";
		closeRequestPeriod($defaultPeriod);
	}

	function getWishlistContents() {
		global $_USER;
		$dbQueryWishlist = sprintf("SELECT * FROM USER
		LEFT JOIN Mentor_Breadth_Track ON Mentor_Breadth_Track.username = USER.username
		LEFT JOIN Mentor_BME_Organization ON Mentor_BME_Organization.username = USER.username
		LEFT JOIN Mentor_Tutor_Teacher_Program ON Mentor_Tutor_Teacher_Program.username = USER.username
		LEFT JOIN Mentor_BME_Academic_Experience ON Mentor_BME_Academic_Experience.username = USER.username
		LEFT JOIN Mentor_International_Experience ON Mentor_International_Experience.username = USER.username
		LEFT JOIN Mentor_Career_Dev_Program ON Mentor_Career_Dev_Program.username = USER.username
		LEFT JOIN Wishlist ON Wishlist.mentor = USER.username
		LEFT JOIN Mentor ON  USER.username = Mentor.username
		WHERE Wishlist.mentee = '%s' AND (SELECT COUNT(*) FROM Matches WHERE Wishlist.mentor = mentor_user) < (SELECT settingValue FROM GlobalSettings where settingName = 'MaxMenteesPerMentor')", $_USER['uid']);
		$result=getDBResultsArray($dbQueryWishlist);
		header("Content-type: application/json");
		echo json_encode($result);
	}

	/**
	 * Adds a mentor to the currently logged in user's wishlist.
	 */
	function addWishlistMentor($username) {
		global $_USER;
		$dbQueryWishlist = sprintf("INSERT INTO Wishlist (mentee, mentor) VALUES ('%s', '%s')", $_USER['uid'], $username);
		$result = getDBRegInserted($dbQueryWishlist);
		echo "added";
	}

	/**
	 * Removes a mentor from the currently logged in user's wishlist.
	 */
	function removeWishlistMentor($username) {
		global $_USER;
		$dbQueryWishlist = sprintf("DELETE FROM Wishlist WHERE mentee='%s' AND mentor='%s'", $_USER['uid'], $username);
		$result = deleteDBEntries($dbQueryWishlist);
		print($result);
	}

	function assignAllMentees() {
		$query = "SELECT settingValue FROM GlobalSettings WHERE settingName='MaxMenteesPerMentor'";
		$result = getDBResultRecord($query);
		$maxCount = $result["settingValue"];
		//Get all unmatched mentees
		$menteeQuery = "SELECT Mentee.username FROM Mentee WHERE Mentee.username NOT IN (SELECT mentee_user FROM Matches)";
		$mentees = getDBResultsArray($menteeQuery);
		//Get all mentors with open spots
		$mentorQuery = sprintf("SELECT Mentor.username AS username, COUNT(*) AS count
						 FROM Mentor JOIN Matches ON Mentor.username = Matches.mentor_user
						 GROUP BY Mentor.username HAVING COUNT(*) < %s ", $maxCount);
		$mentorQuery .= " UNION ALL SELECT Mentor.username AS username, 0 AS count
						 FROM Mentor WHERE Mentor.username NOT IN (SELECT mentor_user FROM Matches)
						 AND Mentor.approved = 1";
		$mentors = getDBResultsArray($mentorQuery);
		$mentorIndex = 0;
		$currentCount = $mentors[0]['count'];
		
		foreach ($mentees as $mentee) {
			$insertQuery = sprintf("INSERT INTO Matches (mentor_user, mentee_user) VALUES ('%s', '%s')", $mentors[$mentorIndex]['username'], $mentee['username']);
			$result = getDBRegInserted($insertQuery);
			$currentCount++;
			if ($currentCount == $maxCount) {
				$mentorIndex++;
				$currentCount = $mentors[$mentorIndex]["count"];
			}
		}
	}

	function getMatches() {
		$dbQuery = "SELECT Mentors.username AS mentor_username,
							Mentors.first_name AS mentor_first_name,
							Mentors.last_name AS mentor_last_name,
							Mentees.username AS mentee_username,
							Mentees.first_name AS mentee_first_name,
							Mentees.last_name AS mentee_last_name
					FROM Matches
					JOIN USER AS Mentees ON Matches.mentee_user = Mentees.username
					JOIN USER AS Mentors ON Matches.mentor_user = Mentors.username";
		$result = getDBResultsArray($dbQuery);
		echo json_encode($result);
	}

	function getUnmatchedMentors() {
		$dbQuery = "SELECT Mentor.username,
							USER.first_name,
							USER.last_name
					FROM Mentor JOIN USER
					ON Mentor.username = USER.username
					LEFT JOIN Matches
					ON Mentor.username = Matches.mentor_user
					WHERE Matches.mentor_user IS NULL
					AND Mentor.approved = 1";
		$result = getDBResultsArray($dbQuery);
		echo json_encode($result);
	}

	function getUnmatchedMentees() {
		$dbQuery = "SELECT Mentee.username,
							USER.first_name,
							USER.last_name
					FROM Mentee JOIN USER
					ON Mentee.username = USER.username
					LEFT JOIN Matches
					ON Mentee.username = Matches.mentee_user
					WHERE Matches.mentee_user IS NULL";
		$result = getDBResultsArray($dbQuery);
		echo json_encode($result);
	}
	
	function mentorHasSpace($username){
		$countHasName = sprintf("SELECT TRUE FROM Mentor WHERE Mentor.username = '%s'
			AND (SELECT COUNT(*) FROM Matches WHERE username = mentor_user) < (SELECT settingValue 				FROM GlobalSettings where settingName = 'MaxMenteesPerMentor')", $username);
		$result = mysql_num_rows(mysql_query($countHasName));
		header("Content-type: application/json");
		echo json_encode($result == 1);
	}
?>