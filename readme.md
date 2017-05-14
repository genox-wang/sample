## Lavarel 入门教程学习笔记


### RESTful

#### 创建路由

route.php

```php
resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);
```

### Model

#### 事件监听

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

### Migrate

#### 添加列

```bash
$ php artisan make:migration add_activation_to_users_table --table=users

```

```php
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('activation_token')->nullable();
            $table->boolean('activated')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activation_token');
            $table->dropColumn('activated');
        });
    }
```

#### 创建新表

```bash
$ php artisan make:migration create_statuses_table --create="statuses"
```

```php
public function up()
{
  Schema::create('statuses', function (Blueprint $table) {
    $table->increments('id');
    $table->text('content');
    $table->integer('user_id')->index();
    $table->index(['created_at']);
    $table->timestamps();
  });
}
```


### 邮件

#### 配置

配置`.env` ，日志调试

```
.
.
.
MAIL_DRIVER=log
.
.
.
```

#### 发送邮件

```php
use Mail

Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
    $message->from($from, $name)->to($to)->subject($subject);
});
```

### Eloquent

#### 一对多

##### 建立关系

Status.php
```php
class Status extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```
User.php
```php
class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    .
    .
    .
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }    
}
```

##### 使用

```php
$statuses = $user->statuses();//取出当前用户的所有状态
```

#### 多对多

##### 建立关系

```php
class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    .
    .
    .
    public function followers()
    {
        return $this->belongsToMany(User::Class, 'followers', 'user_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::Class, 'followers', 'follower_id', 'user_id');
    }
}    
```

##### 使用

```php
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
