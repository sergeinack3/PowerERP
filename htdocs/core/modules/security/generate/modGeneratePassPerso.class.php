<?php
/* Copyright (C) 2006-2011	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2014		Teddy Andreotti		<125155@supinfo.com>
 * Copyright (C) 2017		Regis Houssin		<regis.houssin@inodbox.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 * or see https://www.gnu.org/
 */

/**
 *      \file       htdocs/core/modules/security/generate/modGeneratePassPerso.class.php
 *      \ingroup    core
 *      \brief      File to manage no password generation.
 */

require_once DOL_DOCUMENT_ROOT.'/core/modules/security/generate/modules_genpassword.php';


/**
 *	Class to generate a password according to personal rules
 */
class modGeneratePassPerso extends ModeleGenPassword
{
	/**
	 * @var int ID
	 */
	public $id;

	public $picto = 'fa-shield-alt';

	/**
	 * Minimum length (text visible by end user)
	 *
	 * @var string
	 */
	public $length;

	/**
	 * Minimum length in number of characters
	 *
	 * @var integer
	 */
	public $length2;

	public $NbMaj;
	public $NbNum;
	public $NbSpe;
	public $NbRepeat;

	/**
	 * Flag to 1 if we must clean ambiguous charaters for the autogeneration of password (List of ambiguous char is in $this->Ambi)
	 *
	 * @var integer
	 */
	public $WithoutAmbi = 0;

	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	public $conf;
	public $lang;
	public $user;

	public $Maj;
	public $Min;
	public $Nb;
	public $Spe;
	public $Ambi;
	public $All;

	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db			Database handler
	 *	@param		Conf		$conf		Handler de conf
	 *	@param		Translate	$langs		Handler de langue
	 *	@param		User		$user		Handler du user connecte
	 */
	public function __construct($db, $conf, $langs, $user)
	{
		$this->id = "Perso";
		$this->length = $langs->trans("SetupPerso");

		$this->db = $db;
		$this->conf = $conf;
		$this->langs = $langs;
		$this->user = $user;

		if (empty($conf->global->USER_PASSWORD_PATTERN)) {
			// default value at auto generation (12 chars, 1 uppercase, 1 digit, 0 special char, 3 repeat max, exclude ambiguous characters).
			powererp_set_const($db, "USER_PASSWORD_PATTERN", '12;1;1;0;3;1', 'chaine', 0, '', $conf->entity);
		}

		$this->Maj = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$this->Min = strtolower($this->Maj);
		$this->Nb = "0123456789";
		$this->Spe = "!@#$%&*()_-+={}[]\\|:;'/";
		$this->Ambi = array("1", "I", "l", "|", "O", "0");

		$tabConf = explode(";", $conf->global->USER_PASSWORD_PATTERN);
		$this->length2 = $tabConf[0];
		$this->NbMaj = $tabConf[1];
		$this->NbNum = $tabConf[2];
		$this->NbSpe = $tabConf[3];
		$this->NbRepeat = $tabConf[4];
		$this->WithoutAmbi = $tabConf[5];
	}

	/**
	 *	Init the property ->All and clean ->Maj, ->Min, ->Nb and ->Spe with list of valid chars
	 *
	 *  @return		void
	 */
	private function initAll()
	{
		if ($this->WithoutAmbi) {
			$this->Maj = str_replace($this->Ambi, "", $this->Maj);
			$this->Min = str_replace($this->Ambi, "", $this->Min);
			$this->Nb  = str_replace($this->Ambi, "", $this->Nb);
			$this->Spe = str_replace($this->Ambi, "", $this->Spe);
		}

		$pattern = $this->Min.(!empty($this->NbMaj) ? $this->Maj : '').(!empty($this->NbNum) ? $this->Nb : '').(!empty($this->NbSpe) ? $this->Spe : '');
		$this->All = str_shuffle($pattern);
	}

	/**
	 *	Return description of module
	 *
	 *  @return     string      Description of text
	 */
	public function getDescription()
	{
		global $langs;
		return $langs->trans("PasswordGenerationPerso");
	}

