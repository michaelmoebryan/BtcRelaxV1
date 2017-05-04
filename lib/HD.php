<?php

require_once('vendor/autoload.php');

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Key\Deterministic\HierarchicalKeyFactory;
use BitWasp\Bitcoin\Key\Deterministic\HierarchicalKeySequence;
use BitWasp\Bitcoin\Key\Deterministic\MultisigHD;
use BitWasp\Bitcoin\Network\NetworkFactory;

class HD {
  private $network = NULL;
  private $xpub = NULL;
  private $multisig_xpubs = NULL;

  public function __construct($network = 'bitcoin') {
    if (version_compare(PHP_VERSION, '5.3') >= 0) {
      $this->network = NetworkFactory::$network();
    } elseif (version_compare(PHP_VERSION, '5.2.3') >= 0) {
      $this->network = call_user_func("NetworkFactory::$network");
    } else {
      $this->network = call_user_func('NetworkFactory', $network);
    }
  }

  public function set_xpub($xpub) {
    $this->xpub = $xpub;
  }

  public function set_multisig_xpubs($xpubs) {
    $this->multisig_xpubs = $xpubs;
  }

  public function address_from_xpub($path = '0/0') {
    if ($this->xpub === '') {
      throw new Exception("XPUB key is not present!");
    }

    $key = HierarchicalKeyFactory::fromExtended($this->xpub, $this->network);

    $child_key = $key->derivePath($path);
    $pub_key = $child_key->getPublicKey();

    return $pub_key->getAddress()->getAddress();
  }

  public function multisig_address_from_xpub($m, $path = '0/0') {
    if (count($this->multisig_xpubs) < 2) {
      throw new Exception("XPUB keys are not present!");
    }

    $keys = array();

    foreach ($this->multisig_xpubs as $xpub) {
      $keys[] = HierarchicalKeyFactory::fromExtended($xpub, $this->network);
    }

    $sequences = new HierarchicalKeySequence();
    $hd = new MultisigHD($m, 'm', $keys, $sequences, TRUE);

    $child_key = $hd->derivePath($path);

    return $child_key->getAddress()->getAddress();
  }
}