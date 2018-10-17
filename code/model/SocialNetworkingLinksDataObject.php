<?php

namespace SunnysideUp\ShareThis;

use \Page;
use SilverStripe\Assets\Image;
use SilverStripe\Security\Permission;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\Filters\PartialMatchFilter;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataObject;

/**
 *
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: creates a list of places where people can follow you (e.g. twitter, your blog, etc...)
 *
 */
class SocialNetworkingLinksDataObject extends DataObject
{
    private static $table_name = 'SocialNetworkingLinksDataObject';

    private static $db = array(
        'URL' => 'Varchar(255)',
        'Title' => 'Varchar(255)',
        'Sort' => 'Int'
    );

    private static $casting = array(
        'Code' => 'Varchar(255)',
        'Link' => 'Varchar(255)',
        'IconHTML' => 'HTMLText'
    );

    private static $has_one = array(
        'Icon' => Image::class,
        'InternalLink' => Page::class
    );

    private static $searchable_fields = array(
        'Title' => PartialMatchFilter::class
    );

    private static $field_labels = array(
        'InternalLink' => 'Internal Link',
        'URL' => 'External Link (e.g. http://twitter.com/myname/) - will override internal link',
        'Title' => 'Title',
        'Sort' => 'Sort Index (lower numbers shown first)',
        'IconID' => 'Icon (preferably 32px X 32px)'
    );

    private static $summary_fields = array(
        'Title' => 'Title',
        'IconHTML' => 'Icon'
    );

    private static $default_sort = 'Sort ASC, Title ASC';

    private static $singular_name = 'Join Us link';

    private static $plural_name = 'Join Us links';

    public function canView($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    public function canEdit($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    public function canDelete($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    /**
     * @return String - returns the title with all non-alphanumeric + spaces removed.
     */
    public function Code()
    {
        return strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $this->Title));
    }

    public function IconHTML()
    {
        return $this->getIconHTML();
    }

    public function getIconHTML()
    {
        $icon = $this->Icon();
        if ($icon && $icon->exists()) {
            $html = $icon->ScaleHeight(32);
        } else {
            $html = DBField::create_field("HTMLText", '<img src="/' . SS_SHARETHIS_DIR . "/images/icons/{$this->Code}.png\" alt=\"{$this->Code}\"/>");
        }
        return  $html;
    }

    public function Link()
    {
        if ($this->URL) {
            return $this->URL;
        } elseif ($this->InternalLinkID) {
            $page = SiteTree::get()->byID($this->InternalLinkID);
            if ($page->exists()) {
                return $page->Link();
            }
        }
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        if ($this->ID) {
            $fields->addFieldToTab('Root.Main', LiteralField::create('Code', "<p>Code: {$this->Code()}</p>"));
            $fields->addFieldToTab('Root.Main', LiteralField::create('Link', "<p>Link: <a href=\"{$this->Link()}\">{$this->Link()}</a></p>"));
            $fields->addFieldToTab('Root.Main', LiteralField::create('Link', "<p>{$this->IconHTML()}</p>"));
        }
        $fields->removeFieldFromTab('Root.Main', 'InternalLinkID');
        $fields->addFieldToTab('Root.Main', TreeDropdownField::create('InternalLinkID', 'Internal Link', SiteTree::class), 'URL');
        return $fields;
    }
}
