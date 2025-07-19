<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AttachmentController extends Controller
{
    public function show(Request $request, Media $media)
    {
        $model = $media->model;

        $ticket = null;
        if ($model instanceof \App\Models\Ticket) {
            $ticket = $model;
        } elseif ($model instanceof \App\Models\Reply) {
            $ticket = $model->ticket;
        }

        if (!$ticket || Gate::denies('view', $ticket)) {
            abort(403, 'This action is unauthorized.');
        }

        if ($request->has('inline')) {
            return response()->file($media->getPath());
        }

        return response()->download($media->getPath(), $media->file_name);
    }
}
