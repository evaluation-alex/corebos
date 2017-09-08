<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
global $current_user,$mod_strings,$app_strings,$theme;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('include/database/PearDatabase.php');
require_once('modules/Calendar/CalendarCommon.php');
require_once('modules/Calendar4You/Calendar4You.php');
require_once('modules/Calendar4You/CalendarUtils.php');
$t=Date("Ymd");
$userDetails=getSharingUserName($current_user->id);
$shareduser_ids = getSharedUserId($current_user->id);

$c_mod_strings = return_module_language($current_language,'Calendar');
$users_mod_strings = return_module_language($current_language,'Users');

$save_google_sync = "0";
$id = $_REQUEST["id"];

if ($id != "task") 
	$google_sync_id = true;
else
	$google_sync_id = false;

$mode = $_REQUEST["mode"];

$user_view_type = $_REQUEST['user_view_type'];

$Calendar4You = new Calendar4You();

$Calendar_Settings = $Calendar4You->getSettings();

$Event_Colors = $Calendar4You->getEventColor($mode,$id);
?>
<table class="layerHeadingULine" width="600px" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr class="slds-text-title--caps">
		<th scope="col" style="padding: .5rem 1.5rem .5rem 1rem;">
			<div class="slds-truncate moduleName">
				<?php
					echo "&quot;";
					if ($mode == "user") {
						$event_name = getITSUserFullName($id);
					} else {
						if ($id == "task") {
							$event_name = $c_mod_strings["LBL_TASK"];
						} elseif ($id == "invite") {
							$event_name = $mod_strings["LBL_INVITE"];
						} else {
							$event_name = getActTypeForCalendar($id);
						}
					}
					echo $event_name;
					echo "&quot; ";
					echo $app_strings["LBL_SETTINGS"];
				?>
			</div>
		</th>
		<th scope="col" style="padding: .5rem 1rem .5rem 1.5rem;">
			<div class="slds-truncate close-icon" align=right>
				<a href="javascript:fninvsh('event_setting');">
					<img src="<?php echo vtiger_imageurl('close.gif', $theme) ?>" border="0"  align="absmiddle" />
				</a>
			</div>
		</th>
	</tr>
