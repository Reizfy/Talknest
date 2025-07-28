<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nest;


class HomeController extends Controller
{
    /*
     * Dashboard Pages Routs
     */
    public function index(Request $request)
    {
        $assets = ['chart', 'animation'];
        $nests = Nest::select('id', 'name', 'banner')->get();
        return view('home.home', compact('assets', 'nests'));
    }

    // API: Get home feed posts (random, prefer joined nests for logged in)
    public function homeFeed(Request $request)
    {
        $user = auth()->user();
        $limit = $request->input('limit', 10);
        $postsQuery = \App\Models\Post::with(['user', 'nest', 'votes', 'comments']);
        if ($user) {
            $joinedNestIds = $user->nests()->pluck('nests.id')->toArray();
            $postsQuery->whereIn('nest_id', $joinedNestIds)->inRandomOrder();
            // If not enough, fill with random from other nests
            $posts = $postsQuery->limit($limit)->get();
            if ($posts->count() < $limit) {
                $otherPosts = \App\Models\Post::with(['user', 'nest', 'votes', 'comments'])
                    ->whereNotIn('nest_id', $joinedNestIds)
                    ->inRandomOrder()
                    ->limit($limit - $posts->count())
                    ->get();
                $posts = $posts->concat($otherPosts);
            }
        } else {
            $posts = $postsQuery->inRandomOrder()->limit($limit)->get();
        }
        // Format for frontend
        $result = $posts->map(function($post) use ($user) {
            $currentUserVote = 0;
            if ($user) {
                $vote = $post->votes->where('user_id', $user->id)->first();
                $currentUserVote = $vote ? (int)$vote->value : 0;
            }
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'media' => $post->media,
                'created_at' => $post->created_at,
                'username' => $post->user ? $post->user->username : null,
                'user_image' => $post->user && $post->user->avatar ? $post->user->avatar : null,
                'nest_name' => $post->nest ? $post->nest->name : null,
                'votes_count' => $post->votes->sum('value'),
                'comments_count' => $post->comments->count(),
                'current_user_vote' => $currentUserVote,
            ];
        });
        return response()->json(['data' => $result]);
    }


        // API: Get recent posts for sidebar (joined nests for user, random for guest)
    public function sidebarRecentPosts(Request $request)
    {
        $user = auth()->user();
        $limit = $request->input('limit', 5);
        $postsQuery = \App\Models\Post::with(['user', 'nest', 'votes', 'comments']);
        if ($user) {
            $joinedNestIds = $user->nests()->pluck('nests.id')->toArray();
            $postsQuery->whereIn('nest_id', $joinedNestIds)->orderBy('created_at', 'desc');
            $posts = $postsQuery->limit($limit)->get();
            if ($posts->count() < $limit) {
                $otherPosts = \App\Models\Post::with(['user', 'nest', 'votes', 'comments'])
                    ->whereNotIn('nest_id', $joinedNestIds)
                    ->orderBy('created_at', 'desc')
                    ->limit($limit - $posts->count())
                    ->get();
                $posts = $posts->concat($otherPosts);
            }
        } else {
            $posts = $postsQuery->inRandomOrder()->limit($limit)->get();
        }
        $result = $posts->map(function($post) use ($user) {
            $currentUserVote = 0;
            if ($user) {
                $vote = $post->votes->where('user_id', $user->id)->first();
                $currentUserVote = $vote ? (int)$vote->value : 0;
            }
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'media' => $post->media,
                'created_at' => $post->created_at,
                'username' => $post->user ? $post->user->username : null,
                'user_image' => $post->user && $post->user->avatar ? $post->user->avatar : null,
                'nest_name' => $post->nest ? $post->nest->name : null,
                'nest_image' => $post->nest && $post->nest->profile ? $post->nest->profile : null,
                'votes_count' => $post->votes->sum('value'),
                'comments_count' => $post->comments->count(),
                'current_user_vote' => $currentUserVote,
            ];
        });
        return response()->json(['data' => $result]);
    }

    /*
     * Menu Style Routs
     */
    public function horizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.horizontal',compact('assets'));
    }
    public function dualhorizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-horizontal',compact('assets'));
    }
    public function dualcompact(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-compact',compact('assets'));
    }
    public function boxed(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed',compact('assets'));
    }
    public function boxedfancy(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed-fancy',compact('assets'));
    }

    /*
     * Pages Routs
     */
    public function billing(Request $request)
    {
        return view('special-pages.billing');
    }

    public function calender(Request $request)
    {
        $assets = ['calender'];
        return view('special-pages.calender',compact('assets'));
    }

    public function kanban(Request $request)
    {
        return view('special-pages.kanban');
    }

    public function pricing(Request $request)
    {
        return view('special-pages.pricing');
    }

    public function rtlsupport(Request $request)
    {
        return view('special-pages.rtl-support');
    }

    public function timeline(Request $request)
    {
        return view('special-pages.timeline');
    }


    /*
     * Widget Routs
     */
    public function widgetbasic(Request $request)
    {
        return view('widget.widget-basic');
    }
    public function widgetchart(Request $request)
    {
        $assets = ['chart'];
        return view('widget.widget-chart', compact('assets'));
    }
    public function widgetcard(Request $request)
    {
        return view('widget.widget-card');
    }

    /*
     * Maps Routs
     */
    public function google(Request $request)
    {
        return view('maps.google');
    }
    public function vector(Request $request)
    {
        return view('maps.vector');
    }

    /*
     * Auth Routs
     */
    public function signin(Request $request)
    {
        return view('auth.login');
    }
    public function signup(Request $request)
    {
        return view('auth.register');
    }
    public function confirmmail(Request $request)
    {
        return view('auth.confirm-mail');
    }
    public function lockscreen(Request $request)
    {
        return view('auth.lockscreen');
    }
    public function recoverpw(Request $request)
    {
        return view('auth.recoverpw');
    }
    public function userprivacysetting(Request $request)
    {
        return view('auth.user-privacy-setting');
    }

    /*
     * Error Page Routs
     */

    public function error404(Request $request)
    {
        return view('errors.error404');
    }

    public function error500(Request $request)
    {
        return view('errors.error500');
    }
    public function maintenance(Request $request)
    {
        return view('errors.maintenance');
    }

    /*
     * uisheet Page Routs
     */
    public function uisheet(Request $request)
    {
        return view('uisheet');
    }

    /*
     * Form Page Routs
     */
    public function element(Request $request)
    {
        return view('forms.element');
    }

    public function wizard(Request $request)
    {
        return view('forms.wizard');
    }

    public function validation(Request $request)
    {
        return view('forms.validation');
    }

     /*
     * Table Page Routs
     */
    public function bootstraptable(Request $request)
    {
        return view('table.bootstraptable');
    }

    public function datatable(Request $request)
    {
        return view('table.datatable');
    }

    /*
     * Icons Page Routs
     */

    public function solid(Request $request)
    {
        return view('icons.solid');
    }

    public function outline(Request $request)
    {
        return view('icons.outline');
    }

    public function dualtone(Request $request)
    {
        return view('icons.dualtone');
    }

    public function colored(Request $request)
    {
        return view('icons.colored');
    }

    /*
     * Extra Page Routs
     */
    public function privacypolicy(Request $request)
    {
        return view('privacy-policy');
    }
    public function termsofuse(Request $request)
    {
        return view('terms-of-use');
    }
}
