<?php
class NSAA_language_maker_Kulasi 
{
    public function __construct()
    {
    add_action('init', array($this, 'nsaa_language_packx'));
    }

     public function nsaa_language_packx()
    {
        load_plugin_textdomain('no-spam-at-all', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
    }

}
$nsaaStartTranslating = new NSAA_language_maker_Kulasi;
?>