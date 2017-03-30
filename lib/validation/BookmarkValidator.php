<?phpnamespace BtcRelax\Validation;use \Exception\NotFoundException;use \BtcRelax\Model\Bookmark;final class BookmarkValidator {	private function __construct() {	}	public static function validate(Bookmark $bookmark) {		$errors = [];		return $errors;	}	public static function validateStatus($status) {		if (!self::isValidStatus($status)) {			throw new NotFoundException('Unknown status: ' . $status);		}                return $status;	}	private static function isValidStatus($status) {		return in_array($status, Bookmark::allStatuses());	}}