<?php

namespace App\Http\Controllers\Admin;

use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class FrontendController extends Controller
{

    public function templates()
    {
        $pageTitle = 'Templates';
        $temPaths = array_filter(glob('application/resources/views/presets/*'), 'is_dir');

        foreach ($temPaths as $key => $temp) {
            $arr = explode('/', $temp);
            $tempname = end($arr);
            $templates[$key]['name'] = $tempname;
            $templates[$key]['image'] = asset($temp) . '/preview.jpg';
        }
        $extra_templates = json_decode(getTemplates(), true);
        return view('Admin::frontend.templates', compact('pageTitle', 'templates', 'extra_templates'));

    }

    public function templatesActive(Request $request)
    {
        $general = GeneralSetting::first();

        $general->active_template = $request->name;
        $general->save();

        $notify[] = ['success', strtoupper($request->name) . ' template activated successfully'];
        return back()->withNotify($notify);
    }

    public function seoEdit()
    {
        $pageTitle = 'SEO Configuration';
        $seo = Frontend::where('data_keys', 'seo.data')->first();
        if (!$seo) {
            $data_values = '{"keywords":[],"description":"","social_title":"","social_description":"","image":null}';
            $data_values = json_decode($data_values, true);
            $frontend = new Frontend();
            $frontend->data_keys = 'seo.data';
            $frontend->data_values = $data_values;
            $frontend->save();
        }
        return view('Admin::frontend.seo', compact('pageTitle', 'seo'));
    }



    public function frontendSections($key)
    {
        $sections = getPageSections();

        if (!isset($sections->$key)) {
            return abort(404);
        }

        $section = $sections->$key;
        $content = Frontend::where('data_keys', $key . '.content')->orderBy('id', 'desc')->first();
        $elements = Frontend::where('data_keys', $key . '.element')->orderBy('id')->orderBy('id', 'desc')->get();
        $pageTitle = $section->name;

        return view('Admin::frontend.index', compact('section', 'content', 'elements', 'key', 'pageTitle'));
    }

    public function frontendContent(Request $request, $key)
    {
        $purifier = new \HTMLPurifier();
        $valInputs = $request->except('_token', 'image_input', 'key', 'status', 'type', 'id');

        $inputContentValue = [];
        foreach ($valInputs as $keyName => $input) {
            if (gettype($input) == 'array') {
                $inputContentValue[$keyName] = $input;
                continue;
            }
            $inputContentValue[$keyName] = $purifier->purify($input);
        }

        $type = $request->type;
        if (!$type) {
            abort(404);
        }

        // safer access instead of @getPageSections()
        $sections = getPageSections();
        $imgJson = null;
        if (isset($sections->$key) && isset($sections->$key->$type) && isset($sections->$key->$type->images)) {
            $imgJson = $sections->$key->$type->images;
        }

        $validation_rule = [];
        $validation_message = [];

        foreach ($request->except('_token', 'video') as $input_field => $val) {
            if ($input_field == 'has_image' && $imgJson) {
                foreach ($imgJson as $imgValKey => $imgJsonVal) {
                    $validation_rule['image_input.' . $imgValKey] = ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])];
                    $validation_message['image_input.' . $imgValKey . '.image'] = keyToTitle($imgValKey) . ' must be an image';
                    $validation_message['image_input.' . $imgValKey . '.mimes'] = keyToTitle($imgValKey) . ' file type not supported';
                }
                continue;
            } elseif ($input_field == 'seo_image') {
                $validation_rule['image_input'] = ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];
                continue;
            }
            $validation_rule[$input_field] = 'required';
        }

        $request->validate($validation_rule, $validation_message, ['image_input' => 'image']);

        if ($request->id) {
            $content = Frontend::findOrFail($request->id);
        } else {
            $content = Frontend::where('data_keys', $key . '.' . $request->type)->first();
            if (!$content || $request->type == 'element') {
                $content = new Frontend();
                $content->data_keys = $key . '.' . $request->type;
                $content->save();
            }
        }

        if ($type == 'data') {
            $inputContentValue['image'] = $content->data_values->image ?? null;
            if ($request->hasFile('image_input')) {
                try {
                    $inputContentValue['image'] = fileUploader(
                        $request->image_input,
                        getFilePath('seo'),
                        getFileSize('seo'),
                        $content->data_values->image ?? null
                    );
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload the image'];
                    return back()->withNotify($notify);
                }
            }
        } else {
            if ($imgJson) {
                foreach ($imgJson as $imgKey => $imgValue) {
                    $imgData = $request->image_input[$imgKey] ?? null;
                    if (is_file($imgData)) {
                        try {
                            $inputContentValue[$imgKey] = $this->storeImage($imgJson,$type,$key,$imgData,$imgKey,$content->data_values->$imgKey ?? null);
                        } catch (\Exception $exp) {
                            $notify[] = ['error', 'Couldn\'t upload the image'];
                            return back()->withNotify($notify);
                        }
                    } elseif (isset($content->data_values->$imgKey)) {
                        $inputContentValue[$imgKey] = $content->data_values->$imgKey;
                    }
                }
            }
        }

        $content->data_values = $inputContentValue;
        $content->save();

        $notify[] = ['success', 'Content has been updated successfully'];
        return back()->withNotify($notify);
    }


    public function frontendElement($key, $id = null)
    {
        $sections = getPageSections();
        if (!isset($sections->$key)) {
            abort(404);
        }

        $section = $sections->$key;

        if (isset($section->element) && isset($section->element->modal)) {
            unset($section->element->modal);
        }

        $pageTitle = $section->name . ' Items';

        if ($id) {
            $data = Frontend::findOrFail($id);
            return view('Admin::frontend.element', compact('section', 'key', 'pageTitle', 'data'));
        }

        return view('Admin::frontend.element', compact('section', 'key', 'pageTitle'));
    }


    protected function storeImage($imgJson, $type, $key, $image, $imgKey, $old_image = null)
    {
        $path = 'assets/images/frontend/' . $key;
        $size = null;
        $thumb = null;

        if ($type == 'element' || $type == 'content') {
            // Safely access size and thumb
            if (isset($imgJson->$imgKey)) {
                $size = $imgJson->$imgKey->size ?? null;
                $thumb = $imgJson->$imgKey->thumb ?? null;
            }
        } else {
            $path = getFilePath($key);
            $size = getFileSize($key);
            $fileManager = fileManager();
            if (method_exists($fileManager, $key) && isset($fileManager->$key()->thumb)) {
                $thumb = $fileManager->$key()->thumb;
            }
        }

        return fileUploader($image, $path, $size, $old_image, $thumb);
    }


    public function remove($id)
    {
        $frontend = Frontend::findOrFail($id);

        // Parse data_keys safely
        $dataKeys = explode('.', $frontend->data_keys ?? '');
        $key = $dataKeys[0] ?? null;
        $type = $dataKeys[1] ?? null;

        if (!$key || !$type) {
            abort(404, 'Invalid data keys');
        }

        if ($type === 'element' || $type === 'content') {
            $path = 'assets/images/frontend/' . $key;
            $sections = getPageSections();
            $imgJson = isset($sections->$key->$type->images) ? $sections->$key->$type->images : null;

            if ($imgJson) {
                foreach ($imgJson as $imgKey => $imgValue) {
                    $imageName = $frontend->data_values->$imgKey ?? null;
                    if ($imageName) {
                        fileManager()->removeFile($path . '/' . $imageName);
                        fileManager()->removeFile($path . '/thumb_' . $imageName);
                    }
                }
            }
        }

        $frontend->delete();
        $notify[] = ['success', 'Content removed successfully'];
        return back()->withNotify($notify);
    }



}
