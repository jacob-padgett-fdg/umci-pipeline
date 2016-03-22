<?PHP
session_start();

global $use_odbc;
global $global_user;

$use_odbc = 1;

require_once("settings.cfg");
require_once("querylib.inc");
require_once("global-auth.inc");
require_once("viewpoint_libs.inc");
require_once("timesheet_libs.inc");

//$jobNumber 	= $_REQUEST["jobNumber"];

//check last character
//$lastCharacter = substr($jobNumber,strlen($jobNumber) - 1);

//if ($lastCharacter != "-")
//    $jobNumber .= "-";
    
if ($global_contacts_id='353'||$global_contacts_id=='4517'||$global_contacts_id='2'||$global_contacts_id='1') include("viewpoint_connect_ro_pr.phtml");
else include("viewpoint_connect_ro.phtml");

$job = is_valid_viewpoint_job($jobNumber);

$resultArray = array();

$phases_locked=ms_getoneb("select * from JCJM with (NOLOCK) where JCCo = 1 and Job = '".$job->Job."' and LockPhases = 'Y'");

if ($phases_locked)
{
	$query="select Phase,Description from JCJP with (NOLOCK) where Job = '".$job->Job."' and JCCo = 1 and ActiveYN = 'Y' and Phase NOT LIKE 'YYY%' and Phase NOT LIKE 'ZZZ%'";
}
else
{
	$query="select * from JCPC with (NOLOCK),JCPM with (NOLOCK) where JCPC.JCCo = JCPM.JCCo and JCPC.JCCo = 1 and JCPC.Phase = JCPM.Phase and JCPC.CostType = 1 and JCPC.PhaseGroup = 1 and JCPM.PhaseGroup = 1  and JCPC.Phase NOT LIKE 'YYY%' and JCPC.Phase NOT LIKE 'ZZZ%'";
}

if (!$use_odbc)
{
	$res=@mssql_query($query);	
	while($row=@mssql_fetch_array($res))
	{
        if (is_valid_viewpoint_labor_phase($row['Phase'],$job->Job))
            array_push($resultArray, $row);
	}
}
else
{
    $res=odbc_exec($conn, $query);
    while($row=odbc_fetch_array($res))
    {
        if (is_valid_viewpoint_labor_phase($row['Phase'],$job->Job))
            array_push($resultArray, $row);
    }
}

echo json_encode( $resultArray );


//$resultArray = get_viewpoint_job_phases_array($jobNumber);


/*
$phases_locked_sql = "select * from JCJM with (NOLOCK) where JCCo = 1 and Job = '$jobNumber' and LockPhases = 'Y'";

//$phases_locked=ms_getoneb("select * from JCJM with (NOLOCK) where JCCo = 1 and Job = '$jobNumber' and LockPhases = 'Y'");
$sql = "";

/*if ($phases_locked)
{
    $sql="select Phase,Description from JCJP with (NOLOCK) where Job = '$jobNumber' and JCCo = 1 and ActiveYN = 'Y'";
}
else
{
    $sql="select * from JCPC with (NOLOCK),JCPM with (NOLOCK) where JCPC.JCCo = JCPM.JCCo and JCPC.JCCo = 1 and JCPC.Phase = JCPM.Phase and JCPC.CostType = 1 and JCPC.PhaseGroup = 1 and JCPM.PhaseGroup = 1";
}*/
/*
$resultArray = array();

if ($use_odbc)
{
    $phases_locked_result = odbc_exec($conn,$phases_locked_sql);
    if (odbc_num_rows($phases_locked_result) > 0)
        $sql="select Phase,Description from JCJP with (NOLOCK) where Job = '$jobNumber' and JCCo = 1 and ActiveYN = 'Y'";
    else
        $sql="select * from JCPC with (NOLOCK),JCPM with (NOLOCK) where JCPC.JCCo = JCPM.JCCo and JCPC.JCCo = 1 and JCPC.Phase = JCPM.Phase and JCPC.CostType = 1 and JCPC.PhaseGroup = 1 and JCPM.PhaseGroup = 1";
        
    $result = odbc_exec($conn,$sql);
    while ( $row = odbc_fetch_array($result))
    {
        array_push($resultArray, $row);
    }
}
else
{
    $phases_locked_result = mssql_query($phases_locked_sql);
    if (mssql_num_rows($phases_locked_result) > 0)
        $sql="select Phase,Description from JCJP with (NOLOCK) where Job = '$jobNumber' and JCCo = 1 and ActiveYN = 'Y'";
    else
        $sql="select * from JCPC with (NOLOCK),JCPM with (NOLOCK) where JCPC.JCCo = JCPM.JCCo and JCPC.JCCo = 1 and JCPC.Phase = JCPM.Phase and JCPC.CostType = 1 and JCPC.PhaseGroup = 1 and JCPM.PhaseGroup = 1";

    $result = mssql_query($sql);
    while ( $row = mssql_fetch_array($result))
    {
        array_push($resultArray, $row);
    }
}
*/

?>