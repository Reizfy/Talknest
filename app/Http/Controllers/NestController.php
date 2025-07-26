<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nest;

class NestController extends Controller
{
    public function index($name)
    {
        $nest = Nest::with(['owner', 'moderators', 'users'])->where('name', $name)->firstOrFail();

        $user = auth()->user();
        $isJoined = 0;
        if ($user) {
            $isJoined = $nest->users->contains($user->id) ? 1 : 0;
        }

        $ownerUsername = $nest->owner ? ($nest->owner->username ?? $nest->owner->name ?? $nest->owner->email) : null;
        $moderatorUsernames = $nest->moderators
            ->reject(function($moderator) use ($ownerUsername) {
                $modUsername = $moderator->username ?? $moderator->name ?? $moderator->email;
                return $modUsername === $ownerUsername;
            })
            ->map(function($moderator) {
                return $moderator->username ?? $moderator->name ?? $moderator->email;
            })->values()->toArray();

        $isOwner = $user && $nest->owner_id === $user->id;
        $data = [
            'nest' => $nest,
            'memberCount' => $nest->users()->count(),
            'isJoined' => $isJoined,
            'ownerUsername' => $ownerUsername,
            'moderatorUsernames' => $moderatorUsernames,
            'canJoin' => $user ? true : false,
            'canCreate' => $user ? true : false,
            'isOwner' => $isOwner,
        ];
        return view('nests.nests', $data);
    }

    public function posts($name)
    {
        $nest = Nest::where('name', $name)->firstOrFail();
        $sort = request('sort', 'popular');
        $query = $nest->posts()
            ->select(['id', 'title', 'content', 'media', 'created_at', 'user_id'])
            ->with(['user:id,username,avatar'])
            ->withCount('comments')
            ->with(['votes']);

        if ($sort === 'latest') {
            $query->orderBy('created_at', 'desc');
        } else {
            // Sort by votes_count (popular)
            // Need to sort after fetching, since votes are summed in transform
            $query->orderBy('created_at', 'desc');
        }

        $posts = $query->paginate(10);

        $user = auth()->user();
        $posts->getCollection()->transform(function($post) use ($user) {
            $currentUserVote = 0;
            if ($user) {
                $vote = $post->votes->where('user_id', $user->id)->first();
                $currentUserVote = $vote ? (int)$vote->value : 0;
            }
            return [
                'id' => $post->id,
                'title' => $post->title,
                'username' => $post->user ? $post->user->username : null,
                'user_image' => $post->user && $post->user->avatar ? $post->user->avatar : null,
                'created_at' => $post->created_at,
                'media' => $post->media,
                'content' => $post->content,
                'votes_count' => $post->votes->sum('value'),
                'comments_count' => $post->comments_count,
                'current_user_vote' => $currentUserVote,
            ];
        });

        // If sorting by popular, sort the collection by votes_count
        if ($sort === 'best') {
            $posts->setCollection(
                $posts->getCollection()->sortByDesc('votes_count')->values()
            );
        }

        return response()->json($posts);
    }

