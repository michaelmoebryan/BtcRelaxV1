<?php
require_once 'ganja_core.php';

class User 
{
	public $pIdUser;
	public $pIsOperator;
	public $pIsConfirmed;   
	public $pCreateDate;    
	public $pIsSaler;
	public $pNickname;
	public $pDefaultIncomeAddress;
	public $pIsBanned;
	public $pBanReason;
	public $pRowHash; 

	public function __construct($pUserMail) {
		try
		{
			if (GetDbConnector($dbCon))
			{
				$query = "SELECT 'idUser','isOperator','isConfirmed','CreateDate','isSaler','Nickname',
				'DefaultIncomeAddress','isBanned','BanReason','rawhash'                                    
				FROM vwUsers WHERE idUser = ?" ;
				$stmt = $dbCon->prepare($query);
				if (isset($stmt)) {
					$stmt->bind_param('i', $pUserMail );
					$stmt->execute();
					$rows_cnt = $stmt->num_rows; 
					if ($rows_cnt = 1)
					{
						$stmt->bind_result($pIdUser ,$pIsOperator , $pIsConfirmed, $pCreateDate, $pIsSaler,
							$pNickname, $pDefaultIncomeAddress, $pIsBanned, $pBanReason, $pRowHash);
						$this->pOrderId = $pIdUser ;
						$this->pIsOperator = $pIsOperator ;
						$this->pIsConfirmed = $pIsConfirmed ;
						$this->pCreateDate = $pCreateDate ;
						$this->pNickname = $pNickname;
						$this->pDefaultIncomeAddress = $pDefaultIncomeAddress;
						$this->pIsBanned = $pIsBanned;
						$this->pBanReason = $pBanReason;
						$this->pRowHash = $pRowHash;

					}
					else
					{
					    error_log('User with mail' . $pUserMail  . ' does not exists');
					}
					$stmt->close();
					//wf_log("Order ".$orderId." initialized.", 'INFO') ;
				} 
			}
		}
		catch (Exception $e )
		{
			error_log('Error while try to get from DB user:' . $pUserMail);
		};
	}


};
?>
