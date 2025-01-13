<?php

class SiteModel extends ModelWithScheme
{
    public function flush()
    {
        if ($this->hasChanges()) {
            $this->last_update_time = time();
        }
        return parent::flush();
    }
}