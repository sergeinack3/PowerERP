<?php

/** Includes */
dol_include_once('/chat/class/chat.class.php');

/**
 * ActionsChat class (hooks manager)
 */

class ActionsChat
{
	/**
	 * Overloading the printTopRightMenu function
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function printTopRightMenu($parameters, &$object, &$action, $hookmanager)
	{
		global $langs, $conf, $user;

		$error = 0; // Error counter

		if (in_array('toprightmenu', explode(':', $parameters['context'])))
		{
			// do something only for the context 'toprightmenu'
			if ($conf->global->REPLACE_CHAT_MAIN_MENU_WITH_TOP_RIGHT_SHORTCUT && $user->rights->chat->lire)
			{
				// Chat shortcut
				$langs->load('chat@chat');
				$text = '<a href="'.dol_buildpath('/chat/index.php', 1).'">';
				$powererp_version = explode('.', DOL_VERSION);
				if ((int)$powererp_version[0] >= 6) {
					$text.= '<span class="fa fa-comment atoplogin"></span>';
				}
				else {
					$text.= img_picto($langs->trans("Module452000Name"), 'chat-16-white@chat');
				}
				$text.= '</a>';
				$this->resprints = @Form::textwithtooltip('',$langs->trans("Module452000Name"),2,1,$text,'login_block_elem',2);
			}
		}

		if (! $error)
		{
			return 0; // or return 1 to replace standard code
		}
		else
		{
			$this->errors[] = 'Could not add chat shortcut to the top right menu';
			return -1;
		}
	}

	/**
	 * Overloading the printCommonFooter function
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function printCommonFooter($parameters, &$obj, &$action, $hookmanager)
	{
		global $conf, $db, $user, $object; // object should be global

		$error = 0; // Error counter
		$context = explode(':', $parameters['context']);

		if (in_array('main', $context) || in_array('login', $context))
		{
			$is_chat_index_page = $_SERVER['PHP_SELF'] == dol_buildpath('/chat/index.php', 1) ? true : false;

			if ($conf->global->CHAT_ENABLE_POPUP && ! empty($conf->use_javascript_ajax) && ! $is_chat_index_page && $user->rights->chat->lire)
			{
				$object = new Chat($db);

	            // récupération des messages (to populate popup)
	            $result = $object->fetch_messages($user, -1, 'popup');
	            // PS: fetch_messages() get users without checking if online (so we can't use that..)

	            if ($result)
	            {
	                // free users array
	                unset($object->users);
	                $object->users = array();

	                // fetch users
	                $result = $object->fetch_users($user, 1, '', 1);

	                // filter online users
	                foreach ($object->users as $user_rowid => $f_user) {
	                    if (! $f_user->is_online) {
	                        unset($object->users[$user_rowid]);
	                    }
	                }

	                if ($result)
	                {
	                	dol_include_once('/chat/tpl/popup.tpl.php');
	                }
	            }
	        }
		}

		if (! $error)
		{
			return 0; // or return 1 to replace standard code
		}
		else
		{
			$this->errors[] = 'Could not render chat popup';
			return -1;
		}
	}
}
