<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('notifications', function (Request $request) {
    return $request->user()->notifications()->paginate(10, ['*'], 'page', $request->page);
    // return $request->user()->notifications->map(function ($n) {
    //     return [
    //         'id' => $n->id,
    //         'created_at' => $n->created_at->diffForHumans(),
    //         'data' => $n->data,
    //         'notifiable_id' => $n->notifiable_id,
    //         'notifiable_type' => $n->notifiable_type,
    //         'type' => $n->type,
    //         'updated_at' => $n->updated_at,
    //         'read_at' => $n->read_at
    //     ];
    // });
});
Route::get('unreadNotifications', function (Request $request) {
    return $request->user()->unreadNotifications;
});
Route::get('notifications/{id}', function (Request $request, $id) {
    $notifications = $request->user()->notifications;
    foreach ($notifications as $notify) {
        if ($notify->id === $id) {
            $notify->markAsRead();
        }
    }
    return $notifications->map(function ($n) {
        return [
            'id' => $n->id,
            'created_at' => $n->created_at->diffForHumans(),
            'data' => $n->data,
            'notifiable_id' => $n->notifiable_id,
            'notifiable_type' => $n->notifiable_type,
            'type' => $n->type,
            'updated_at' => $n->updated_at,
            'read_at' => $n->read_at
        ];
    });
});
Route::get('notifications/{id}/delete', function (Request $request, $id) {
    $notifications = $request->user()->notifications;
    foreach ($notifications as $notify) {
        if ($notify->id === $id) {
            $notify->delete();
        }
    }
    return $notifications->map(function ($n) {
        return [
            'id' => $n->id,
            'created_at' => $n->created_at->diffForHumans(),
            'data' => $n->data,
            'notifiable_id' => $n->notifiable_id,
            'notifiable_type' => $n->notifiable_type,
            'type' => $n->type,
            'updated_at' => $n->updated_at,
            'read_at' => $n->read_at
        ];
    });
});
