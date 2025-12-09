<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frontend;
use App\Models\Page;
use Illuminate\Http\Request;

class CustomSectionController extends Controller
{
    public function index()
    {
        $pageTitle = 'Custom Section Management';
        $sectionsData = getPageSections();
        $sectionsArray = get_object_vars($sectionsData);
        $sections = [];

        foreach (array_keys($sectionsArray) as $key=>$value) {
            if (strpos($value, 'custom_') !== false) {
                $sections[$value] = $sectionsArray[$value];
            }
        }

        return view('Admin::custom_section.index',compact('pageTitle', 'sections'));
    }

    public function store(Request $request, $id = null)
    {
        $request->validate([
            'name' => 'required|string',
            'title' => 'required|string',
        ]);


        $sectionName = slug(preg_replace('/[^a-zA-Z0-9_]/', '', $request->input('name')));
        $sectionTitle = $request->input('title');

        // Build the full JSON file path
        $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . '/sections/builder/builder.json';

        // Load JSON or initialize empty array if file does not exist
        $json = file_exists($jsonUrl) ? json_decode(file_get_contents($jsonUrl), true) : [];

        // Add/update the new section
        $json["custom_{$sectionName}"] = [
            "builder" => true,
            "name" => $sectionTitle,
            "content" => [
                "textarea" => "textarea-rich"
            ]
        ];

        // Save the updated JSON
        file_put_contents($jsonUrl, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // (Optional) Create matching Blade file
        $bladeFile = resource_path("views/" . str_replace('.', '/', activeTemplate()) . "/sections/custom_{$sectionName}.blade.php");
        if (!file_exists(dirname($bladeFile))) {
            mkdir(dirname($bladeFile), 0755, true);
        }

        $sectionKey = "custom_{$sectionName}";

        $bladeContent = <<<BLADE
        {{-- Auto-generated section: {$sectionTitle} --}}
        @php
          \$content = getContent('{$sectionKey}.content', true);
        @endphp

        <div class="row">
            <div class="col-lg-12">
                @php echo \$content->data_values->textarea ?? ''; @endphp
            </div>
        </div>
        BLADE;


        file_put_contents($bladeFile, $bladeContent);

        $notify[] = ['success', 'Section created successfully'];
        return back()->withNotify($notify);

    }

    public function update(Request $request, $name)
    {
        $request->validate([
            'title' => 'required|string',
        ]);

        $sectionName = preg_replace('/[^a-zA-Z0-9_]/', '', $name);
        $sectionTitle = $request->input('title');

        $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . '/sections/builder/builder.json';
        $json = file_exists($jsonUrl) ? json_decode(file_get_contents($jsonUrl), true) : [];

        if (!isset($json["$sectionName"])) {
            return back()->withErrors(['name' => 'Section does not exist.']);
        }

        $json["$sectionName"]['name'] = $sectionTitle;

        file_put_contents($jsonUrl, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $notify[] = ['success', 'Section updated successfully'];
        return back()->withNotify($notify);
    }

    public function delete($key)
    {
        $sectionKey = preg_replace('/[^a-zA-Z0-9_]/', '', $key);

        // Build the full JSON file path
        $jsonPath = resource_path('views/') . str_replace('.', '/', activeTemplate()) . '/sections/builder/builder.json';

        $pages = Page::all();
        foreach ($pages as $page) {
            $sections = is_array($page->secs) ? $page->secs : json_decode($page->secs, true);

            if (!empty($sections) && in_array($sectionKey, $sections)) {
                $filtered = array_filter($sections, fn($s) => $s !== $sectionKey);
                $page->secs = array_values($filtered); // Reindex
                $page->save();
            }
        }

        // Load JSON
        if (!file_exists($jsonPath)) {
            $notify = ['error', 'Builder file does not exist.'];
            return redirect()->back()->withNotify($notify);
        }

        $json = json_decode(file_get_contents($jsonPath), true);

        // Remove section from JSON
        if (isset($json[$sectionKey])) {
            unset($json[$sectionKey]);
            file_put_contents($jsonPath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $notify[] = ['error', 'Section not found.'];
            return redirect()->back()->withNotify($notify);
        }

        // Delete corresponding Blade file if it exists
        $bladePath = resource_path("views/" . str_replace('.', '/', activeTemplate()) . "sections/{$sectionKey}.blade.php");
        if (file_exists($bladePath)) {
            $data = Frontend::where('data_keys', "{$key}.content")->first();

            if ($data) {
                $data->delete();
            }
            unlink($bladePath);
        }

        $notify[] = ['success', 'Section deleted successfully'];
        return back()->withNotify($notify);
    }

}
