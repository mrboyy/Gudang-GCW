<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $query = AuditLog::with('user')->orderByDesc('created_at');

        if ($request->action)        $query->where('action', $request->action);
        if ($request->user_id)       $query->where('user_id', $request->user_id);
        if ($request->model_type)    $query->where('model_type', $request->model_type);
        if ($request->search)        $query->where('description', 'like', "%{$request->search}%");
        if ($request->tanggal_dari)  $query->whereDate('created_at', '>=', $request->tanggal_dari);
        if ($request->tanggal_sampai)$query->whereDate('created_at', '<=', $request->tanggal_sampai);

        $logs  = $query->paginate(30)->withQueryString();
        $users = User::orderBy('username')->get(['id', 'username']);

        return view('audit.index', compact('logs', 'users'));
    }
}
