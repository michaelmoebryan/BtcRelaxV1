<?php
    namespace BtcRelax;

    use BtcRelax\Dao\BookmarkSearchCriteria;
    use BtcRelax\SecureSession;
    use BtcRelax\Utils;
    use BtcRelax\Validation\BookmarkValidator;


        require('./lib/core.inc');

        $core = new \BtcRelax\Core();
        $core->init();
        $core->setBitId('1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN');
        $core->run();
        ?>