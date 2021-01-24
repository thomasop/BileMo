<?php

namespace App\Handler;

class Paging
{
    public $data;
    public $meta;

    public function __construct($data)
    {
        $this->data = $data->getItems();
        $this->setMeta('limit', $data->getItemNumberPerPage());
        $this->setMeta('page', $data->getCurrentPageNumber());
        $this->setMeta('total_items', $data->getTotalItemCount());
    }

    public function setMeta($name, $value)
    {
        $this->meta[$name] = $value;
    }
}
