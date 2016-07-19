<?php

namespace Vendor\Menu;

class MenuModel extends \Model {

    /**
     * @var MenuModel The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * Returns a MenuModel instance, creating it if it did not exist.
     * @return MenuModel
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }
    
    public function getMenu() {
        $parents = $this->queryIndexed('SELECT m.id, m.* FROM ' . $this->id . '_menu m WHERE id_parent IS NULL', array(), TRUE);
        $sons = $this->queryIndexed('SELECT m.id_parent, m.* FROM ' . $this->id . '_menu m WHERE id_parent IS NOT NULL', array(), FALSE);
        foreach ($parents as $key => $parent) {
            $parents[$key]['sons'] = $sons[$key];
        }
        return $parents;
    }
    
    public function getFooter() {
        $parents = $this->queryIndexed('SELECT m.id, m.* FROM ' . $this->id . '_footer m WHERE id_parent IS NULL', array(), TRUE);
        $sons = $this->queryIndexed('SELECT m.id_parent, m.* FROM ' . $this->id . '_footer m WHERE id_parent IS NOT NULL', array(), FALSE);
        foreach ($parents as $key => $parent) {
            $parents[$key]['sons'] = $sons[$key];
        }
        return $parents;
    }
}