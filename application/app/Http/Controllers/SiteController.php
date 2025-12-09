<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Frontend;
use App\Models\Language;
use App\Constants\Status;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;

class SiteController extends Controller
{
    public function index(){
        if (isset($_GET['reference'])) {
            session()->put('reference', $_GET['reference']);
        }
        $pageTitle = 'Home';
        $sections = Page::where('slug','/')->first();
        return view('Template::home', compact('pageTitle','sections'));
    }

    public function pages($slug)
    {
        $page = Page::where('slug',$slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections = $page->secs;
        return view('Template::pages', compact('pageTitle','sections'));
    }


    public function pricing(){
        $pageTitle = 'Pricing';
        $sections = Page::where('slug','pricing')->first();
        $plans = Plan::where('status', Status::ENABLE)->latest()->get();
        return view('Template::pricing',compact('pageTitle', 'sections', 'plans'));
    }


    public function features(){
        $pageTitle = 'Features';
        $sections = Page::where('slug','features')->first();
        $features = Frontend::where('data_keys','feature.element')->latest()->paginate(getPaginate());
        return view('Template::features',compact('pageTitle', 'sections', 'features'));
    }


    public function contact()
    {
        $pageTitle = "Contact Us";
        $sections = Page::where('slug','contact')->first();
        return view('Template::contact',compact('pageTitle', 'sections'));
    }


    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        if(!verifyCaptcha()){
            $notify[] = ['error','Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $request->session()->regenerateToken();

        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = 2;


        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = 0;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title = 'A new support ticket has opened ';
        $adminNotification->click_url = urlPath('admin.ticket.view',$ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug,$id)
    {
        $policy = Frontend::where('id',$id)->where('data_keys','policy_pages.element')->firstOrFail();
        $pageTitle = $policy->data_values->title;
        return view('Template::policy',compact('policy','pageTitle'));
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) $lang = 'en';
        session()->put('lang', $lang);
        return back();
    }


    public function blogSearch(Request $request){
        $query = $request->get('q', '');

        $blogs = \App\Models\Frontend::where('data_keys', 'blog.element')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data_values, '$.title')) LIKE ?", ["%{$query}%"])
            ->latest()
            ->take(5)
            ->get();

        $data = $blogs->map(function ($blog) {
            $title = $blog->data_values->title ?? '';
            $image = getImage(getFilePath('blog') . '/thumb_' . ($blog->data_values->blog_image ?? 'default.jpg'));
            $date  = showDateTime($blog->created_at, 'd M, Y');

            return [
                'title' => __($title),
                'url'   => route('blog.details', [
                    'slug' => slug($title),
                    'id'   => $blog->id
                ]),
                'image' => $image,
                'date'  => $date,
            ];
        });

        return response()->json(['data' => $data]);
    }


    public function blogs(){
        $blogs = Frontend::where('data_keys','blog.element')->latest()->paginate(getPaginate());
        $pageTitle = 'Our Blogs';
        $sections = Page::where('slug','blog')->first();
        return view('Template::blogs',compact('blogs','pageTitle', 'sections'));
    }

    public function blogDetails($slug,$id){
        $blog = Frontend::where('id',$id)->where('data_keys','blog.element')->firstOrFail();
        $pageTitle = $blog->data_values->title;
        $recentBlogs = Frontend::whereNot('id',$id)->where('data_keys','blog.element')->latest()->limit(4)->get();
        return view('Template::blog_details',compact('blog','pageTitle', 'recentBlogs'));
    }


    public function cookieAccept(){
        $general = gs();
        Cookie::queue('gdpr_cookie', $general->site_name , 43200);
        return back();
    }

    public function cookiePolicy(){
        $pageTitle = 'Cookie Policy';
        $cookie = Frontend::where('data_keys','cookie.data')->first();
        return view('Template::cookie',compact('pageTitle','cookie'));
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        $general = gs();
        if($general->maintenance_mode){
            $maintenance = Frontend::where('data_keys','maintenance.data')->first();
            return view('Template::maintenance',compact('pageTitle','maintenance'));
        }
        return to_route('home');
    }

    public function placeholderImage($size = null){
        $imgWidth = explode('x',$size)[0];
        $imgHeight = explode('x',$size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font') . DIRECTORY_SEPARATOR . 'RobotoMono-Regular.ttf';
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if($imgHeight < 100 && $fontSize > 30){
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 255, 255, 255);
        $bgFill    = imagecolorallocate($image, 28, 35, 47);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function subscribers(Request $request){
        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);
        $subscriber = new Subscriber();
        $subscriber->email = $request->email;
        $subscriber->save();
        $notify[] = ['success','You email has been added successfully'];
        return back()->withNotify($notify);
    }
}
