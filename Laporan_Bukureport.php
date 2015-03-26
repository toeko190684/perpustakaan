<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php

// Global variable for table object
$Laporan_Buku = NULL;

//
// Table class for Laporan Buku
//
class cLaporan_Buku extends cTableBase {
	var $kode_buku;
	var $judul;
	var $pengarang;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'Laporan_Buku';
		$this->TableName = 'Laporan Buku';
		$this->TableType = 'REPORT';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->ExportExcelPageOrientation = ""; // Page orientation (PHPExcel only)
		$this->ExportExcelPageSize = ""; // Page size (PHPExcel only)
		$this->UserIDAllowSecurity = 0; // User ID Allow

		// kode_buku
		$this->kode_buku = new cField('Laporan_Buku', 'Laporan Buku', 'x_kode_buku', 'kode_buku', '`kode_buku`', '`kode_buku`', 3, -1, FALSE, '`kode_buku`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->kode_buku->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['kode_buku'] = &$this->kode_buku;

		// judul
		$this->judul = new cField('Laporan_Buku', 'Laporan Buku', 'x_judul', 'judul', '`judul`', '`judul`', 200, -1, FALSE, '`judul`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['judul'] = &$this->judul;

		// pengarang
		$this->pengarang = new cField('Laporan_Buku', 'Laporan Buku', 'x_pengarang', 'pengarang', '`pengarang`', '`pengarang`', 3, -1, FALSE, '`pengarang`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->pengarang->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['pengarang'] = &$this->pengarang;
	}

	// Report detail level SQL
	var $_SqlDetailSelect = "";

	function getSqlDetailSelect() { // Select
		return ($this->_SqlDetailSelect <> "") ? $this->_SqlDetailSelect : "SELECT * FROM `buku`";
	}

	function SqlDetailSelect() { // For backward compatibility
    	return $this->getSqlDetailSelect();
	}

	function setSqlDetailSelect($v) {
    	$this->_SqlDetailSelect = $v;
	}
	var $_SqlDetailWhere = "";

	function getSqlDetailWhere() { // Where
		return ($this->_SqlDetailWhere <> "") ? $this->_SqlDetailWhere : "";
	}

	function SqlDetailWhere() { // For backward compatibility
    	return $this->getSqlDetailWhere();
	}

	function setSqlDetailWhere($v) {
    	$this->_SqlDetailWhere = $v;
	}
	var $_SqlDetailGroupBy = "";

	function getSqlDetailGroupBy() { // Group By
		return ($this->_SqlDetailGroupBy <> "") ? $this->_SqlDetailGroupBy : "";
	}

	function SqlDetailGroupBy() { // For backward compatibility
    	return $this->getSqlDetailGroupBy();
	}

	function setSqlDetailGroupBy($v) {
    	$this->_SqlDetailGroupBy = $v;
	}
	var $_SqlDetailHaving = "";

	function getSqlDetailHaving() { // Having
		return ($this->_SqlDetailHaving <> "") ? $this->_SqlDetailHaving : "";
	}

	function SqlDetailHaving() { // For backward compatibility
    	return $this->getSqlDetailHaving();
	}

	function setSqlDetailHaving($v) {
    	$this->_SqlDetailHaving = $v;
	}
	var $_SqlDetailOrderBy = "";

	function getSqlDetailOrderBy() { // Order By
		return ($this->_SqlDetailOrderBy <> "") ? $this->_SqlDetailOrderBy : "";
	}

	function SqlDetailOrderBy() { // For backward compatibility
    	return $this->getSqlDetailOrderBy();
	}

	function setSqlDetailOrderBy($v) {
    	$this->_SqlDetailOrderBy = $v;
	}

	// Check if Anonymous User is allowed
	function AllowAnonymousUser() {
		switch (@$this->PageID) {
			case "add":
			case "register":
			case "addopt":
				return FALSE;
			case "edit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return FALSE;
			case "delete":
				return FALSE;
			case "view":
				return FALSE;
			case "search":
				return FALSE;
			default:
				return FALSE;
		}
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = EW_USER_ID_ALLOW;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Report detail SQL
	function DetailSQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = "";
		return ew_BuildSelectSql($this->getSqlDetailSelect(), $this->getSqlDetailWhere(),
			$this->getSqlDetailGroupBy(), $this->getSqlDetailHaving(),
			$this->getSqlDetailOrderBy(), $sFilter, $sSort);
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "Laporan_Bukureport.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "Laporan_Bukureport.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("", $this->UrlParm($parm));
		else
			return $this->KeyUrl("", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			return "?" . $this->UrlParm($parm);
		else
			return "";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->kode_buku->CurrentValue)) {
			$sUrl .= "kode_buku=" . urlencode($this->kode_buku->CurrentValue);
		} else {
			return "javascript:alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort());
			return ew_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET)) {
			$arKeys[] = @$_GET["kode_buku"]; // kode_buku

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		foreach ($arKeys as $key) {
			if (!is_numeric($key))
				continue;
			$ar[] = $key;
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->kode_buku->CurrentValue = $key;
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {
		global $conn;

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$Laporan_Buku_report = NULL; // Initialize page object first

class cLaporan_Buku_report extends cLaporan_Buku {

	// Page ID
	var $PageID = 'report';

	// Project ID
	var $ProjectID = "{6362B67B-391D-4CF2-AF36-D4D235D92D64}";

	// Table name
	var $TableName = 'Laporan Buku';

	// Page object name
	var $PageObjName = 'Laporan_Buku_report';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		return $PageUrl;
	}

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Custom export
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

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
		return TRUE;
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

		// Table object (Laporan_Buku)
		if (!isset($GLOBALS["Laporan_Buku"]) || get_class($GLOBALS["Laporan_Buku"]) == "cLaporan_Buku") {
			$GLOBALS["Laporan_Buku"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Laporan_Buku"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// User table object (users)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'report', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'Laporan Buku', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";
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

		// Get export parameters
		$custom = "";
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
			$custom = @$_GET["custom"];
		} elseif (@$_POST["export"] <> "") {
			$this->Export = $_POST["export"];
			$custom = @$_POST["custom"];
		}
		$gsExportFile = $this->TableVar; // Get export file, used in header

		// Get custom export parameters
		if ($this->Export <> "" && $custom <> "") {
			$this->CustomExport = $this->Export;
			$this->Export = "print";
		}
		$gsCustomExport = $this->CustomExport;
		$gsExport = $this->Export; // Get export parameter, used in header

		// Update Export URLs
		if (defined("EW_USE_PHPEXCEL"))
			$this->ExportExcelCustom = FALSE;
		if ($this->ExportExcelCustom)
			$this->ExportExcelUrl .= "&amp;custom=1";
		if (defined("EW_USE_PHPWORD"))
			$this->ExportWordCustom = FALSE;
		if ($this->ExportWordCustom)
			$this->ExportWordUrl .= "&amp;custom=1";
		if ($this->ExportPdfCustom)
			$this->ExportPdfUrl .= "&amp;custom=1";
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Setup export options
		$this->SetupExportOptions();

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
		global $EW_EXPORT_REPORT;
		if ($this->Export <> "" && array_key_exists($this->Export, $EW_EXPORT_REPORT)) {
			$sContent = ob_get_contents();
			$fn = $EW_EXPORT_REPORT[$this->Export];
			$this->$fn($sContent);
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
	var $ExportOptions; // Export options
	var $RecCnt = 0;
	var $ReportSql = "";
	var $ReportFilter = "";
	var $DefaultFilter = "";
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $MasterRecordExists;
	var $Command;
	var $DtlRecordCount;
	var $ReportGroups;
	var $ReportCounts;
	var $LevelBreak;
	var $ReportTotals;
	var $ReportMaxs;
	var $ReportMins;
	var $Recordset;
	var $DetailRecordset;
	var $RecordExists;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;
		$this->ReportGroups = &ew_InitArray(1, NULL);
		$this->ReportCounts = &ew_InitArray(1, 0);
		$this->LevelBreak = &ew_InitArray(1, FALSE);
		$this->ReportTotals = &ew_Init2DArray(1, 4, 0);
		$this->ReportMaxs = &ew_Init2DArray(1, 4, 0);
		$this->ReportMins = &ew_Init2DArray(1, 4, 0);

		// Set up Breadcrumb
		$this->SetupBreadcrumb();
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// kode_buku
		// judul
		// pengarang

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// kode_buku
			$this->kode_buku->ViewValue = $this->kode_buku->CurrentValue;
			$this->kode_buku->ViewCustomAttributes = "";

			// judul
			$this->judul->ViewValue = $this->judul->CurrentValue;
			$this->judul->ViewCustomAttributes = "";

			// pengarang
			$this->pengarang->ViewValue = $this->pengarang->CurrentValue;
			if (strval($this->pengarang->CurrentValue) <> "") {
				$sFilterWrk = "`kode_pengarang`" . ew_SearchString("=", $this->pengarang->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `kode_pengarang`, `nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `pengarang`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->pengarang->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->pengarang->ViewValue = $this->pengarang->CurrentValue;
				}
			} else {
				$this->pengarang->ViewValue = NULL;
			}
			$this->pengarang->ViewCustomAttributes = "";

			// kode_buku
			$this->kode_buku->LinkCustomAttributes = "";
			$this->kode_buku->HrefValue = "";
			$this->kode_buku->TooltipValue = "";

			// judul
			$this->judul->LinkCustomAttributes = "";
			$this->judul->HrefValue = "";
			$this->judul->TooltipValue = "";

			// pengarang
			$this->pengarang->LinkCustomAttributes = "";
			$this->pengarang->HrefValue = "";
			$this->pengarang->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\" class=\"ewExportLink ewPrint\" title=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\" class=\"ewExportLink ewExcel\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\" class=\"ewExportLink ewWord\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = TRUE;

		// Drop down button for export
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = TRUE;
		$this->ExportOptions->UseDropDownButton = TRUE;
		if ($this->ExportOptions->UseButtonGroup && ew_IsMobile())
			$this->ExportOptions->UseDropDownButton = TRUE;
		$this->ExportOptions->DropDownButtonPhrase = $Language->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide options for export
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("report", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}

	// Export report to HTML
	function ExportReportHtml($html) {

		//global $gsExportFile;
		//header('Content-Type: text/html' . (EW_CHARSET <> '' ? ';charset=' . EW_CHARSET : ''));
		//header('Content-Disposition: attachment; filename=' . $gsExportFile . '.html');
		//echo $html;

	}

	// Export report to WORD
	function ExportReportWord($html) {
		global $gsExportFile;
		header('Content-Type: application/vnd.ms-word' . (EW_CHARSET <> '' ? ';charset=' . EW_CHARSET : ''));
		header('Content-Disposition: attachment; filename=' . $gsExportFile . '.doc');
		echo $html;
	}

	// Export report to EXCEL
	function ExportReportExcel($html) {
		global $gsExportFile;
		header('Content-Type: application/vnd.ms-excel' . (EW_CHARSET <> '' ? ';charset=' . EW_CHARSET : ''));
		header('Content-Disposition: attachment; filename=' . $gsExportFile . '.xls');
		echo $html;
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
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($Laporan_Buku_report)) $Laporan_Buku_report = new cLaporan_Buku_report();

// Page init
$Laporan_Buku_report->Page_Init();

// Page main
$Laporan_Buku_report->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$Laporan_Buku_report->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($Laporan_Buku->Export == "") { ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<div class="ewToolbar">
<?php if ($Laporan_Buku->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($Laporan_Buku->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php
$Laporan_Buku_report->RecCnt = 1; // No grouping
if ($Laporan_Buku_report->DbDetailFilter <> "") {
	if ($Laporan_Buku_report->ReportFilter <> "") $Laporan_Buku_report->ReportFilter .= " AND ";
	$Laporan_Buku_report->ReportFilter .= "(" . $Laporan_Buku_report->DbDetailFilter . ")";
}

// Set up detail SQL
$Laporan_Buku->CurrentFilter = $Laporan_Buku_report->ReportFilter;
$Laporan_Buku_report->ReportSql = $Laporan_Buku->DetailSQL();

// Load recordset
$Laporan_Buku_report->Recordset = $conn->Execute($Laporan_Buku_report->ReportSql);
$Laporan_Buku_report->RecordExists = !$Laporan_Buku_report->Recordset->EOF;
?>
<?php if ($Laporan_Buku->Export == "") { ?>
<?php if ($Laporan_Buku_report->RecordExists) { ?>
<div class="ewViewExportOptions"><?php $Laporan_Buku_report->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php } ?>
<?php $Laporan_Buku_report->ShowPageHeader(); ?>
<table class="ewReportTable">
<?php

	// Get detail records
	$Laporan_Buku_report->ReportFilter = $Laporan_Buku_report->DefaultFilter;
	if ($Laporan_Buku_report->DbDetailFilter <> "") {
		if ($Laporan_Buku_report->ReportFilter <> "")
			$Laporan_Buku_report->ReportFilter .= " AND ";
		$Laporan_Buku_report->ReportFilter .= "(" . $Laporan_Buku_report->DbDetailFilter . ")";
	}
	if (!$Security->CanReport()) {
		if ($sFilter <> "") $sFilter .= " AND ";
		$sFilter .= "(0=1)";
	}

	// Set up detail SQL
	$Laporan_Buku->CurrentFilter = $Laporan_Buku_report->ReportFilter;
	$Laporan_Buku_report->ReportSql = $Laporan_Buku->DetailSQL();

	// Load detail records
	$Laporan_Buku_report->DetailRecordset = $conn->Execute($Laporan_Buku_report->ReportSql);
	$Laporan_Buku_report->DtlRecordCount = $Laporan_Buku_report->DetailRecordset->RecordCount();

	// Initialize aggregates
	if (!$Laporan_Buku_report->DetailRecordset->EOF) {
		$Laporan_Buku_report->RecCnt++;
	}
	if ($Laporan_Buku_report->RecCnt == 1) {
		$Laporan_Buku_report->ReportCounts[0] = 0;
	}
	$Laporan_Buku_report->ReportCounts[0] += $Laporan_Buku_report->DtlRecordCount;
	if ($Laporan_Buku_report->RecordExists) {
?>
	<tr>
		<td class="ewGroupHeader"><?php echo $Laporan_Buku->kode_buku->FldCaption() ?></td>
		<td class="ewGroupHeader"><?php echo $Laporan_Buku->judul->FldCaption() ?></td>
		<td class="ewGroupHeader"><?php echo $Laporan_Buku->pengarang->FldCaption() ?></td>
	</tr>
<?php
	}
	while (!$Laporan_Buku_report->DetailRecordset->EOF) {
		$Laporan_Buku->kode_buku->setDbValue($Laporan_Buku_report->DetailRecordset->fields('kode_buku'));
		$Laporan_Buku->judul->setDbValue($Laporan_Buku_report->DetailRecordset->fields('judul'));
		$Laporan_Buku->pengarang->setDbValue($Laporan_Buku_report->DetailRecordset->fields('pengarang'));

		// Render for view
		$Laporan_Buku->RowType = EW_ROWTYPE_VIEW;
		$Laporan_Buku->ResetAttrs();
		$Laporan_Buku_report->RenderRow();
?>
	<tr>
		<td<?php echo $Laporan_Buku->kode_buku->CellAttributes() ?>>
<span<?php echo $Laporan_Buku->kode_buku->ViewAttributes() ?>>
<?php echo $Laporan_Buku->kode_buku->ViewValue ?></span>
</td>
		<td<?php echo $Laporan_Buku->judul->CellAttributes() ?>>
<span<?php echo $Laporan_Buku->judul->ViewAttributes() ?>>
<?php echo $Laporan_Buku->judul->ViewValue ?></span>
</td>
		<td<?php echo $Laporan_Buku->pengarang->CellAttributes() ?>>
<span<?php echo $Laporan_Buku->pengarang->ViewAttributes() ?>>
<?php echo $Laporan_Buku->pengarang->ViewValue ?></span>
</td>
	</tr>
<?php
		$Laporan_Buku_report->DetailRecordset->MoveNext();
	}
	$Laporan_Buku_report->DetailRecordset->Close();
?>
<?php if ($Laporan_Buku_report->RecordExists) { ?>
	<tr><td colspan=3>&nbsp;<br></td></tr>
	<tr><td colspan=3 class="ewGrandSummary"><?php echo $Language->Phrase("RptGrandTotal") ?>&nbsp;(<?php echo ew_FormatNumber($Laporan_Buku_report->ReportCounts[0], 0) ?>&nbsp;<?php echo $Language->Phrase("RptDtlRec") ?>)</td></tr>
<?php } ?>
<?php if ($Laporan_Buku_report->RecordExists) { ?>
	<tr><td colspan=3>&nbsp;<br></td></tr>
<?php } else { ?>
	<tr><td><?php echo $Language->Phrase("NoRecord") ?></td></tr>
<?php } ?>
</table>
<?php
$Laporan_Buku_report->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($Laporan_Buku->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$Laporan_Buku_report->Page_Terminate();
?>
