<?php
	namespace BtcRelax;
	
	use BtcRelax\NotFoundException;
	use BtcRelax\Config;
	use BtcRelax\DAO;
	use BtcRelax\Model\Customer;
	use BtcRelax\Model\Bookmark;
	use BtcRelax\Validation\BookmarkValidator;
	use BtcRelax\CustomerDao ;
	use BtcRelax\Mapping\CustomerMapper;
	
final class Utils {
	
	private static $STATUS_ICONS = [
		Bookmark::STATUS_PENDING => 'event_note',
		Todo::STATUS_DONE => 'event_available',
		Todo::STATUS_VOIDED => 'event_busy',
	];

	private function __construct() {
	}

	/**
	 * Generate link.
	 * @param string $page target page
	 * @param array $params page parameters
	 */
	public static function createLink($page, array $params = []) {
		unset($params['page']);
		return 'index.php?' .http_build_query(array_merge(['page' => $page], $params));
	}

	/**
	 * Format date.
	 * @param DateTime $date date to be formatted
	 * @return string formatted date
	 */
	public static function formatDate(DateTime $date = null) {
		if ($date === null) {
			return '';
		}
		return $date->format('m/d/Y');
	}

	/**
	 * Format date and time.
	 * @param DateTime $date date to be formatted
	 * @return string formatted date and time
	 */
	public static function formatDateTime(DateTime $date = null) {
		if ($date === null) {
			return '';
		}
		return $date->format('m/d/Y H:i');
	}

	/**
	 * Returns icon for status.
	 * @param int $status status
	 * @param boolean $disabled whether to disable (change color)
	 * @param boolean $tooltip whether to show tooltip
	 * @return string icon for status
	 */
	public static function iconStatus($status, $disabled = false, $tooltip = true) {
		Bookmark::validateStatus($status);
		$title = $tooltip ?  : '';
		$icon = '<i class="material-icons ' . ($disabled ? 'disabled' : strtolower($status)) . '"';
		if ($tooltip) {
			$icon .= ' title="' . self::capitalize($status) . '"';
		}
		$icon .= '>' . self::$STATUS_ICONS[$status] . '</i>';
		return $icon;
	}

	/**
	 * Returns icon for priority.
	 * @param int $priority priority
	 * @return string icon for priority
	 */
	public static function iconPriority($priority) {
		return str_repeat(
				'<i class="material-icons multi priority" title="Priority ' . $priority . '">star</i>',
				4 - $priority);
	}
	
	/**
	 * Redirect to the given page.
	 * @param type $page target page
	 * @param array $params page parameters
	 */
	public static function redirect($page, array $params = []) {
		header('Location: ' . self::createLink($page, $params));
		die();
	}

	/**
	 * Get value of the URL param.
	 * @return string parameter value
	 * @throws NotFoundException if the param is not found in the URL
	 */
	public static function getUrlParam($name) {
		if (!array_key_exists($name, $_GET)) {
			throw new NotFoundException('URL parameter "' . $name . '" not found.');
		}
		return $_GET[$name];
	}
    
    public static function tryGetUrlParam($name) {
    if (!array_key_exists($name, $_GET)) {
            return false;
        }
        return $_GET[$name];
    }

	/**
	 * Get {@link Todo} by the identifier 'id' found in the URL.
	 * @return Todo {@link Todo} instance
	 * @throws NotFoundException if the param or {@link Todo} instance is not found
	 */
	public static function getCustomerById($custId) {
		if ($custId == null)
		{
			throw new NotFoundException('Customer not found.');
		};

		$dao = new CustomerDao();
		$cust = $dao->findById($custId);
		if ($cust === null) {
			throw new NotFoundException('Unknown customer identifier provided.');
		}
		return $cust;
	}

	/**
	 * Capitalize the first letter of the given string
	 * @param string $string string to be capitalized
	 * @return string capitalized string
	 */
	public static function capitalize($string) {
		return ucfirst(mb_strtolower($string));
	}

	/**
	 * Escape the given string
	 * @param string $string string to be escaped
	 * @return string escaped string
	 */
	public static function escape($string) {
		return htmlspecialchars($string, ENT_QUOTES);
	}

}

?>
