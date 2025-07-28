<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Nest;
use App\Models\User;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        // Scan for u/{username} pattern
        if (preg_match('/u\/(\w+)/', $q, $matches)) {
            $user = User::where('username', $matches[1])->first();
            if ($user) {
                return redirect()->route('users.show', $user->id);
            }
        }
        $posts = Post::where('title', 'like', "%$q%")->limit(5)->get();
        $nests = Nest::where('name', 'like', "%$q%")->limit(5)->get();
        $users = User::where('username', 'like', "%$q%")->limit(5)->get();
        return view('search.index', compact('posts', 'nests', 'users'));
    }
    // For full results
    public function posts(Request $request) {
        $q = $request->input('q');
        $posts = Post::where('title', 'like', "%$q%")->paginate(20);
        return view('search.posts', compact('posts'));
    }
    public function nests(Request $request) {
        $q = $request->input('q');
        $nests = Nest::where('name', 'like', "%$q%")->paginate(20);
        return view('search.nests', compact('nests'));
    }
    public function users(Request $request) {
        $q = $request->input('q');
        $users = User::where('username', 'like', "%$q%")->paginate(20);
        return view('search.users', compact('users'));
    }
}
