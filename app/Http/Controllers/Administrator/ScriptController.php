<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScriptRequest;
use App\Models\Script;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ScriptController extends Controller
{
    public function index(Request $request)
    {
        // DataTables
        if ($request->ajax()) {
            $scripts = Script::all();

            return DataTables::of($scripts)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return "<a href='".route('admin.scripts.show', $row->id)."'><i class='fa-solid fa-eye'></i></a>
                    <a style='color: red; cursor-pointer' onclick='deleteScript($row->id)'><i class='fa-solid fa-trash'></i></a>
                    ";
                })
                ->rawColumns(['action', 'desc'])
                ->make(true);
        }

        return view('admin.scripts.index', [
            'breadcrumb_main' => 'Script',
            'breadcrumb' => 'Script',
        ]);
    }

    public function show($id)
    {
        $script = Script::findOrFail($id);

        return view('admin.scripts.detail', compact('script'));
    }

    public function store(ScriptRequest $request)
    {
        Script::create($request->all());

        return redirect()->route('admin.scripts.index');
    }

    public function update(ScriptRequest $request, $id)
    {
        $script = Script::find((int) $id);
        $script->update($request->all());

        return redirect()->route('admin.scripts.index');
    }

    public function destroy($id)
    {
        $script = Script::findOrFail($id);
        $script->delete();

        return response()->json(null, 204);
    }
}
