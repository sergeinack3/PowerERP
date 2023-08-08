<?php

if (!function_exists('hasPermissionForKanbanView')) {

	function hasPermissionForKanbanView($kanbanView, $returnPermsCode = false) {
		global $user, $conf;
		$rightsOK_part1_code = '';	// rights if not use advanced perms
		$rightsOK_part2_code = '';	// rights if use advanced perms
		$rightsOK_code			 = '';	// rights 

		$rightsOK_part1	 = 0;	// rights if not use advanced perms
		$rightsOK_part2	 = 0;	// rights if use advanced perms
		$rightsOK				 = 0;	// rights 

		if (!$user->rights->kanprospects->canuse)
			return false;

		switch ($kanbanView) {
			
			// ------------------------------------ prospects
			case 'prospects':
				$rightsOK_part1_code = '$conf->kanprospects->enabled && $user->rights->kanprospects->canuse && $conf->societe->enabled && $user->rights->societe->lire && $user->rights->societe->creer';
//				$rightsOK_part2_code = '$rightsOK_part1 && $user->rights->kanprospects->kanprospects_advance->canuse_prospects';
//				$rightsOK_code			 = '((!$conf->global->MAIN_USE_ADVANCED_PERMS) && ' . $rightsOK_part1_code . ') || ($conf->global->MAIN_USE_ADVANCED_PERMS && ' . $rightsOK_part2_code . ')';
				$rightsOK_code = $rightsOK_part1_code;
				
				$rightsOK_part1	 = $conf->kanprospects->enabled && $user->rights->kanprospects->canuse && $conf->societe->enabled && $user->rights->societe->lire && $user->rights->societe->creer;
//				$rightsOK_part2	 = $rightsOK_part1 && $user->rights->kanprospects->kanprospects_advance->canuse_prospects;
//				$rightsOK				 = ((!$conf->global->MAIN_USE_ADVANCED_PERMS) && $rightsOK_part1) || ($conf->global->MAIN_USE_ADVANCED_PERMS && $rightsOK_part2);
				$rightsOK = $rightsOK_part1;
				
				if ($returnPermsCode)
					return $rightsOK_code;
				else
					return $rightsOK;
				break;

			default:
				return false;
				break;
		}
	}

}