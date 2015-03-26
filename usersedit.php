<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$users_edit = NULL; // Initialize page object first

class cusers_edit extends cusers {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{6362B67B-391D-4CF2-AF36-D4D235D92D64}";

	// Table name
	var $TableName = 'users';

	// Page object name
	var $PageObjName = 'users_edit';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME]);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (users)
		if (!isset($GLOBALS["users"]) || get_class($GLOBALS["users"]) == "cusers") {
			$GLOBALS["users"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["users"];
		}

		// User table object (users)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'users', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate(ew_GetUrl("login.php"));
		}
		$Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		$Security->TablePermission_Loaded();
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		if ($Security->IsLoggedIn() && strval($Security->CurrentUserID()) == "") {
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("userslist.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
		}

		// Create Token
		$this->CreateToken();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn, $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $users;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($users);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $DbMasterFilter;
	var $DbDetailFilter;
	var $DisplayRecs = 1;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $RecCnt;
	var $RecKey = array();
	var $Recordset;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load current record
		$bLoadCurrentRecord = FALSE;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;

		// Load key from QueryString
		if (@$_GET["user_id"] <> "") {
			$this->user_id->setQueryStringValue($_GET["user_id"]);
			$this->RecKey["user_id"] = $this->user_id->QueryStringValue;
		} else {
			$bLoadCurrentRecord = TRUE;
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load recordset
		$this->StartRec = 1; // Initialize start position
		if ($this->Recordset = $this->LoadRecordset()) // Load records
			$this->TotalRecs = $this->Recordset->RecordCount(); // Get record count
		if ($this->TotalRecs <= 0) { // No record found
			if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
				$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
			$this->Page_Terminate("userslist.php"); // Return to list page
		} elseif ($bLoadCurrentRecord) { // Load current record position
			$this->SetUpStartRec(); // Set up start record position

			// Point to current record
			if (intval($this->StartRec) <= intval($this->TotalRecs)) {
				$bMatchRecord = TRUE;
				$this->Recordset->Move($this->StartRec-1);
			}
		} else { // Match key values
			while (!$this->Recordset->EOF) {
				if (strval($this->user_id->CurrentValue) == strval($this->Recordset->fields('user_id'))) {
					$this->setStartRecordNumber($this->StartRec); // Save record position
					$bMatchRecord = TRUE;
					break;
				} else {
					$this->StartRec++;
					$this->Recordset->MoveNext();
				}
			}
		}

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$bMatchRecord) {
					if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
						$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
					$this->Page_Terminate("userslist.php"); // Return to list page
				} else {
					$this->LoadRowValues($this->Recordset); // Load row values
				}
				break;
			Case "U": // Update
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$sReturnUrl = $this->getReturnUrl();
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
		$this->profile->Upload->Index = $objForm->Index;
		$this->profile->Upload->UploadFile();
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->user_id->FldIsDetailKey) {
			$this->user_id->setFormValue($objForm->GetValue("x_user_id"));
		}
		if (!$this->password->FldIsDetailKey) {
			$this->password->setFormValue($objForm->GetValue("x_password"));
		}
		if (!$this->level->FldIsDetailKey) {
			$this->level->setFormValue($objForm->GetValue("x_level"));
		}
		if (!$this->_email->FldIsDetailKey) {
			$this->_email->setFormValue($objForm->GetValue("x__email"));
		}
		if (!$this->activated->FldIsDetailKey) {
			$this->activated->setFormValue($objForm->GetValue("x_activated"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->user_id->CurrentValue = $this->user_id->FormValue;
		$this->password->CurrentValue = $this->password->FormValue;
		$this->level->CurrentValue = $this->level->FormValue;
		$this->_email->CurrentValue = $this->_email->FormValue;
		$this->activated->CurrentValue = $this->activated->FormValue;
	}

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Load List page SQL
		$sSql = $this->SelectSQL();

		// Load recordset
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->SelectLimit($sSql, $rowcnt, $offset);
		$conn->raiseErrorFn = '';

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}

		// Check if valid user id
		if ($res) {
			$res = $this->ShowOptionLink('edit');
			if (!$res) {
				$sUserIdMsg = $Language->Phrase("NoPermission");
				$this->setFailureMessage($sUserIdMsg);
			}
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->user_id->setDbValue($rs->fields('user_id'));
		$this->password->setDbValue($rs->fields('password'));
		$this->level->setDbValue($rs->fields('level'));
		$this->_email->setDbValue($rs->fields('email'));
		$this->activated->setDbValue($rs->fields('activated'));
		$this->profile->Upload->DbValue = $rs->fields('profile');
		if (is_array($this->profile->Upload->DbValue) || is_object($this->profile->Upload->DbValue)) // Byte array
			$this->profile->Upload->DbValue = ew_BytesToStr($this->profile->Upload->DbValue);
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->user_id->DbValue = $row['user_id'];
		$this->password->DbValue = $row['password'];
		$this->level->DbValue = $row['level'];
		$this->_email->DbValue = $row['email'];
		$this->activated->DbValue = $row['activated'];
		$this->profile->Upload->DbValue = $row['profile'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// user_id
		// password
		// level
		// email
		// activated
		// profile

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// user_id
			$this->user_id->ViewValue = $this->user_id->CurrentValue;
			$this->user_id->ViewCustomAttributes = "";

			// password
			$this->password->ViewValue = $this->password->CurrentValue;
			$this->password->ViewCustomAttributes = "";

			// level
			$this->level->ViewValue = $this->level->CurrentValue;
			$this->level->ViewCustomAttributes = "";

			// email
			$this->_email->ViewValue = $this->_email->CurrentValue;
			$this->_email->ViewCustomAttributes = "";

			// activated
			$this->activated->ViewValue = $this->activated->CurrentValue;
			$this->activated->ViewCustomAttributes = "";

			// profile
			if (!ew_Empty($this->profile->Upload->DbValue)) {
				$this->profile->ViewValue = $this->profile->FldCaption();
			} else {
				$this->profile->ViewValue = "";
			}
			$this->profile->ViewCustomAttributes = "";

			// user_id
			$this->user_id->LinkCustomAttributes = "";
			$this->user_id->HrefValue = "";
			$this->user_id->TooltipValue = "";

			// password
			$this->password->LinkCustomAttributes = "";
			$this->password->HrefValue = "";
			$this->password->TooltipValue = "";

			// level
			$this->level->LinkCustomAttributes = "";
			$this->level->HrefValue = "";
			$this->level->TooltipValue = "";

			// email
			$this->_email->LinkCustomAttributes = "";
			$this->_email->HrefValue = "";
			$this->_email->TooltipValue = "";

			// activated
			$this->activated->LinkCustomAttributes = "";
			$this->activated->HrefValue = "";
			$this->activated->TooltipValue = "";

			// profile
			$this->profile->LinkCustomAttributes = "";
			if (!empty($this->profile->Upload->DbValue)) {
				$this->profile->HrefValue = "users_profile_bv.php?user_id=" . $this->user_id->CurrentValue;
				$this->profile->LinkAttrs["target"] = "_blank";
				if ($this->Export <> "") $this->profile->HrefValue = ew_ConvertFullUrl($this->profile->HrefValue);
			} else {
				$this->profile->HrefValue = "";
			}
			$this->profile->HrefValue2 = "users_profile_bv.php?user_id=" . $this->user_id->CurrentValue;
			$this->profile->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// user_id
			$this->user_id->EditAttrs["class"] = "form-control";
			$this->user_id->EditCustomAttributes = "";
			$this->user_id->EditValue = $this->user_id->CurrentValue;
			$this->user_id->ViewCustomAttributes = "";

			// password
			$this->password->EditAttrs["class"] = "form-control";
			$this->password->EditCustomAttributes = "";
			$this->password->EditValue = ew_HtmlEncode($this->password->CurrentValue);
			$this->password->PlaceHolder = ew_RemoveHtml($this->password->FldCaption());

			// level
			$this->level->EditAttrs["class"] = "form-control";
			$this->level->EditCustomAttributes = "";
			$this->level->EditValue = ew_HtmlEncode($this->level->CurrentValue);
			$this->level->PlaceHolder = ew_RemoveHtml($this->level->FldCaption());

			// email
			$this->_email->EditAttrs["class"] = "form-control";
			$this->_email->EditCustomAttributes = "";
			$this->_email->EditValue = ew_HtmlEncode($this->_email->CurrentValue);
			$this->_email->PlaceHolder = ew_RemoveHtml($this->_email->FldCaption());

			// activated
			$this->activated->EditAttrs["class"] = "form-control";
			$this->activated->EditCustomAttributes = "";
			$this->activated->EditValue = ew_HtmlEncode($this->activated->CurrentValue);
			$this->activated->PlaceHolder = ew_RemoveHtml($this->activated->FldCaption());

			// profile
			$this->profile->EditAttrs["class"] = "form-control";
			$this->profile->EditCustomAttributes = "";
			if (!ew_Empty($this->profile->Upload->DbValue)) {
				$this->profile->EditValue = $this->profile->FldCaption();
			} else {
				$this->profile->EditValue = "";
			}
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->profile);

			// Edit refer script
			// user_id

			$this->user_id->HrefValue = "";

			// password
			$this->password->HrefValue = "";

			// level
			$this->level->HrefValue = "";

			// email
			$this->_email->HrefValue = "";

			// activated
			$this->activated->HrefValue = "";

			// profile
			if (!empty($this->profile->Upload->DbValue)) {
				$this->profile->HrefValue = "users_profile_bv.php?user_id=" . $this->user_id->CurrentValue;
				$this->profile->LinkAttrs["target"] = "_blank";
				if ($this->Export <> "") $this->profile->HrefValue = ew_ConvertFullUrl($this->profile->HrefValue);
			} else {
				$this->profile->HrefValue = "";
			}
			$this->profile->HrefValue2 = "users_profile_bv.php?user_id=" . $this->user_id->CurrentValue;
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->user_id->FldIsDetailKey && !is_null($this->user_id->FormValue) && $this->user_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->user_id->FldCaption(), $this->user_id->ReqErrMsg));
		}
		if (!$this->password->FldIsDetailKey && !is_null($this->password->FormValue) && $this->password->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->password->FldCaption(), $this->password->ReqErrMsg));
		}
		if (!$this->level->FldIsDetailKey && !is_null($this->level->FormValue) && $this->level->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->level->FldCaption(), $this->level->ReqErrMsg));
		}
		if (!$this->_email->FldIsDetailKey && !is_null($this->_email->FormValue) && $this->_email->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->_email->FldCaption(), $this->_email->ReqErrMsg));
		}
		if (!$this->activated->FldIsDetailKey && !is_null($this->activated->FormValue) && $this->activated->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->activated->FldCaption(), $this->activated->ReqErrMsg));
		}
		if ($this->profile->Upload->FileName == "" && !$this->profile->Upload->KeepFile) {
			ew_AddMessage($gsFormError, str_replace("%s", $this->profile->FldCaption(), $this->profile->ReqErrMsg));
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Update record based on key values
	function EditRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// user_id
			// password

			$this->password->SetDbValueDef($rsnew, $this->password->CurrentValue, "", $this->password->ReadOnly || (EW_ENCRYPTED_PASSWORD && $rs->fields('password') == $this->password->CurrentValue));

			// level
			$this->level->SetDbValueDef($rsnew, $this->level->CurrentValue, "", $this->level->ReadOnly);

			// email
			$this->_email->SetDbValueDef($rsnew, $this->_email->CurrentValue, "", $this->_email->ReadOnly);

			// activated
			$this->activated->SetDbValueDef($rsnew, $this->activated->CurrentValue, "", $this->activated->ReadOnly);

			// profile
			if (!($this->profile->ReadOnly) && !$this->profile->Upload->KeepFile) {
				if (is_null($this->profile->Upload->Value)) {
					$rsnew['profile'] = NULL;
				} else {
					$rsnew['profile'] = $this->profile->Upload->Value;
				}
			}

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
					if (!$this->profile->Upload->KeepFile) {
					}
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();

		// profile
		ew_CleanUploadTempPath($this->profile, $this->profile->Upload->Index);
		return $EditRow;
	}

	// Show link optionally based on User ID
	function ShowOptionLink($id = "") {
		global $Security;
		if ($Security->IsLoggedIn() && !$Security->IsAdmin() && !$this->UserIDAllow($id))
			return $Security->IsValidUserID($this->user_id->CurrentValue);
		return TRUE;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "userslist.php", "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($users_edit)) $users_edit = new cusers_edit();

// Page init
$users_edit->Page_Init();

// Page main
$users_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$users_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var users_edit = new ew_Page("users_edit");
users_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = users_edit.PageID; // For backward compatibility

// Form object
var fusersedit = new ew_Form("fusersedit");

// Validate form
fusersedit.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	this.PostAutoSuggest();
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_user_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $users->user_id->FldCaption(), $users->user_id->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_password");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $users->password->FldCaption(), $users->password->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_level");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $users->level->FldCaption(), $users->level->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "__email");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $users->_email->FldCaption(), $users->_email->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_activated");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $users->activated->FldCaption(), $users->activated->ReqErrMsg)) ?>");
			felm = this.GetElements("x" + infix + "_profile");
			elm = this.GetElements("fn_x" + infix + "_profile");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, "<?php echo ew_JsEncode2(str_replace("%s", $users->profile->FldCaption(), $users->profile->ReqErrMsg)) ?>");

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fusersedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fusersedit.ValidateRequired = true;
<?php } else { ?>
fusersedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $users_edit->ShowPageHeader(); ?>
<?php
$users_edit->ShowMessage();
?>
<form name="ewPagerForm" class="form-horizontal ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($users_edit->Pager)) $users_edit->Pager = new cPrevNextPager($users_edit->StartRec, $users_edit->DisplayRecs, $users_edit->TotalRecs) ?>
<?php if ($users_edit->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($users_edit->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $users_edit->PageUrl() ?>start=<?php echo $users_edit->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($users_edit->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $users_edit->PageUrl() ?>start=<?php echo $users_edit->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $users_edit->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($users_edit->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $users_edit->PageUrl() ?>start=<?php echo $users_edit->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($users_edit->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $users_edit->PageUrl() ?>start=<?php echo $users_edit->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $users_edit->Pager->PageCount ?></span>
</div>
<?php } ?>
<div class="clearfix"></div>
</form>
<form name="fusersedit" id="fusersedit" class="form-horizontal ewForm ewEditForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($users_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $users_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="users">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<div>
<?php if ($users->user_id->Visible) { // user_id ?>
	<div id="r_user_id" class="form-group">
		<label id="elh_users_user_id" for="x_user_id" class="col-sm-2 control-label ewLabel"><?php echo $users->user_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $users->user_id->CellAttributes() ?>>
<span id="el_users_user_id">
<span<?php echo $users->user_id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $users->user_id->EditValue ?></p></span>
</span>
<input type="hidden" data-field="x_user_id" name="x_user_id" id="x_user_id" value="<?php echo ew_HtmlEncode($users->user_id->CurrentValue) ?>">
<?php echo $users->user_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($users->password->Visible) { // password ?>
	<div id="r_password" class="form-group">
		<label id="elh_users_password" for="x_password" class="col-sm-2 control-label ewLabel"><?php echo $users->password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $users->password->CellAttributes() ?>>
<span id="el_users_password">
<input type="text" data-field="x_password" name="x_password" id="x_password" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($users->password->PlaceHolder) ?>" value="<?php echo $users->password->EditValue ?>"<?php echo $users->password->EditAttributes() ?>>
</span>
<?php echo $users->password->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($users->level->Visible) { // level ?>
	<div id="r_level" class="form-group">
		<label id="elh_users_level" for="x_level" class="col-sm-2 control-label ewLabel"><?php echo $users->level->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $users->level->CellAttributes() ?>>
<span id="el_users_level">
<input type="text" data-field="x_level" name="x_level" id="x_level" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($users->level->PlaceHolder) ?>" value="<?php echo $users->level->EditValue ?>"<?php echo $users->level->EditAttributes() ?>>
</span>
<?php echo $users->level->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($users->_email->Visible) { // email ?>
	<div id="r__email" class="form-group">
		<label id="elh_users__email" for="x__email" class="col-sm-2 control-label ewLabel"><?php echo $users->_email->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $users->_email->CellAttributes() ?>>
<span id="el_users__email">
<input type="text" data-field="x__email" name="x__email" id="x__email" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($users->_email->PlaceHolder) ?>" value="<?php echo $users->_email->EditValue ?>"<?php echo $users->_email->EditAttributes() ?>>
</span>
<?php echo $users->_email->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($users->activated->Visible) { // activated ?>
	<div id="r_activated" class="form-group">
		<label id="elh_users_activated" for="x_activated" class="col-sm-2 control-label ewLabel"><?php echo $users->activated->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $users->activated->CellAttributes() ?>>
<span id="el_users_activated">
<input type="text" data-field="x_activated" name="x_activated" id="x_activated" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($users->activated->PlaceHolder) ?>" value="<?php echo $users->activated->EditValue ?>"<?php echo $users->activated->EditAttributes() ?>>
</span>
<?php echo $users->activated->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($users->profile->Visible) { // profile ?>
	<div id="r_profile" class="form-group">
		<label id="elh_users_profile" class="col-sm-2 control-label ewLabel"><?php echo $users->profile->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $users->profile->CellAttributes() ?>>
<span id="el_users_profile">
<div id="fd_x_profile">
<span title="<?php echo $users->profile->FldTitle() ? $users->profile->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($users->profile->ReadOnly || $users->profile->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-field="x_profile" name="x_profile" id="x_profile">
</span>
<input type="hidden" name="fn_x_profile" id= "fn_x_profile" value="<?php echo $users->profile->Upload->FileName ?>">
<?php if (@$_POST["fa_x_profile"] == "0") { ?>
<input type="hidden" name="fa_x_profile" id= "fa_x_profile" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_profile" id= "fa_x_profile" value="1">
<?php } ?>
<input type="hidden" name="fs_x_profile" id= "fs_x_profile" value="0">
<input type="hidden" name="fx_x_profile" id= "fx_x_profile" value="<?php echo $users->profile->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_profile" id= "fm_x_profile" value="<?php echo $users->profile->UploadMaxFileSize ?>">
</div>
<table id="ft_x_profile" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $users->profile->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
	</div>
</div>
<?php if (!isset($users_edit->Pager)) $users_edit->Pager = new cPrevNextPager($users_edit->StartRec, $users_edit->DisplayRecs, $users_edit->TotalRecs) ?>
<?php if ($users_edit->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($users_edit->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $users_edit->PageUrl() ?>start=<?php echo $users_edit->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($users_edit->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $users_edit->PageUrl() ?>start=<?php echo $users_edit->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $users_edit->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($users_edit->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $users_edit->PageUrl() ?>start=<?php echo $users_edit->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($users_edit->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $users_edit->PageUrl() ?>start=<?php echo $users_edit->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $users_edit->Pager->PageCount ?></span>
</div>
<?php } ?>
<div class="clearfix"></div>
</form>
<script type="text/javascript">
fusersedit.Init();
</script>
<?php
$users_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$users_edit->Page_Terminate();
?>
