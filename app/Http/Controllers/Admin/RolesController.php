<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class RolesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('role_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::where('title','!=','Developer')->with(['permissions'])->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        abort_if(Gate::denies('role_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permissions = Permission::pluck('title', 'id');
        $perms = config('permissions');
        return view('admin.roles.create', compact('permissions', 'perms'));
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->all());
        $permissions = Permission::whereIn('title', $request['permissions'])->pluck('id');
        $role->permissions()->sync($permissions);

        return redirect()->route('admin.roles.index');
    }

    public function edit(Role $role)
    {
        abort_if(Gate::denies('role_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permissions = Permission::pluck('title', 'id');
        $perms = config('permissions');
        $role->load('permissions');

        return view('admin.roles.edit', compact('permissions', 'role', 'perms'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->all());
        
        $permissions = Permission::whereIn('title', $request['permissions'])->pluck('id');
        $role->permissions()->sync($permissions);
        Alert::success(NULL, 'Updated Successfully');
        return redirect()->route('admin.roles.index');
    }

    public function show(Role $role)
    {
        abort_if(Gate::denies('role_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role->load('permissions');

        return view('admin.roles.show', compact('role'));
    }

    public function destroy(Role $role)
    {
        abort_if(Gate::denies('role_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role->delete();

        return back();
    }

    public function massDestroy(MassDestroyRoleRequest $request)
    {
        Role::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function editRoles($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::pluck('title', 'id');
        $perms = config('roles_permissions');
        $role->load('permissions');

        return view('admin.roles.edit-roles', compact('permissions', 'role', 'perms'));
    }
}
