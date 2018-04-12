<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
    # 任务相关
    Route::group(['prefix' => 'task'], function () {
        Route::get('/', 'TaskController@index')->name('task.index'); # 任务列表
        Route::get('{id}', 'TaskController@show')->name('task.show'); # 查看任务详情
        Route::get('create', 'TaskController@create')->name('task.create'); # 创建任务页面
        Route::post('/', 'TaskController@store')->name('task.store'); # 创建任务
        Route::get('{id}/edit', 'TaskController@edit')->name('task.edit'); # 编辑任务页面
        Route::put('{id}', 'TaskController@update')->name('task.update'); # 更新任务
        Route::delete('{id}', 'TaskController@destroy')->name('task.delete'); # 删除任务
        Route::get('{id}/log', 'TaskController@log')->name('task.log'); # 查看任务日志页面
        Route::post('{id}/retry', 'TaskController@retry')->name('task.retry'); # 重试任务
        Route::post('{id}/cancel', 'TaskController@cancel')->name('task.cancel'); # 终止任务
        Route::post('{id}/disable', 'TaskController@disable')->name('task.disable'); # 禁用任务
        Route::post('{id}/enable', 'TaskController@enable')->name('task.enable'); # 启用任务
    });

    # 服务器相关
    Route::group(['prefix' => 'server'], function () {
        Route::get('index', 'ServerController@index')->name('server.index'); # 服务器列表
        Route::get('{id}', 'ServerController@show')->name('server.show'); # 查看服务器详情
        Route::delete('{id}', 'ServerController@destroy')->name('server.delete'); # 删除服务器
        Route::patch('{id}', 'ServerController@restore')->name('server.restore'); # 恢复已删除的服务器
    });

    # 集群相关
    Route::group(['prefix' => 'cluster'], function () {
        Route::get('/', 'ClusterController@index')->name('cluster.index'); # 集群列表
        Route::get('{id}', 'ClusterController@show')->name('cluster.show'); # 查看集群
        Route::get('create', 'ClusterController@create')->name('cluster.create'); # 创建集群页面
        Route::post('/', 'ClusterController@store')->name('cluster.store'); # 创建集群
        Route::get('{id}/edit', 'ClusterController@edit')->name('cluster.edit'); # 编辑集群页面
        Route::put('{id}', 'ClusterController@update')->name('cluster.update'); # 更新集群
        Route::delete('{id}', 'ClusterController@destroy')->name('cluster.destroy'); # 删除集群
        Route::patch('{id}', 'ClusterController@restore')->name('cluster.restore'); # 恢复已被删除的集群
    });

    Route::get('/profile', 'UserController@profile')->name('profile');
});