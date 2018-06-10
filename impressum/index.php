<?php

if (!defined('MODULE_FILE')) {
	header("Location: index.php");
	die();
}

require_once("cmsmain.php");

include ('header.php');

$box_title = _IMPRESSUM;
$box_firm = $cmsoption[firm];
$box_holder = $cmsoption[holder];
$box_street = $cmsoption[street];
$box_zipcode = $cmsoption[zipcode];
$box_city = $cmsoption[city];
$box_taxid = $cmsoption[taxid];
$box_vatid = $cmsoption[vatid];
$box_venue = $cmsoption[venue];
$box_register = $cmsoption[register];
$box_phoneone = $cmsoption[phoneone];
$box_phonetwo = $cmsoption[phonetwo];
$box_fax = $cmsoption[fax];
$box_email = $cmsoption[email];
$box_website = $cmsoption[website];
$box_contentby = $cmsoption[content];
 
$countrystats = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_country WHERE id='$cmsoption[countryid]'"));
if ($language == $cmsoption[languagetwo] AND $language != "" AND $countrystats[nametwo] != "") {
	$box_country = $countrystats[nametwo];
}else{
	$box_country = $countrystats[name];
}

include("themes/$sitetheme/templates/box_content_open.tpl");
include("themes/$sitetheme/templates/impressum.tpl");
include("themes/$sitetheme/templates/box_content_close.tpl");

if ($language == $cmsoption[languagetwo] AND $language != "" AND $cmsoption[disclaimertwo] != "" AND $cmsoption[copyrighttwo] != "") {
	if ($cmsoption[disclaimertwo]) {
		$box_title = _IMPRESSUMDISCLAIMER;
		$box_text = html_entity_decode($cmsoption[disclaimertwo], ENT_QUOTES, "UTF-8");
		include("themes/$sitetheme/templates/box_content.tpl");
	}
	if ($cmsoption[copyrighttwo]) {
		$box_title = _IMPRESSUMCOPYRIGHT;
		$box_text = html_entity_decode($cmsoption[copyrighttwo], ENT_QUOTES, "UTF-8");
		include("themes/$sitetheme/templates/box_content.tpl");
	}
}else{
	if ($cmsoption[disclaimer]) {
		$box_title = _IMPRESSUMDISCLAIMER;
		$box_text = html_entity_decode($cmsoption[disclaimer], ENT_QUOTES, "UTF-8");
		include("themes/$sitetheme/templates/box_content.tpl");
	}
	if ($cmsoption[copyright]) {
		$box_title = _IMPRESSUMCOPYRIGHT;
		$box_text = html_entity_decode($cmsoption[copyright], ENT_QUOTES, "UTF-8");
		include("themes/$sitetheme/templates/box_content.tpl");
	}
}

include ('footer.php');

?>