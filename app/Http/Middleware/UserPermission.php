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
                }if($request->route()->getName()=='admin.designation.addorupdate' && (is_add(1) == 0 || is_edit(1) == 0)){
                    return redirect(route('admin.403_page'));
                }if($request->route()->getName()=='admin.designation.edit' && is_edit(1) == 0){
                    return redirect(route('admin.403_page'));
                }if($request->route()->getName()=='admin.designation.delete' && is_delete(1) == 0){
                    return redirect(route('admin.403_page'));
                }else if($request->route()->getName()=='admin.designation.permissiondesignation' && is_view(2) == 0){
                    return redirect(route('admin.403_page'));
                }else if($request->route()->getName()=='admin.designation.savepermission' && is_edit(2) == 0){
                    return redirect(route('admin.403_page'));
                }else if($request->route()->getName()=='admin.users.list' && is_view(3) == 0){
                    return redirect(route('admin.403_page'));
                }else if($request->route()->getName()=='admin.users.addorupdate' && (is_add(3) == 0 || is_edit(3) == 0)){
                    return redirect(route('admin.403_page'));
                }else if($request->route()->getName()=='admin.users.delete' && is_delete(3) == 0){
                    return redirect(route('admin.403_page'));
                }else if($request->route()->getName()=='admin.users.edit' && is_edit(3) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.emergencycontact.list' && is_view(4) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.emergencycontact.addorupdate' && (is_add(4) == 0 || is_edit(4) == 0)){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.emergencycontact.edit' && is_edit(4) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.emergencycontact.delete' && is_delete(4) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.businesscategory.list' && is_view(5) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.businesscategory.addorupdate' && (is_add(5) == 0 || is_edit(5) == 0)){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.businesscategory.edit' && is_edit(5) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.businesscategory.delete' && is_delete(5) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.society.list' && is_view(7) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.society.addorupdate' && (is_add(7) == 0 || is_edit(7) == 0)){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.society.edit' && is_edit(7) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.society.delete' && is_delete(7) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.block.list' && is_view(8) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.block.addorupdate' && (is_add(8) == 0 || is_edit(8) == 0)){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.block.edit' && is_edit(8) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.block.delete' && is_delete(8) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.flat.list' && is_view(9) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.flat.addorupdate' && (is_add(9) == 0 || is_edit(9) == 0)){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.flat.edit' && is_edit(9) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.flat.delete' && is_delete(9) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.subscriptionorder.list' && is_view(10) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.subscriptionorder.addorupdate' && (is_add(10) == 0 || is_edit(10) == 0)){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.subscriptionorder.edit' && is_edit(10) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.subscriptionorder.delete' && is_delete(10) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.orderpayment.list' && is_view(11) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.orderpayment.addorupdate' && is_add(11) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.orderpayment.delete' && is_delete(11) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.company.profile' && is_view(12) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.company.profile.update' && is_edit(12) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.servicevendor.list' && is_view(13) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.servicevendor.addorupdate' && (is_add(13) == 0 || is_edit(13) == 0)){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.servicevendor.edit' && is_edit(13) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.servicevendor.delete' && is_delete(13) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.dailyhelpservice.list' && is_view(14) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.dailyhelpservice.addorupdate' && (is_add(14) == 0 || is_edit(14) == 0)){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.dailyhelpservice.edit' && is_edit(14) == 0){
                    return redirect(route('admin.403_page'));
                }else if ($request->route()->getName()=='admin.dailyhelpservice.delete' && is_delete(14) == 0){
                    return redirect(route('admin.403_page'));
                }else{
                    return $next($request);
                }
            }
        }

        abort(404);
    }
}
