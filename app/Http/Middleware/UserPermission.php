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
            $designation_id = getUserDesignationId();
            if ($designation_id==1){
                return $next($request);
            }
            elseif ($request->route()->getName() == 'admin.dashboard'){
                return $next($request);
            }
            else{
                if($request->route()->getName()=='admin.designation.list' && is_view(1) == 0){
                    return redirect(route('admin.403_page'));
                }else if($request->route()->getName()=='admin.designation.permissiondesignation' && is_view(2) == 0){
                    return redirect(route('admin.403_page'));
                }else if($request->route()->getName()=='admin.users.list' && is_view(3) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.emergencycontact.list' && is_view(4) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.businesscategory.list' && is_view(5) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.society.list' && is_view(7) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.block.list' && is_view(8) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.flat.list' && is_view(9) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.subscriptionorder.list' && is_view(10) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.orderpayment.list' && is_view(11) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.company.profile' && is_view(12) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.servicevendor.list' && is_view(13) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.dailyhelpservice.list' && is_view(14) == 0){
                    return redirect(route('admin.403_page'));
                }else{
                    return $next($request);
                }
            }
        }

        abort(404);
    }
}
