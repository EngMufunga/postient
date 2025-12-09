<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Post;
use App\Lib\FileManager;
use App\Models\Platform;
use App\Constants\Status;
use App\Models\PostMedia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SocialAccount;
use App\Rules\FileTypeValidate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Posts';
        $posts = Post::searchable(['title', 'socialAccount:profile_name'])->with(['socialAccount', 'socialAccount.platform'])->where('user_id',auth()->id())->latest()->paginate(getPaginate());
        return view('UserTemplate::post.index', compact('posts', 'pageTitle'));
    }

    public function selectType()
    {
        $pageTitle = 'Select Platform';
        $platforms = Platform::active()->latest()->get();
        $subscription = activePlan();
        return view('UserTemplate::post.platform', compact('pageTitle', 'platforms', 'subscription'));
    }


    public function create($platform)
    {
        $pageTitle = 'Create Post';

        if (!activePlan()) {
            $notify[] = ['error', 'You need to subscribe to a plan to create post on social media platforms'];
            return to_route('user.social.account.index')->withNotify($notify);
        }

        if(auth()->user()->post_count < 1){
            $notify[] = ['error', 'You have reached your post limit. Please upgrade your plan'];
            return to_route('user.social.account.index')->withNotify($notify);
        }

        if(!checkPlanPlatform($platform)){
            $notify[] = ['error', 'Your subscripton plan does not support this platform post. You need to upgrade your plan.'];
            return to_route('user.social.account.index')->withNotify($notify);
        }

        $accounts = SocialAccount::active()->whereHas('platform', function ($query) use ($platform) {
            $query->where('status', Status::ENABLE)->where('name', $platform);
        })->where('user_id', auth()->id())->get();

        return view('UserTemplate::post.create', compact('pageTitle', 'accounts', 'platform'));
    }

    public function generateImage(Request $request)
    {
        $prompt = $request->input('prompt');
        if (!$prompt) {
            return response()->json(['status'=>'error','message'=>'Prompt is required'],400);
        }

        if(gs('image_generate_status') == Status::DISABLE){
            return response()->json([
                'status' => 'error',
                'message' => 'Image credit refill is currently disabled'
            ], 200);
        }

        if(auth()->user()->image_credit < gs('per_image_credit')){
            return response()->json([
                'status' => 'error',
                'message' => 'You have not enough credits left. Please refill your credits.'
            ], 500);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . gs('api_key'),
            'Content-Type' => 'application/json',
        ])
        ->timeout(120)
        ->post('https://api.openai.com/v1/images/generations', [
            'model' => 'gpt-image-1',
            'prompt' => $prompt,
            'size' => '1024x1024',
        ]);


        if ($response->failed()) {
            return response()->json(['status'=>'error','message'=>$response->json()],500);
        }

        $data = $response->json();



       if (isset($data['data'][0]['url'])) {
            $imageContents = file_get_contents($data['data'][0]['url']);
        } elseif (isset($data['data'][0]['b64_json'])) {
            $imageContents = base64_decode($data['data'][0]['b64_json']);
        } else {
            return response()->json(['status'=>'error','message'=>'Image data not found'],500);
        }

        $tempPath = storage_path('app/tmp_image_' . time() . '.png');
        file_put_contents($tempPath, $imageContents);

        $file = new UploadedFile($tempPath, basename($tempPath),'image/png',null,true);

        $filename = fileUploader($file, getFilePath('postMedia'));

        @unlink($tempPath);

        $user = auth()->user();
        $user->image_credit = $user->image_credit - gs('per_image_credit');
        $user->save();

        userNotification($user->id, 'AI Image Generated Successfully', 'javascript:void(0)');

        return response()->json([
            'status' => 'success',
            'filename' => $filename,
            'url' => url('/') . '/' . getFilePath('postMedia') . '/' . $filename
        ]);
    }

    public function removeAiImage(Request $request)
    {
        $filename = $request->input('filename');
        Log::info('Removing AI image: ' . $filename);
        if (!$filename) return response()->json(['status'=>'error','message'=>'No filename provided'],400);

        $path = getFilePath('postMedia') . '/' . $filename;
        Log::info('Computed file path: ' . $path);

        if(!file_exists($path)){
            Log::warning('File not found: ' . $path);
            return response()->json(['status'=>'error','message'=>'File not found'],404);
        }
        Log::info('File exists. Proceeding to delete: ' . $path);

        fileManager()->removeFile(getFilePath('postMedia') . '/' . $filename);
        Log::info('File deleted successfully: ' . $path);


        return response()->json(['status'=>'success', 'message'=>'File removed successfully']);
    }

    public function scheduled()
    {
        $pageTitle = 'Scheduled Posts';
        $user = auth()->user();

        $timezones = timezone_identifiers_list();
        $currentTimezone = config('app.timezone') ?? 'UTC';

        $posts = Post::with(['socialAccount', 'mediaAssets', 'socialAccount.platform', 'user'])
                    ->where('user_id', $user->id)
                    ->where('is_schedule', Status::ENABLE)
                    ->where('schedule_time', '>', now())
                    ->get()
                    ->map(function ($post) {
                        return [
                            'id' => $post->id,
                            'title' => $post->title,
                            'schedule_time' => $post->schedule_time,
                            'post_description' => $post->post_content,
                            'post_tags' => $post->tags,
                            'platform_image' => $post->socialAccount ? getFilePath('platform') . '/' . $post->socialAccount->platform->image : null,
                            'media_videos' => $post->mediaAssets->where('type', 2)->map(function($file)  {
                                return [
                                    'id' => $file->id,
                                    'post_id' => $file->post_id,
                                    'file_name' => $file->filename,
                                    'file_path' => getFilePath('postMedia'),
                                    'file_type' => $file->type,
                                    'created_at' => $file->created_at,
                                    'updated_at' => $file->updated_at,
                                ];
                            })->values(),
                            'media_images' => $post->mediaAssets->where('type', 1)->map(function($file) {
                                return [
                                    'id' => $file->id,
                                    'post_id' => $file->post_id,
                                    'file_name' => $file->filename,
                                    'file_path' => getFilePath('postMedia'),
                                    'file_type' => $file->type,
                                    'created_at' => $file->created_at,
                                    'updated_at' => $file->updated_at,
                                ];
                            }),
                            'social_profile_image' => $post->socialAccount->profile_image,
                            'social_profile_name' => $post->socialAccount->profile_name,
                        ];
                    });


        return view('UserTemplate::post.schedule_post', compact('pageTitle', 'posts', 'currentTimezone'));
    }


    public function store(Request $request)
    {
        $user = auth()->user();
        $validation = validator($request->all(), [
            'platform_id'         => 'required|exists:platforms,id',
            'submit_type'           => 'required|in:draft,schedule,post_now',
            'account_id'      => 'required|exists:social_accounts,id',
            'title'                 => 'required_if:platform_type,video|max:255',
            'post_content'               => 'required|string',
            'schedule_datetime'         => 'required_if:submit_type,schedule',
            'media.*'               => ['required', new FileTypeValidate(['jpeg', 'jpg', 'png', 'mp4', 'mkv'])],
        ]);



        if ($validation->fails()) {
            $notify = $validation->errors()->first();
            return response()->json([
                'status' => 'error',
                'remark' => 'validation_error',
                'message' => $validation->errors()->all(),
            ], 422);
        }

        $platform = Platform::find($request->platform_id);


        if (!$user->plan_id) {
            if (!$user->free_plan_used) {
                return response()->json([
                    'status' => 'error',
                    'remark' => 'subscription_required',
                    'message' => ['error' => 'Please subscribe to a plan to proceed.'],
                ], 404);
            }

            return response()->json([
                'status' => 'error',
                'remark' => 'subscription_required',
                'message' => ['error' => 'A valid subscription plan is required.'],
            ], 404);
        }


        if(now() >$user->expired_at){
            $notify[] = 'Your plan has been expired. Please upgrade your plan';
            return response()->json([
                'remark'=>'subscription_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ], 500);
        }

        if(!$user->schedule_status && $request->submit_type == 'schedule_post'){
            $notify[] = 'You can\'t schedule post. Please upgrade your plan';
            return response()->json([
                'remark'=>'subscription_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ], 500);
        }


        if (!activePlan()) {
            $notify[] = 'You need to subscribe to a plan to create post on social media platforms';
            return response()->json([
                'remark'=>'subscription_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ], 500);
        }

        if(auth()->user()->post_count < 1){
            $notify[] = 'You have reached your post limit. Please upgrade your plan';
            return response()->json([
                'remark'=>'subscription_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ], 500);
        }

        if(!checkPlanPlatform($platform->name)){
            $notify[] = 'Your subscripton plan does not support this platform post. You need to upgrade your plan.';
            return response()->json([
                'remark'=>'subscription_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ], 500);
        }

        try {
            $user = auth()->user();
            $post = new Post();
            $post->user_id          = $user->id;
            $post->social_account_id      = $request->account_id;
            $post->title            = $request->title;
            $post->post_content          = $request->post_content;
            $post->tags             = $request->tags;

            if ($request->submit_type == 'schedule') {
                $post->status = Status::SCHEDULE;
                $post->is_schedule = Status::YES;
    
                $raw = $request->schedule_datetime;
                $formats = [
                    'd M, Y - h:i A',
                    'Y-m-d H:i',
                    'd-m-Y h:i A',
                    'Y-m-d H:i:s',
                ];
    
                $scheduleTime = null;
                foreach ($formats as $format) {
                    try {
                        $scheduleTime = Carbon::createFromFormat($format, $raw);
                        break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
    
                // fallback parser if all formats fail
                if (!$scheduleTime) {
                    try {
                        $scheduleTime = Carbon::parse($raw);
                    } catch (\Exception $e) {
                        return response()->json([
                            'status' => 'error',
                            'remark' => 'date_parse_error',
                            'message' => ['error' => ['Invalid date format for schedule_datetime']],
                        ], 422);
                    }
                }
    
                $post->schedule_time = $scheduleTime->format('Y-m-d H:i:s');
                
            } elseif ($request->submit_type == 'post_now') {
                $post->status = Status::PUBLISH;
                $post->schedule_time = null;
                $post->is_schedule = Status::NO;
            } else {
                $post->status = Status::DRAFT;
                $post->schedule_time = null;
                $post->is_schedule = Status::NO;
            }

            $post->save();

            $user->post_count = max(0, $user->post_count - 1);
            $user->save();


            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {

                    $mime = $file->getMimeType();
                    $mediaType = Str::startsWith($mime, 'image/') ? 1 : (Str::startsWith($mime, 'video/') ? 2 : 'unknown');

                    if($mediaType == 'unknown'){
                        $notify[] = ['error', 'Invalid media type'];
                        return response()->json([
                            'status' => 'error',
                            'remark' => 'media_type_error',
                            'message' => $notify
                        ], 422);
                    }

                    $media = new PostMedia();
                    $media->post_id = $post->id;
                    $media->type = $mediaType;
                    $media->filename = fileUploader($file, getFilePath('postMedia'));
                    $media->save();
                }
            }

            if ($request->has('ai_media')) {
                foreach ($request->ai_media as $filename) {
                    $media = new PostMedia();
                    $media->post_id = $post->id;
                    $media->type = 1; // image
                    $media->filename = $filename;
                    $media->save();
                }
            }





            if ($post->status == Status::PUBLISH) {
                $account = $post->socialAccount;
                $platform = $account->platform;

                $response = match ($platform->id) {
                    1 => (new \App\Services\Platform\Facebook())->publishPost($post, $account),
                    2 => (new \App\Services\Platform\Instagram())->publishPost($post, $account),
                    3 => (new \App\Services\Platform\Linkedin())->publishPost($post, $account),
                    4 => (new \App\Services\Platform\Twitter())->publishPost($post, $account),
                    5 => (new \App\Services\Platform\Tiktok())->publishPost($post, $account),
                    6 => (new \App\Services\Platform\Youtube())->publishPost($post, $account),
                    default => ['status' => 'error', 'message' => 'Unsupported platform']
                };

                if ($response['status'] == 'error') {
                    return response()->json([
                        'status' => 'error',
                        'remark' => 'publish_error',
                        'message' => ['error' => $response['message']],
                    ], 500);
                }

                $post->publish_date = now();
                $post->status = Status::PUBLISH;
                $post->is_schedule = Status::DISABLE;
                $post->schedule_time = null;
                $post->save();
            }

            $notify[] = ['success', 'Post created successfully'];
            return response()->json([
                'remark'=>'post_created',
                'status'=>'success',
                'message'=>['success' => $notify],
            ], 200);

        }catch (\Throwable $th) {
            $notify[] = ['error', $th->getMessage()];
            return response()->json([
                'remark'=>'post_create_error',
                'status'=>'error',
                'message'=>['error' => $notify],
            ], 500);
        }
    }


    public function hashtagGenerate(Request $request)
    {
        $user = auth()->user();
        if(!$user->plan){
            return response()->json([
                'status' => 'error',
                'remark' => 'exception',
                'message' => 'You have\'t any current plan. Please upgrade your plan.',
            ], 500);
        }

        if(!$user->ai_assistant_status){
            return response()->json([
                'status' => 'error',
                'remark' => 'exception',
                'message' => 'Your plan does not allow to generate content. Please upgrade your plan.',
            ], 500);
        }

        if($user->generated_content_count < 1){
            return response()->json([
                'status' => 'error',
                'remark' => 'exception',
                'message' => 'You have reached the maximum renerated content count.',
            ], 500);
        }



        $request->validate([
            'prompt' => 'required|string|max:255',
        ]);

        $prompt = <<<PROMPT
        Generate exactly 5 relevant, popular hashtags for this topic: "{$request->prompt}".
        Follow these rules strictly:
        1. Only output the 5 hashtags, nothing else
        2. Format as: #OneWordHashtag1 #SecondHashtag2 #Third3 #Fourth4 #Fifth5
        3. No explanations, titles, or additional text
        4. Use current trending terms when applicable
        5. Ensure all hashtags are single words or combined words (no spaces)
        6. Make them highly relevant to the input topic
        7. Include 1-2 broad interest hashtags and 3-4 niche-specific ones
        8. Give the tags in PascalCase format
        PROMPT;


        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . gs('api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => gs('gpt_model') ?? 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Generate 5 highly relevant hashtags for: {$request->prompt}\n\n" .
                            "Format requirements:\n" .
                            "- Only output #hashtag1 #hashtag2 #hashtag3 #hashtag4 #hashtag5\n" .
                            "- All hashtags must be single compound words (no spaces)\n" .
                            "- Mix 2 popular hashtags with 3 niche-specific ones\n" .
                            "- Use current trending terms when applicable\n" .
                            "- Never include any other text or explanations"
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.5,
                'max_tokens' => 60,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'remark' => 'OpenAI API Error',
                    'message' => $response->json(),
                ], 500);
            }

            $content = $response->json('choices.0.message.content') ?? '';
            $hashTags = preg_split('/\s+/', trim($content));

            $user->generated_content = min(0, $user->generated_content - 1);
            $user->save();


            $title = 'Post Tag Generated Successfully';

            userNotification($user->id,$title,'javascript:void(0)');

            return response()->json([
                'status' => 'success',
                'remark' => 'hashTags',
                'data' => [
                    'hashTags' => $hashTags,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'remark' => 'exception',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function contentGenerate(Request $request)
    {
        $user = auth()->user();

        if(!$user->plan){
            return response()->json([
                'status' => 'error',
                'remark' => 'exception',
                'message' => 'You have\'t any current plan. Please upgrade your plan.',
            ], 500);
        }

        if($user?->plan?->ai_assistant_status){
            return response()->json([
                'status' => 'error',
                'remark' => 'exception',
                'message' => 'Your plan does not allow to generate content. Please upgrade your plan.',
            ], 500);
        }

        if($user->generated_content >= $user?->plan?->generated_content_count){
            return response()->json([
                'status' => 'error',
                'remark' => 'exception',
                'message' => 'You have reached the maximum renerated content count.',
            ], 500);
        }

        $request->validate([
            'prompt' => 'required|string|max:255',
            'language' => 'required|string|max:255',
            'tone' => 'required|string|max:255',
            'creativity' => 'required|integer|gte:0|max:1',
            'length' => 'required|integer|gte:10|max:'.gs('gpt_max_result_length'),
        ]);

        $prompt = <<<PROMPT
        Generate only post content for this topic: "{$request->prompt}".
        Follow these rules strictly:
        1. Only output the content, nothing else
        2. No explanations, titles, or additional text
        3. Use current trending terms when applicable
        4. Ensure the content is relevant to the input topic
        5. Make the content realistic and engaging
        6. Keep the content under 60 words
        7. Do not add any hashtags
        PROMPT;

        $tone = $request->tone;
        $creativity = $request->creativity;
        $length = $request->length;
        $language = $request->language;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . gs('api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => gs('gpt_model') ?? 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are a social media caption writer.
                            Your job is to create short, catchy, and trending captions for posts.
                            Do NOT write paragraphs or long explanations.
                            The output must look like a real social media caption:
                            - One to three short lines max
                            - Can include emojis, trending slang, or light humor
                            - Keep it under 100 words
                            - Must be engaging and human-like
                            - Never include any hashtags."
                    ],
                    [
                        'role' => 'user',
                        'content' => "Language: {$language}\nTone of Voice: {$tone}\n\nPrompt: {$prompt}"
                    ],
                ],
                'temperature' => (float) $creativity,
                'max_tokens' => (int) $length,
            ]);

            // Handle API errors
            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'remark' => 'OpenAI API Error',
                    'message' => $response->json(),
                ], 500);
            }

            $content = trim($response->json('choices.0.message.content') ?? '');
            $user->generated_content = $user->generated_content + 1;
            $user->save();

            $title = 'Post Content Generated Successfully';

            userNotification($user->id,$title,'javascript:void(0)');

            return response()->json([
                'status' => 'success',
                'remark' => 'content',
                'data' => [
                    'content' => $content,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'remark' => 'exception',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete($id)
    {
        $post = Post::with('mediaAssets')->findOrFail($id);

        foreach ($post->medias as $media) {
            fileManager()->removeFile(getFilePath('postMedia') . '/' . $media->filename);
            $media->delete();
        }

        $post->delete();
        $notify[] = ['success', 'Post deleted successfully'];
        return back()->withNotify($notify);
    }

    public function deleteImage($id)
    {
        $media = PostMedia::findOrFail($id);
        fileManager()->removeFile(getFilePath('postMedia') . '/' . $media->filename);
        $media->delete();
        $notify[] = ['success', 'Image deleted successfully'];
        return back()->withNotify($notify);
    }


    public function edit($id)
    {
        $pageTitle = 'Post Edit';
        $user = auth()->user();



        $post = Post::with(['mediaAssets', 'socialAccount.platform'])->where('user_id', $user->id)->findOrFail($id);

        $platform = optional($post->socialAccount?->platform)->name;

        if (!activePlan()) {
            $notify[] = ['error', 'You need to subscribe to a plan to create post on social media platforms'];
            return to_route('user.social.account.index')->withNotify($notify);
        }

        if(auth()->user()->post_count < 1){
            $notify[] = ['error', 'You have reached your post limit. Please upgrade your plan'];
            return to_route('user.social.account.index')->withNotify($notify);
        }

        if(!checkPlanPlatform($platform)){
            $notify[] = ['error', 'Your subscripton plan does not support this platform post. You need to upgrade your plan.'];
            return to_route('user.social.account.index')->withNotify($notify);
        }


        if($post->status == Status::PUBLISH){
            $notify[] = ['error', 'Post already published'];
            return back()->withNotify($notify);
        }


        $accounts = SocialAccount::active()
            ->where('user_id', $user->id)
            ->whereHas('platform', function ($query) use ($platform) {
                $query->where('status', Status::ENABLE)
                    ->where('name', $platform);
            })
            ->get();

        if ($accounts->isEmpty()) {
            return back()->withNotify([['error', 'No active social account found for this platform.']]);
        }

        return view('UserTemplate::post.edit', compact('pageTitle', 'accounts', 'platform', 'post'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $post = Post::where('user_id', $user->id)->findOrFail($id);
        $platform = Platform::find($request->platform_id);

        if (!$post)
        {
            return response()->json([
                'status' => 'error',
                'remark' => 'post_not_found',
                'message' => ['error' => 'Post not found'],
            ], 500);
        }


        $validation = validator($request->all(), [
            'platform_id'         => 'required|exists:platforms,id',
            'submit_type'           => 'required|in:draft,schedule,post_now',
            'account_id'      => 'required|exists:social_accounts,id',
            'title'                 => 'required_if:platform_type,video|max:255',
            'post_content'               => 'required|string',
            'schedule_datetime'         => 'required_if:submit_type,schedule',
            'media.*'               => ['required', new FileTypeValidate(['jpeg', 'jpg', 'png', 'mp4', 'mkv'])],
        ]);

        if ($validation->fails()) {
            $notify = $validation->errors()->first();
            return response()->json([
                'status' => 'error',
                'remark' => 'validation_error',
                'message' => $validation->errors()->all(),
            ], 422);
        }


        if (!$user->plan_id) {
            if (!$user->free_plan_used) {
                return response()->json([
                    'status' => 'error',
                    'remark' => 'subscription_required',
                    'message' => ['error' => 'Please subscribe to a plan to proceed.'],
                ], 404);
            }

            return response()->json([
                'status' => 'error',
                'remark' => 'subscription_required',
                'message' => ['error' => 'A valid subscription plan is required.'],
            ], 404);
        }


        if(now() >$user->expired_at){
            $notify[] = 'Your plan has been expired. Please upgrade your plan';
            return response()->json([
                'remark'=>'subscription_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ], 500);
        }

        if(!$user->schedule_status && $request->submit_type == 'schedule_post'){
            $notify[] = 'You can\'t schedule post. Please upgrade your plan';
            return response()->json([
                'remark'=>'subscription_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ], 500);
        }


        if (!activePlan()) {
            $notify[] = 'You need to subscribe to a plan to create post on social media platforms';
            return response()->json([
                'remark'=>'subscription_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ], 500);
        }

        if($user->post_count < 1){
            $notify[] = 'You have reached your post limit. Please upgrade your plan';
            return response()->json([
                'remark'=>'subscription_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ], 500);
        }

        if(!checkPlanPlatform($platform->name)){
            $notify[] = 'Your subscripton plan does not support this platform post. You need to upgrade your plan.';
            return response()->json([
                'remark'=>'subscription_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ], 500);
        }


        try {
            $post->user_id          = $user->id;
            $post->social_account_id      = $request->account_id;
            $post->title            = $request->title;
            $post->post_content          = $request->post_content;
            $post->tags             = $request->tags;

            if ($request->submit_type == 'schedule') {
                // $post->status = Status::SCHEDULE;
                // $post->is_schedule = Status::YES;
                // $raw = $request->schedule_datetime;
                // $post->schedule_time = Carbon::createFromFormat('d M, Y - h:i A', $raw)->format('Y-m-d H:i:s');
                
                
                
                
                                $post->status = Status::SCHEDULE;
                $post->is_schedule = Status::YES;
    
                $raw = $request->schedule_datetime;
                $formats = [
                    'd M, Y - h:i A',
                    'Y-m-d H:i',
                    'd-m-Y h:i A',
                    'Y-m-d H:i:s',
                ];
    
                $scheduleTime = null;
                foreach ($formats as $format) {
                    try {
                        $scheduleTime = Carbon::createFromFormat($format, $raw);
                        break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
    
                // fallback parser if all formats fail
                if (!$scheduleTime) {
                    try {
                        $scheduleTime = Carbon::parse($raw);
                    } catch (\Exception $e) {
                        return response()->json([
                            'status' => 'error',
                            'remark' => 'date_parse_error',
                            'message' => ['error' => ['Invalid date format for schedule_datetime']],
                        ], 422);
                    }
                }
    
                $post->schedule_time = $scheduleTime->format('Y-m-d H:i:s');
                
                
                
            } elseif ($request->submit_type == 'post_now') {
                $post->status = Status::PUBLISH;
                $post->schedule_time = null;
                $post->is_schedule = Status::NO;
            } else {
                $post->status = Status::DRAFT;
                $post->schedule_time = null;
                $post->is_schedule = Status::NO;
            }

            $post->save();

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {

                    $mime = $file->getMimeType();
                    $mediaType = Str::startsWith($mime, 'image/') ? 1 : (Str::startsWith($mime, 'video/') ? 2 : 'unknown');

                    if($mediaType == 'unknown'){
                        $notify[] = ['error', 'Invalid media type'];
                        return response()->json([
                            'status' => 'error',
                            'remark' => 'media_type_error',
                            'message' => $notify
                        ], 422);
                    }

                    $media = new PostMedia();
                    $media->post_id = $post->id;
                    $media->type = $mediaType;
                    $media->filename = fileUploader($file, getFilePath('postMedia'));
                    $media->save();
                }
            }


            if ($request->has('ai_media')) {
                foreach ($request->ai_media as $filename) {
                    $media = new PostMedia();
                    $media->post_id = $post->id;
                    $media->type = 1; // image
                    $media->filename = $filename;
                    $media->save();
                }
            }


            if ($post->status == Status::PUBLISH) {
                $account = $post->socialAccount;
                $platform = $account->platform;

                $response = match ($platform->id) {
                    1 => (new \App\Services\Platform\Facebook())->publishPost($post, $account),
                    2 => (new \App\Services\Platform\Instagram())->publishPost($post, $account),
                    3 => (new \App\Services\Platform\Linkedin())->publishPost($post, $account),
                    4 => (new \App\Services\Platform\Twitter())->publishPost($post, $account),
                    5 => (new \App\Services\Platform\Tiktok())->publishPost($post, $account),
                    6 => (new \App\Services\Platform\Youtube())->publishPost($post, $account),
                    default => ['status' => 'error', 'message' => 'Unsupported platform']
                };

                if ($response['status'] == 'error') {
                    return response()->json([
                        'status' => 'error',
                        'remark' => 'publish_error',
                        'message' => ['error' => $response['message']],
                    ], 500);
                }

                $post->publish_date = now();
                $post->status = Status::PUBLISH;
                $post->is_schedule = Status::DISABLE;
                $post->schedule_time = null;
                $post->save();
            }

            $notify[] = ['success', 'Post updated successfully'];

            return response()->json([
                'remark'=>'post_updated',
                'status'=>'success',
                'message'=>['success' => $notify],
            ], 200);

        }catch (\Throwable $th) {
            $notify[] = ['error', $th->getMessage()];
            return response()->json([
                'remark'=>'post_updated_error',
                'status'=>'error',
                'message'=>['error' => $notify],
            ], 500);

        }
    }
}
