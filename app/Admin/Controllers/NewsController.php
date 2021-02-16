<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Illuminate\Http\Request;
use App\Models\News as ModelNews;

class NewsController extends Controller
{
    private $modelNews;

    public function __construct(ModelNews $modelNews) {
        $this->modelNews = $modelNews;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Content
     */
    public function index()
    {
        //
        return Content::make(function (Content $content) {
            $content->body(
                Grid::make($this->modelNews, function (Grid $grid) {
                    $grid->model()
                        ->join("admin_users","news.last_action_admin_user_id","=","admin_users.id","LEFT")
                        ->select(["{$this->modelNews->getTable()}.*", "admin_users.name as admin_username"]);
                    $grid->column("title","標題");
                    $grid->column('admin_username','最後修改管理者名稱');
                    $grid->column('updated_at');
                    $grid->column('created_at');
                })
            );
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Form
     */
    public function create()
    {
        return $this->getFrom();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge(['last_action_admin_user_id' => Admin::user()->id]);
        return $this->getFrom()
            ->store($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Content
     */
    public function show(Content $content,$id)
    {
        return $content->body(
            Show::make($id,$this->modelNews,function (Show $show) {
                $show->title("標題");
                $show->content("內容");
            })
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->getFrom()->edit($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->merge(['last_action_admin_user_id' => Admin::user()->id]);
        return $this->getFrom()->update($id,$request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->getFrom()->destroy($id);
    }

    private function getFrom() {
        return Form::make($this->modelNews, function (Form $form) {
            $form->hidden("last_action_admin_user_id");
            $form->text("title", '標題');
            $form->textarea("content","內容");
        });
    }
}
