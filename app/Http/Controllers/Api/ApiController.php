<?php

namespace App\Http\Controllers\Api;

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    private function PaginatedData(Request $request, string $table)
    {
        $limit = (int) $request->input('limit', 500);
        $query = DB::table($table);

        if ($request->has('dateFrom')) {
            $query->where('created_at', '>=', $request->dateFrom);
        }
        if ($request->has('dateTo')) {
            $query->where('created_at', '<=', $request->dateTo);
        }

        return response()->json($query->paginate($limit));
    }

    public function orders(Request $request)
    { 
        return $this->PaginatedData($request, 'orders');
    }
    
    public function sales(Request $request)
    { 
        return $this->PaginatedData($request, 'sales');
    }

    public function incomes(Request $request)
    {
        return $this->PaginatedData($request, 'incomes');
    }

    public function stocks(Request $request)
    {
        $limit = (int) $request->input('limit', 500);
        $query = DB::table('stocks');

        if ($request->has('dateFrom')) {
            $query->whereDate('created_at', $request->dateFrom);
        } else {
            $query->whereDate('created_at', now()->toDateString());
        }

        return response()->json($query->paginate($limit));
    }
}

