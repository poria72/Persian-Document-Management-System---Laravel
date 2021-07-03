<?php

namespace App\Http\Controllers;

use App\CustomField;
use App\Document;
use App\File;
use App\FileType;
use App\Http\Requests\CreateDocumentRequest;
use App\Http\Requests\CreateFilesRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Repositories\CustomFieldRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\FileTypeRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\TagRepository;
use App\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Laracasts\Flash\Flash;
use Spatie\Permission\Models\Permission;

class DocumentController extends Controller
{
    // for solve persian text search
    protected function asJson($value){
        return json_encode($value,JSON_UNESCAPED_UNICODE);
    }

    /** @var  TagRepository */
    private $tagRepository;

    /** @var DocumentRepository */
    private $documentRepository;

    /** @var CustomFieldRepository */
    private $customFieldRepository;

    /** @var FileTypeRepository */
    private $fileTypeRepository;

    /** @var PermissionRepository $permissionRepository */
    private $permissionRepository;

    public function __construct(TagRepository $tagRepository,
                                DocumentRepository $documentRepository,
                                CustomFieldRepository $customFieldRepository,
                                FileTypeRepository $fileTypeRepository,
                                PermissionRepository $permissionRepository)
    {
        $this->tagRepository = $tagRepository;
        $this->documentRepository = $documentRepository;
        $this->customFieldRepository = $customFieldRepository;
        $this->fileTypeRepository = $fileTypeRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);
        $documents = $this->documentRepository->searchDocuments(
            $request->get('search'),
            $request->get('tags'),
            $request->get('status')
        );
        $tags = $this->tagRepository->all();
        return view('documents.index', compact('documents', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Document::class);
        $tags = $this->tagRepository->all();
        $customFields = $this->customFieldRepository->getForModel('documents');
        return view('documents.create', compact('tags', 'customFields'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateDocumentRequest $request)
    {
        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['status'] = config('constants.STATUS.PENDING');

        $this->authorize('store', [Document::class, $data['tags']]);

        $document = $this->documentRepository->createWithTags($data);
        Flash::success(ucfirst(config('settings.document_label_singular')) . " با موفقیت ذخیره شد");
        $document->newActivity(ucfirst(config('settings.document_label_singular')) . " ایجاد شد");

        //create permission for new document
        foreach (config('constants.DOCUMENT_LEVEL_PERMISSIONS') as $perm_key => $perm) {
            Permission::create(['name' => $perm_key . $document->id]);
        }

        if ($request->has('justsave')) {
            return redirect()->route('documents.edit', $document->id);
        }
        if ($request->has('savnup')) {
            return redirect()->route('documents.files.create', $document->id);
        }
        return redirect()->route('documents.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /** @var Document $document */
        $document = $this->documentRepository
            ->getOneEagerLoaded($id,['files', 'files.fileType', 'files.createdBy', 'activities', 'activities.createdBy', 'tags']);
        if (empty($document)) {
            abort(404);
        }
        $this->authorize('view', $document);

        $missigDocMsgs = $this->documentRepository->buildMissingDocErrors($document);

        $users = null;
        $thisDocPermissionUsers = null;
        $tagWisePermList = null;
        $globalPermissionUsers = null;
        if (auth()->user()->can('user manage permission')) {
            $users = User::where('id', '!=', 1)->get();
            $thisDocPermissionUsers = $this->permissionRepository->getUsersWiseDocumentLevelPermissionsForDoc($document);
            //Tag Level permission
            $tagWisePermList = $this->permissionRepository->getTagWiseUsersPermissionsForDoc($document);
            //Global Permission
            $globalPermissionUsers = $this->permissionRepository->getGlobalPermissionsForDoc($document);
        }
        return view('documents.show', compact('document', 'missigDocMsgs', 'users', 'thisDocPermissionUsers', 'tagWisePermList', 'globalPermissionUsers'));
    }

    public function print($id)
    {
        $doc = Document::find($id);

        $my_template = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('app/files/templates/template.docx'));
        $my_template->setValue('name', strip_tags($doc->name));
        $my_template->setValue('description', strip_tags($doc->description));
        $custom_fields = "";

        foreach ($doc->custom_fields as $key => $value) {
            $custom_fields = $custom_fields . $key . ":" . $value . "<w:br />";
        }
        $barchasb = $doc->tags->pluck('name');
        $my_template->setValue('tags', ($barchasb));
        $my_template->setValue('custom_fields', $custom_fields);
        try{
            $my_template->saveAs(storage_path('app/files/docs_onepage/doc'.$id.'.docx'));
        }catch (Exception $e){
            //handle exception
        }

        return response()->download(storage_path('app/files/docs_onepage/doc'.$id.'.docx'));
    }

    public function storePermission($id, Request $request)
    {
        abort_if(!auth()->user()->can('user manage permission'), 403, 'This action is unauthorized .');
        $input = $request->all();
        $user = User::findOrFail($input['user_id']);
        $doc_permissions = $input['document_permissions'];
        $document = Document::findOrFail($id);
        $this->permissionRepository->setDocumentLevelPermissionForUser($user,$document,$doc_permissions);
        Flash::success(ucfirst(config('settings.document_label_singular')) . " Permission allocated");
        return redirect()->back();
    }

    public function deletePermission($documentId, $userId)
    {
        abort_if(!auth()->user()->can('user manage permission'), 403, 'This action is unauthorized.');
        $user = User::findOrFail($userId);
        $document = Document::findOrFail($documentId);
        $this->permissionRepository->deleteDocumentLevelPermissionForUser($document,$user);
        Flash::success(ucfirst(config('settings.document_label_singular')) . " Permission removed");
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $document = Document::findOrFail($id);
        $this->authorize('edit', $document);
        $tags = Tag::all();
        $customFields = $this->customFieldRepository->getForModel('documents');
        return view('documents.edit', compact('tags', 'customFields', 'document'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDocumentRequest $request, $id)
    {
        $document = Document::findOrFail($id);
        $data = $request->all();
        $this->authorize('update', [$document, $data['tags']]);
        $this->documentRepository->updateWithTags($data,$document);
        $document->newActivity(ucfirst(config('settings.document_label_singular')) . " به روز رسانی شد");
        Flash::success(ucfirst(config('settings.document_label_singular')) . " با موفقیت به روز رسانی شد");
        if ($request->has('savnup')) {
                return redirect()->route('documents.files.create', $document->id);
        }
        if ($request->has('justsave')) {
            return redirect()->route('documents.edit', $document->id);
        }
        return redirect()->route('documents.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $this->authorize('delete', $document);
        $document->newActivity(ucfirst(config('settings.document_label_singular')) . " حذف شد");
        $this->documentRepository->deleteWithFiles($document,true);
        Flash::success(ucfirst(config('settings.document_label_singular')) . " با موفقیت حذف شد");
        return redirect()->route('documents.index');
    }

    public function verify($id, Request $request)
    {
        $document = Document::findOrFail($id);
        $this->authorize('verify', $document);
        $action = $request->get('action');
        $comment = $request->get('vcomment',"");
        if (!empty($comment)) {
            $comment = " with comment: <i>" . $comment . "</i>";
        }
        $msg = "";
        if ($action == 'approve') {
            $this->documentRepository->approveDoc($document);
            $msg = "پذیرفته شده";
        } elseif ($action == 'reject') {
            $this->documentRepository->rejectDoc($document);
            $msg = "رد شده";
        } else {
            abort(404);
        }
        $document->newActivity(ucfirst(config('settings.document_label_singular')) . " $msg $comment");

        Flash::success(ucfirst(config('settings.document_label_singular')) . " $msg با موفقیت");
        return redirect()->back();
    }

    public function showUploadFilesUi($id)
    {
        $document = Document::findOrFail($id);
        $this->authorize('update', [$document, $document->tags->pluck('id')]);
        $fileTypes = FileType::pluck('name', 'id');
        $customFields = $this->customFieldRepository->getForModel('files');
        return view('documents.file_upload', compact('document', 'fileTypes', 'customFields'));
    }

    public function storeFiles($id, CreateFilesRequest $request)
    {
        $document = Document::findOrFail($id);
        $this->authorize('update', [$document, $document->tags->pluck('id')]);
        $filesData = $request->all('files')['files'] ?? [];
        /* Prepare final data */
        $filesData = $this->prepareFilesData($filesData);
        $this->documentRepository->saveFilesWithDoc($filesData, $document);
        $document->newActivity(count($filesData) . " جدید " . ucfirst(config('settings.file_label_plural')) . " بارگذاری شد در " . ucfirst(config('settings.document_label_singular')));
        Flash::success(ucfirst(config('settings.file_label_plural')) . " با موفقیت بارگذاری شد");
        if (!$request->ajax()) {
            return redirect()->route('documents.show', ['id' => $document->id]);
        } else {
            return ["msg" => "Success"];
        }
    }

    private function prepareFilesData($filesData){
        $imageVariants = explode(',', config('settings.image_files_resize'));
        foreach ($filesData as $i => $fileData) {
            /** @var UploadedFile $file */
            $file = $filesData[$i]['file'];
            $filePath = $file->store('files/original');
            if (isImage($file->getMimeType())) {
                /*Image intervention resize*/
                foreach ($imageVariants as $imageVariant) {
                    $resizeSavePath = "app/files/$imageVariant/";
                    if (!file_exists(storage_path($resizeSavePath))) {
                        mkdir(storage_path($resizeSavePath), 0755, true);
                    }
                    $imageIntervention = Image::make(storage_path('app/' . $filePath));
                    $imageIntervention->resize($imageVariant, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(storage_path($resizeSavePath . $file->hashName()));
                }

                //create thumb
                $thumbPath = "app/files/thumb/";
                if (!file_exists(storage_path($thumbPath))) {
                    mkdir(storage_path($thumbPath), 0755, true);
                }
                Image::make(storage_path('app/' . $filePath))
                    ->resize(193, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(storage_path($thumbPath . $file->hashName()));
            }

            $filesData[$i]['custom_fields'] = json_encode($filesData[$i]['custom_fields'] ?? []);
            $filesData[$i]['file'] = $file->hashName();
            $filesData[$i]['created_by'] = Auth::id();
            $filesData[$i]['created_at'] = now();
            $filesData[$i]['updated_at'] = now();
        }

        return $filesData;
    }

    public function deleteFile($id)
    {
        $file = File::findOrFail($id);
        $this->authorize('delete', $file->document);
        $file->document->newActivity($file->name . " حذف شد از " . ucfirst(config('settings.document_label_singular')));
        $this->documentRepository->deleteFile($file);
        Flash::success(ucfirst(config('settings.file_label_singular')) . " با موفقیت حذف شد");
        return redirect()->back();
    }
}
