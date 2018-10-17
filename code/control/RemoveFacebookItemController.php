<?php

namespace SunnysideUp\ShareThis;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SunnysideUp\ShareThis\FacebookFeed_Item;
use SilverStripe\Control\Controller;

/**
 * RemoveFacebookItemController
 */
class RemoveFacebookItemController extends Controller
{
    private static $allowed_actions = array(
        'remove' => 'ADMIN'
    );

    private static $url_segment = 'removefacebooklink';

    public function Link($action = null)
    {
        $urlSegment = Config::inst()->get(RemoveFacebookItemController::class, 'url_segment');
        return '/'.$urlSegment.'/'.$action;
    }

    public function remove($request)
    {
        $uid = Convert::raw2sql($request->param('ID'));
        $item = FacebookFeed_Item::get()->filter(array("UID" => $uid))->first();
        if ($item) {
            $item->Hide = true;
            $item->write();
            $this->redirect('/?flush=all');
        }
    }
}