	/**
	 * 	Return an example of password generated by this module
	 *
	 *  @return     string      Example of password
	 */
	public function getExample()
	{
		return $this->getNewGeneratedPassword();
	}

	/**
	 *  Build new password
	 *
	 *  @return     string      Return a new generated password
	 */
	public function getNewGeneratedPassword()
	{
		$this->initAll();

		$pass = "";
		for ($i = 0; $i < $this->NbMaj; $i++) {
			// Y
			$pass .= $this->Maj[mt_rand(0, strlen($this->Maj) - 1)];
		}

		for ($i = 0; $i < $this->NbNum; $i++) {
			// X
			$pass .= $this->Nb[mt_rand(0, strlen($this->Nb) - 1)];
		}

		for ($i = 0; $i < $this->NbSpe; $i++) {
			// @
			$pass .= $this->Spe[mt_rand(0, strlen($this->Spe) - 1)];
		}

		for ($i = strlen($pass); $i < $this->length2; $i++) {
			// y
			$pass .= $this->All[mt_rand(0, strlen($this->All) - 1)];
		}

		$pass = str_shuffle($pass);

		if ($this->validatePassword($pass)) {
			return $pass;
		}

		return $this->getNewGeneratedPassword();	// warning, may generate infinite loop if conditions are not possible
	}

	/**
	 *  Validate a password.
	 * 	This function is called by User->setPassword() and internally to validate that the password matches the constraints.
	 *
	 *  @param      string  $password   Password to check
	 *  @return     int					0 if KO, >0 if OK
	 */
	public function validatePassword($password)
	{
		global $langs;

		$this->initAll();	// For the case this method is called alone

		$password_a = preg_split('//u', $password, null, PREG_SPLIT_NO_EMPTY);
		$maj = preg_split('//u', $this->Maj, null, PREG_SPLIT_NO_EMPTY);
		$num = preg_split('//u', $this->Nb, null, PREG_SPLIT_NO_EMPTY);;
		$spe = preg_split('//u', $this->Spe, null, PREG_SPLIT_NO_EMPTY);
		/*
		$password_a = str_split($password);
		$maj = str_split($this->Maj);
		$num = str_split($this->Nb);
		$spe = str_split($this->Spe);
		*/

		if (dol_strlen($password) < $this->length2) {
			$langs->load("other");
			$this->error = $langs->trans("YourPasswordMustHaveAtLeastXChars", $this->length2);
			return 0;
		}

		if (count(array_intersect($password_a, $maj)) < $this->NbMaj) {
			$langs->load("other");
			$this->error = $langs->trans('PasswordNeedAtLeastXUpperCaseChars', $this->NbMaj);
			return 0;
		}

		if (count(array_intersect($password_a, $num)) < $this->NbNum) {
			$langs->load("other");
			$this->error = $langs->trans('PasswordNeedAtLeastXDigitChars', $this->NbNum);
			return 0;
		}

		if (count(array_intersect($password_a, $spe)) < $this->NbSpe) {
			$langs->load("other");
			$this->error = $langs->trans('PasswordNeedAtLeastXSpecialChars', $this->NbSpe);
			return 0;
		}

		if (!$this->consecutiveIterationSameCharacter($password)) {
			$langs->load("other");
			$this->error = $langs->trans('PasswordNeedNoXConsecutiveChars', $this->NbRepeat);
			return 0;
		}

		return 1;
	}

	/**
	 *  Check the consecutive iterations of the same character.
	 *
	 *  @param		string	$password	Password to check
	 *  @return     bool				False if the number doesn't match the maximum consecutive value allowed.
	 */
	public function consecutiveIterationSameCharacter($password)
	{
		$this->initAll();

		if (empty($this->NbRepeat)) {
			return true;
		}

		$char = preg_split('//u', $password, null, PREG_SPLIT_NO_EMPTY);

		$last = "";
		$count = 0;
		foreach ($char as $c) {
			if ($c != $last) {
				$last = $c;
				$count = 1;
				//print "Char $c - count = $count\n";
				continue;
			}

			$count++;
			//print "Char $c - count = $count\n";

			if ($count > $this->NbRepeat) {
				return false;
			}
		}

		return true;
	}
}