    public function comments($nest, $post_id)
    {
        $post = \App\Models\Post::with(['user:id,username,avatar', 'comments.user:id,username,avatar'])->findOrFail($post_id);
        $nestModel = \App\Models\Nest::where('name', $nest)->firstOrFail();
        if ($post->nest_id !== $nestModel->id) {
            abort(404);
        }
        $comments = $post->comments->map(function($comment) {
            return [
                'id' => $comment->id,
                'content' => $comment->content,
                'username' => $comment->user ? $comment->user->username : null,
                'user_image' => $comment->user && $comment->user->avatar ? $comment->user->avatar : null,
                'created_at' => $comment->created_at,
                'parent_id' => $comment->parent_id,
            ];
        })->values()->all();

        // Build nested comment tree
        $items = [];
        foreach ($comments as $comment) {
            $comment['replies'] = [];
            $items[$comment['id']] = $comment;
        }
        $tree = [];
        foreach ($items as $id => &$item) {
            if ($item['parent_id']) {
                $items[$item['parent_id']]['replies'][] = &$item;
            } else {
                $tree[] = &$item;
            }
        }

        $user = auth()->user();
        $currentUserVote = 0;
        if ($user) {
            $vote = $post->votes()->where('user_id', $user->id)->first();
            $currentUserVote = $vote ? (int)$vote->value : 0;
        }
        return response()->json([
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'media' => $post->media,
                'created_at' => $post->created_at,
                'username' => $post->user ? $post->user->username : null,
                'user_image' => $post->user && $post->user->avatar ? $post->user->avatar : null,
                'nest_name' => $nestModel->name,
                'user_avatar' => $post->user && $post->user->avatar ? $post->user->avatar : null,
                'votes_count' => $post->votes->sum('value'),
                'current_user_vote' => $currentUserVote,
            ],
            'comments' => $tree,
        ]);
    }

    public function vote(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $request->validate([
            'value' => 'required|in:1,-1,0',
        ]);
        $post = \App\Models\Post::findOrFail($id);
        $vote = $post->votes()->where('user_id', $user->id)->first();
        $value = (int) $request->input('value');
        if ($value === 0) {
            if ($vote) $vote->delete();
        } else if ($vote) {
            $vote->value = $value;
            $vote->save();
        } else {
            $post->votes()->create([
                'user_id' => $user->id,
                'value' => $value,
            ]);
        }
        // Return updated vote count and user vote
        $votes_count = $post->votes()->sum('value');
        return response()->json([
            'votes_count' => $votes_count,
            'current_user_vote' => $value,
        ]);
    }

    public function create()
    {
        return view('nests.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('register');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:nests',
            'description' => 'required|string',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $nest = new \App\Models\Nest();
        $nest->name = $request->name;
        $nest->description = $request->description;
        $nest->owner_id = auth()->id();

        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('nests/banners', 'public');
            $nest->banner = basename($bannerPath);
        }
        if ($request->hasFile('profile_image')) {
            $profilePath = $request->file('profile_image')->store('nests/profiles', 'public');
            $nest->profile_image = basename($profilePath);
        }
        $nest->save();

        $nest->users()->attach(auth()->id());

        return redirect()->route('nests.index', $nest->name)->with('success', 'Nest created successfully!');
    }

    public function storePost(Request $request, $nest)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:4096',
        ]);

        $nestModel = \App\Models\Nest::where('name', $nest)->firstOrFail();
        $post = new \App\Models\Post();
        $post->title = $request->title;
        $post->content = $request->description;
        $post->nest_id = $nestModel->id;
        $post->user_id = auth()->id();

        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('posts', 'public');
            $post->media = basename($mediaPath);
        }

        $post->save();

        return redirect()->route('nests.index', $nestModel->name)->with('success', 'Post created successfully!');
    }

    public function edit($name)
    {
        $nest = \App\Models\Nest::where('name', $name)->firstOrFail();
        if (auth()->id() !== $nest->owner_id) {
            abort(403);
        }
        return view('nests.edit', compact('nest'));
    }

    public function update(Request $request, $name)
    {
        $nest = \App\Models\Nest::where('name', $name)->firstOrFail();
        // Only owner can update
        if (auth()->id() !== $nest->owner_id) {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:nests,name,' . $nest->id,
            'description' => 'required|string',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $nest->name = $request->name;
        $nest->description = $request->description;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('nests/banners', 'public');
            $nest->banner = basename($bannerPath);
        }
        if ($request->hasFile('profile_image')) {
            $profilePath = $request->file('profile_image')->store('nests/profiles', 'public');
            $nest->profile_image = basename($profilePath);
        }
        $nest->save();
        return redirect()->route('nests.index', $nest->name)->with('success', 'Nest updated successfully!');
    }

    public function destroy($name)
    {
        $nest = \App\Models\Nest::where('name', $name)->firstOrFail();
        if (auth()->id() !== $nest->owner_id) {
            abort(403);
        }
        $moderator = $nest->moderators()->where('id', '!=', $nest->owner_id)->first();
        if ($moderator) {
            $nest->owner_id = $moderator->id;
            $nest->save();
            return redirect()->route('nests.index', $nest->name)->with('success', 'Ownership transferred to moderator.');
        } else {
            $nest->delete();
            return redirect()->route('home')->with('success', 'Nest deleted successfully!');
        }
    }

    // Promote a member to moderator
    public function promote($nest, $user)
    {
        $nest = \App\Models\Nest::where('name', $nest)->firstOrFail();
        $currentUser = auth()->user();
        if (!$currentUser || $nest->owner_id !== $currentUser->id) {
            return redirect()->back()->with('error', 'Only the owner can promote members.');
        }
        if ($nest->moderators->contains($user)) {
            return redirect()->back()->with('error', 'User is already a moderator.');
        }
        if ($user == $nest->owner_id) {
            return redirect()->back()->with('error', 'Owner cannot be promoted.');
        }
        $nest->moderators()->attach($user);
        return redirect()->back()->with('success', 'Member promoted to moderator.');
    }

    // Kick a member from the nest
    public function kick($nest, $user)
    {
        $nest = \App\Models\Nest::where('name', $nest)->firstOrFail();
        $currentUser = auth()->user();
        $isOwner = $currentUser && $nest->owner_id === $currentUser->id;
        $isModerator = $currentUser && $nest->moderators->contains($currentUser->id);
        if (!$isOwner && !$isModerator) {
            return redirect()->back()->with('error', 'Only owner or moderators can kick members.');
        }
        if ($user == $nest->owner_id) {
            return redirect()->back()->with('error', 'Cannot kick the owner.');
        }
        // Moderators cannot kick other moderators
        if ($isModerator && $nest->moderators->contains($user)) {
            return redirect()->back()->with('error', 'Moderators cannot kick other moderators.');
        }
        $nest->users()->detach($user);
        $nest->moderators()->detach($user);
        return redirect()->back()->with('success', 'Member has been kicked.');
    }

}
