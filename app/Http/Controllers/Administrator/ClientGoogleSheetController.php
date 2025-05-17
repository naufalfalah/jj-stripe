<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ClientGoogleSheetController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::select('id', 'client_name', 'agency_id', 'industry_id', 'spreadsheet_id')
            ->with(['user_agency', 'user_industry'])
            ->get();
            return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('agency', function ($row) {
                return $row->user_agency?->name;
            })
            ->addColumn('industry', function ($row) {
                return $row->user_industry?->industries;
            })->editColumn('spreadsheet', function ($row) {
                $disabled = !$row->spreadsheet_id ? 'disabled-link' : '';
                return "<a target='_blank' rel='noreferrer' href='https://docs.google.com/spreadsheets/d/$row->spreadsheet_id/edit' class='$disabled'>Preview</a>";
            })->rawColumns(['spreadsheet'])
            ->make(true);
        }
        return view('admin.client_google_sheets.index', [
            'breadcrumb_main' => 'Clients Google Sheet',
            'breadcrumb' => 'Clients Google Sheet',
        ]);
    }
}
