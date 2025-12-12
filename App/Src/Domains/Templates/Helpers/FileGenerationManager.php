<?php

namespace App\Src\Domains\Templates\Helpers;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use App\Src\Domains\Templates\DTOs\FileDataDTO;
use App\Src\Domains\Templates\DTOs\TemplateContextDTO;
use App\Src\Domains\Templates\DTOs\TemplateDataDTO;
use Illuminate\Support\Facades\File;
use App\Src\Core\Helpers\PathManager;

// ===============================================
// Class: FileGenerationManager
// Purpose: Handles creation of files based on templates.
//          - Loads context data from ContextBus (Template & Component contexts)
//          - Determines valid file paths for file generation
//          - Applies naming conventions
//          - Writes content to disk, optionally creating missing directories
// Functions:
//   - createFile(): generate file(s) from a template and file metadata
//   - getFullFilePath(): compute the absolute paths for generated files
//   - loadContexts(): loads template and component contexts from ContextBus
// ===============================================
class FileGenerationManager
{
    // Constructor
    // Inputs:
    //   - $templateEngineManager: engine that processes templates (mustache/twig/blade)
    //   - $pathManager: helper for normalizing and managing paths
    // Outputs: none
    // Purpose: inject dependencies
    public function __construct(
        private TemplateEngineManager $templateEngineManager,
        private PathManager $pathManager,
    ){}

    private TemplateContextDTO $templateContextDTO;
    private ComponentContextDTO $componentContextDTO;

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: none
    // Purpose: Loads global contexts from ContextBus for use in file generation
    // Logic:
    //   1. Fetch TemplateContextDTO
    //   2. Fetch ComponentContextDTO
    //   3. Log loading info
    // Side Effects: none
    // Uses: ContextBus(), Debugger()
    // ===============================================
    private function loadContexts(): void
    {
        $this->templateContextDTO = ContextBus()->get(TemplateContextDTO::class);
        Debugger()->info("Loaded context: 'TemplateContextDTO' from the context bus");

        $this->componentContextDTO = ContextBus()->get(ComponentContextDTO::class);
        Debugger()->info("Loaded context: 'ComponentContextDTO' from the context bus");
    }

    // ===============================================
    // Function: createFile
    // Inputs:
    //   - $fileData: metadata about the file to generate (name, extension, paths, disabled flag)
    //   - $templateData: template metadata (template name, etc.)
    //   - $fileContent: string content to write into file
    // Outputs: none
    // Purpose: Generate one or more files based on template data
    // Logic:
    //   1. Load contexts
    //   2. Skip generation if content is null
    //   3. Skip if fileData indicates file is disabled
    //   4. Build full file name
    //   5. Determine full paths using getFullFilePath()
    //   6. Write content to each valid path
    //   7. Log info for each generated file
    // Side Effects: writes files to disk, may create directories
    // Uses: loadContexts(), getFullFilePath(), Debugger(), File, NamingConventions()
    // ===============================================
    public function createFile(FileDataDTO $fileData, TemplateDataDTO $templateData, string $fileContent): void
    {
        $this->loadContexts();

        if ($fileContent === null) {
            Debugger()->warning("'{$templateData->templateName}' template's content is empty. Skipping");
            return;
        }

        if ($fileData->fileDisabled) {
            Debugger()->warning("Skipping disabled template: '{$templateData->templateName}'");
            return;
        }

        $name = NamingConventions()->apply("templates", $templateData->templateName,$fileData->fileName);

        $fileName = $name . '.' . $fileData->fileExtension;

        $fullFilePaths = $this->getFullFilePath($this->componentContextDTO->componentPath, $fileData->filePaths, $fileName);

        if (empty($fullFilePaths)) {
            Debugger()->error("File '{$fileData->fileName}' have no valid paths; skipping");
            return;
        }

        foreach ($fullFilePaths as $fullFilePath) {
            File::put($fullFilePath, $fileContent);
            Debugger()->info("File generated: '{$fileName}' from template: '{$templateData->templateName}' at path '{$fullFilePath}'");
            // Reporter()->logCreated('Files', $fileName);
        }
    }

    // ===============================================
    // Function: getFullFilePath
    // Inputs:
    //   - $basePath: base path of the component
    //   - $filePaths: array of relative paths from base where file should be generated
    //   - $fileName: the final file name with extension
    // Outputs: array of full paths where file should be generated
    // Purpose: Compute valid paths, create missing directories if allowed
    // Logic:
    //   1. Apply naming conventions to file name
    //   2. Iterate through provided file paths
    //   3. Normalize full path
    //   4. If path doesn't exist:
    //       - If template requires existing dirs, log error and skip
    //       - Else, create directory recursively and log info
    //   5. Append full path + fileName to result array
    // Side Effects: may create directories
    // Uses: File, PathManager, Debugger()
    // ===============================================
    private function getFullFilePath(string $basePath, array $filePaths, string $fileName): array
    {
        $fullFilePaths = [];
        foreach ($filePaths as $path) {
            $fullPath = $this->pathManager->normalizeSlashes($basePath . '/' . $path);

            if (!File::exists($fullPath)) {
                if ($this->templateContextDTO->templateRequireExistingDirs) {
                    Debugger()->error("Path '{$fullPath}' does not exist; skipping generating file '{$fileName}' in that directory");
                    continue;
                }
                File::makeDirectory($fullPath, 0755, true);
                Debugger()->info("Creating path '{$fullPath}' for file '{$fileName}'");
            }

            $fullFilePaths[] = $this->pathManager->normalizeSlashes($fullPath . '/' . $fileName);
        }

        return $fullFilePaths;
    }
}
