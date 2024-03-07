<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if( Auth::check() )
        {
            $designation_id = getUserDesignation();
            if ($designation_id==1){
                return $next($request);
            }
            elseif ($request->route()->getName() == 'admin.dashboard'){
                return $next($request);
            }
            else{
                $modules = getModulesArray();
                foreach($modules as $key => $module){
                    $user_permission = \App\Models\CompanyDesignationAuthority::where('company_designation_id',$designation_id)->where('eAuthority',$key)
                        ->where(function($query) {
                            $query->where('can_view',1)
                                ->orWhere('can_add', 1)
                                ->orWhere('can_edit', 1)
                                ->orWhere('can_delete', 1)
                                ->orWhere('can_print', 1);
                        })
                        ->first(); 
                    if ($user_permission){
                        if ($request->route()->getName()=='admin.designation.list' && $key == 1 && $user_permission->can_view == 2){
                            return redirect(route('admin.403_page'));
                        }
                        else if ($request->route()->getName()=='admin.designation.addorupdate' && $key == 1 && $user_permission->can_add == 2){
                            return redirect(route('admin.403_page'));
                        }
                        else if ($request->route()->getName()=='admin.usdesignationers.edit' && $key == 1 && $user_permission->can_edit == 2){
                            return redirect(route('admin.403_page'));
                        }
                        else if ($request->route()->getName()=='admin.designation.delete' && $key == 1 && $user_permission->can_delete == 2){
                            return redirect(route('admin.403_page'));
                        }
                        else if ($request->route()->getName()=='admin.designation.print' && $key == 1 && $user_permission->can_print == 2){
                            return redirect(route('admin.403_page'));
                        }else if ($request->route()->getName()=='admin.businesscategory.list' && $key == 5 && $user_permission->can_view == 2){
                            return redirect(route('admin.403_page'));
                        }
                        else if ($request->route()->getName()=='admin.businesscategory.addorupdate' && $key == 5 && $user_permission->can_add == 2){
                            return redirect(route('admin.403_page'));
                        }
                        else if ($request->route()->getName()=='admin.businesscategory.edit' && $key == 5 && $user_permission->can_edit == 2){
                            return redirect(route('admin.403_page'));
                        }
                        else if ($request->route()->getName()=='admin.businesscategory.delete' && $key == 5 && $user_permission->can_delete == 2){
                            return redirect(route('admin.403_page'));
                        }
                        else if ($request->route()->getName()=='admin.businesscategory.print' && $key == 5 && $user_permission->can_print == 2){
                            return redirect(route('admin.403_page'));
                        }
                    }
                }
                return $next($request);
            }
        }

        abort(404);
    }
}
