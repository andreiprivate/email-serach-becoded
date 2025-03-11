<?php

namespace App\Http\Controllers;

use App\Services\EmailFinderService;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index()
    {
        return view('form');
    }
    public function findEmails(Request $request, EmailFinderService $emailFinderService)
    {
        $request->validate([
            'name' => 'required|string',
            'company' => 'nullable|string',
            'linkedin_url' => 'nullable|url',
        ]);

        $emails = $emailFinderService->searchEmails($request->name, $request->company, $request->linkedin_url);


        return view('result', ['emails' => $emails]);
    }

}
