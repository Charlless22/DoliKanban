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
 * \file    admin/about.php
 * \ingroup dolikanban
 * \brief   About page of module DoliKanban.
 */

// Load DoliKanban environment
if (file_exists('../dolikanban.main.inc.php')) {
    require_once __DIR__ . '/../dolikanban.main.inc.php';
} elseif (file_exists('../../dolikanban.main.inc.php')) {
    require_once __DIR__ . '/../../dolikanban.main.inc.php';
} else {
    die('Include of dolikanban main fails');
}

//DoliKanban Libraries
require_once __DIR__ . '/../lib/dolikanban.lib.php';
require_once __DIR__ . '/../core/modules/modDoliKanban.class.php';

// Global variables definitions
global $db, $langs, $user;

// Translations
$langs->loadLangs(['errors', 'admin', 'dolikanban@dolikanban']);

// Initialize technical objects
$modDoliKanban = new modDoliKanban($db);

// Parameters
$backtopage = GETPOST('backtopage', 'alpha');

// Access control
$permissiontoread = $user->rights->dolikanban->adminpage->read;
if (empty($conf->dolikanban->enabled) || !$permissiontoread) {
    accessforbidden();
}

/*
 * View
 */

$helpUrl  = 'FR:Module_DoliKanban';
$title    = $langs->trans('DoliKanbanAbout');

saturne_header(0,'', $title, $helpUrl);

// Subheader
$linkback = '<a href="'.($backtopage ?: DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans('BackToModuleList').'</a>';
print load_fiche_titre($title, $linkback, 'dolikanban_color@dolikanban');

// Configuration header
$head = dolikanbanAdminPrepareHead();
print dol_get_fiche_head($head, 'about', $title, -1, 'dolikanban_color@dolikanban');

print $modDoliKanban->getDescLong();

// Page end
print dol_get_fiche_end();
llxFooter();
$db->close();
