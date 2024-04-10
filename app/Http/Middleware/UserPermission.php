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
                $message = "Permission denied. You do not have permission to perform this action.";
                if($request->route()->getName()=='admin.designation.list' && is_view(1) == 0){
                    return redirect(route('admin.403_page'));
                }if($request->route()->getName()=='admin.designation.add' && is_add(1) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }if($request->route()->getName()=='admin.designation.update' && is_edit(1) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }if($request->route()->getName()=='admin.designation.edit' && is_edit(1) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }if($request->route()->getName()=='admin.designation.delete' && is_delete(1) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if($request->route()->getName()=='admin.designation.permissiondesignation' && is_view(2) == 0){
                    return redirect(route('admin.403_page'));
                }else if($request->route()->getName()=='admin.designation.savepermission' && is_edit(2) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if($request->route()->getName()=='admin.users.list' && is_view(3) == 0){
                    return redirect(route('admin.403_page'));
                }else if($request->route()->getName()=='admin.users.add' && is_add(3) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if($request->route()->getName()=='admin.users.update' && is_edit(3) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if($request->route()->getName()=='admin.users.delete' && is_delete(3) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if($request->route()->getName()=='admin.users.edit' && is_edit(3) == 0){
                   return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.emergencycontact.list' && is_view(4) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.emergencycontact.add' && is_add(4) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.emergencycontact.update' && is_edit(4) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.emergencycontact.edit' && is_edit(4) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.emergencycontact.delete' && is_delete(4) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.businesscategory.list' && is_view(5) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.businesscategory.add' && is_add(5) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.businesscategory.update' && is_edit(5) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.businesscategory.edit' && is_edit(5) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.businesscategory.delete' && is_delete(5) == 0){
                   return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.society.list' && is_view(7) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.society.add' && is_add(7) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.society.update' && is_edit(7) == 0){
                   return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.society.edit' && is_edit(7) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.society.delete' && is_delete(7) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.block.list' && is_view(8) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.block.add' && is_add(8) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.block.update' && is_edit(8) == 0){
                   return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.block.edit' && is_edit(8) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.block.delete' && is_delete(8) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.flat.list' && is_view(9) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.flat.add' && is_add(9) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.flat.update' && is_edit(9) == 0){
                   return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.flat.edit' && is_edit(9) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.flat.delete' && is_delete(9) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.subscriptionorder.list' && is_view(10) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.subscriptionorder.add' && is_add(10) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.subscriptionorder.update' && is_edit(10) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.subscriptionorder.edit' && is_edit(10) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.subscriptionorder.delete' && is_delete(10) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.orderpayment.list' && is_view(11) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.orderpayment.add' && is_add(11) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.orderpayment.delete' && is_delete(11) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.company.profile' && is_view(12) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.company.profile.update' && is_edit(12) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.servicevendor.list' && is_view(13) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.servicevendor.add' && is_add(13) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.servicevendor.update' && is_edit(13) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.servicevendor.edit' && is_edit(13) == 0){
                   return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.servicevendor.delete' && is_delete(13) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.dailyhelpservice.list' && is_view(14) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.dailyhelpservice.add' && is_add(14) == 0){
                   return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.dailyhelpservice.update' && is_edit(14) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.dailyhelpservice.edit' && is_edit(14) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else if ($request->route()->getName()=='admin.dailyhelpservice.delete' && is_delete(14) == 0){
                    return response()->json(['status' => '300','message' => $message]);
                }else{
                    return $next($request);
                }
            }
        }

        abort(404);
    }
}
