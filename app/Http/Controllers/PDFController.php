<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendant;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Response;
use Vinkla\Hashids\Facades\Hashids;

class PDFController extends Controller
{
    public function index()
    {
        $hashed_id = "2PkaqG0M3v";
        $arr_id = Hashids::decode($hashed_id);
        $note_id = $arr_id[0];
        $notes = Note::findOrFail($note_id);
        $attendants = Attendant::join('users', 'attendants.user_id', '=', 'users.id')
            ->where(['note_id' => $note_id])
            ->orderBy('attendants.created_at', 'asc')->get();
        // dd($attendants);
        // return view('pdf.daftar_hadir_ses', compact(['title', 'attendants']));

        $data = [
            'title' => "DAFTAR HADIR RAPAT",
            'notes' => $notes,
            'attendants' => $attendants
        ];

        // Pass the data to a view file
        $html = View::make('pdf.daftar_hadir_ses', $data)->render();

        // Generate the PDF using Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Return the PDF as a download response
        // return $dompdf->stream('document.pdf');
        // Return the PDF Preview
        return Response::make($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
    }
    public function generateAttendanceList(String $hashed_id)
    {
        $arr_id = Hashids::decode($hashed_id);
        if (is_array($arr_id)) {
            $note_id = $arr_id[0];
            $notes = Note::findOrFail($note_id);
            $attendants = Attendant::join('users', 'attendants.user_id', '=', 'users.id')
                ->select('attendants.*')
                ->where(['note_id' => $note_id])
                ->orderBy('attendants.created_at', 'asc')->get();

            $data = [
                'title' => "DAFTAR HADIR RAPAT",
                'notes' => $notes,
                'attendants' => $attendants
            ];

            $code = $notes->team->satker->code;
            if($code == "SBP") $view = 'pdf.daftar_hadir_ses';
            else if($code == "BPM") $view = 'pdf.daftar_hadir_bpm';
            else if($code == "BPA") $view = 'pdf.daftar_hadir_bpa';
            else if($code == "BPG") $view = 'pdf.daftar_hadir_bpg';
            else if($code == "BPE") $view = 'pdf.daftar_hadir_bpe';
            else if($code == "BPP") $view = 'pdf.daftar_hadir_bpp';
            else if($code == "BPB") $view = 'pdf.daftar_hadir_bpb';
            else if($code == "BDT") $view = 'pdf.daftar_hadir_bdt';

            // Pass the data to a view file
            $html = View::make($view, $data)->render();

            // Generate the PDF using Dompdf
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            $filename = "Daftar Hadir - ".$notes->name.".pdf";

            // Return the PDF as a download response
            // return $dompdf->stream('document.pdf');
            // Return the PDF Preview
            return Response::make($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
            ]);
        } else {
            return "Invalid IDs";
        }
    }
}
