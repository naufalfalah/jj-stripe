<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Designer;
use App\Models\DesignerImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AssignTaskController extends Controller
{
    public function create_design(Request $request)
    {
        if (Auth::user('admin')->can('designer-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $query = Designer::query()->latest();

        if ($request->ajax()) {
            if ($search = $request->input('search')) {
                $query->whereLike(['name'], $search);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return $data->name ?? '';
                })
                ->addColumn('description', function ($data) {
                    return $data->description ?? '';
                })
                ->addColumn('image', function ($data) {
                    return '<img src="'.
                        check_file($data->image, 'user').
                        '" class="rounded-circle shadow" style="width: 50px; height: 50px;" alt="" />';
                })
                ->addColumn('action', function ($data) {
                    return view('admin.asign_task.include.action', [
                        'data' => $data,
                    ]);
                })
                ->rawColumns(['image', 'action']) // Allow HTML in columns
                ->orderColumn('DT_RowIndex', function ($query, $order) {
                    $query->orderBy('id', $order);
                })
                ->make(true);
        }

        $data = [
            'breadcrumb_main' => 'Create Designer',
            'breadcrumb' => 'Create Designer',
            'title' => 'Create Designer',
        ];

        return view('admin.asign_task.index', $data);
    }

    public function edit($id)
    {
        if (Auth::user('admin')->can('designer-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'breadcrumb_main' => 'Create Designer',
            'breadcrumb' => 'Create Designer',
            'title' => 'Create Designer',
            'edit' => Designer::hashidFind($id),
        ];

        return view('admin.asign_task.index', $data);
    }

    public function save_designer(Request $request)
    {
        if (
            Auth::user('admin')->can('designer-write') != true ||
            Auth::user('admin')->can('designer-update') != true
        ) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $isUpdate = $request->has('id') && $request->id;

            $rules = [
                'name' => 'string|max:50|required',
                'description' => 'required',
                'image' => $isUpdate
                    ? 'nullable|image|mimes:jpeg,png,jpg'
                    : 'required|image|mimes:jpeg,png,jpg',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }

            $user = $isUpdate
                ? Designer::findOrfail(hashids_decode($request->id))
                : new Designer;

            if ($isUpdate && !$user) {
                return response()->json(
                    [
                        'error' => 'Designer not found for the provided ID.',
                    ],
                    404
                );
            }

            $user->name = $request->name;
            $user->description = $request->description;

            if ($request->hasFile('image')) {
                $profile_img = uploadSingleFile(
                    $request->file('image'),
                    'uploads/designer/',
                    'png,jpeg,jpg'
                );
                if (is_array($profile_img)) {
                    return response()->json($profile_img);
                }

                if ($isUpdate && $user->image && file_exists($user->image)) {
                    @unlink($user->image);
                }

                $user->image = $profile_img;
            }

            $user->save();

            return response()->json([
                'success' => $isUpdate
                    ? 'Designer Updated Successfully'
                    : 'Designer Created Successfully',
                'redirect' => route('admin.assign_task.create_design'),
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'An error occurred: '.$e->getMessage(),
                ],
                500
            );
        }
    }

    public function delete($id)
    {
        if (Auth::user('admin')->can('designer-delete') != true) {
            abort(403, 'Unauthorized action.');
        }

        $designer = Designer::hashidFind($id);
        $designer->delete();

        return response()->json([
            'success' => 'Record Delete Successfully',
            'reload' => true,
        ]);
    }

    public function upload_images(Request $request)
    {
        if (Auth::user('admin')->can('graphic-task-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $query = DesignerImage::query()->latest();

        if ($request->ajax()) {
            if ($search = $request->input('search')) {
                $query->whereLike(['name'], $search);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return $data->name ?? '';
                })
                ->addColumn('description', function ($data) {
                    return $data->description ?? '';
                })
                ->addColumn('image', function ($data) {
                    return '<img src="'.
                        check_file($data->image, 'user').
                        '" class="rounded-circle shadow" style="width: 50px; height: 50px;" alt="" />';
                })
                ->addColumn('action', function ($data) {
                    return view('admin.asign_task.include.action', [
                        'data' => $data,
                    ]);
                })
                ->rawColumns(['image', 'action']) // Allow HTML in columns
                ->orderColumn('DT_RowIndex', function ($query, $order) {
                    $query->orderBy('id', $order);
                })
                ->make(true);
        }

        $data = [
            'breadcrumb_main' => 'Upload Images',
            'breadcrumb' => 'Upload Images',
            'title' => 'Upload Images',
            'designers' => Designer::all(),
        ];

        return view('admin.designer_images.index', $data);
    }

    public function saveDesignerImages(Request $request)
    {
        if (Auth::user('admin')->can('graphic-task-write') != true) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'designer_id' => 'required|exists:designers,id',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $fileName = $image->getClientOriginalName();

                $filePath = public_path('uploads/designer_images');
                $image->move($filePath, $fileName);
                DesignerImage::create([
                    'designer_id' => $request->designer_id,
                    'image_path' => 'uploads/designer_images/'.$fileName,
                ]);
            }

            return response()->json([
                'success' => 'Images Uploaded Successfully',
                'reload' => true,
            ]);
        }

        return response()->json(
            [
                'error' => 'An error occurred: '.$e->getMessage(),
            ],
            500
        );
    }

    public function designer_images(Request $request)
    {
        if (Auth::user('admin')->can('graphic-task-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $query = DesignerImage::where('designer_id', $request->ads_id)->get();

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('image_path', function ($data) {
                    $imageUrl = asset($data->image_path);

                    return '<img src="'.
                        $imageUrl.
                        '" alt="Image" style="width: 100px; height: auto;">';
                })
                ->addColumn('created_at', function ($data) {
                    return get_fulltime($data->created_at);
                })
                ->addColumn('action', function ($data) {
                    $deleteUrl = route(
                        'admin.assign_task.designer_image_delete',
                        $data->hashid
                    );

                    return '<div class="btn-group">
                            <a href="javascript:void(0)" class="text-danger" onclick="ajaxRequest(this)" data-url="'.
                        $deleteUrl.
                        '" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </div>';
                })
                ->filter(function ($query) {
                    $search = request()->input('search.value');
                })
                ->rawColumns(['image_path', 'action'])
                ->make(true);
        } else {
            return $query->get();
        }
    }

    public function designer_image_delete($id)
    {
        if (Auth::user('admin')->can('graphic-task-delete') != true) {
            abort(403, 'Unauthorized action.');
        }

        $designer_img = DesignerImage::hashidFind($id);
        $designer_img->delete();

        return response()->json([
            'success' => 'Record Delete Successfully',
            'reload' => true,
        ]);
    }
}
