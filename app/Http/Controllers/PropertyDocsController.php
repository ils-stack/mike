<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyDocsController extends Controller
{
    private const DOCUMENT_FOLDERS = ['images', 'pdfs', 'docs', 'excels', 'presentations'];
    private const DOCUMENT_MIMES = 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx';

    public function index()
    {
        $query = Asset::with(['user', 'assignments'])
            ->whereIn('folder', self::DOCUMENT_FOLDERS);

        if ($types = request()->input('types')) {
            $query->where(function ($q) use ($types) {
                foreach ((array) $types as $type) {
                    match ($type) {
                        'image' => $q->orWhere('folder', 'images'),
                        'pdf' => $q->orWhere('folder', 'pdfs'),
                        'word' => $q->orWhere('folder', 'docs'),
                        'excel' => $q->orWhere('folder', 'excels'),
                        'ppt' => $q->orWhere('folder', 'presentations'),
                        default => null,
                    };
                }
            });
        }

        if ($users = request()->input('users')) {
            $query->whereIn('user_id', (array) $users);
        }

        if (request()->boolean('unassigned')) {
            $query->whereDoesntHave('assignments');
        } elseif ($modules = request()->input('modules')) {
            $query->whereHas('assignments', function ($q) use ($modules) {
                $q->whereIn('module_type', (array) $modules);
            });
        }

        $assets = $query->latest()->get();

        if (request()->ajax()) {
            $assets->transform(function ($asset) {
                $asset->created_at_display = optional($asset->created_at)->format('Y-m-d H:i');
                $asset->public_url = route('documents.preview', $asset->id);
                $asset->download_url = route('documents.download', $asset->id);
                $asset->doc_type = $this->docType($asset);

                return $asset;
            });

            return response()->json(['data' => $assets]);
        }

        return view('property_docs.index');
    }

    public function upload(Request $request)
    {
        $data = $request->validate([
            'file' => 'required|file|max:51200|mimes:' . self::DOCUMENT_MIMES,
            'module_type' => 'nullable|in:property_doc,unit_doc',
            'module_id' => 'nullable|required_with:module_type|integer',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $folder = $this->folderForExtension($ext);
        $userId = auth()->id();
        $storedName = uniqid() . '.' . $ext;
        $path = "assets/user_{$userId}/{$folder}/{$storedName}";

        $file->storeAs("assets/user_{$userId}/{$folder}", $storedName, 'public');

        $asset = Asset::create([
            'user_id' => $userId,
            'folder' => $folder,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_path' => $path,
        ]);

        if (! empty($data['module_type']) && ! empty($data['module_id'])) {
            AssetAssignment::updateOrCreate([
                'asset_id' => $asset->id,
                'module_type' => $data['module_type'],
                'module_id' => $data['module_id'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'asset_id' => $asset->id,
        ]);
    }

    public function propertyDocs($propertyId)
    {
        return response()->json($this->docsFor('property_doc', (int) $propertyId));
    }

    public function unitDocs($unitId)
    {
        return response()->json($this->docsFor('unit_doc', (int) $unitId));
    }

    public function toggle(Request $request)
    {
        $data = $request->validate([
            'asset_id' => 'required|integer|exists:assets,id',
            'module_type' => 'required|in:property_doc,unit_doc',
            'module_id' => 'required|integer',
        ]);

        $asset = Asset::whereIn('folder', self::DOCUMENT_FOLDERS)->findOrFail($data['asset_id']);

        $assignment = AssetAssignment::where([
            'asset_id' => $asset->id,
            'module_type' => $data['module_type'],
            'module_id' => $data['module_id'],
        ])->first();

        if ($assignment) {
            $assignment->delete();

            return response()->json(['success' => true, 'assigned' => false]);
        }

        AssetAssignment::create([
            'asset_id' => $asset->id,
            'module_type' => $data['module_type'],
            'module_id' => $data['module_id'],
        ]);

        return response()->json(['success' => true, 'assigned' => true]);
    }

    public function destroy($id)
    {
        $asset = Asset::whereIn('folder', self::DOCUMENT_FOLDERS)->findOrFail($id);

        AssetAssignment::where('asset_id', $asset->id)->delete();

        if ($asset->file_path && Storage::disk('public')->exists($asset->file_path)) {
            Storage::disk('public')->delete($asset->file_path);
        }

        $asset->delete();

        return response()->json(['success' => true, 'message' => 'Document deleted successfully']);
    }

    public function preview($id)
    {
        $asset = Asset::whereIn('folder', self::DOCUMENT_FOLDERS)->findOrFail($id);

        abort_unless($asset->file_path && Storage::disk('public')->exists($asset->file_path), 404);

        return Storage::disk('public')->response($asset->file_path, $asset->file_name, [
            'Content-Type' => $asset->file_type ?: 'application/octet-stream',
        ]);
    }

    public function download($id)
    {
        $asset = Asset::whereIn('folder', self::DOCUMENT_FOLDERS)->findOrFail($id);

        abort_unless($asset->file_path && Storage::disk('public')->exists($asset->file_path), 404);

        return Storage::disk('public')->download($asset->file_path, $asset->file_name);
    }

    private function docsFor(string $moduleType, int $moduleId)
    {
        $assignedIds = AssetAssignment::where([
            'module_type' => $moduleType,
            'module_id' => $moduleId,
        ])->pluck('asset_id')->all();

        return Asset::with('user')
            ->whereIn('folder', self::DOCUMENT_FOLDERS)
            ->where(function ($query) use ($moduleType, $moduleId) {
                $query->whereDoesntHave('assignments')
                    ->orWhereHas('assignments', function ($assignmentQuery) use ($moduleType, $moduleId) {
                        $assignmentQuery
                            ->where('module_type', $moduleType)
                            ->where('module_id', $moduleId);
                    });
            })
            ->latest()
            ->get()
            ->map(function ($asset) use ($assignedIds) {
                $asset->public_url = route('documents.preview', $asset->id);
                $asset->download_url = route('documents.download', $asset->id);
                $asset->assigned = in_array($asset->id, $assignedIds);
                $asset->doc_type = $this->docType($asset);
                $asset->created_at_display = optional($asset->created_at)->format('Y-m-d H:i');

                return $asset;
            });
    }

    private function folderForExtension(string $ext): string
    {
        return match ($ext) {
            'jpg', 'jpeg', 'png', 'gif' => 'images',
            'pdf' => 'pdfs',
            'doc', 'docx' => 'docs',
            'xls', 'xlsx' => 'excels',
            'ppt', 'pptx' => 'presentations',
            default => 'docs',
        };
    }

    private function docType(Asset $asset): string
    {
        return match ($asset->folder) {
            'images' => 'Image',
            'pdfs' => 'PDF',
            'docs' => 'Word',
            'excels' => 'Excel',
            'presentations' => 'PowerPoint',
            default => 'Document',
        };
    }
}
