<?php
	namespace BtcRelax;
	
	use \PDO;
	use \Exception;
	use BtcRelax\Config;
	use BtcRelax\NotFoundException;
	use BtcRelax\Model\Customer;
	use BtcRelax\Mapping\CustomerMapper;
	use BtcRelax\BaseDao;
	
	class BaseDao
	{
		protected $db = null;


		public function __destruct() {
				// close db connection
				$this->db = null;
		}
		
		public function getDb() {
		if ($this->db !== null) {
				return $this->db;
			}
			$config = Config::getConfig();
			try {
				$this->db = new PDO($config['DSN'], $config['DB_USER'], $config['DB_PASS'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                        } catch (Exception $ex) {
				throw new Exception('DB connection error: ' . $ex->getMessage());
			}
			return $this->db;
		}
		
                public function addToFilter($filter,$newWhere)
                {
                    if (empty($filter))
                    {
                        return sprintf('WHERE %s', $newWhere);
                    }
                    else {
                        return sprintf('%s AND %s', $filter, $newWhere);
                    }
                }


                public function query($sql) {
			$statement = $this->getDb()->query($sql, PDO::FETCH_ASSOC);
			if ($statement === false) {
				self::throwDbError($this->getDb()->errorInfo());
			}
			return $statement;
		}
                
                public function get_numeric($val) { 
                    if (is_numeric($val)) { 
                        return $val + 0; 
                    } 
                    return 0; 
                } 
	 
		private static function throwDbError(array $errorInfo) {
			$error_message = 'DB error [' . $errorInfo[0] . ', ' . $errorInfo[1] . ']: ' . $errorInfo[2];
			throw new Exception($error_message);
		}
		
		private static function formatDateTime(DateTime $date) {
			return $date->format('Y-m-d H:i:s');
		}

		private static function formatBoolean($bool) {
				return $bool ? 1 : 0;
		}
                
                
	} 
