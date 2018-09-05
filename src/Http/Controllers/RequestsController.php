<?php

namespace Laravel\Telescope\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Telescope\EntryType;
use Illuminate\Routing\Controller;

class RequestsController extends EntryController
{
    /**
     * The entry type for the controller.
     *
     * @return int
     */
    protected function entryType()
    {
        return EntryType::REQUEST;
    }
}