</table>
<form name="SettingForm" method="post" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="module" value="Calendar4You">
<input type="hidden" name="action" value="SaveEventSettings">
<input type="hidden" name="view" value="<?php echo vtlib_purify($_REQUEST['view']); ?>">
<input type="hidden" name="hour" value="<?php echo (isset($_REQUEST['hour']) ? vtlib_purify($_REQUEST['hour']) : ''); ?>">
<input type="hidden" name="day" value="<?php echo (isset($_REQUEST['day']) ? vtlib_purify($_REQUEST['day']) : ''); ?>">
<input type="hidden" name="month" value="<?php echo (isset($_REQUEST['month']) ? vtlib_purify($_REQUEST['month']) : ''); ?>">
<input type="hidden" name="year" value="<?php echo (isset($_REQUEST['year']) ? vtlib_purify($_REQUEST['year']) : ''); ?>">
<input type="hidden" name="user_view_type" value="<?php echo $user_view_type; ?>">
<input type="hidden" name="save_fields" value="<?php if ($mode != "user" && $id != "invite") echo "1"; else echo "0"; ?>">
<input type="hidden" name="mode" value="<?php echo $mode; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="current_userid" value="<?php echo $current_user->id; ?>" >
<input type="hidden" name="shar_userid" id="shar_userid" >
<div style="padding:5px">
	<table class="slds-table slds-no-row-hover dvtContentSpace">
		<tbody>
			<tr>
				<td>
					<div class="slds-table--scoped">
						<!-- Tab Controls -->
						<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">

							<?php 
								if ($mode != "user" && $id != "invite") {
							?>
								<li class="slds-tabs--scoped__item active" id="cellTabEventColor" onclick="switchClass('cellTabEventColor','on');switchClass('cellTabEventInfo','off');switchClass('cellTabGoogleSync','off');fnShowDrop('TabColorInCalendar');fnHideDrop('TabEventInfoInCalendar');fnHideDrop('TabEventGoogleCalSync');" role="presentation" style="border-top-left-radius: .25rem;">
									<a href="javascript:doNothing()" class="slds-tabs--scoped__link " role="tab" tabindex="0" aria-selected="true"><?php echo $mod_strings['LBL_COLOR_IN_CALENDAR']; ?></a>
								</li>
								<li class="slds-tabs--scoped__item" id="cellTabEventInfo" onclick="switchClass('cellTabEventColor','off');switchClass('cellTabEventInfo','on');switchClass('cellTabGoogleSync','off');fnHideDrop('TabColorInCalendar');fnShowDrop('TabEventInfoInCalendar');fnHideDrop('TabEventGoogleCalSync');" role="presentation" style="border-top-left-radius: .25rem;">
									<a href="javascript:doNothing()" class="slds-tabs--scoped__link " role="tab" tabindex="0" aria-selected="true"><?php echo $mod_strings["LBL_DISPLAYED_INFO"]; ?></a>
								</li>
							<?php if ($google_sync_id) { ?>
								<li class="slds-tabs--scoped__item" id="cellTabGoogleSync" onclick="switchClass('cellTabEventColor','off');switchClass('cellTabEventInfo','off');switchClass('cellTabGoogleSync','on');fnHideDrop('TabColorInCalendar');fnHideDrop('TabEventInfoInCalendar');fnShowDrop('TabEventGoogleCalSync');" role="presentation" style="border-top-left-radius: .25rem;">
									<a href="javascript:doNothing()" class="slds-tabs--scoped__link " role="tab" tabindex="0" aria-selected="true"><?php echo $mod_strings["LBL_GOOGLE_SYNC"]; ?></a>
								</li>
							<?php } ?>

							<?php } else { ?>
								<li class="slds-tabs--scoped__item active" id="cellTabEventColor" role="presentation" style="border-top-left-radius: .25rem;">
									<a href="javascript:doNothing()" class="slds-tabs--scoped__link " role="tab" tabindex="0" aria-selected="true"><?php echo $mod_strings['LBL_COLOR_IN_CALENDAR']; ?></a>
								</li>
							<?php } ?>
						</ul>

						<!-- Color In calendat UI -->
						<div id="TabColorInCalendar" style="display: block;" role="tabpanel" aria-labelledby="TabColorInCalendar" class="slds-tabs--scoped__content slds-truncate">
							<!-- <table class="slds-table slds-no-row-hover slds-table-moz"> -->
							<table class="slds-table slds-no-row-hover slds-table-moz" style="border-collapse:separate; border-spacing: .5rem;">
								<tr class="slds-line-height--reset">
									<td class="dvtCellLabel">
										<?php echo $mod_strings['LBL_COLOR_IN_CALENDAR_BACKGROUND'] ?>
									</td>
									<td class="dvtCellInfo">
										<input class="slds-input" style="background-color:<?php echo $Event_Colors["bg"]; ?>;" value="<?php echo $Event_Colors["bg"]; ?>" id="event_color_bg" name="event_color_bg" size="10" onblur="this.style.backgroundColor=this.value;" type="text">
										<a href="javascript:C_TCP.popup(document.SettingForm.event_color_bg, 3)">
											<img alt="Click here to Pick up the background color" title="Click here to Pick up the background color" src="modules/Calendar4You/images/color_picker-background.png" style="width: 20px; vertical-align: middle;">
										</a>
									</td>
								</tr>
								<tr class="slds-line-height--reset">
									<td class="dvtCellLabel">
										<?php echo $mod_strings['LBL_COLOR_IN_CALENDAR_TEXT'] ?>:
									</td>
									<td class="dvtCellInfo">
										<input class="slds-input" style="background-color:<?php echo $Event_Colors["text"]; ?>;" value="<?php echo $Event_Colors["text"]; ?>" id="event_color_text" name="event_color_text" size="10" onblur="this.style.backgroundColor=this.value;" type="text">
										<a href="javascript:C_TCP.popup(document.SettingForm.event_color_text, 3)">
											<img alt="Click here to Pick up the text color" title="Click here to Pick up the text color" src="modules/Calendar4You/images/color_picker-text.png" style="width: 20px; vertical-align: middle;">
										</a>
									</td>
								</tr>
							</table>
						</div>

						<!-- Displayed info -->
						<div id="TabEventInfoInCalendar" style="display: none;" role="tabpanel" aria-labelledby="TabEventInfoInCalendar" class="slds-tabs--scoped__content slds-truncate">
							<?php
								if ($mode != "user" && $id != "invite")
								{
									$Event_Fields = array();
									$Fields_Label = array();
									
									if ($id == "task")
										$for_module = "Calendar";
									else
										$for_module = "Events";
									
									$tabid = getTabId($for_module);

									$sql_field = "SELECT fieldid, uitype, fieldname, fieldlabel FROM vtiger_field WHERE tabid=? and (displaytype != 3 OR uitype = 55) and vtiger_field.fieldname not in ('notime') ORDER BY sequence ASC";
									$res_field = $adb->pquery($sql_field,array($tabid));
									$num_field = $adb->num_rows($res_field);  
									
									if ($num_field > 0) 
									{
										while($row_field = $adb->fetch_array($res_field)) {
											$fieldid = $row_field['fieldid'];
											$fieldlabel = getTranslatedString($row_field['fieldlabel'], "Calendar");

											$field_data = array();
											$field_data["fieldid"] = $fieldid;
											$field_data["fieldname"] = $row_field['fieldname'];
											$field_data["fieldlabel"] = $fieldlabel;
											$field_data['module'] = $for_module;
											$Fields_Array[$fieldid] = $field_data;
											unset($field_data);
											
											$Fields_Label[$row_field['fieldname']] = $fieldlabel;
										}
									}
									uasort($Fields_Array, function($a,$b) {
										return (strtolower($a['fieldlabel']) < strtolower($b['fieldlabel'])) ? -1 : 1;
									});
									$OnlyEventFields = $Fields_Array;
									$cl = Calendar_getReferenceFieldColumnsList($for_module);
									if (count($cl) > 0) 
									{
										foreach ($cl as $mod => $field_info) {
											foreach ($field_info as $fieldid => $field_data) {
												$Fields_Array[$fieldid] = $field_data;
												$Fields_Label[$field_data['fieldname']] = $field_data['fieldlabel'];
											}
										}
									}

									$sql = "SELECT * FROM its4you_calendar4you_event_fields WHERE userid = ? AND event = ?";
									$result = $adb->pquery($sql,array($current_user->id,$id));
									$num_rows = $adb->num_rows($result);
									
									if ($num_rows > 0)
									{
										while($row = $adb->fetchByAssoc($result)) {
											list($fname,$fid) = explode(':', $row['fieldname']);
											if ($row['type'] == "1") {
												$Showed_Field[$row['view']] = $fname;
											} else {
												$mname = getModuleForField($fid);
												$mname = getTranslatedString($mname,$mname);
												$Event_Fields[$row['view']][$fname.':'.$fid] = $Fields_Label[$fname].' ('.$mname.')';
											}
										}
									}
									else
									{
										$Showed_Field["day"] = "subject";
										$Showed_Field["week"] = "subject";
										$Showed_Field["month"] = "subject";
									}
								?>
									<!-- Displayed info in day calendar -->
									<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered detailview_table">
										<tr class="slds-line-height--reset">
											<td class="dvtCellLabel text-left">
												<b><?php echo $mod_strings['LBL_DAY_EVENT_INFO']; ?>:</b>
												<select name="day_showed_field" id="day_showed_field" class="slds-select" style="width: 30%;">
													<?php echo createFieldsOptions($OnlyEventFields, $Showed_Field['day']); ?>
												</select>
											</td>
										</tr>
									</table>
									<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered detailview_table" style="padding: 0 .5rem;">
										<tr class="slds-line-height--reset">
											<td class="dvtCellLabel text-left" width="35%">
												<?php echo $mod_strings['LBL_AVAILABLE_INFO']; ?>
											</td>
											<td class="dvtCellLabel"></td>
											<td class="dvtCellLabel text-left" width="35%">
												<?php echo $mod_strings['LBL_SELECTED_INFO']; ?>
											</td>
										</tr>
										<tr class="slds-line-height--reset">
											<td class="dvtCellInfo" width="35%">
												<select name="day_available_fields" id="day_available_fields" class="slds-select" size=5 multiple style="height:100px;width:100%">
													<?php echo createFieldsOptions($Fields_Array); ?>
												</select>
											</td>
											<td class="dvtCellInfo">
												<input type=button value="<?php echo $c_mod_strings['LBL_ADD_BUTTON'] ?> >>" class="slds-button slds-button--small slds-button_success slds-width" onClick="incUser('day_available_fields','selected_day_fields')">
												<br>
												<input type=button value="<< <?php echo $c_mod_strings['LBL_RMV_BUTTON'] ?> " class="slds-button slds-button--small slds-button--destructive slds-width" onClick="rmvUser('selected_day_fields')">
											</td>
											<td class="dvtCellInfo" width="35%">
												<select name="selected_day_fields" id="selected_day_fields" class="slds-select" size=5 multiple style="height:100px;width:100%">
													<?php echo createFieldsOptions((isset($Event_Fields["day"]) ? $Event_Fields["day"] : '')); ?>
												</select>
												</td>
											</tr>
									</table>
									<br>
									<!-- Displayed info in week calendar -->
									<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered detailview_table">
										<tr class="slds-line-height--reset">
											<td class="dvtCellLabel text-left">
												<b><?php echo $mod_strings['LBL_WEEK_EVENT_INFO']; ?>:</b>
												<select name="week_showed_field" id="day_showed_field" class="slds-select" style="width: 30%;">
													<?php echo createFieldsOptions($OnlyEventFields,$Showed_Field['week']); ?>
												</select>
											</td>
										</tr>
									</table>
									<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered detailview_table" style="padding: 0 .5rem;">
										<tr class="slds-line-height--reset">
											<td class="dvtCellLabel text-left" width="35%">
												<?php echo $mod_strings['LBL_AVAILABLE_INFO']; ?>
											</td>
											<td class="dvtCellLabel">
											<td class="dvtCellLabel text-left" width="35%">
												<?php echo $mod_strings['LBL_SELECTED_INFO']; ?>
											</td>
										</tr>
										<tr class="slds-line-height--reset">
											<td class="dvtCellInfo" width="35%">
												<select name="week_available_fields" id="week_available_fields" class="slds-select" size=5 multiple style="height:100px;width:100%">
													<?php echo createFieldsOptions($Fields_Array); ?>
												</select>
											</td>
											<td class="dvtCellInfo">
												<input type=button value="<?php echo $c_mod_strings['LBL_ADD_BUTTON'] ?> >>" class="slds-button slds-button--small slds-button_success slds-width" onClick="incUser('week_available_fields','selected_week_fields')"><br>
												<input type=button value="<< <?php echo $c_mod_strings['LBL_RMV_BUTTON'] ?> " class="slds-button slds-button--small slds-button--destructive slds-width" onClick="rmvUser('selected_week_fields')">
											</td>
											<td class="dvtCellInfo" width="35%">
												<select name="selected_week_fields" id="selected_week_fields" class="slds-select" size=5 multiple style="height:100px;width:100%">
													<?php echo createFieldsOptions((isset($Event_Fields["week"]) ? $Event_Fields["week"] : '')); ?>
												</select>
											</td>
										</tr>
									</table>
									<br>
									<!-- Displayed info in month calendar -->
									<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered detailview_table">
										<tr class="slds-line-height--reset">
											<td class="dvtCellLabel text-left">
												<b><?php echo $mod_strings['LBL_MONTH_EVENT_INFO']; ?>:</b>
												<select name="month_showed_field" id="day_showed_field" class="slds-select" style="width: 30%;">
													<?php echo createFieldsOptions($OnlyEventFields,$Showed_Field['month']); ?>
												</select>
											</td>
										</tr>
									</table>
									<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered detailview_table" style="padding: 0 .5rem;">
										<tr class="slds-line-height--reset">
											<td class="dvtCellLabel text-left">
												<?php echo $mod_strings['LBL_AVAILABLE_INFO']; ?>
											</td>
											<td class="dvtCellLabel"></td>
											<td class="dvtCellLabel text-left">
												<?php echo $mod_strings['LBL_SELECTED_INFO']; ?>
											</td>
										</tr>
										<tr>
											<td class="dvtCellInfo" width="35%">
												<select name="month_available_fields" id="month_available_fields" class="slds-select" size=5 multiple style="height:100px;width:100%">
													<?php echo createFieldsOptions($Fields_Array); ?>
												</select>
											</td>
											<td class="dvtCellInfo">
												<input type=button value="<?php echo $c_mod_strings['LBL_ADD_BUTTON'] ?> >>" class="slds-button slds-button--small slds-button_success slds-width" onClick="incUser('month_available_fields','selected_month_fields')"><br>
												<input type=button value="<< <?php echo $c_mod_strings['LBL_RMV_BUTTON'] ?> " class="slds-button slds-button--small slds-button--destructive slds-width" onClick="rmvUser('selected_month_fields')">
											</td>
											<td class="dvtCellInfo" width="35%">
												<select name="selected_month_fields" id="selected_month_fields" class="slds-select" size=5 multiple style="height:100px;width:100%">
													<?php echo createFieldsOptions((isset($Event_Fields["month"]) ? $Event_Fields["month"] : '')); ?>
												</select>
											</td>
										</tr>
									</table>
										<input type="hidden" id="day_selected_fields" name="day_selected_fields">
										<input type="hidden" id="week_selected_fields" name="week_selected_fields">
										<input type="hidden" id="month_selected_fields" name="month_selected_fields">
							<?php } ?>
						</div>

						<!-- Google Cal Sync info -->
						<div id="TabEventGoogleCalSync" style="display: none;" role="tabpanel" aria-labelledby="TabEventGoogleCalSync" class="slds-tabs--scoped__content slds-truncate">
							<div class="slds-truncate" style="padding: .5rem;">
								<?php
											if ($google_sync_id)
											{
												$user_fullname = getITSUserFullName($current_user->id);
												require_once('modules/Calendar4You/GoogleSync4You.php');

												$GoogleSync4You = new GoogleSync4You();
												$have_access_data = $GoogleSync4You->setAccessDataForUser($current_user->id);

												if ($have_access_data)
												{
													$GoogleSync4You->connectToGoogle();

													if (!$GoogleSync4You->is_logged)
													{
														echo $GoogleSync4You->getStatus();
													}
													else
													{
														$GoogleSync4You->setEvent($id); 

														$selected_calendar = $GoogleSync4You->getSCalendar("1");
														echo $mod_strings["LBL_TO_GOOGLE_CALENDAR"].": ";
														$listFeed =  $GoogleSync4You->getGoogleCalendars();
														echo "<select name='selected_calendar' class='slds-select' onChange='showGoogleSyncAccDiv(this.value)'>";
														echo "<option value=''></option>";
														foreach ($listFeed as $calendar) {
															if ($calendar->id == $selected_calendar)  $selected = "selected"; else $selected = "";
															echo "<option value='".$calendar->id."' ".$selected.">".$calendar->summary."</option>";
														}
														echo "</select>";

														echo "<br /><br />";

														if ($selected_calendar != "") $display = "block"; else $display = "none";
														echo "<div id='google_sync_acc_div' style='display:".$display."'>";

														$is_export_disabled = $GoogleSync4You->isDisabled(1);
														if (!$is_export_disabled) $checked1 = "checked"; else $checked1 = "";

														echo $app_strings["LBL_EXPORT"]." &quot;".$event_name."&quot; ";
														echo $mod_strings["LBL_EVENTS_TO_GOOGLE"].": ";
														echo "<input type='checkbox' name='export_to_calendar' value='1' ".$checked1."><br>";

														$is_import_disabled = $GoogleSync4You->isDisabled(2);
														if (!$is_import_disabled) $checked2 = "checked"; else $checked2 = "";

														echo $mod_strings["LBL_IMPORT_FROM_G_GOOGLE"]." &quot;".$event_name."&quot; ";
														echo "<input type='checkbox' name='import_from_calendar' value='1' ".$checked2."><br>";
														echo "</div>";

														$save_google_sync = "1";
													}

												}
												else
												{
													echo $app_strings["LBL_USER"]." &quot;".$user_fullname."&quot; ".$mod_strings["LBL_HAVE_NOT_ACCESS_DATA"].".";
												}
											}
								?>
							</div>
						</div>

					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<table border=0 cellspacing=0 cellpadding=5 width=100%>
	<tr class="slds-line-height--reset">
		<td align="center" style="padding: .5rem 0;">
			<input type="submit" name="save" value=" &nbsp;<?php echo $app_strings['LBL_SAVE_BUTTON_LABEL'] ?>&nbsp;" class="slds-button slds-button--small slds-button_success" onClick = "saveITSEventSettings();"/>
			&nbsp;&nbsp;
			<input type="button" name="cancel" value=" <?php echo $app_strings['LBL_CANCEL_BUTTON_LABEL'] ?> " class="slds-button slds-button--small slds-button--destructive" onclick="fninvsh('event_setting');" />
		</td>
	</tr>
