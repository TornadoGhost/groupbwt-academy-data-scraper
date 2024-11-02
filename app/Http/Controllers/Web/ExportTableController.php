<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Contracts\ExportTableServiceInterface;
use Illuminate\Support\Facades\Request;

class ExportTableController extends Controller
{
    public function index()
    {
        return view('exportTables.index');
    }
}
