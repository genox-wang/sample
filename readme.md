## Lavarel 入门教程学习笔记


### Git

- 添加全部更新：`git add -A`
- 提交更改：`git commit -m 'ChangeList'`
- 提交远程分支：`git push 仓库名 分支名`
- 检出分支：`git checkout 分支`
- 创建新分支并检出：`git checkout -b 分支名`
- 合并分支：`git merge 分支名`
- 删除缓存中文件: `git rm --cache-r 文件夹/文件名`

### Route

- 目录位置： `app/Http/routes.php`
- 定义路由： `[Route::]get|post|patch|delete(路径, 目标方法)->name(路由名)`
- 资源路由： RESTful

```
resource('users', 'UsersController');

// 等同于

get('/users', 'UsersController@index')->name('users.index');
get('/users/{id}', 'UsersController@show')->name('users.show');
get('/users/create', 'UsersController@create')->name('users.create');
post('/users', 'UsersController@store')->name('users.store');
get('/users/{id}/edit', 'UsersController@edit')->name('users.edit');
patch('/users/{id}', 'UsersController@update')->name('users.update');
delete('/users/{id}', 'UsersController@destroy')->name('users.destroy');

// 资源接口可选
resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);
```

### Controller

- 目录位置： `app/Http/Controllers`
- 创建命令： `php artisan make:controller 控制器名 [--plain]`
- 数据验证：

```php
$this->validate($request, [
    'content' => 'required|max:140'
]);
```
- 闪存：

```php
// 创建闪存
session()->flash('success', '欢迎回来！');

// 验证闪存
session()->has('success');

// 读取闪存
session()->get('success');
```
- 用户认证：`Auth:: attempt(关系数组)`


### Middleware

待补充

### Policy

待补充

### Model

- 创建命令：`php artisan make:model 模型名`
- 字段
  - 数据表： `$table`
  - 可更新字段： `protected $fillable = ['name', 'email', 'password'];`
  - 隐藏敏感信息： `protected $hidden = ['password', 'remember_token'];`
- 测试模型：PERL环境 -> `php artisan tinker`

```
// 查找表单行
>>> User::find(1)
// 更新表单
>>> $user->save() 或者 $user->update(关系数组)
// 查找所有
>>> User::all()
// 分页
>>> User::paginate(每页数量）
```

- 事件监听

```php

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    .
    .
    .
    protected $hidden = ['password', 'remember_token'];

    public static function boot()
    {
        parent::boot();

        // 模型创建之前
        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });

        //模型创建之后
        static::created(function ($user) {
        });
    }
    .
    .
    .
}

```

- 一对多

```php
// 建立关系

// Status.php


class Status extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// User.php
class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }    
}


// 使用

$statuses = $user->statuses();//取出当前用户的所有状态

```

- 多对多


```php
// 建立关系
class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    public function followers()
    {
        return $this->belongsToMany(User::Class, 'followers', 'user_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::Class, 'followers', 'follower_id', 'user_id');
    }
}    


// 使用

// 获取粉丝关系列表
$user->followers();
// 获取用户关注人列表
$user->followings();
// 添加关注
$user->followings()->attach([2])
$this->followings()->sync($user_ids, false);

// 取消关注
$this->followings()->detach($user_ids);
```

### Migrate

- 创建：

```
// 添加列
$ php artisan make:migration add_is_admin_to_users_table --table=users

// 创建新表
$ php artisan make:migration create_statuses_table --create="statuses"
```

- 定义：

```php
// 添加列
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('is_admin')->default(false);
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('is_admin');
    });
}

// 创建新表
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
    });
}

public function down()
{
    Schema::drop('users');
}
```

### Seeder

- 定义工厂 `database/factories/ModelFactory.php`

```php
$factory->define(App\Models\Status::class, function (Faker\Generator $faker) {
    $date_time = $faker->date . ' ' . $faker->time;
    return [
        'content'    => $faker->text(),
        'created_at' => $date_time,
        'updated_at' => $date_time,
    ];
});
```

- 创建Seeder: `php artisan db:seed --class=UsersTableSeeder`
- 定义假数据：`database/seeds/UsersTableSeeder.php`

```php
public function run()
{
    $users = factory(User::class)->times(50)->make();
    User::insert($users->toArray());
}
```
- 注册Seeder: `database/seeds/DatabaseSeeder.php`

```php
$this->call('StatusesTableSeeder');
```

- 执行Seeder: `php artisan migrate:refresh --seed`

### Artisan

- 生成AppKey: `key:generate`
- 生成模型： `moke:model`
- 生成授权策略： `make:policy`
- 生成Seeder： `make:seeder`
- 自行迁移： `migrate`
- 回滚迁移： `migrate:rollback`
- 重置迁移： `migrate:refresh`
- 填充数据库： `db:seed`
- 进入tinker环境： `tinker`
- 查看路由列表： `route:list`


### 邮件

- 配置

配置`.env` ，日志调试

```

MAIL_DRIVER=log

```

- 发送邮件

```php
use Mail

Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
    $message->from($from, $name)->to($to)->subject($subject);
});
```
