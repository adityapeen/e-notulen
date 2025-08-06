<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Vinkla\Hashids\Facades\Hashids;
use Google_Client;
use Google_Service_Docs;
use Google_Service_Docs_Request;
use Google_Service_Docs_BatchUpdateDocumentRequest;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

class GDocsController extends Controller
{
    protected $client;
    protected $docsService;
    protected $driveService;

    public function __construct()
    {
        $this->initializeGoogleClient();
    }

    protected function initializeGoogleClient()
    {
        // Set up the API client
        $this->client = new Google_Client();
        $this->client->setAuthConfig(config('services.google.service_account_credentials_json'));
        $this->client->setScopes(config('services.google.scopes'));
        $this->client->setAccessType('offline');

        $this->docsService = new Google_Service_Docs($this->client);
        $this->driveService = new Google_Service_Drive($this->client);
    }

    public function createNoteDocs(String $hashed_id)
    {
        $note_id = Hashids::decode($hashed_id)[0];
        $notes = Note::where('id', $note_id)->first();

        $filename = str_replace('-', '.', $notes->date) . ' ' . $notes->name;
        $template_id = sizeof($notes->agendas) == 0 ? NULL : $notes->agendas[0]->docs_template_id;
        $meta = [
            'filename' => $filename,
            'name' => $notes->name,
            'date' => $this->formatDateIndo($notes->date),
            'time' => date('H:i', strtotime($notes->start_time)).' - '.date('H:i', strtotime($notes->end_time)),
            'place' => $notes->place 
        ];
        $metadata = (object) $meta;

        $doc_id = $this->createDocumentFromTemplate($filename, $template_id, $metadata);

        $link_drive = "https://docs.google.com/document/d/" . $doc_id;
        $notes->update([
            'link_drive_notulen' => $link_drive,
            'updated_by' => auth()->user()->id,
        ]);

        return redirect()->route("home")->with('success', 'Data <strong>berhasil</strong> disimpan');
    }

    public function createDocumentFromTemplate($copyTitle = "Copy Title", $template_id = NULL, $metadata = NULL)
    {
        // Get the ID of the template document
        if($template_id == NULL)
        $template_id = env('DOCS_TEMPLATE_ID');

        // Create a new document from the template
        // $body = new Google_Service_Docs_Document();
        $copy = new Google_Service_Drive_DriveFile(array(
            'name' => $copyTitle
        ));
        $driveResponse = $this->driveService->files->copy($template_id, $copy);
        $documentCopyId = $driveResponse->id;

        $this->replaceDocsDetail($documentCopyId, $metadata);

        // printf("Created document with id: %s\n", $documentCopyId);
        $folderId = env('DOCS_FOLDER_ID');
        $this->moveFileToFolder($documentCopyId, $folderId);

        return $documentCopyId;
    }

    public function replaceDocsDetail($docs_id, $metadata)
    {
        if($metadata == NULL)
        {
            $metadata->name = 'Rapat';
            $metadata->date = 'Senin, 01 Januari 2025';
            $metadata->time = '09.00 - 12.00';
            $metadata->place = 'R. Rapat Kepala Badan Lt. 2 Gedung BPSDM';

        }

        // Create replace requests
        $requests = [
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => [
                        'text' => '{{name}}',
                        'matchCase' => true
                    ],
                    'replaceText' => $metadata->name
                ]
            ]),
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => [
                        'text' => '{{date}}',
                        'matchCase' => true
                    ],
                    'replaceText' => $metadata->date
                ]
            ]),
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => [
                        'text' => '{{time}}',
                        'matchCase' => true
                    ],
                    'replaceText' => $metadata->time
                ]
            ]),
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => [
                        'text' => '{{place}}',
                        'matchCase' => true
                    ],
                    'replaceText' => $metadata->place
                ]
            ])
        ];

        // Run batchUpdate
        $batchUpdateRequest = new Google_Service_Docs_BatchUpdateDocumentRequest([
            'requests' => $requests
        ]);

        $this->docsService->documents->batchUpdate($docs_id, $batchUpdateRequest);

    }

    function moveFileToFolder($fileId, $folderId)
    {
        try {
            $emptyFileMetadata = new Google_Service_Drive_DriveFile();
            // Retrieve the existing parents to remove
            $file = $this->driveService->files->get($fileId, array('fields' => 'parents'));
            $previousParents = join(',', $file->parents);
            // Move the file to the new folder
            $file = $this->driveService->files->update($fileId, $emptyFileMetadata, array(
                'addParents' => $folderId,
                'removeParents' => $previousParents,
                'fields' => 'id, parents'
            ));
            return $file->parents;
        } catch (Exception $e) {
            echo "Error Message: " . $e;
        }
    }

    function exportPDF(String $hashed_id)
    {
        $note_id = Hashids::decode($hashed_id)[0];
        $notes = Note::where('id', $note_id)->first();
        $url = $notes->link_drive_notulen;

        if (valid_docs_id($url)) {
            $documentId = valid_docs_id($url);
            $filename = str_replace('-', '.', $notes->date) . ' ' . $notes->name.'.pdf';
            $filename = str_replace('/', '_', $filename);
            $localPath = $this->exportDocsToPDF($documentId, $filename);

            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Description' => 'File Transfer',
                'Content-Disposition' => "attachment; filename={$filename}",
                'Content-Transfer-Encoding' => 'binary',
            ];

            $notes->update(['file_notulen'=>$filename]);

            return response()->download($localPath, $filename, $headers);
           
            // redirect()->route("admin.notes.index")->with('success', 'Data <strong>berhasil</strong> disimpan');
        } else {
            echo "Invalid Google Docs URL";
        }
    }

    function exportDocsToPDF($docs_id, $filename = "exported.pdf")
    {
        try {
            // Create a Google API client
            $client = new Google_Client();
            $client->setAuthConfig(config('services.google.service_account_credentials_json'));
            $client->addScope(Google_Service_Drive::DRIVE_READONLY);

            // Create a Google Drive service
            $service = new Google_Service_Drive($client);

            // Specify the Google Docs file ID
            $fileId = $docs_id;

            // Export the document as a PDF
            $response = $service->files->export($fileId, 'application/pdf', array('alt' => 'media'));

            // Save the PDF to a local directory
            $localPath = public_path('/notulensi/'.$filename);
            file_put_contents($localPath, $response->getBody()->getContents());

            return $localPath;
        } catch (Exception $e) {
            echo "Error Message: " . $e;
        }
    }

    function changeFilePremission($docs_id, $type = "lock")
    {

        if($type == "lock"){
            $role = "reader";
        }
        else {
            $role = "writer";
        }
        $permission = new \Google_Service_Drive_Permission([
            'type' => 'anyone',
            'role' => $role, // 'reader' allows reading, 'writer' allows editing
            'withLink' => false, // set to false to make sure it's not public
        ]);

        $this->driveService->permissions->create($docs_id, $permission);

        return "OK";
    }

    function formatDateIndo($date)
    {
    $hari = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu'
    ];

    $bulan = [
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    $timestamp = strtotime($date);
    $namaHari = $hari[date('l', $timestamp)];
    $tgl = date('d', $timestamp);
    $bln = $bulan[(int)date('m', $timestamp)];
    $thn = date('Y', $timestamp);

    return "$namaHari, $tgl $bln $thn";
    }
}