</table>
<input type="hidden" name="savegooglesync" value="<?php echo $save_google_sync; ?>">
</form>
<?php
function createFieldsOptions($Fields_Array,$selected_field = "") {
	if (!is_array($Fields_Array)) return '';
	$c = "";
	$mod = '';
	$closetag = false;
	foreach ($Fields_Array AS $fieldid => $fielddata) {
		if (is_array($fielddata)) {
			if ($mod!=$fielddata['module']) {
				$mod = $fielddata['module'];
				if ($closetag) $c .= '</optgroup>';
				$c .= '<optgroup label="'.getTranslatedString($mod,$mod).'">';
				$closetag = true;
			}
			$sel = ($selected_field == $fielddata['fieldname'] ? 'selected' : '');
			$c .= "<option value='".$fielddata["fieldname"].':'.$fieldid."' ".$sel.">".$fielddata["fieldlabel"]."</option>";
		} else {
			$sel = ($selected_field == $fieldid ? 'selected' : '');
			$c .= "<option value='".$fieldid."' ".$sel.">".$fielddata."</option>";
		}
	}
	return $c;
}

function getITSUserFullName($id) {
	global $adb;
	$u_query="select * from vtiger_users where id=?";
	$u_result = $adb->pquery($u_query, array($id));
	return trim(getFullNameFromQResult($u_result, 0, 'Users'));
}
?>