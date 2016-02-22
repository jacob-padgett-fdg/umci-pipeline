<?PHP
session_start();

global $use_odbc;
global $global_user;

$use_odbc = 1;

require_once("settings.cfg");
require_once("querylib.inc");
require_once("global-auth.inc");

$jobNumber = $_REQUEST["jobNumber"];

//check last character
$lastCharacter = substr($jobNumber,strlen($jobNumber) - 1);

if ($lastCharacter != "-")
    $jobNumber .= "-";

if ($global_contacts_id='353'||$global_contacts_id=='4517'||$global_contacts_id='2'||$global_contacts_id='1') include("viewpoint_connect_ro_pr.phtml");
else include("viewpoint_connect_ro.phtml");

$sql = "Select
                a.Phase,
                b.Description
            from
                dbo.JCCHPM as a
                left outer join dbo.JCJP as b on a.Phase=b.Phase and a.Job=b.Job and a.JCCo=b.JCCo 
            where
                a.JCCo=1 and
                a.CostType=1 and
                a.Job='".$jobNumber."'
            Order by
                a.Phase
            ";

$resultArray = array();

if ($use_odbc)
{    
    $result = odbc_exec($conn,$sql);
    while ( $row = odbc_fetch_array($result))
    {
        array_push($resultArray, $row);
    }
}
else
{
    $result = mssql_query($sql);
    while ( $row = mssql_fetch_array($result))
    {
        array_push($resultArray, $row);
    }
}

echo json_encode( $resultArray );
?>