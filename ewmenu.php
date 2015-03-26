<!-- Begin Main Menu -->
<?php $RootMenu = new cMenu(EW_MENUBAR_ID) ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(11, "mci_Master", $Language->MenuPhrase("11", "MenuText"), "", -1, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(1, "mi_anggota", $Language->MenuPhrase("1", "MenuText"), "anggotalist.php", 11, "", TRUE, FALSE);
$RootMenu->AddMenuItem(2, "mi_buku", $Language->MenuPhrase("2", "MenuText"), "bukulist.php", 11, "", TRUE, FALSE);
$RootMenu->AddMenuItem(4, "mi_pengarang", $Language->MenuPhrase("4", "MenuText"), "pengaranglist.php", 11, "", TRUE, FALSE);
$RootMenu->AddMenuItem(3, "mi_peminjaman", $Language->MenuPhrase("3", "MenuText"), "peminjamanlist.php", -1, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(12, "mci_Laporan", $Language->MenuPhrase("12", "MenuText"), "", -1, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(5, "mi_Laporan_Buku", $Language->MenuPhrase("5", "MenuText"), "Laporan_Bukureport.php", 12, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(8, "mi_Laporan_Peminjaman", $Language->MenuPhrase("8", "MenuText"), "Laporan_Peminjamanreport.php", 12, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(9, "mi_Laporan_Anggota", $Language->MenuPhrase("9", "MenuText"), "Laporan_Anggotareport.php", 12, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(26, "mci_Settings", $Language->MenuPhrase("26", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(10, "mi_users", $Language->MenuPhrase("10", "MenuText"), "userslist.php", 26, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(13, "mi_user_level", $Language->MenuPhrase("13", "MenuText"), "user_levellist.php", 26, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(14, "mi_user_level_permission", $Language->MenuPhrase("14", "MenuText"), "user_level_permissionlist.php", 26, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(-1, "mi_logout", $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, "mi_login", $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
<!-- End Main Menu -->
