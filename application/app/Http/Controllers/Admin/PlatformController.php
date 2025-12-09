<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Platform;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Platform';
        $items = Platform::latest()->searchable(['name'])->paginate(getPaginate());
        return view('Admin::platform.index', compact('items', 'pageTitle'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg','jpeg','png'])],
        ]);

        $platform = Platform::findOrFail($id);
        $path = getFilePath('platform');
            if ($request->hasFile('image')) {
                try {
                    $platform->image = fileUploader($request->image,$path, getFileSize('platform'));
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload the logo'];
                    return back()->withNotify($notify);
                }
            }
        $platform->save();

        $notify[] = ['success', 'Platform updated successfully'];
        return to_route('admin.platform.index')->withNotify($notify);
    }


    public function status($id)
    {
        $platform = Platform::findOrFail($id);
        $platform->status = $platform->status == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        $platform->save();

        $notify[] = ['success', 'Platform status updated successfully'];
        return to_route('admin.platform.index')->withNotify($notify);
    }











    public function analytics($id)
    {
        $widgets = [];

        $account = SocialAccount::active()->firstOrFail($id);

        $pageTitle = $account->profile_name ?? 'Analytics';
        switch ($account->platform_id ?? null) {
            case Status::FACEBOOK:
                $widgets = $this->facebookAnalytics($account);
                break;
            case Status::INSTAGRAM:
                $widgets = $this->instagramAnalytics($account);
                break;
            case Status::YOUTUBE:
                $widgets = $this->youtubeAnalytics($account);
                break;
            case Status::LINKEDIN:
                $widgets = $this->linkedinAnalytics($account);
                break;
            case Status::TWITTER:
                $widgets = $this->twitterAnalytics($account);
                break;
            default:
                break;
        }
        return view('Admin::posts.analytics', compact('pageTitle', 'socialAccounts', 'widgets'));
    }




    private function facebookAnalytics($account)
    {
        $accessToken = $account->access_token;
        $pageId = $account->profile_id;

        $pageInfoUrl = "https://graph.facebook.com/v23.0/{$pageId}?access_token={$accessToken}&fields=fan_count,followers_count,name,picture";
        $pageInfo = json_decode(CurlRequest::curlContent($pageInfoUrl));

        $postsSummaryUrl = "https://graph.facebook.com/v23.0/{$pageId}?fields=published_posts.limit(1).summary(true)&access_token={$accessToken}";
        $postsSummaryResponse = json_decode(CurlRequest::curlContent($postsSummaryUrl));
        $totalPostsCount = $postsSummaryResponse->published_posts->summary->total_count ?? 0;

        $postsUrl = "https://graph.facebook.com/v23.0/{$pageId}/posts"
            . "?access_token={$accessToken}"
            . "&fields=id,message,full_picture,created_time,likes.summary(true),comments.summary(true),shares"
            . "&limit=100";

        $postsResponse = json_decode(CurlRequest::curlContent($postsUrl));
        $latestPosts = $postsResponse->data ?? [];

        $monthlyStats = [];
        for ($i = 4; $i >= 0; $i--) {
            $monthKey = date("Y-m", strtotime("-{$i} month"));
            $monthlyStats[$monthKey] = [
                "posts"      => 0,
                "likes"      => 0,
                "comments"   => 0,
                "shares"     => 0,
                "engagement" => 0
            ];
        }

        if (!empty($postsResponse->data)) {
            foreach ($postsResponse->data as $post) {
                $monthKey = date("Y-m", strtotime($post->created_time));
                if (isset($monthlyStats[$monthKey])) {
                    $likes    = $post->likes->summary->total_count ?? 0;
                    $comments = $post->comments->summary->total_count ?? 0;
                    $shares   = $post->shares->count ?? 0;

                    $monthlyStats[$monthKey]["posts"]      += 1;
                    $monthlyStats[$monthKey]["likes"]      += $likes;
                    $monthlyStats[$monthKey]["comments"]   += $comments;
                    $monthlyStats[$monthKey]["shares"]     += $shares;
                    $monthlyStats[$monthKey]["engagement"] += ($likes + $comments + $shares);
                }
            }
        }

        $buildWidget = function ($name, $field, $monthlyStats, $lifetimeTotal) {
            $months = array_keys($monthlyStats);
            $latestMonth = end($months);
            $prevMonth   = prev($months);

            $latestVal = $monthlyStats[$latestMonth][$field] ?? 0;
            $prevVal   = $monthlyStats[$prevMonth][$field] ?? 0;

            $percentChange = $prevVal > 0 ? round((($latestVal - $prevVal) / $prevVal) * 100, 2) : 0;
            $badge = $percentChange >= 0 ? "success" : "danger";

            $graphData = array_map(fn($m) => $m[$field], $monthlyStats);

            return [
                "total{$name}"   => $lifetimeTotal,
                "monthly{$name}" => $percentChange,
                "badge"          => $badge,
                "monthlyRecord"  => $graphData
            ];
        };

        $followers       = $pageInfo->followers_count ?? $pageInfo->fan_count ?? 0;
        $totalLikes      = array_sum(array_column($monthlyStats, "likes"));
        $totalComments   = array_sum(array_column($monthlyStats, "comments"));
        $totalShares     = array_sum(array_column($monthlyStats, "shares"));

        $totalEngagement = $totalLikes + $totalComments + $totalShares;

        $totalEngagementRate = $followers > 0
            ? round(($totalEngagement / $followers), 2)
            : 0;

        $widgets["followers"]   = $buildWidget("Followers", "posts", $monthlyStats, $followers);
        $widgets["posts"]       = $buildWidget("Posts", "posts", $monthlyStats, $totalPostsCount);
        $widgets["likes"]       = $buildWidget("Likes", "likes", $monthlyStats, $totalLikes);
        $widgets["comments"]    = $buildWidget("Comments", "comments", $monthlyStats, $totalComments);
        $widgets["shares"]      = $buildWidget("Shares", "shares", $monthlyStats, $totalShares);
        $widgets["engagement"]  = $buildWidget("Engagement", "engagement", $monthlyStats, $totalEngagementRate);
        $widgets["pageName"]    = $pageInfo->name;
        $widgets["profileImage"] = $pageInfo->picture?->data?->url;

        $latestPostsRaw = $postsResponse->data ?? [];
        $latestPosts = $this->normalizePosts($latestPostsRaw, 'facebook');
        $widgets["latestPosts"] = $latestPosts;
        return $widgets;
    }



    private function instagramAnalytics($account)
    {
        $accessToken = $account->access_token;
        $pageId      = $account->profile_id;

        $igAccountUrl = "https://graph.facebook.com/v23.0/{$pageId}"
            . "?fields=instagram_business_account"
            . "&access_token={$accessToken}";
        $igAccountResponse = json_decode(CurlRequest::curlContent($igAccountUrl));
        $igUserId = $igAccountResponse->instagram_business_account->id ?? null;

        if (!$igUserId) {
            return [
                "error" => "No Instagram Business Account connected to this Facebook Page."
            ];
        }

        $profileUrl = "https://graph.facebook.com/v23.0/{$igUserId}"
            . "?fields=name,username,profile_picture_url,followers_count,follows_count,media_count"
            . "&access_token={$accessToken}";
        $profileInfo = json_decode(CurlRequest::curlContent($profileUrl));

        $mediaUrl = "https://graph.facebook.com/v23.0/{$igUserId}/media"
            . "?fields=id,caption,media_url,permalink,timestamp,like_count,comments_count,thumbnail_url"
            . "&limit=100"
            . "&access_token={$accessToken}";
        $mediaResponse = json_decode(CurlRequest::curlContent($mediaUrl));
        $latestPosts   = $mediaResponse->data ?? [];

        $monthlyStats = [];
        for ($i = 4; $i >= 0; $i--) {
            $monthKey = date("Y-m", strtotime("-{$i} month"));
            $monthlyStats[$monthKey] = [
                "posts"      => 0,
                "likes"      => 0,
                "comments"   => 0,
                "shares"     => 0,
                "engagement" => 0
            ];
        }

        if (!empty($latestPosts)) {
            foreach ($latestPosts as $post) {
                $monthKey = date("Y-m", strtotime($post->timestamp));
                if (isset($monthlyStats[$monthKey])) {
                    $likes    = $post->like_count ?? 0;
                    $comments = $post->comments_count ?? 0;
                    $shares   = 0;

                    $monthlyStats[$monthKey]["posts"]      += 1;
                    $monthlyStats[$monthKey]["likes"]      += $likes;
                    $monthlyStats[$monthKey]["comments"]   += $comments;
                    $monthlyStats[$monthKey]["shares"]     += $shares;
                    $monthlyStats[$monthKey]["engagement"] += ($likes + $comments + $shares);
                }
            }
        }

        $buildWidget = function ($name, $field, $monthlyStats, $lifetimeTotal) {
            $months = array_keys($monthlyStats);
            $latestMonth = end($months);
            $prevMonth   = prev($months);

            $latestVal = $monthlyStats[$latestMonth][$field] ?? 0;
            $prevVal   = $monthlyStats[$prevMonth][$field] ?? 0;

            $percentChange = $prevVal > 0 ? round((($latestVal - $prevVal) / $prevVal) * 100, 2) : 0;
            $badge = $percentChange >= 0 ? "success" : "danger";

            $graphData = array_map(fn($m) => $m[$field], $monthlyStats);

            return [
                "total{$name}"   => $lifetimeTotal,
                "monthly{$name}" => $percentChange,
                "badge"          => $badge,
                "monthlyRecord"  => $graphData
            ];
        };

        $followers       = $profileInfo->followers_count ?? 0;
        $totalPosts      = $profileInfo->media_count ?? 0;
        $totalLikes      = array_sum(array_column($monthlyStats, "likes"));
        $totalComments   = array_sum(array_column($monthlyStats, "comments"));
        $totalShares     = 0;
        $totalEngagement = $totalLikes + $totalComments + $totalShares;

        $totalEngagementRate = $followers > 0
            ? round(($totalEngagement / $followers), 2)
            : 0;

        $widgets["followers"]   = $buildWidget("Followers", "posts", $monthlyStats, $followers);
        $widgets["posts"]       = $buildWidget("Posts", "posts", $monthlyStats, $totalPosts);
        $widgets["likes"]       = $buildWidget("Likes", "likes", $monthlyStats, $totalLikes);
        $widgets["comments"]    = $buildWidget("Comments", "comments", $monthlyStats, $totalComments);
        $widgets["shares"]      = $buildWidget("Shares", "shares", $monthlyStats, $totalShares);
        $widgets["engagement"]  = $buildWidget("Engagement", "engagement", $monthlyStats, $totalEngagementRate);
        $widgets["pageName"]    = $profileInfo->username ?? $profileInfo->name ?? '';
        $widgets["profileImage"] = $profileInfo->profile_picture_url ?? null;

        $latestPostsRaw = $mediaResponse->data ?? [];
        $latestPosts = $this->normalizePosts($latestPostsRaw, 'instagram');
        $widgets["latestPosts"] = $latestPosts;

        return $widgets;
    }


    private function youtubeAnalytics($account)
    {
        $apiKey = gs()->social_connect_credential->youtube->api_key;
        $channelId = $account->profile_id;

        $channelUrl = "https://www.googleapis.com/youtube/v3/channels"
            . "?part=snippet,statistics,contentDetails"
            . "&id={$channelId}"
            . "&key={$apiKey}";

        $channelResponse = json_decode(CurlRequest::curlContent($channelUrl));
        $channel = $channelResponse->items[0] ?? null;

        if (!$channel) {
            return ["error" => "Channel not found"];
        }

        $pageName     = $channel->snippet->title ?? '';
        $profileImage = $channel->snippet->thumbnails->default->url ?? '';
        $subscribers  = $channel->statistics->subscriberCount ?? 0;
        $totalVideos  = $channel->statistics->videoCount ?? 0;

        $uploadsPlaylist = $channel->contentDetails->relatedPlaylists->uploads ?? null;
        $videosUrl = "https://www.googleapis.com/youtube/v3/playlistItems"
            . "?part=snippet,contentDetails"
            . "&playlistId={$uploadsPlaylist}"
            . "&maxResults=50"
            . "&key={$apiKey}";

        $videosResponse = json_decode(CurlRequest::curlContent($videosUrl));
        $videos = $videosResponse->items ?? [];

        $videoIds = implode(',', array_map(fn($v) => $v->contentDetails->videoId ?? '', $videos));

        $statsUrl = "https://www.googleapis.com/youtube/v3/videos"
            . "?part=statistics,snippet"
            . "&id={$videoIds}"
            . "&key={$apiKey}";

        $statsResponse = json_decode(CurlRequest::curlContent($statsUrl));

        $videoStats = [];
        foreach ($statsResponse->items ?? [] as $item) {
            $videoStats[$item->id] = $item;
        }

        $monthlyStats = [];
        for ($i = 4; $i >= 0; $i--) {
            $monthKey = date("Y-m", strtotime("-{$i} month"));
            $monthlyStats[$monthKey] = [
                "posts"      => 0,
                "likes"      => 0,
                "comments"   => 0,
                "shares"     => 0,
                "engagement" => 0
            ];
        }

        $latestPostsRaw = [];
        foreach ($videos as $video) {
            $videoId   = $video->contentDetails->videoId ?? '';
            $snippet   = $video->snippet ?? null;
            $stats     = $videoStats[$videoId]->statistics ?? null;
            $published = $snippet->publishedAt ?? null;

            $monthKey = date("Y-m", strtotime($published));
            if (isset($monthlyStats[$monthKey])) {
                $likes    = $stats->likeCount ?? 0;
                $comments = $stats->commentCount ?? 0;
                $shares   = 0;

                $monthlyStats[$monthKey]["posts"]      += 1;
                $monthlyStats[$monthKey]["likes"]      += $likes;
                $monthlyStats[$monthKey]["comments"]   += $comments;
                $monthlyStats[$monthKey]["shares"]     += $shares;
                $monthlyStats[$monthKey]["engagement"] += ($likes + $comments + $shares);
            }


            $latestPostsRaw[] = (object) [
                "id"             => $videoId,
                "message"        => $snippet->title ?? '',
                "full_picture"   => $snippet->thumbnails->high->url ?? '',
                "created_time"   => $published,
                "likes"          => (object) ["summary" => ["total_count" => $stats->likeCount ?? 0]],
                "comments"       => (object) ["summary" => ["total_count" => $stats->commentCount ?? 0]],
                "permalink_url"  => "https://www.youtube.com/watch?v={$videoId}"
            ];
        }

        $buildWidget = function ($name, $field, $monthlyStats, $lifetimeTotal) {
            $months = array_keys($monthlyStats);
            $latestMonth = end($months);
            $prevMonth   = prev($months);

            $latestVal = $monthlyStats[$latestMonth][$field] ?? 0;
            $prevVal   = $monthlyStats[$prevMonth][$field] ?? 0;

            $percentChange = $prevVal > 0 ? round((($latestVal - $prevVal) / $prevVal) * 100, 2) : 0;
            $badge = $percentChange >= 0 ? "success" : "danger";

            $graphData = array_map(fn($m) => $m[$field], $monthlyStats);

            return [
                "total{$name}"   => $lifetimeTotal,
                "monthly{$name}" => $percentChange,
                "badge"          => $badge,
                "monthlyRecord"  => $graphData
            ];
        };

        $totalLikes    = array_sum(array_column($monthlyStats, "likes"));
        $totalComments = array_sum(array_column($monthlyStats, "comments"));
        $totalShares   = 0;
        $totalEngagement = $totalLikes + $totalComments + $totalShares;

        $totalEngagementRate = $subscribers > 0
            ? round(($totalEngagement / $subscribers), 2)
            : 0;

        $widgets["followers"]   = $buildWidget("Followers", "posts", $monthlyStats, $subscribers);
        $widgets["posts"]       = $buildWidget("Posts", "posts", $monthlyStats, $totalVideos);
        $widgets["likes"]       = $buildWidget("Likes", "likes", $monthlyStats, $totalLikes);
        $widgets["comments"]    = $buildWidget("Comments", "comments", $monthlyStats, $totalComments);
        $widgets["shares"]      = $buildWidget("Shares", "shares", $monthlyStats, $totalShares);
        $widgets["engagement"]  = $buildWidget("Engagement", "engagement", $monthlyStats, $totalEngagementRate);
        $widgets["pageName"]    = $pageName;
        $widgets["profileImage"] = $profileImage;

        $widgets["latestPosts"] = $this->normalizePosts($latestPostsRaw, 'youtube');
        return $widgets;
    }

    public function linkedinAnalytics($account)
    {
        $accessToken = $account->access_token;
        $authorUrn = "urn:li:person:{$account->profile_id}";
        $headers = ["Authorization: Bearer {$accessToken}"];

        $followersUrl = "https://api.linkedin.com/v2/networkSizes/{$authorUrn}?edgeType=CompanyFollowedByMember";
        $followersResponse = json_decode(CurlRequest::curlContent($followersUrl, $headers));
        $followersCount = $followersResponse->firstDegreeSize ?? 0;

        $postsUrl = "https://api.linkedin.com/v2/ugcPosts?q=authors&authors=List({$authorUrn})&count=100";
        $postsResponse = json_decode(CurlRequest::curlContent($postsUrl, $headers));
        $latestPostsRaw = $postsResponse->elements ?? [];

        $monthlyStats = [];
        for ($i = 4; $i >= 0; $i--) {
            $monthKey = date("Y-m", strtotime("-{$i} month"));
            $monthlyStats[$monthKey] = [
                "posts"      => 0,
                "likes"      => 0,
                "comments"   => 0,
                "shares"     => 0,
                "engagement" => 0
            ];
        }

        foreach ($latestPostsRaw as &$post) {
            $created = $post->created->time ?? null;
            if (!$created) continue;

            $monthKey = date("Y-m", $created / 1000);
            if (!isset($monthlyStats[$monthKey])) continue;

            $postUrn = $post->id;

            $socialUrl = "https://api.linkedin.com/v2/socialActions/{$postUrn}";
            $socialResp = json_decode(CurlRequest::curlContent($socialUrl, $headers));

            $likes    = $socialResp->likesSummary->totalLikes ?? 0;
            $comments = $socialResp->commentsSummary->totalFirstLevelComments ?? 0;
            $shares   = $socialResp->shareStatistics->shareCount ?? 0;

            $post->likes = $likes;
            $post->comments = $comments;
            $post->shares = $shares;

            $monthlyStats[$monthKey]["posts"] += 1;
            $monthlyStats[$monthKey]["likes"] += $likes;
            $monthlyStats[$monthKey]["comments"] += $comments;
            $monthlyStats[$monthKey]["shares"] += $shares;
            $monthlyStats[$monthKey]["engagement"] += ($likes + $comments + $shares);
        }

        $buildWidget = function ($name, $field, $monthlyStats, $lifetimeTotal) {
            $months = array_keys($monthlyStats);
            $latestMonth = end($months);
            $prevMonth   = prev($months);

            $latestVal = $monthlyStats[$latestMonth][$field] ?? 0;
            $prevVal   = $monthlyStats[$prevMonth][$field] ?? 0;

            $percentChange = $prevVal > 0 ? round((($latestVal - $prevVal) / $prevVal) * 100, 2) : 0;
            $badge = $percentChange >= 0 ? "success" : "danger";

            $graphData = array_map(fn($m) => $m[$field], $monthlyStats);

            return [
                "total{$name}"   => $lifetimeTotal,
                "monthly{$name}" => $percentChange,
                "badge"          => $badge,
                "monthlyRecord"  => $graphData
            ];
        };

        $totalLikes = array_sum(array_column($monthlyStats, "likes"));
        $totalComments = array_sum(array_column($monthlyStats, "comments"));
        $totalShares = array_sum(array_column($monthlyStats, "shares"));
        $totalEngagement = $totalLikes + $totalComments + $totalShares;
        $totalPostsCount = array_sum(array_column($monthlyStats, "posts"));

        $totalEngagementRate = $followersCount > 0
            ? round(($totalEngagement / $followersCount), 2)
            : 0;

        $widgets["followers"]    = $buildWidget("Followers", "posts", $monthlyStats, $followersCount);
        $widgets["posts"]        = $buildWidget("Posts", "posts", $monthlyStats, $totalPostsCount);
        $widgets["likes"]        = $buildWidget("Likes", "likes", $monthlyStats, $totalLikes);
        $widgets["comments"]     = $buildWidget("Comments", "comments", $monthlyStats, $totalComments);
        $widgets["shares"]       = $buildWidget("Shares", "shares", $monthlyStats, $totalShares);
        $widgets["engagement"]   = $buildWidget("Engagement", "engagement", $monthlyStats, $totalEngagementRate);
        $widgets["pageName"]     = $account->profile_name ?? null;
        $widgets["profileImage"] = $account->profile_image ?? null;

        $latestPosts = $this->normalizePosts($latestPostsRaw, 'linkedin');
        $widgets["latestPosts"] = $latestPosts;

        return $widgets;
    }


    private function twitterAnalytics($account)
    {
        $accessToken = $account->access_token;
        $headers = ["Authorization: Bearer {$accessToken}"];
        $userId = $account->profile_id;

        $userInfoUrl = "https://api.x.com/2/users/{$userId}";
        $userInfo = CurlRequest::curlContent($userInfoUrl, $headers);
        $userInfo = json_decode($userInfo, true);

        $followersCount = $userInfo['data']['public_metrics']['followers_count'] ?? 0;

        $tweetsUrl = "https://api.twitter.com/2/users/{$userId}/tweets"
            . "?tweet.fields=created_at,public_metrics"
            . "&max_results=100";
        $tweetsResponse =  CurlRequest::curlContent($tweetsUrl, $headers);
        $tweets = $tweetsResponse['data'] ?? [];

        $monthlyStats = [];
        for ($i = 4; $i >= 0; $i--) {
            $monthKey = date("Y-m", strtotime("-{$i} month"));
            $monthlyStats[$monthKey] = [
                "posts"      => 0,
                "likes"      => 0,
                "comments"   => 0,
                "shares"     => 0,
                "engagement" => 0
            ];
        }

        foreach ($tweets as $tweet) {
            $created = $tweet['created_at'];
            $metrics = $tweet['public_metrics'];
            $monthKey = date("Y-m", strtotime($created));

            if (!isset($monthlyStats[$monthKey])) {
                continue;
            }

            $monthlyStats[$monthKey]['posts'] += 1;
            $likes = $metrics['like_count'] ?? 0;
            $replies = $metrics['reply_count'] ?? 0;
            $retweets = $metrics['retweet_count'] ?? 0;
            $quotes = $metrics['quote_count'] ?? 0;
            $shares = $retweets + $quotes;

            $monthlyStats[$monthKey]['likes']    += $likes;
            $monthlyStats[$monthKey]['comments'] += $replies;
            $monthlyStats[$monthKey]['shares']   += $shares;

            $monthlyStats[$monthKey]['engagement'] += ($likes + $replies + $shares);
        }

        $buildWidget = function ($name, $field, $monthlyStats, $lifetimeTotal) {
            $months = array_keys($monthlyStats);
            $latestMonth = end($months);
            $prevMonth = prev($months);

            $latestVal = $monthlyStats[$latestMonth][$field] ?? 0;
            $prevVal = $monthlyStats[$prevMonth][$field] ?? 0;

            $percentChange = $prevVal > 0
                ? round((($latestVal - $prevVal) / $prevVal) * 100, 2)
                : 0;

            $badge = $percentChange >= 0 ? "success" : "danger";

            $graphData = array_map(fn($m) => $m[$field], $monthlyStats);

            return [
                "total{$name}"   => $lifetimeTotal,
                "monthly{$name}" => $percentChange,
                "badge"          => $badge,
                "monthlyRecord"  => $graphData
            ];
        };

        $totalPosts      = array_sum(array_column($monthlyStats, 'posts'));
        $totalLikes      = array_sum(array_column($monthlyStats, 'likes'));
        $totalComments   = array_sum(array_column($monthlyStats, 'comments'));
        $totalShares     = array_sum(array_column($monthlyStats, 'shares'));
        $totalEngagement = array_sum(array_column($monthlyStats, 'engagement'));

        $engagementRate = $followersCount > 0
            ? round($totalEngagement / $followersCount, 2)
            : 0;

        $latestPosts = $this->normalizePosts($tweets, 'twitter');

        $widgets = [];
        $widgets["followers"]  = $buildWidget("Followers", "posts",      $monthlyStats, $followersCount);
        $widgets["posts"]      = $buildWidget("Posts",     "posts",      $monthlyStats, $totalPosts);
        $widgets["likes"]      = $buildWidget("Likes",     "likes",      $monthlyStats, $totalLikes);
        $widgets["comments"]   = $buildWidget("Comments",  "comments",   $monthlyStats, $totalComments);
        $widgets["shares"]     = $buildWidget("Shares",    "shares",     $monthlyStats, $totalShares);
        $widgets["engagement"] = $buildWidget("Engagement", "engagement", $monthlyStats, $engagementRate);
        $widgets["pageName"]   = $userInfo['data']['username'] ?? ($userInfo['data']['name'] ?? "Unknown");
        $widgets["profileImage"] = $userInfo['data']['profile_image_url'] ?? null;

        $widgets["latestPosts"] = $latestPosts;

        return $widgets;
    }

    private function normalizePosts($posts, $platform)
    {
        $normalized = [];

        foreach ($posts as $post) {
            if ($platform === 'facebook') {
                $normalized[] = [
                    'id'        => $post->id ?? null,
                    'image'     => $post->full_picture ?? null,
                    'text'      => $post->message ?? '',
                    'date'      => $post->created_time ?? null,
                    'likes'     => $post->likes->summary->total_count ?? 0,
                    'comments'  => $post->comments->summary->total_count ?? 0,
                    'permalink' => "https://facebook.com/{$post->id}"
                ];
            }

            if ($platform === 'instagram') {
                $normalized[] = [
                    'id'        => $post->id ?? null,
                    'image'     => isset($post->thumbnail_url) ? $post->thumbnail_url : $post->media_url ?? null,
                    'text'      => $post->caption ?? '',
                    'date'      => $post->timestamp ?? null,
                    'likes'     => $post->like_count ?? 0,
                    'comments'  => $post->comments_count ?? 0,
                    'permalink' => $post->permalink ?? null
                ];
            }

            if ($platform === 'youtube') {
                $normalized[] = [
                    'id'        => $post->id ?? null,
                    'image'     => $post->full_picture ?? null,
                    'text'      => $post->message ?? '',
                    'date'      => $post->created_time ?? null,
                    'likes'     => $post->likes->summary->total_count ?? 0,
                    'comments'  => $post->comments->summary->total_count ?? 0,
                    'permalink' => $post->permalink_url ?? ''
                ];
            }

            if ($platform === 'linkedin') {
                foreach ($posts as $post) {
                    $normalized[] = [
                        'id'        => $post->id ?? null,
                        'image'     => $post->specificContent->{'com.linkedin.ugc.ShareContent'}->media[0]->media ?? null,
                        'text'      => $post->specificContent->{'com.linkedin.ugc.ShareContent'}->shareCommentary->text ?? '',
                        'date'      => isset($post->created->time) ? date('Y-m-d H:i:s', $post->created->time / 1000) : null,
                        'likes'     => $post->likes ?? 0,
                        'comments'  => $post->comments ?? 0,
                        'permalink' => "https://www.linkedin.com/feed/update/{$post->id}"
                    ];
                }
            }

            if ($platform === 'twitter') {
                foreach ($posts as $post) {
                    $normalized[] = [
                        'id'        => $post['id'] ?? null,
                        'image'     => $post['attachments'][0]['media_url'] ?? null,
                        'text'      => $post['text'] ?? '',
                        'date'      => $post['created_at'] ?? null,
                        'likes'     => $post['public_metrics']['like_count'] ?? 0,
                        'comments'  => $post['public_metrics']['reply_count'] ?? 0,
                        'shares'    => ($post['public_metrics']['retweet_count'] ?? 0) + ($post['public_metrics']['quote_count'] ?? 0),
                        'permalink' => "https://twitter.com/{$post['author_id']}/status/{$post['id']}"
                    ];
                }
            }

            if ($platform === 'telegram') {
                $normalized[] = [
                    'id'        => $post->id ?? null,
                    'image'     => getImage(getFilePath('postMedia') . '/' . $post->medias[0]?->media_path ?? null) ?? null,
                    'text'      => $post->content ?? '',
                    'date'      => $post->created_at ?? null,
                    'likes'     => 0,
                    'comments'  => 0,
                    'shares'    => 0,
                    'permalink' => '',
                ];
            }
        }

        return $normalized;
    }

    
}
