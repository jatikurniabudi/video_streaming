<?php

namespace App\Http\Controllers;

use App\Models\video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



class videoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('video.index',[
            'videos' => video::latest()->get(),
        ]);
    }

    public function uploadVideo(Request $request)
   {
      $this->validate($request, [
         'title' => 'required|string|max:255',
         'video' => 'required|file|mimetypes:video/mp4',
   ]);
   $video = new Video;
   $video->title = $request->title;
   if ($request->hasFile('video'))
   {
     $path = $request->file('video')->store('videos', ['disk' =>      'my_files']);
    $video->video = $path;
   }
   $video->save();
   
  }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('video.upload');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'video' => 'required|file|mimetypes:video/mp4',
        ]);

        $path = $request->file('video')->store('public/videos');
 
        if ($path) {
            $video = new Video();
            $video->name = $request->name;
            $video->path = $path;
            $video->save();

            return redirect()->back()->with('message','Video Submited');
        }

        return redirect()->back()->with('message','Submit Failed');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        return view('video.edit',[
            'video' => $video,
        ]);

        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video)
    {
        $request->validate([
            'name' => 'required',
        ]);
        
        if($request->hasFile('video')){
            $request->validate([
              'video' => 'required|file|mimetypes:video/mp4',
            ]);
            $path = $request->file('video')->store('public/videos');
            Storage::delete($video->path);
            $video->path = $path;
        }
        $video->name = $request->name;
        $video->save();

        return redirect()->route('video.index')->with('message','Success Update Video');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        Storage::delete($video->path);
        $video->delete();

        return redirect()->back()->with('message','Success Delete Video');
    }
}
