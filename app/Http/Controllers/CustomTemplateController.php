<?php

namespace App\Http\Controllers;

use App\Models\OpenaiGeneratorChatCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomTemplateController extends Controller
{
  public function customTemplateList()
  {
    $userType = auth()->user()->type;

    // if ($userType === 'admin') {
    //   $list = OpenaiGeneratorChatCategory::where('type', 'admin')->orderBy('name', 'asc')->get();
    // } else {
    $userId = auth()->user()->id;
    $list = OpenaiGeneratorChatCategory::where('user_id', $userId)->orderBy('name', 'asc')->get();
    // }
    return view('panel.user.openai_chat.custom_template.list', compact('list'));
  }

  public function openAIChatAddOrUpdate($id = null)
  {
    if ($id == null) {
      $template = null;
    } else {
      $template = OpenaiGeneratorChatCategory::where('id', $id)->firstOrFail();
    }

    return view('panel.user.openai_chat.custom_template.form', compact('template'));
  }

  public function openAICustomChatAddOrUpdateSave(Request $request)
  {

    if ($request->template_id != 'undefined') {
      $template = OpenaiGeneratorChatCategory::where('id', $request->template_id)->firstOrFail();
      dd($template);
    } else {
      $template = new OpenaiGeneratorChatCategory();
    }

    if ($request->hasFile('avatar')) {
      $path = 'upload/images/chatbot/';
      $image = $request->file('avatar');
      $image_name = Str::random(4) . '-' . Str::slug($request->name) . '-avatar.' . $image->getClientOriginalExtension();

      //Resim uzantı kontrolü
      $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
      if (!in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
        $data = array(
          'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
        );
        return response()->json($data, 419);
      }

      $image->move($path, $image_name);

      $template->image = $path . $image_name;
    }

    $template->user_id = auth()->user()->id;
    $template->name = $request->name;
    $template->parent_id = "0";
    $template->slug = Str::slug($request->name) . '-' . Str::random(5);
    $template->short_name = $request->short_name;
    $template->description = $request->description;
    $template->role = $request->role;
    $template->human_name = $request->human_name;
    $template->helps_with = $request->helps_with;
    $template->color = $request->color;
    $template->chat_completions = $request->chat_completions;
    $template->prompt_prefix = "As a " . $request->role . ", ";
    $template->type = 'user';
    $template->save();
  }

  public function openAICustomChatDelete($id = null)
  {
    $template = OpenaiGeneratorChatCategory::where('id', $id)->firstOrFail();
    $template->delete();
    return back()->with(['message' => 'Deleted Successfully', 'type' => 'success']);
  }
}
