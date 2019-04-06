<?php
namespace FseoOuter\common\roles;

/**
 * Class FseoRole
 * @package FseoOuter\common\roles
 */
class FseoRole
{
    public $name; // название роли
    public $display_name; // отображаемое имя
    public $capabilities; // набор разрешений

    /**
     * FseoRole constructor. Добавляет роль с заданными параметрами
     * @param $name
     * @param $display_name
     * @param $capabilities
     */
    public function __construct($name, $display_name, $capabilities)
    {
        $this->name = $name;
        $this->display_name = $display_name;
        $this->capabilities = $capabilities;
        add_role($name,$display_name,$capabilities);
    }
}
