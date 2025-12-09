<?php

use Carbon\Carbon;
use App\Lib\Captcha;
use App\Models\Plugin;
use App\Notify\Notify;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\Frontend;
use App\Constants\Status;
use Illuminate\Support\Str;
use App\Models\GeneralSetting;
use App\Lib\GoogleAuthenticator;
use App\Models\Plan;
use App\Models\Platform;
use App\Models\Subscription;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Cache;


function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0)
        return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function sysInfo()
{
    $system['name'] = 'quickpost';
    $system['version'] = '1.0.0';
    $system['build_version'] = '1.0.5';
    $system['admin_version'] = '12.0.0';
    return $system;
}

function activeTemplate($asset = false)
{
    $general = gs();
    $template = $general->active_template;
    if ($asset)
        return 'assets/presets/' . $template . '/';
    return 'presets.' . $template . '.';
}

function activeTemplateName()
{
    $general = gs();
    $template = $general->active_template;
    return $template;
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $analytics = Plugin::where('act', $key)->where('status', Status::ENABLE)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}


function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}


function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.wstacks.com/updates/templates/' . sysInfo()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections/builder/builder.json';
    $sections = json_decode(file_get_contents($jsonUrl));


    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}

function getImage($image, $size = null)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/general/default.png');
}

function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true)
{
    $general = GeneralSetting::first();
    $globalShortCodes = [
        'site_name' => $general->site_name,
        'site_currency' => $general->cur_text,
        'currency_symbol' => $general->cur_sym,
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->userColumn = getColumnName($user);
    $notify->send();
}

function getColumnName($user)
{
    $array = explode("\\", get_class($user));
    return strtolower(end($array)) . '_id';
}

function getPaginate($paginate = 20)
{
    return $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}


function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3) $class = request()->routeIs('admin.*') ? 'side-menu--open' : 'active';
    elseif ($type == 2) $class = request()->routeIs('admin.*') ? 'sidebar-submenu__open' : 'd-block';
    else $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower($routeParam[0]) == strtolower($param)) return $class;
            else return;
        }
        return $class;
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $filename = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'M d, Y - h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}


function gatewayRedirectUrl($type = false)
{
    if ($type) {
        return 'user.deposit.history';
    } else {
        return 'user.deposit';
    }
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = Status::YES;
        $user->save();
        return true;
    } else {
        return false;
    }
}


function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (isset($_SERVER['HTTP_FORWARDED']) && filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (isset($_SERVER['HTTP_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (isset($_SERVER['HTTP_X_REAL_IP']) && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}


function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function gs($key = null)
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if ($key) return $general->$key;
    return $general;
}

function siteLogo($type = null)
{
    $version = '?v=' . urlencode(gs('updated_at'));;
    $name = $type ? "/logo_{$type}.png" : '/logo.png';
    // return getImage(getFilePath('logoIcon') . $name ) . $version;
    return getImage(getFilePath('logoIcon') . $name );
}

function siteFavicon()
{
    $version = '?v=' . urlencode(gs('updated_at'));;
    // return getImage(getFilePath('logoIcon') . '/favicon.png') . $version;
    return getImage(getFilePath('logoIcon') . '/favicon.png');
}

function getPlatformNames($platformAccess): array
{
    if (empty($platformAccess)) {
        return [];
    }

    if (is_string($platformAccess)) {
        $decoded = json_decode($platformAccess, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $platformIds = $decoded;
        } else {
            $platformIds = array_map('trim', explode(',', $platformAccess));
        }
    } elseif (is_array($platformAccess)) {
        $platformIds = $platformAccess;
    } else {
        return [];
    }

    return Platform::whereIn('id', $platformIds)->pluck('name')->toArray();
}

    function getActivePlatformNames(): array
    {
        return Platform::where('status', Status::ENABLE)->pluck('name')->toArray();
    }

function userNotification($userId,$title, $clickUrl = 'javascript:void(0)')
{
    $notification = new UserNotification();
    $notification->user_id = $userId;
    $notification->title = $title;
    $notification->click_url = ($clickUrl == 'javascript:void(0)' || $clickUrl == '#') ? '#' : $clickUrl;
    $notification->is_read = Status::NO;
    $notification->save();
}

function platform($name)
{
    $data = Platform::where('name', $name)->first();
    if($data){
        return [
            'name' => $data->name,
            'image' => getImage(getFilePath('platform') . '/' . $data->image,getFileSize('platform'))
        ];
    }else{
        return [
            'name' => 'Not Found',
            'image' => getImage(getFilePath('platform') . '/' . 'default.png',getFileSize('platform'))
        ];
    }
}

function checkPlanPlatform($platformName)
{
    $platform = Platform::where('name', $platformName)->first();
    if($platform){
        $user = auth()->user();

        if($user->expired_at > Carbon::now()){
            if($user->plan_id){
                $plan = Plan::where('id', $user->plan_id)->first();

                $platformAccess = getPlatformNames($plan->platform_access);

                return in_array($platform->name, $platformAccess) ? true : false;

            }else{
                return true;
            }
        }else{
            return false;
        }
    }else{
        return false;
    }
}



function buildOAuthHeader($url, $method, $params, $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret) {
    $oauth = [
        'oauth_consumer_key' => $consumerKey,
        'oauth_nonce' => bin2hex(random_bytes(16)),
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp' => time(),
        'oauth_token' => $accessToken,
        'oauth_version' => '1.0',
    ];

    $allParams = array_merge($oauth, $params);
    ksort($allParams);

    $baseString = strtoupper($method) . '&' . rawurlencode($url) . '&' . rawurlencode(http_build_query($allParams, '', '&', PHP_QUERY_RFC3986));
    $signingKey = rawurlencode($consumerSecret) . '&' . rawurlencode($accessTokenSecret);
    $oauth['oauth_signature'] = base64_encode(hash_hmac('sha1', $baseString, $signingKey, true));

    $header = 'Authorization: OAuth ';
    $values = [];
    foreach ($oauth as $key => $value) {
        $values[] = $key . '="' . rawurlencode($value) . '"';
    }
    $header .= implode(', ', $values);
    return $header;
}


function platformName($name)
{
    $data = Platform::where('name', $name)->first();
    if($data){
        return $data->id;
    }else{
        return null;
    }
}

function activePlan()
{
    $user = auth()->user();

    if ($user->plan_id && $user->expired_at >= now()) {
        $plan = $user->plan;
    } elseif (gs('free_post_status') == Status::YES && $user->plan_id == 0 && $user->expired_at >= now() && $user->free_plan_used == Status::YES) {
        $plan                       = new stdClass();
        $plan->name                 = "Free Plan";
        $plan->connected_profile        = gs('channel_count');
        $plan->schedule_post  = gs('schedule_count');
        $plan->ai_assistant_status         = Status::NO;
        $plan->generated_content_count = 0;
        $plan->schedule_status      = Status::NO;
        $plan->platform_access      = [Platform::where('status', Status::ENABLE)->pluck('id')->toArray()];
    } else {
        $plan = null;
    }


    if ($plan) {
        return $plan;
    }
}




