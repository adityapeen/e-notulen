<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Vinkla\Hashids\Facades\Hashids;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

class GDocsController extends Controller
{

    public function createNoteDocs(String $hashed_id)
    {
        $note_id = Hashids::decode($hashed_id)[0];
        $notes = Note::where('id', $note_id)->first();

        $filename = str_replace('-', '.', $notes->date) . ' ' . $notes->name;
        $template_id = $notes->agenda == NULL ? NULL : $notes->agenda->docs_template_id;

        $doc_id = $this->createDocumentFromTemplate($filename, $template_id);

        $link_drive = "https://docs.google.com/document/d/" . $doc_id;
        $notes->update([
            'link_drive_notulen' => $link_drive,
            'updated_by' => auth()->user()->id,
        ]);

        return redirect()->route("home")->with('success', 'Data <strong>berhasil</strong> disimpan');
    }
    public function createDocumentFromTemplate($copyTitle = "Copy Title", $template_id = NULL)
    {
        // Set up the API client
        $client = new Google_Client();
        $client->setApplicationName('My App');
        $client->setAuthConfig(config('services.google.service_account_credentials_json'));
        $client->setScopes(config('services.google.scopes'));
        // $docs_service = new Google_Service_Docs($client);
        $driveService = new Google_Service_Drive($client);

        // Get the ID of the template document
        if($template_id == NULL)
        $template_id = env('DOCS_TEMPLATE_ID');

        // Create a new document from the template
        // $body = new Google_Service_Docs_Document();
        $copy = new Google_Service_Drive_DriveFile(array(
            'name' => $copyTitle
        ));
        $driveResponse = $driveService->files->copy($template_id, $copy);
        $documentCopyId = $driveResponse->id;

        // printf("Created document with id: %s\n", $documentCopyId);
        $folderId = env('DOCS_FOLDER_ID');
        $this->moveFileToFolder($documentCopyId, $folderId);

        return $documentCopyId;
    }

    function moveFileToFolder($fileId, $folderId)
    {
        try {
            $client = new Google_Client();
            $client->setAuthConfig(config('services.google.service_account_credentials_json'));
            $client->addScope(config('services.google.scopes'));
            $driveService = new Google_Service_Drive($client);
            $emptyFileMetadata = new Google_Service_Drive_DriveFile();
            // Retrieve the existing parents to remove
            $file = $driveService->files->get($fileId, array('fields' => 'parents'));
            $previousParents = join(',', $file->parents);
            // Move the file to the new folder
            $file = $driveService->files->update($fileId, $emptyFileMetadata, array(
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
        $regex = '/^(?:https?:\/\/)?(?:docs\.google\.com\/(?:document|spreadsheets|presentation)\/d\/|drive\.google\.com\/(?:file\/d\/|open\?id=))([a-zA-Z0-9_-]+)(?:\/[a-zA-Z0-9_-]+)?$/';

        if (preg_match($regex, $url, $matches)) {
            $documentId = $matches[1];
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
}
