<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Advisor;
use App\Models\ClientDetail;
use App\Models\Dependant;
use App\Models\Investor;
use App\Models\NoteTask;
use App\Models\NoteTaskList;
use App\Models\SpouseDetail;

use App\Http\Controllers\SecureRequest;

class BladeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Core / Dashboard
    |--------------------------------------------------------------------------
    */

    public function home(?string $date = null)
    {
        // try {
        //
        //     $api = new SecureRequest();
        //
        //     $params = [];
        //
        //     if ($date) {
        //         $params['date'] = "2026-02-16";
        //
        //     }
        //     $params['date'] = $date;
        //
        //     //Working
        //     // $response = $api->get('/api/accounts/totalMarketValue', $params, false);
        //     // $response = $api->get('/api/accounts/methodOfPayment', $params, false);
        //     // $response = $api->get('/api/accounts/products', $params, false);
        //
        //
        //     // $response = $api->get('/api/modelmanager/investors', $params, false);
        //
        //     // $response = $api->get('/api/advisors', $params, false);
        //     //
        //     // header('Content-Type: application/json');
        //     // echo $response;
        //     // exit;
        //
        //     // | Advisor Code | Advisor Name              |
        //     // | ------------ | ------------------------- |
        //     // | **758101**   | Lawrence Anthony Johnson  |
        //     // | **758106**   | Philippus Mulder Botha    |
        //     // | **758102**   | Annelize Kruger           |
        //     // | **758103**   | Hendrik Leon Botha        |
        //     // | **758105**   | Bernice Joana Whitehead   |
        //     // | **121351**   | Jacqueline Nina Alcock    |
        //     // | **758107**   | Andrew Pratt              |
        //     // | **758108**   | Ricardo Delfino De Agrela |
        //
        //
        //     // $response = $api->get('/api/advisors/13597296641/clientDetails?currency=ZAR&number_of_investors=all', $params, false);
        //
        //     $response = $api->get(
        //           '/api/advisors/758103/clientDetails',
        //           [
        //               'currency' => 'ZAR',
        //               'number_of_investors' => 'all'
        //           ],
        //           false
        //       );
        //
        //       header('Content-Type: application/json');
        //       echo $response;
        //       exit;
        //
        // } catch (\Exception $e) {
        //
        //     header('Content-Type: application/json');
        //     echo json_encode([
        //         'error' => $e->getMessage()
        //     ]);
        //     exit;
        // }

        return view('dashboard.home');
    }

    /*
    |--------------------------------------------------------------------------
    | Client & Personal Information
    |--------------------------------------------------------------------------
    */
    public function familyDetails(Request $request)
    {
        if ($request->advisor_id) {
            session(['advisor_id' => $request->advisor_id]);
            session(['investor_id' => ""]);
        }

        if ($request->investor_id) {
            session(['investor_id' => $request->investor_id]);
        }

        $advisors = Advisor::orderBy('advisor_name', 'asc')->get();
        $investors = collect();

        if (session('advisor_id')) {
            $advisor = Advisor::find(session('advisor_id'));

            if ($advisor) {
                $investors = Investor::where('advisor_code', $advisor->advisor_code)
                    ->orderBy('investor_name', 'asc')
                    ->get();
            }
        }

        $selectedInvestor = null;
        $clientDetail = null;
        $spouseDetail = null;
        $dependants = collect();

        if (session('investor_id')) {
            $selectedInvestor = Investor::find(session('investor_id'));

            if ($selectedInvestor) {
                $clientDetail = ClientDetail::where('investor_id', $selectedInvestor->id)->first();
                $spouseDetail = SpouseDetail::where('investor_id', $selectedInvestor->id)->first();
                $dependants = Dependant::where('investor_id', $selectedInvestor->id)
                    ->where('is_deleted', false)
                    ->orderBy('id')
                    ->get();
            }
        }

        return view('clients.family-details', compact(
            'advisors',
            'investors',
            'selectedInvestor',
            'clientDetail',
            'spouseDetail',
            'dependants'
        ));
    }

    public function storeFamilyDetails(Request $request)
    {
        $validated = $request->validate([
            'investor_id' => ['required', 'integer', 'exists:investors,id'],
            'client' => ['required', 'array'],
            'client.marital_status' => ['nullable', 'string', 'max:255'],
            'client.entity' => ['nullable', 'string', 'max:255'],
            'client.surname' => ['nullable', 'string', 'max:255'],
            'client.first_name' => ['nullable', 'string', 'max:255'],
            'client.id_number' => ['nullable', 'string', 'max:255'],
            'client.tax_number' => ['nullable', 'string', 'max:255'],
            'client.email' => ['nullable', 'email', 'max:255'],
            'client.date_of_birth' => ['nullable', 'date'],
            'client.physical_address' => ['nullable', 'string'],
            'client.postal_address' => ['nullable', 'string'],
            'client.cellular' => ['nullable', 'string', 'max:255'],
            'client.home_tel' => ['nullable', 'string', 'max:255'],
            'client.work_tel' => ['nullable', 'string', 'max:255'],
            'spouse' => ['required', 'array'],
            'spouse.marital_status' => ['nullable', 'string', 'max:255'],
            'spouse.entity' => ['nullable', 'string', 'max:255'],
            'spouse.surname' => ['nullable', 'string', 'max:255'],
            'spouse.first_name' => ['nullable', 'string', 'max:255'],
            'spouse.id_number' => ['nullable', 'string', 'max:255'],
            'spouse.tax_number' => ['nullable', 'string', 'max:255'],
            'spouse.email' => ['nullable', 'email', 'max:255'],
            'spouse.date_of_birth' => ['nullable', 'date'],
            'spouse.physical_address' => ['nullable', 'string'],
            'spouse.postal_address' => ['nullable', 'string'],
            'spouse.cellular' => ['nullable', 'string', 'max:255'],
            'spouse.home_tel' => ['nullable', 'string', 'max:255'],
            'spouse.work_tel' => ['nullable', 'string', 'max:255'],
            'dependants_json' => ['nullable', 'string'],
        ]);

        $investor = Investor::findOrFail($validated['investor_id']);
        $investorId = $investor->id;
        $clientNumber = $investor->client_number;
        $normalizeNullableFields = function (array $attributes): array {
            return collect($attributes)->map(function ($value) {
                return $value === '' ? null : $value;
            })->all();
        };

        ClientDetail::updateOrCreate(
            ['investor_id' => $investorId],
            array_merge($normalizeNullableFields($validated['client']), [
                'investor_id' => $investorId,
                'client_number' => $clientNumber,
            ])
        );

        SpouseDetail::updateOrCreate(
            ['investor_id' => $investorId],
            array_merge($normalizeNullableFields($validated['spouse']), [
                'investor_id' => $investorId,
                'client_number' => $clientNumber,
            ])
        );

        $dependantsPayload = collect(json_decode($validated['dependants_json'] ?? '[]', true));

        $dependantsPayload = $dependantsPayload
            ->filter(fn ($dependant) => is_array($dependant))
            ->map(function (array $dependant) {
                return [
                    'id' => isset($dependant['id']) ? (int) $dependant['id'] : null,
                    'first_name' => ($dependant['first_name'] ?? null) === '' ? null : ($dependant['first_name'] ?? null),
                    'surname' => ($dependant['surname'] ?? null) === '' ? null : ($dependant['surname'] ?? null),
                    'relationship' => ($dependant['relationship'] ?? null) === '' ? null : ($dependant['relationship'] ?? null),
                    'id_number' => ($dependant['id_number'] ?? null) === '' ? null : ($dependant['id_number'] ?? null),
                    'date_of_birth' => ($dependant['date_of_birth'] ?? null) === '' ? null : ($dependant['date_of_birth'] ?? null),
                    'gender' => ($dependant['gender'] ?? null) === '' ? null : ($dependant['gender'] ?? null),
                    'email' => ($dependant['email'] ?? null) === '' ? null : ($dependant['email'] ?? null),
                    'phone' => ($dependant['phone'] ?? null) === '' ? null : ($dependant['phone'] ?? null),
                    'notes' => ($dependant['notes'] ?? null) === '' ? null : ($dependant['notes'] ?? null),
                ];
            })
            ->filter(function (array $dependant) {
                return collect($dependant)
                    ->except('id')
                    ->filter(fn ($value) => !is_null($value) && $value !== '')
                    ->isNotEmpty();
            })
            ->values();

        $incomingIds = $dependantsPayload->pluck('id')->filter()->all();

        $dependantsToDelete = Dependant::where('investor_id', $investorId)
            ->where('is_deleted', false);

        if (!empty($incomingIds)) {
            $dependantsToDelete->whereNotIn('id', $incomingIds);
        }

        $dependantsToDelete->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);

        foreach ($dependantsPayload as $dependant) {
            $dependantAttributes = [
                'investor_id' => $investorId,
                'client_number' => $clientNumber,
                'first_name' => $dependant['first_name'],
                'surname' => $dependant['surname'],
                'relationship' => $dependant['relationship'],
                'id_number' => $dependant['id_number'],
                'date_of_birth' => $dependant['date_of_birth'],
                'gender' => $dependant['gender'],
                'email' => $dependant['email'],
                'phone' => $dependant['phone'],
                'notes' => $dependant['notes'],
                'is_deleted' => false,
                'deleted_at' => null,
            ];

            if (!empty($dependant['id'])) {
                Dependant::updateOrCreate(
                    [
                        'id' => $dependant['id'],
                        'investor_id' => $investorId,
                    ],
                    $dependantAttributes
                );
            } else {
                Dependant::create($dependantAttributes);
            }
        }

        session(['investor_id' => $investorId]);

        return redirect('/family-details')->with('success', 'Family details saved successfully.');
    }

    public function clientList()
    {
        return view('clients.client-list');
    }

    public function notesTasks(Request $request)
    {
        if ($request->advisor_id) {
            session(['advisor_id' => $request->advisor_id]);
            session(['investor_id' => ""]);
        }

        if ($request->investor_id) {
            session(['investor_id' => $request->investor_id]);
        }

        $advisors = Advisor::orderBy('advisor_name', 'asc')->get();
        $investors = collect();

        if (session('advisor_id')) {
            $advisor = Advisor::find(session('advisor_id'));

            if ($advisor) {
                $investors = Investor::where('advisor_code', $advisor->advisor_code)
                    ->orderBy('investor_name', 'asc')
                    ->get();
            }
        }

        $tasksQuery = NoteTask::with(['list'])
            ->where('is_deleted', false)
            ->orderByDesc('created_at');

        if (session('advisor_id')) {
            $tasksQuery->where('advisor_id', session('advisor_id'));
        }

        if (session('investor_id')) {
            $tasksQuery->where('investor_id', session('investor_id'));
        }

        $tasks = $tasksQuery->get();
        $tasksByList = $tasks->groupBy('list_id');
        $tasksByStartDate = $tasks
            ->filter(fn (NoteTask $task) => !is_null($task->start_date))
            ->groupBy(fn (NoteTask $task) => $task->start_date->format('Y-m-d'));
        $calendarTasks = $tasksByStartDate->map(function ($groupedTasks) {
            return $groupedTasks->map(function (NoteTask $task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description ?? '',
                    'list_id' => $task->list_id,
                    'start_date' => optional($task->start_date)->format('Y-m-d'),
                    'end_date' => optional($task->end_date)->format('Y-m-d'),
                ];
            })->values();
        });
        $ganttTasks = $tasks
            ->filter(fn (NoteTask $task) => !is_null($task->start_date) && !is_null($task->end_date))
            ->map(function (NoteTask $task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'list_id' => $task->list_id,
                    'start_date' => $task->start_date->format('Y-m-d'),
                    'end_date' => $task->end_date->format('Y-m-d'),
                ];
            })
            ->values();
        $noteTaskLists = NoteTaskList::orderBy('id')->get();

        return view('clients.notes-tasks', compact('advisors', 'investors', 'noteTaskLists', 'tasksByList', 'tasksByStartDate', 'calendarTasks', 'ganttTasks'));
    }

    public function storeNoteTask(Request $request)
    {
        $validated = $this->validateNoteTask($request);

        NoteTask::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $request->boolean('has_end_date') ? ($validated['end_date'] ?? null) : null,
            'advisor_id' => $validated['advisor_id'] ?? session('advisor_id'),
            'investor_id' => $validated['investor_id'] ?? session('investor_id'),
            'list_id' => $validated['list_id'],
            'is_deleted' => false,
            'created_by' => Auth::id(),
        ]);

        return redirect('/notes-tasks')->with('success', 'Note/task saved successfully.');
    }

    public function updateNoteTask(Request $request, NoteTask $noteTask)
    {
        $validated = $this->validateNoteTask($request);

        $noteTask->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $request->boolean('has_end_date') ? ($validated['end_date'] ?? null) : null,
            'advisor_id' => $validated['advisor_id'] ?? session('advisor_id'),
            'investor_id' => $validated['investor_id'] ?? session('investor_id'),
            'list_id' => $validated['list_id'],
        ]);

        return redirect('/notes-tasks')->with('success', 'Note/task updated successfully.');
    }

    public function destroyNoteTask(NoteTask $noteTask)
    {
        $noteTask->update([
            'is_deleted' => true,
        ]);

        return redirect('/notes-tasks')->with('success', 'Note/task deleted successfully.');
    }

    public function updateNoteTaskList(Request $request, NoteTask $noteTask)
    {
        $validated = $request->validate([
            'list_id' => ['required', 'integer', 'exists:notes_task_lists,id'],
        ]);

        $noteTask->update([
            'list_id' => $validated['list_id'],
        ]);

        return response()->json([
            'success' => true,
            'task_id' => $noteTask->id,
            'list_id' => $noteTask->list_id,
        ]);
    }

    protected function validateNoteTask(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'list_id' => ['required', 'integer', 'exists:notes_task_lists,id'],
            'start_date' => ['nullable', 'date'],
            'has_end_date' => ['nullable', 'boolean'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'advisor_id' => ['nullable', 'integer', 'exists:advisors,id'],
            'investor_id' => ['nullable', 'integer', 'exists:investors,id'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Financial Planning & Analysis
    |--------------------------------------------------------------------------
    */
    public function budget()
    {
        return view('finance.budget');
    }

    public function assets()
    {
        return view('finance.assets');
    }

    public function calculators()
    {
        return redirect('/short-assessments');
    }

    public function reports()
    {
        return view('finance.reports');
    }

    /*
    |--------------------------------------------------------------------------
    | Insurance, Policies & Compliance
    |--------------------------------------------------------------------------
    */
    public function policies()
    {
        return view('insurance.policies');
    }

    public function compliance()
    {
        return view('insurance.compliance');
    }

    /*
    |--------------------------------------------------------------------------
    | Documents & Digital Assets
    |--------------------------------------------------------------------------
    */
    public function dropboxAssets()
    {
        return view('documents.dropbox-assets');
    }

    public function webDocs()
    {
        return view('documents.web-docs');
    }

    public function webImages()
    {
        return view('documents.web-images');
    }

    public function pdfImport()
    {
        return view('documents.pdf-import');
    }
}
