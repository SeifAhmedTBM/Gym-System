<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\TrainerAttendant;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class TrainerAttendancesController extends Controller
{
    public function destroy(Request $request,$id)
    {
        TrainerAttendant::findOrFail($id)->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        TrainerAttendant::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
