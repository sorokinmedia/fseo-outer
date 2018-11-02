<?php
namespace FseoOuter\common\setting;

class QuietSkin extends \WP_Upgrader_Skin {
    public function feedback($string)
    {
        // just keep it quiet
    }
}