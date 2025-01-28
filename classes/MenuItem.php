<?php
/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 13/04/16
 * Time: 12:18
 */

class MenuItem {
    protected $id;
    public $menuid;
    public $parent;
    public $link;
    public $link_friendly;
    public $label;
    public $label2;
    public $label3;
    public $show_label;
    public $position;
    public $permision;
    public $active;
    public $target;
    public $icon;
    public $fontawesomeicon;
    public $childs = array();

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getMenuid()
    {
        return $this->menuid;
    }

    /**
     * @param mixed $menuid
     */
    public function setMenuid($menuid)
    {
        $this->menuid = $menuid;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getLinkfriendly()
    {
        return $this->link_friendly;
    }

    /**
     * @param mixed $link
     */
    public function setLinkfriendly($link)
    {
        $this->link_friendly = $link;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getLabel2()
    {
        return $this->label2;
    }

    /**
     * @param mixed $label2
     */
    public function setLabel2($label2)
    {
        $this->label2 = $label2;
    }

    /**
     * @return mixed
     */
    public function getLabel3()
    {
        return $this->label3;
    }

    /**
     * @param mixed $label3
     */
    public function setLabel3($label3)
    {
        $this->label3 = $label3;
    }

    /**
     * @return mixed
     */
    public function getShowLabel()
    {
        return $this->show_label;
    }

    /**
     * @param mixed $show_label
     */
    public function setShowLabel($show_label)
    {
        $this->show_label = $show_label;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getPermision()
    {
        return $this->permision;
    }

    /**
     * @param mixed $permision
     */
    public function setPermision($permision)
    {
        $this->permision = $permision;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param mixed $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return mixed
     */
    public function getFontawesomeIcon()
    {
        return $this->fontawesomeicon;
    }

    /**
     * @param mixed $fontawesomeicon
     */
    public function setFontawesomeIcon($fontawesomeicon)
    {
        $this->fontawesomeicon = $fontawesomeicon;
    }

    /**
     * @return array
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * @param array $childs
     */
    public function setChilds($childs)
    {
        $this->childs = $childs;
    }


}