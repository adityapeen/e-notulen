<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Google_Client;
use Google_Service_Docs;
use Google_Service_Drive;
use Google_Service_Docs_Document;
use Google_Service_Docs_DocumentStyle;
use Google_Service_Drive_DriveFile;


class GDocsController extends Controller
{

    public function createNoteDocs(String $hashed_id)
    {
        $note_id = Hashids::decode($hashed_id)[0];
        $notes = Note::where('id',$note_id)->first();

        $filename = str_replace('-','.',$notes->date).' '.$notes->name;

        $doc_id = $this->createDocumentFromTemplate($filename);

        $link_drive = "https://docs.google.com/document/d/".$doc_id;
        $notes->update([
            'link_drive_notulen' => $link_drive,
            'updated_by' => auth()->user()->id,
        ]);

        return redirect()->route("home")->with('success','Data <strong>berhasil</strong> disimpan');
    }
    public function createDocumentFromTemplate($copyTitle = "Copy Title")
    {
        // Set up the API client
        $client = new Google_Client();
        $client->setApplicationName('My App');
        $client->setAuthConfig(config('services.google.service_account_credentials_json'));
        $client->setScopes(config('services.google.scopes'));
        // $docs_service = new Google_Service_Docs($client);
        $driveService = new Google_Service_Drive($client);

        // Get the ID of the template document
        $template_id = '12zcCTsPB1JJ7qtcsmHYih-fULfvxaG2N';

        // Create a new document from the template
        // $body = new Google_Service_Docs_Document();
        $copy = new Google_Service_Drive_DriveFile(array(
            'name' => $copyTitle
        ));
        $driveResponse = $driveService->files->copy($template_id, $copy);
        $documentCopyId = $driveResponse->id;

        // printf("Created document with id: %s\n", $documentCopyId);
        $folderId = "15jqyBLq94S3QU5fYdYhOH6BApFTWbahw";
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
}
