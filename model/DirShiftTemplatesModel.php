<?php

class DirShiftTemplatesModel extends DirShiftsModel
{
    public function scheme()
    {
        $ret = parent::scheme();
        return array_merge($ret, ['is_template' => 'bool']);
    }

    public function flush()
    {
        if (
            !$this->isExists()
            && count($this->getData())
            && !$this->isDeleted()
            && !$this->isOnlyShow()
        ) {
            $this->is_template = true;
        }
        return parent::flush();
    }
}