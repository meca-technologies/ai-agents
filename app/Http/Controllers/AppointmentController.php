<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
  public function index()
  {
    $events = [];

    $appointments = Appointment::with(['client', 'employee'])->get();

    foreach ($appointments as $appointment) {
      $events[] = [
        'title' => $appointment->client->title . ' (' . $appointment->employee->name . ')',
        'start' => $appointment->start_time,
        'end' => $appointment->finish_time,
      ];
    }
    // dd($events);

    return view('appointment', compact('events'));
  }
}
