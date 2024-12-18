<?php

class DirShiftTemplatesModel extends DirShiftsModel
{
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