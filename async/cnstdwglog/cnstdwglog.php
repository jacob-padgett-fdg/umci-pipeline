<?php


require("cnfgdwglog.cfg");
require("global-auth.inc");

$jobinfo_id = $_GET["jobinfo_id"];

$current_cnstdwglog_section = $_GET["section"];
//die($current_cnstdwglog_section);
$last_issuance_id = $_GET["issuance_id"];
$query = "select `doc_id`,`cnstdwglog_id`, `cnstdwglog`.`description` as `description`, `drawing_type`, `drawing_num` from cnstdwglog right join documents on ( doc_type = 'cnstdwglog' and documents.app_record_id = cnstdwglog.cnstdwglog_id ) where ( cnstdwglog.jobinfo_id = '$jobinfo_id' and cnstdwglog.section = '$current_cnstdwglog_section') order by cnstdwglog.drawing_type, documents.sort_rank, documents.doc_id, cnstdwglog.drawing_num";
//die($query);
//tabledump ("explain " . $query);die;
$res = @mysql_query($query);
$last_drawing_type = '';

$cnstpagename = "/global/front_desk/cnstdwglog/index.php";
$pagename = $cnstpagename;

echo ' { "data": [ ';

$first_time = true;
while ($dwg = @mysql_fetch_assoc($res)) {
    if (!$first_time) {
        echo ',';
    } else {
        $first_time = false;
    }
    echo " [ ";

    $cookie_link = batch_this_document($dwg["doc_id"]);
    //////////////////////////////
    // look at this next line and
    // include jobinfo_id to pre-
    // vent errors in display
    //////////////////////////////
    $cnstdwglog_info = getoneb("select cnstdwglog_files.file_group_id as file_group_id, issuance_type, revision, name from cnstdwglog_files right join cnstdwglog_issuances on (cnstdwglog_issuances.issuance_id = cnstdwglog_files.issuance_id) right join webfile_groups on ( cnstdwglog_files.file_group_id = webfile_groups.file_group_id ) right join webfile_files on ( webfile_groups.file_group_id = webfile_files.file_group_id ) where cnstdwglog_id = '{$dwg["cnstdwglog_id"]}' and cnstdwglog_issuances.issuance_id = cnstdwglog_files.issuance_id order by cnstdwglog_issuances.issuance_type, cnstdwglog_issuances.issuance_date desc, cnstdwglog_issuances.sort_priority desc, cnstdwglog_issuances.name desc limit 1");
    $paperclip_link = webfile_paperclip_view($cnstdwglog_info->file_group_id, "&nbsp;", false);
    $addenda_count = 0;
   if (!empty($last_issuance_id)) {
        $addenda_files = getoneb("select count(*) as count from cnstdwglog_addenda where files_id = (select * from cnstdwglog_files where cnstdwglog_id = '{$dwg["cnstdwglog_id"]}' and issuance_id = '$last_issuance_id') and required_in_this_issuance = 'Y' and posted = 'N'");
        $addenda_count = "$addenda_files->count";
    }

    $drawing_num_text = ($dwg["drawing_num"] ? $dwg["drawing_num"] : "??????");
    if ($write) {
        $edit_record_link = "<a href=$pagename?mode=cnstdwglog_edit&cnstdwglog_id={$dwg["cnstdwglog_id"]}>$drawing_num_text</a>";
    } else {
        $edit_record_link = "$drawing_num_text";
    }

    $bgcolortext = "";
    if ($cnstdwglog_info->issuance_type == "Design") $bgcolortext = " style='width:100%;background-color:$fd_color_6''";
    if ($cnstdwglog_info->issuance_type == "Construction Orig Issue") $bgcolortext = " style='width:100%;background-color:$fd_color_5''";
    if ($cnstdwglog_info->issuance_type == "") $bgcolortext = " style='width:100%;background-color:$fd_color_2'";

    echo "\"<div class='flt'>$paperclip_link</div><div class='flt'> $cookie_link</div>\",";
    echo "\"$addenda_count&nbsp;\", ";
    echo "\"$edit_record_link\", ";
    echo "\"<font size=-1>{$dwg["description"]}</font>\", ";
    echo "\"$cnstdwglog_info->revision\", ";
    echo "\"<div $bgcolortext>$cnstdwglog_info->name</div>\", ";
    echo "\"{$dwg["drawing_type"]}\"";

    if ($_SESSION['cnstdwglog_view'] == "expanded") {
        // Just revised to hide errant extra data.. There are extra cnstdwglog_files items from a different job showing up.. they have
        // the cnstdwglog_id from a record in job a, but have a issuance_id from a issuance in job b - must look into.
        $issuances_res = @mysql_query("select cnstdwglog_files.file_group_id as file_group_id from cnstdwglog_files right join cnstdwglog_issuances on (cnstdwglog_issuances.issuance_id = cnstdwglog_files.issuance_id) left join webfile_groups on ( cnstdwglog_files.file_group_id = webfile_groups.file_group_id ) left join webfile_files on ( webfile_groups.file_group_id = webfile_files.file_group_id ) where cnstdwglog_files.cnstdwglog_id = '{$dwg["cnstdwglog_id"]}' and cnstdwglog_issuances.issuance_id = cnstdwglog_files.issuance_id and cnstdwglog_issuances.jobinfo_id = '{$_SESSION['global_jobinfo_id']}' group by cnstdwglog_issuances.issuance_id order by cnstdwglog_issuances.issuance_type desc, cnstdwglog_issuances.issuance_date, cnstdwglog_issuances.sort_priority, cnstdwglog_issuances.name");
        while ($issuance_row = @mysql_fetch_assoc($issuances_res)) {
            $paperclip_link = webfile_paperclip_view($issuance_row["file_group_id"], "&nbsp;", true);
            $bgcolortext = "";
            if ($paperclip_link != "&nbsp;") {
                if ($cnstdwglog_info->issuance_type == "Design") $bgcolortext = " style='width:100%;background-color:" . $fd_color_6 . "'";
                if ($cnstdwglog_info->issuance_type == "Construction Orig Issue") $bgcolortext = " style='width:100%;background-color:" . $fd_color_5 . "'";
            }
            echo  ", \"<div $bgcolortext>$paperclip_link</div>\"";
        }
    }
    echo " ]";
}
echo " ] }";
