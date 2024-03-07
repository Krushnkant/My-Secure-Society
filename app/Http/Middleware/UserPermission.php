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
           
            if (getUserType()==1){
                return $next($request);
            }
            elseif ($request->route()->getName() == 'admin.dashboard'){
                return $next($request);
            }
            else{
                $inner_routes = explode(",",$project_page['inner_routes']);
                if (isset($project_page['inner_routes']) && in_array($request->route()->getName(),$inner_routes)){
                    $page_id = $project_page['id'];
                    $user_permission = \App\Models\CompanyDesignationAuthority::where('company_designation_id',Auth::user()->user_type)
                        ->where(function($query) {
                            $query->where('can_view',1)
                                ->orWhere('can_add', 1)
                                ->orWhere('can_edit', 1)
                                ->orWhere('can_delete', 1)
                                ->orWhere('can_print', 1);
                        })
                        ->first();
                    if ($user_permission){
                        if ($request->route()->getName()=='admin.designation.list' && $user_permission->can_view == 0){
                            return redirect(route('admin.403_page'));
                        }
                        else if ($request->route()->getName()=='admin.designation.addorupdate' && $user_permission->can_add == 0){
                            return redirect(route('admin.403_page'));
                        }
                        else if ($request->route()->getName()=='admin.users.edit' && $user_permission->can_edit == 0){
                            return redirect(route('admin.403_page'));
                        }
                        else if ($request->route()->getName()=='admin.users.delete' && $user_permission->can_delete == 0){
                            return redirect(route('admin.403_page'));
                        }
                        else if ($request->route()->getName()=='admin.users.print' && $user_permission->can_print == 0){
                            return redirect(route('admin.403_page'));
                        }else{
                            return $next($request);
                        }
                    }else{
                        return redirect(route('admin.403_page'));
                    }
                    
                }
            }
        }

        abort(404);
    }
}
