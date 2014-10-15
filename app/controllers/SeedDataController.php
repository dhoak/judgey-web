<?php

class SeedDataController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function store()
	{
        $user = DB::select('select * from USERS where FB_ID = ?', array(Input::get('user.fb_id')));
        $date = date('Y-m-d H:i:s');
        $this->saveSeedData($user, $date);
        Log::error("in SeedDataController.store method.");

		return Input::get('user.name');
	}

    private function saveSeedData($user, $date)
    {
        if(!empty($user))
        {
            DB::update('update USERS set NAME = ?, EMAIL = ?, LAST_LOGIN = ? where FB_ID = ?', array(Input::get('user.name'), Input::get('user.email'), $date, Input::get('user.fb_id')));
        }
        else
        {
            DB::insert('insert into USERS (FB_ID, NAME, EMAIL, CREATED, LAST_LOGIN) values (?, ?, ?, ?, ?)', array(Input::get('user.fb_id'), Input::get('user.name'), Input::get('user.email'), $date, $date));
            $user = DB::select('select * from USERS where FB_ID = ?', array(Input::get('user.fb_id')));
        }

        $this->saveUserBackground($user, $date);
        $this->saveUserLikes($user, $date);
        $this->saveUserPosts($user, $date);
    }

    private function saveUserBackground($user, $date)
    {
        DB::delete('delete from BACKGROUND where USER_ID = ?', array($user[0]->ID));
        DB::insert('insert into BACKGROUND (USER_ID, BG_KEY, BG_VALUE, CREATED) values (?, ?, ?, ?)', array($user[0]->ID, 'hometown', Input::get('background.hometown'), $date));
        DB::insert('insert into BACKGROUND (USER_ID, BG_KEY, BG_VALUE, CREATED) values (?, ?, ?, ?)', array($user[0]->ID, 'relationship_status', Input::get('background.relationship_status'), $date));
        DB::insert('insert into BACKGROUND (USER_ID, BG_KEY, BG_VALUE, CREATED) values (?, ?, ?, ?)', array($user[0]->ID, 'location', Input::get('background.location'), $date));
        DB::insert('insert into BACKGROUND (USER_ID, BG_KEY, BG_VALUE, CREATED) values (?, ?, ?, ?)', array($user[0]->ID, 'gender', Input::get('background.gender'), $date));
//        DB::insert('insert into BACKGROUND (USER_ID, BG_KEY, BG_VALUE, CREATED) values (?, ?, ?, ?)', array($user[0]->ID, 'education', Input::get('background.education'), $date));
//        DB::insert('insert into BACKGROUND (USER_ID, BG_KEY, BG_VALUE, CREATED) values (?, ?, ?, ?)', array($user[0]->ID, 'work', Input::get('background.work'), $date));
    }

    private function saveUserLikes($user, $date)
    {
        DB::delete('delete from LIKES where USER_ID = ?', array($user[0]->ID));

        $likes = Input::get('likes.data');
        foreach ($likes as &$like)
        {
//            Log::error($like);
            DB::insert('insert into LIKES (USER_ID, CATEGORY, NAME, CREATED) values (?, ?, ?, ?)', array($user[0]->ID, $like['category'], $like['name'], $date));
        }
    }

    private function saveUserPosts($user, $date)
    {
        DB::delete('delete from POSTS where USER_ID = ?', array($user[0]->ID));

        $posts = Input::get('posts.data');
        foreach ($posts as &$post)
        {
//            Log::error($post);
            DB::insert('insert into POSTS (USER_ID, POST_DATE, POST, CREATED) values (?, ?, ?, ?)', array($user[0]->ID, $post['updated_time'], $post['message'], $date));
        }
    }

}
