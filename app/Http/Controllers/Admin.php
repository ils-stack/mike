<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Hash;

use App\Models\User as UserAdmin;

class Admin extends Controller
{
  public static function getScreen(){
    unset($usr_dta);
    $usr_dta = UserAdmin::where('deleted', 0)
        ->orderByDesc('alpha')   // alpha users first
        ->orderBy('role')        // Super Admin → Admin → User (alphabetical)
        ->orderBy('name')        // A–Z
        ->get([
            'id',
            'name',
            'email',
            'active',
            'alpha',
            'role',
            UserAdmin::raw("DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as created_at_format"),
            UserAdmin::raw("DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i') as updated_at_format"),
        ])
        ->toArray();

    return view('admin.admin', [
        'usr_dta' => $usr_dta
    ]);

  }

  public static function editUser($id){

    if(isset($id)){
      $user = UserAdmin::findOrFail($id);

      return view('admin.edit_user', compact('user'));
    }
  }

  public function update(Request $request, $id)
  {
    // echo 2;
    // exit;

    $user = UserAdmin::findOrFail($id);

    $user->name = $request->input('name');
    $user->email = $request->input('email');
    $user->email_verified_at = $request->input('email_verified_at') ?: null;
    $user->active = $request->input('active');
    $user->alpha = $request->input('alpha');
    echo $user->save();

    return redirect('/admin/list')->with('success', 'User updated successfully!');
  }

  public function create()
  {
      return view('admin.create');
  }

  public function store(Request $request)
  {
      $validated = $request->validate([
          'name' => 'required|string|max:255',
          'email' => 'required|email|unique:users,email',
          'email_verified_at' => 'nullable|date',
          'password' => 'required|string|min:6',
          'active' => 'required|in:0,1',
          'alpha' => 'required|in:0,1',
      ]);

      $user = new UserAdmin();
      $user->name = $validated['name'];
      $user->email = $validated['email'];
      $user->email_verified_at = $validated['email_verified_at'] ?? null;
      $user->password = Hash::make($validated['password']);
      $user->active = $validated['active'];
      $user->alpha = $validated['alpha'];
      $user->save();

      return redirect('/admin/list')->with('success', 'User created successfully!');
  }

  public function toggleActive($id)
  {
      $user = UserAdmin::where('id', $id)->first();

      if (!$user || $user->alpha == 1 || $user->deleted == 1) {
          return response()->json([
              'success' => false,
              'message' => 'User cannot be modified'
          ], 403);
      }

      $user->active = $user->active ? 0 : 1;
      $user->save();

      return response()->json([
          'success' => true,
          'active'  => $user->active
      ]);
  }

  public function updateRole(Request $request, $id)
  {
      $user = UserAdmin::where('id', $id)->first();

      if (!$user || $user->alpha == 1 || $user->deleted == 1) {
          return response()->json([
              'success' => false,
              'message' => 'Role cannot be changed'
          ], 403);
      }

      $allowed = ['Super Admin', 'Admin', 'User'];

      if (!in_array($request->role, $allowed)) {
          return response()->json([
              'success' => false,
              'message' => 'Invalid role'
          ], 422);
      }

      $user->role = $request->role;
      $user->save();

      return response()->json([
          'success' => true
      ]);
  }

  public function softDelete($id)
  {
      $user = UserAdmin::where('id', $id)->first();

      if (!$user || $user->alpha == 1 || $user->deleted == 1) {
          return response()->json([
              'success' => false,
              'message' => 'User cannot be deleted'
          ], 403);
      }

      $user->deleted = 1;
      $user->active  = 0;
      $user->save();

      return response()->json([
          'success' => true
      ]);
  }
}
