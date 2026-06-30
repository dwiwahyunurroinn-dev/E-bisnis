<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $log = ActivityLog::with('user')
            ->when($request->q, fn ($q) => $q->where('subjek', 'like', '%'.$request->q.'%')
                ->orWhere('deskripsi', 'like', '%'.$request->q.'%'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.log.index', compact('log'));
    }
}
