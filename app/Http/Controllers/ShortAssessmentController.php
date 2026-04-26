<?php

namespace App\Http\Controllers;

class ShortAssessmentController extends Controller
{
    public function estate()
    {
        return view('short_assessments.estate');
    }

    public function disability()
    {
        return view('short_assessments.disability');
    }

    public function retirement()
    {
        return view('short_assessments.retirement');
    }

    public function tax()
    {
        return view('short_assessments.tax');
    }

    public function newTax()
    {
        return view('short_assessments.tax');
    }
}
