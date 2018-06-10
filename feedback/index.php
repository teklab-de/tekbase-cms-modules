<?php

if (!defined('MODULE_FILE')) {
	die ("You can't access this file directly...");
}

$index = 1;
require_once("cmsmain.php");
$module_name = basename(dirname(__FILE__));

function feedback($save, $fbname, $fbemail, $fbsubject, $fbmessage, $dataprivacy, $gfxcheck, $randomnum) {
    global $prefix, $db, $member, $admin, $name, $op, $gfx_chk, $language, $sitetheme, $feedbackpath, $recaptcha_siteKey, $recaptcha_secret;

    include("header.php");

	if (is_admin($admin)) {
		$userstats = $db->sql_fetchrow($db->sql_query("SELECT admin, email FROM ".$prefix."_admin WHERE id='$cookie[0]'"));
		$inputname = $userstats['admin'];
		$inputemail = $userstats['email'];
	}
	if (is_member($member)) {
		if ($member[6] == 0) {
			$userstats = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_members WHERE id='$member[0]'"));
			$inputname = $userstats[surname];
		}elseif ($member[6] > 0) {
			$userstats = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_subusers WHERE id='$member[0]'"));
			$inputname = $userstats[surname];
		}
		$inputemail = $userstats[email];
	}

	$box_title = ""._CONTACT."";

	if ($save == "") {
		mt_srand ((double)microtime()*1000000);
		$maxran = 1000000;
		$randomnum = mt_rand(0, $maxran);
	
		if ($feedbackpath == "" OR !$feedbackpath) {
			$feedbackpath = "modules.php?name=feedback";
		}

		include("themes/$sitetheme/templates/box_content_open.tpl");
		include("themes/$sitetheme/templates/feedback.tpl");
		include("themes/$sitetheme/templates/box_content_close.tpl");
	}elseif ($save == 1) {
		$date = time();
		$fbname = filter($fbname, "", 1);
		$fbemail = filter($fbemail, "", 1);
		$fbsubject = filter($fbsubject, "", 1);
		$fbmessage = filter($fbmessage, "nohtml", 1);
		
		if ($recaptcha_siteKey != "") {
			$captcha = $_POST['g-recaptcha-response'];
		
			if ($captcha == "") {
				$box_text = msg_errorback(""._CONTACTNOCAPTCHA."");
				$save = "0";
			}
			$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$recaptcha_secret."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
 
			if($response.success==false) {
				$box_text = msg_errorback(""._CONTACTFALSECAPTCHA."");
				$save = "0";
			}
		}
		
		if ($fbname == "") {
			$box_text = msg_errorback(""._CONTACTNONAME."");
			$save = "0";
		}

		$fbemail = strtolower($fbemail);

		if ((!$fbemail || !preg_match("/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,6}$/i",$fbemail)) AND $save != "0") {
			$box_text = msg_errorback(""._CONTACTNOEMAIL."");
			$save = "0";
		}

		if ($fbsubject == "" AND $save != "0") {
			$box_text = msg_errorback(""._CONTACTNOSUBJECT."");
			$save = "0";
		}
		if ($fbmessage == "" AND $save != "0") {
			$box_text = msg_errorback(""._CONTACTNOMSG."");
			$save = "0";
		}
		
		if (!$recaptcha_siteKey) {
		    if (extension_loaded("gd")) {
				if ($randomnum != $gfxcheck AND $save != "0") {
					$box_text = msg_errorback(""._CONTACTNOCODE."");
					$save = "0";
				}
			}
		}

		if ($dataprivacy != "1" AND $save != "0") {
			$box_text = msg_errorback(""._CONTACTNOCONSET."");
			$save = "0";
		}

		if ($save != "0") {
		 	$result = $db->sql_query("INSERT INTO ".$prefix."_cms_contact (id, name, email, date, subject, text) VALUES (NULL, '$fbname', '$fbemail', '$date', '$fbsubject', '$fbmessage')");
			if (!$result) {
				$box_text = msg_errorback(""._CONTACTDBUPERROR."");
				$save = "0";
			}
		}
		if ($save == 0) {
			$box_title = ""._ERROR."";
			include("themes/$sitetheme/templates/box_content.tpl");
			include("footer.php");
			die();
		}
		
		$dataprivacyhash = md5($email);
		$date = strftime("%d.%m.%Y, %H:%M:%S", time());
		$log_content = "[$date] - "._DATAPRIVACYACCEPTED." - "._DATAPRIVACYID." $dataprivacyhash - "._DATAPRIVACYCOLLECTEDDATA." "._DATAPRIVACYNAME.", "._DATAPRIVACYEMAIL." - "._DATAPRIVACYSTATUS." "._DATAPRIVACYCOMPLETEDELETION." (CONTACT)
";

		$log_month = strftime ("%m");
		$log_year = strftime ("%Y");
					
		file_put_contents("resources/logs/".$log_year."_".$log_month.".log", $log_content, FILE_APPEND | LOCK_EX);

		$save = 0;
		$box_text = ''._CONTACTTHX.'
					  <META HTTP-EQUIV="refresh" content="5;URL=index.php">';
		include("themes/$sitetheme/templates/box_content.tpl");
	}
    include("footer.php");
}


switch($op) {

    default:
    feedback($save, $fbname, $fbemail, $fbsubject, $fbmessage, $dataprivacy, $gfxcheck, $randomnum);
    break;    

}

?>