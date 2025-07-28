<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\UsersDataTable;
use App\Models\User;
use App\Helpers\AuthHelper;
use Spatie\Permission\Models\Role;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UsersDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title',['form' => trans('users.title')] );
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('users.create').'" class="btn btn-sm btn-primary" role="button">Add User</a>';
        return $dataTable->render('global.datatable', compact('pageTitle','auth_user','assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('status',1)->get()->pluck('title', 'id');

        return view('users.form', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $request['password'] = bcrypt($request->password);

        $request['username'] = $request->username ?? stristr($request->email, "@", true) . rand(100,1000);

        $user = User::create($request->all());

        storeMediaFile($user,$request->profile_image, 'profile_image');

        $user->assignRole('user');

        // No userProfile relationship, only User fields are used now.

        return redirect()->route('users.index')->withSuccess(__('message.msg_added',['name' => __('users.store')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $profileImage = getSingleMedia($user, 'profile_image');
        return view('users.profile', compact('user', 'profileImage'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        // dd($request->all());
        $user = User::findOrFail($id);

        $role = Role::find($request->user_role);
        if(env('IS_DEMO')) {
            if($role->name === 'admin'&& $user->user_type === 'admin') {
                return redirect()->back()->with('error', 'Permission denied');
            }
        }
        $user->assignRole($role->name);

        $request['password'] = $request->password != '' ? bcrypt($request->password) : $user->password;

        // User user data...
        $user->fill($request->all())->update();

        // Save user image...
        if (isset($request->profile_image) && $request->profile_image != null) {
            $user->clearMediaCollection('profile_image');
            $user->addMediaFromRequest('profile_image')->toMediaCollection('profile_image');
        }

        // No userProfile relationship, only User fields are used now.

        if(auth()->check()){
            return redirect()->route('users.index')->withSuccess(__('message.msg_updated',['name' => __('message.user')]));
        }
        return redirect()->back()->withSuccess(__('message.msg_updated',['name' => 'My Profile']));

    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $status = 'errors';
        $message= __('global-message.delete_form', ['form' => __('users.title')]);

        if($user!='') {
            $user->delete();
            $status = 'success';
            $message= __('global-message.delete_form', ['form' => __('users.title')]);
        }

        if(request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
        }

        return redirect()->back()->with($status,$message);

    }

    public function userFeedPosts(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['data' => []]);
        }
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $query = \App\Models\Post::with(['nest', 'votes', 'comments'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');
        $posts = $query->skip(($page-1)*$limit)->take($limit)->get();
        $result = $posts->map(function($post) use ($user) {
            $currentUserVote = $post->votes->where('user_id', $user->id)->first()?->value ?? 0;
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'media' => $post->media,
                'created_at' => $post->created_at,
                'username' => $user->username,
                'user_image' => $user->avatar,
                'nest_image' => $post->nest ? $post->nest->profile_image : null,
                'nest_name' => $post->nest ? $post->nest->name : null,
                'votes_count' => $post->votes->sum('value'),
                'comments_count' => $post->comments->count(),
                'current_user_vote' => $currentUserVote,
            ];
        });
        return response()->json(['data' => $result]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $data = $request->only(['username', 'full_name', 'email', 'gender']);
        // Username uniqueness check
        if (User::where('username', $data['username'])->where('id', '!=', $user->id)->exists()) {
            return redirect()->back()->withErrors(['username' => 'Username is already taken.'])->withInput();
        }
        // Email uniqueness check
        if (User::where('email', $data['email'])->where('id', '!=', $user->id)->exists()) {
            return redirect()->back()->withErrors(['email' => 'Email is already taken.'])->withInput();
        }

        // Password update logic
        if ($request->filled('current_password') || $request->filled('new_password') || $request->filled('new_password_confirmation')) {
            if (! $request->filled('current_password')) {
                return redirect()->back()->withErrors(['current_password' => 'Current password is required to change password.'])->withInput();
            }
            if (! \Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
            if (! $request->filled('new_password')) {
                return redirect()->back()->withErrors(['new_password' => 'New password is required.'])->withInput();
            }
            if ($request->new_password !== $request->new_password_confirmation) {
                return redirect()->back()->withErrors(['new_password_confirmation' => 'New password confirmation does not match.'])->withInput();
            }
            if (strlen($request->new_password) < 8) {
                return redirect()->back()->withErrors(['new_password' => 'New password must be at least 8 characters.'])->withInput();
            }
            $data['password'] = bcrypt($request->new_password);
        }

        $user->fill($data);
        // Avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = uniqid('avatar_') . '.' . $avatar->getClientOriginalExtension();
            $avatar->storeAs('public/profiles/images', $avatarName);
            $user->avatar = $avatarName;
        }
        // Banner upload
        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $bannerName = uniqid('banner_') . '.' . $banner->getClientOriginalExtension();
            $banner->storeAs('public/profiles/banners', $bannerName);
            $user->banner = $bannerName;
        }
        $user->save();
        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
