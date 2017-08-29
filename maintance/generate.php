<?php 
require_once('lib/HD.php');
$xpub = 'xpub661MyMwAqRbcGgwGvP3MbDaUKTEpppjwJZoqZLGS59ystwUKVNhbueXEwRH19nTFm9jFC2fZtgcmkj8a77de1HudQ8Uw4sdq9pA4deTMVdh';
$path = '0/0'; // 1st receiving address
// $path = '0/2'; // 3rd receiving address
// $path = '1/0'; // 1st change address
// $path = '1/1'; // 2nd change address

$hd = new HD();
$hd->set_xpub($xpub);
$address = $hd->address_from_xpub($path);

echo $address;
