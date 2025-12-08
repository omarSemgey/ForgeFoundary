<?php

namespace App\Src\Domains\Templates\Helpers;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use App\Src\Domains\Templates\DTOs\FileDataDTO;
use App\Src\Domains\Templates\DTOs\TemplateContextDTO;
use App\Src\Domains\Templates\DTOs\TemplateDataDTO;
use Illuminate\Support\Facades\File;
use App\Src\Core\Helpers\PathManager;

class FileGenerationManager{
    public function __construct(
        private TemplateEngineManager $templateEngineManager,
        private PathManager $pathManager,
        ){}

    private TemplateContextDTO $templateContextDTO;
    private ComponentContextDTO $componentContextDTO;

    private function loadContexts(): void{
        $this->templateContextDTO = ContextBus()->get(TemplateContextDTO::class);
        Debugger()->info("Loaded context: 'TemplateContextDTO' from the context bus");
        
        $this->componentContextDTO = ContextBus()->get(ComponentContextDTO::class);
        Debugger()->info("Loaded context: 'ComponentContextDTO' from the context bus");
    }

    public function createFile(FileDataDTO $fileData, TemplateDataDTO $templateData, string $fileContent): void{
        $this->loadContexts();
        if($fileContent === null){
            Debugger()->warning("'{$templateData->templateName}' template's content is empty. Skipping");
            return;
        }

        if($fileData->fileDisabled){
            Debugger()->warning("Skipping disabled template: '{$templateData->templateName}'");
            return;
        }

        $fileName = $fileData->fileName . '.' . $fileData->fileExtension;
        
        $fullFilePaths = $this->getFullFilePath($this->componentContextDTO->componentPath,$fileData->filePaths, $fileName);
        
        if(empty($fullFilePaths)){
            Debugger()->error("File '{$fileData->fileName}' have no valid paths; skipping");
            return;
        } 

        foreach($fullFilePaths as $fullFilePath){
            File::put($fullFilePath, $fileContent);
            Debugger()->info("File generated: '{$fileName}' from tmeplate: '{$templateData->templateName}' at path '{$fullFilePath}'");
            // Reporter()->logCreated('Files', $fileName);
        }
    }
    
    private function getFullFilePath(string $basePath, array $filePaths, string $fileName): array{
        $fullFilePaths = [];
        $fileName = NamingConventions()->apply("templates", $fileName);
        foreach($filePaths as $path) {
            $fullPath = $this->pathManager->normalizeSlashes( $basePath . '/' . $path);
            
            if (!File::exists($fullPath)) {
                if($this->templateContextDTO->templateRequireExistingDirs){
                    Debugger()->error("Path '{$fullPath}' does not exist; skipping generating file '{$fileName}' in that directory");
                    continue;
                }
                File::makeDirectory($fullPath, 0755, true); // recursive true = creates nested dirs
                Debugger()->info("Creating path '{$fullPath}' for file '{$fileName}'");
            }
            
            $fullFilePaths[] = $this->pathManager->normalizeSlashes( $fullPath . '/' . $fileName);
        }
        return $fullFilePaths;
    }
}