<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use App\Models\Post;
use App\Models\SocialAccount;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Posts';
        $posts = Post::searchable(['title', 'socialAccount:profile_name'])->with(['socialAccount', 'socialAccount.platform'])->latest()->paginate(getPaginate());
        return view('Admin::posts.index', compact('pageTitle', 'posts'));
    }


}
