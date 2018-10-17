<?php

namespace SunnysideUp\ShareThis;

use SilverStripe\Dev\SapphireTest;

/**
 * SharethisTest
 */
class SharethisTest extends SapphireTest
{
    protected $usesDatabase = false;

    protected $requiredExtensions = array();

    public function TestDevBuild()
    {
        $exitStatus = shell_exec('php framework/cli-script.php dev/build flush=all  > dev/null; echo $?');
        $exitStatus = intval(trim($exitStatus));
        $this->assertEquals(0, $exitStatus);
    }
}
