<?php
/* Copyright (C) 2024 Charles DELACHAPELLE <cdelachapelle4@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    admin/setup.php
 * \ingroup dolikanban
 * \brief   DoliKanban setup page.
 */

// Load DoliKanban environment
if (file_exists('../dolikanban.main.inc.php')) {
    require_once __DIR__ . '/../dolikanban.main.inc.php';
} elseif (file_exists('../../dolikanban.main.inc.php')) {
    require_once __DIR__ . '/../../dolikanban.main.inc.php';
} else {
    die('Include of dolikanban main fails');
}

// Libraries
require_once DOL_DOCUMENT_ROOT. '/core/lib/admin.lib.php';

//Load DoliKanban libraries
require_once __DIR__ . '/../lib/dolikanban.lib.php';

// Global variables definitions
global $db, $langs, $user;

// Load translation files required by the page
saturne_load_langs();

// Parameters
$backtopage = GETPOST('backtopage', 'alpha');

// Security check - Protection if external user
$permissionToRead = $user->rights->dolikanban->adminpage->read;
saturne_check_access($permissionToRead);


/*
 * View
 */

$title    = $langs->trans('ModuleSetup', 'DoliKanban');
$helpUrl = 'FR:Module_DoliKanban';

saturne_header(0,'', $title, $helpUrl);

// Subheader
$linkback = '<a href="'.($backtopage ?: DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans('BackToModuleList').'</a>';
print load_fiche_titre($title, $linkback, 'dolikanban_color@dolikanban');

// Configuration header
$head = dolikanbanAdminPrepareHead();
print dol_get_fiche_head($head, 'settings', $title, -1, 'dolikanban_color@dolikanban');

// Setup page goes here
echo '<span class="opacitymedium">'.$langs->trans('DoliKanbanSetupPage').'</span><br><br>';

// Page end
print dol_get_fiche_end();
llxFooter();
$db->close();
