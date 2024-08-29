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
 * \file    class/actions_dolikanban.class.php
 * \ingroup dolikanban
 * \brief   DoliKanban hook overload
 */

/**
 * Class ActionsDoliKanban
 */
class ActionsDoliKanban
{
    /**
     * @var string Module name
     */
    public $module = 'dolikanban';

    /**
     * @var DoliDB Database handler
     */
    public $db;

    /**
     * @var string Error string
     */
    public $error;

    /**
     * @var string[] Array of error strings
     */
    public $errors = [];

    /**
     * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
     */
    public $results = [];

    /**
     * @var string|null String displayed by executeHook() immediately after return.
     */
    public ?string $resprints;


    /**
     * Constructor
     *
     *  @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Overloading the printCommonFooter function : replacing the parent's function with the one below
     *
     * @param  array      $parameters Hook metadatas (context, etc...)
     * @return int                    0 < on error, 0 on success, 1 to replace standard code
     * @throws Exception
     */
    public function printCommonFooter(array $parameters): int
    {
        global $object;

        if (strpos($parameters['context'], 'productpricecard') !== false) {
            require_once __DIR__ . './dolikanban.class.php';

            $doliKanban = new DoliKanban($this->db);

            $doliKanbans     = $doliKanban->fetchAll('DESC', 'amount_ht', 0, 0, ['customsql' => 't.status >= ' . $doliKanban::STATUS_DRAFT . ' AND t.fk_product = ' . $object->id]);
            $lastDoliKanbans = $doliKanban->fetch('', '', ' ORDER BY t.rowid DESC');
            if (is_array($doliKanbans) && !empty($doliKanbans)) {
                $lastDoliKanban = array_shift($lastDoliKanbans);
                $maxPrices           = array_shift($doliKanbans);
                $minPrices           = end($doliKanbans);
                $pictopath           = dol_buildpath('custom/dolikanban/img/dolikanban_color.png', 1);
                $pictoDoliKanban         = img_picto('', $pictopath, '', 1, 0, 0, '', 'pictoDoliKanban');

                $out  = '<tr><td>';
                $out .= ucfirst($this->module) . ' (' . dol_print_date($lastDoliKanban->date_creation, 'day') . ')</td><td>' . $pictoDoliKanban . ' ';
                $out .= $object->price < $minPrices->amount_ht ? price($object->price, 0, '', 1, -1, -1, 'auto') . ' HT': price($minPrices->amount_ht, 0, '', 1, -1, -1, 'auto') . ' HT';
                $out .= ' <= ' . price($object->price, 0, '', 1, -1, -1, 'auto') . ' HT <= ';
                $out .= $maxPrices->amount_ht < $object->price ? price($object->price, 0, '', 1, -1, -1, 'auto') . ' HT': price($maxPrices->amount_ht, 0, '', 1, -1, -1, 'auto') . ' HT';
                $out .= '</td></tr>';
                ?>
                <script>
                    $('.field_min_price').after(<?php echo json_encode($out); ?>);
                </script>
                <?php
            }
        }
        return 0; // or return 1 to replace standard code
    }

	/**
	 * Overloading the completeTabsHead function : replacing the parent's function with the one below
	 *
	 * @param  array $parameters Hook metadatas (context, etc...)
	 * @return int               0 < on error, 0 on success, 1 to replace standard code
	 */
	public function completeTabsHead(array $parameters): int
	{
		global $langs;

		if (strpos($parameters['context'], 'main') !== false) {
			if (!empty($parameters['head'])) {
				foreach ($parameters['head'] as $headKey => $tabsHead) {
					if (is_array($tabsHead) && !empty($tabsHead)) {
						if (isset($tabsHead[2]) && $tabsHead[2] === 'dolikanban' && strpos($tabsHead[1], $langs->transnoentities('DoliKanban')) && !strpos($parameters['head'][$headKey][1], 'badge')) {
							require_once __DIR__ . '/dolikanban.class.php';

							$doliKanban  = new DoliKanban($this->db);
							$doliKanbans = $doliKanban->fetchAll('', '', 0, 0, ['customsql' => 't.status >= 0 AND t.fk_product = ' . $parameters['object']->id]);
							if (is_array($doliKanbans) && !empty($doliKanbans)) {
                                $parameters['head'][$headKey][1] .= '<span class="badge marginleftonlyshort">' . count($doliKanbans) . '</span>';
                            }
						}
					}
				}
			}

			$this->results = $parameters;
		}

		return 0; // or return 1 to replace standard code
	}
}
