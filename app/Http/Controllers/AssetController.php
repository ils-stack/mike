<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use Illuminate\Support\Facades\Storage;

use App\Models\AssetAssignment;

class AssetController extends Controller
{
    // public function index()
    // {
    //     $assets = Asset::with('user')->latest()->get();
    //
    //     if (request()->ajax()) {
    //         // Format created_at for DataTable
    //         $assets->transform(function ($asset) {
    //             $asset->created_at = $asset->created_at->format('Y-m-d H:i');
    //             return $asset;
    //         });
    //
    //         return response()->json(['data' => $assets]);
    //     }
    //
    //     return view('asset.list');
    // }

    public function index()
    {
        $query = Asset::with(['user', 'assignments']);

        // Filter: Uploaded by users
        if ($users = request()->input('users')) {
            $query->whereIn('user_id', $users);
        }

        // Filter: File types
        // if ($types = request()->input('types')) {
        //     $query->whereIn('file_type', $types);
        // }

        if ($types = request()->input('types')) {
            $query->where(function ($q) use ($types) {
                foreach ($types as $type) {
                    match ($type) {
                        'image' => $q->orWhere('file_type', 'like', 'image/%'),
                        'pdf'   => $q->orWhere('file_type', 'application/pdf'),
                        'word'  => $q->orWhere('file_type', 'like', '%word%'),
                        'excel' => $q->orWhere('file_type', 'like', '%excel%')
                                       ->orWhere('file_type', 'like', '%spreadsheet%')
                                       ->orWhere('file_type', 'like', '%xls%'),
                        'ppt'   => $q->orWhere('file_type', 'like', '%presentation%')
                                       ->orWhere('file_type', 'like', '%powerpoint%')
                                       ->orWhere('file_type', 'like', '%ppt%'),
                        'csv'   => $q->orWhere('file_type', 'like', '%csv%'),
                        'other' => $q->orWhereNotLike('file_type', 'image/%')
                                       ->whereNotIn('file_type', [
                                           'application/pdf'
                                       ]),
                        default => null
                    };
                }
            });
        }


        // Filter: Unassigned (OVERRIDES module filter)
        if (request()->boolean('unassigned')) {
            $query->whereDoesntHave('assignments');
        }
        // Filter: Module
        elseif ($modules = request()->input('modules')) {
            $query->whereHas('assignments', function ($q) use ($modules) {
                $q->whereIn('module_type', $modules);
            });
        }

        $assets = $query->latest()->get();

        if (request()->ajax()) {
            $assets->transform(function ($asset) {
                $asset->created_at = $asset->created_at->format('Y-m-d H:i');
                return $asset;
            });

            return response()->json(['data' => $assets]);
        }

        return view('asset.list');
    }


    public function upload(Request $request)
    {
        // 1. Strict extensions (security) - allow AVIF
        $request->validate([
            'file' => 'required|file|max:51200|mimes:jpg,jpeg,png,gif,webp,avif,pdf,doc,docx,xls,xlsx,ppt,pptx,csv'
        ]);

        $userId = auth()->id();
        $file   = $request->file('file');

        // 2. Extension → safe mapping
        $ext = strtolower($file->getClientOriginalExtension());
        $folder = match ($ext) {
            'jpg','jpeg','png','gif','webp','avif' => 'images',
            'pdf'                                  => 'pdfs',
            'doc','docx'                           => 'docs',
            'xls','xlsx','csv'                     => 'excels',
            'ppt','pptx'                           => 'presentations',
            default                                => 'misc'
        };

        // 3. Safe filename
        $storedName = uniqid() . '.' . $ext;

        // 4. Store file
        $file->storeAs("assets/user_{$userId}/{$folder}", $storedName, 'public');

        // 5. Capture actual MIME
        $actualMime = $file->getMimeType();

        Asset::create([
            'user_id'   => $userId,
            'folder'    => $folder,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $actualMime, // show in table
            'file_path' => "assets/user_{$userId}/{$folder}/{$storedName}",
        ]);

        return response()->json(['success' => true, 'message' => 'File uploaded successfully']);
    }

    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);

        // ✅ Remove physical file
        if ($asset->file_path && Storage::disk('public')->exists($asset->file_path)) {
            Storage::disk('public')->delete($asset->file_path);
        }

        $asset->delete();

        return response()->json(['success' => true, 'message' => 'File deleted successfully']);
    }

    public function sortUnitAssets(Request $r)
{
    foreach ($r->order as $item) {
        AssetAssignment::where([
            'asset_id'    => $item['asset_id'],
            'module_type' => 'unit',
            'module_id'   => $r->unit_id,
        ])->update([
            'sort_order' => $item['sort_order']
        ]);
    }

    return response()->json(['success' => true]);
}

}
