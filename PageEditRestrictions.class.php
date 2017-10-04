<?php
/**
 * PageEditRestrictions
 *
 */

class PageEditRestrictions {

	static function getMsgLines($text) {
		$arr = explode("\n", $text);
		$newArr = array();
		foreach ($arr as $line) {
			$trimmed = trim($line);
			if ($trimmed == '' || $trimmed[0] == '#')
				continue;
			$newArr[] = $trimmed;
		}
		return $newArr;
	}

	public static function onUserCan($title, $user, $action, &$result) {
		$result = null;
		if ($action != 'edit' && $action != 'move')
			return true; // don't care about other actions
		if ($user->isAllowed('editrestrictedpages'))
			return true; // don't affect users that have this right
		$pages = self::getMsgLines(wfMessage('pageeditrestrictions-restricted-pages')->plain());
		$restrictedPage = false;
		$pageName = $title->getPrefixedText();
		$conditionIndices = array();
		foreach ($pages as $page) {
			$hash = strpos($page, '#');
			$idx = false;
			if ($hash !== false) {
				$idx = trim(substr($page, 0, $hash)); // add index to match the condition to
				$page = trim(substr($page, $hash + 1)); // remove everything before the # from the page to match
			}
			// delimiter is # because it's an illegal character in wikipages
			if (preg_match('#'.$page.'#', $pageName)) {
				$restrictedPage = true;
				if ($idx !== false)
					$conditionIndices[] = $idx;
			}
		}
		if (!$restrictedPage)
			return true; // page is not restricted; don't care about it.
		$conditions = self::getMsgLines(strip_tags(wfMessage('pageeditrestrictions-condition', $user->getEmail(), $title->getPrefixedText())->parse()));
		if ($user->isEmailConfirmed()) {
			foreach ($conditions as $condition) {
				$hash = strpos($condition, '#');
				if ($hash !== false) {
					$idx = trim(substr($condition, 0, $hash));
					if (!in_array($idx, $conditionIndices))
						continue; // Only apply this condition to pages that have a matching condition index assigned to it
					$condition = trim(substr($condition, $hash + 1));
				} else if (count($conditionIndices) > 0)
					continue; // condition indices have been set for this page, but this condition has none.
				if (strtolower($user->getEmail()) == strtolower(strip_tags($condition)) || // entire mail matches entire condition
					strtolower(strrchr($user->getEmail())) == strtolower(strip_tags($condition))) // mail domain matches entire condition
					return true; // user is allowed access to edit
			}
		}
		$result = false; // User not emailconfirmed or loop ended without matching
		return false;
	}
}
