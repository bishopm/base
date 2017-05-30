<?php

namespace Bishopm\Connexion\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Form, Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Bishopm\Connexion\Repositories\SettingsRepository;
use Bishopm\Connexion\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Monolog\Handler\SlackWebhookHandler, Monolog\Logger;

class ConnexionServiceProvider extends ServiceProvider
{

    protected $commands = [
        'Bishopm\Connexion\Console\InstallConnexionCommand',
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(Dispatcher $events, SettingsRepository $settings)
    {
        Schema::defaultStringLength(255);
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../Http/routes.php';
        }
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'connexion');
        $this->publishes([
        __DIR__.'/../Resources/views/errors' => base_path('resources/views/errors'),
        ]);
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
        $this->publishes([__DIR__.'/../Assets' => public_path('vendor/bishopm'),], 'public');
        config(['laravel-medialibrary.defaultFilesystem'=>'public']);
        config(['auth.providers.users.model'=>'Bishopm\Connexion\Models\User']);
        config(['queue.default'=>'database']);
        $finset=array();
        if (Schema::hasTable('settings')){
            $finset=$settings->makearray();
        }
        view()->share('setting', $finset);
        if (isset($finset['mail_host'])){
            config(['mail.host'=>$finset['mail_host']]);
            config(['mail.port'=>$finset['mail_port']]);
            config(['mail.username'=>$finset['mail_username']]);
            config(['mail.password'=>$finset['mail_password']]);
            //config(['mail.encryption'=>$finset['mail_encryption']]);
        }
        if (isset($finset['site_name'])){
            config(['app.name'=>$finset['site_name']]);
        }
        config(['mail.from.address'=>$finset['church_email']]);
        config(['mail.from.name'=>$finset['site_name']]);
        config(['user-verification.email.view'=>'connexion::emails.newuser']);
        config(['user-verification.email.type'=>'markdown']);
        config(['app.name'=>$finset['site_name']]);
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $event->menu->menu=array();
            $society=Setting::where('setting_key','=','society_name')->first();
            if ($society->setting_value<>''){
                $society=$society->setting_value . " society";
            } else {
                $society="Members";
            }
            $modules=Setting::where('category','=','modules')->get()->toArray();
            foreach ($modules as $module){
                $mods[$module['setting_key']]=$module['setting_value'];
            }

            $event->menu->add('CHURCH ADMIN');
            $event->menu->add([
                'text' => $society,
                'icon' => 'book',
                'can' => 'edit-backend',
                'submenu' => [
                    [
                        'text' => 'Households',
                        'url'  => 'admin/households',
                        'icon' => 'child',
                        'can' =>  'edit-backend'
                    ],
                    [
                        'text' => 'Groups',
                        'url'  => 'admin/groups',
                        'icon' => 'users',
                        'can' =>  'edit-backend'
                    ],
                    [
                        'text' => 'Messages',
                        'url'  => 'admin/messages/create',
                        'icon' => 'envelope-o',
                        'can' =>  'edit-backend'
                    ],
                    [
                        'text' => 'Events',
                        'url'  => 'admin/events',
                        'icon' => 'calendar-check-o',
                        'can' =>  'edit-backend'
                    ],
                    [
                        'text' => 'Rosters',
                        'url'  => 'admin/rosters',
                        'icon' => 'calendar',
                        'can' =>  'edit-backend'
                    ],
                    [
                        'text' => 'Statistics',
                        'url'  => 'admin/statistics',
                        'icon' => 'line-chart',
                        'can' =>  'edit-backend'
                    ],
                    [
                        'text' => 'Giving',
                        'url'  => 'admin/payments',
                        'icon' => 'gift',
                        'can' =>  'edit-backend'
                    ]
                ]
            ]);
            if ($mods['circuit_preachers']=="yes"){
                $event->menu->add([
                    'text' => 'Circuit',
                    'icon' => 'comments',
                    'can' => 'edit-backend',
                    'submenu' => [
                        [
                            'text' => 'Preachers',
                            'url'  => 'admin/preachers',
                            'icon' => 'child',
                            'can' =>  'edit-backend'
                        ],
                        [
                            'text' => 'Societies',
                            'url'  => 'admin/societies',
                            'icon' => 'envelope-o',
                            'can' =>  'edit-backend'
                        ],
                        [
                            'text' => 'Plan',
                            'url'  => 'admin/plan/' . date('Y') . '/1/edit',
                            'icon' => 'calendar',
                            'can' =>  'edit-backend'
                        ],
                        [
                            'text' => 'Meetings',
                            'url'  => 'admin/meetings',
                            'icon' => 'group',
                            'can' =>  'edit-backend'
                        ],
                        [
                            'text' => 'Special services',
                            'url'  => 'admin/weekdays',
                            'icon' => 'tree',
                            'can' =>  'edit-backend'
                        ]
                    ]
                ]);
            }
            if ($mods['todo_module']=="yes"){
                $event->menu->add([
                    'text' => 'Todo',
                    'icon' => 'list-ol',
                    'can' => 'edit-backend',
                    'submenu' => [
                        [
                            'text' => 'Tasks',
                            'url'  => 'admin/actions',
                            'icon' => 'check-square-o',
                            'can' =>  'edit-backend'
                        ],
                        [
                            'text' => 'Folders',
                            'url'  => 'admin/folders',
                            'icon' => 'folder-open-o',
                            'can' =>  'admin-backend'
                        ],
                        [
                            'text' => 'Projects',
                            'url'  => 'admin/projects',
                            'icon' => 'tasks',
                            'can' =>  'edit-backend'
                        ]
                    ]
                ]);
            }
            if ($mods['worship_module']=="yes"){
                $event->menu->add([
                    'text' => 'Worship',
                    'icon' => 'music',
                    'can' => 'view-worship',
                    'url' => 'admin/worship'
                ]);
            }
            if ($mods['website_module']=="yes"){
                $event->menu->add('WEBSITE');
                $event->menu->add([
                    'text' => 'Blog',
                    'url' => 'admin/blogs',
                    'icon' => 'pencil-square-o',
                    'can' =>  'edit-backend'
                ],
                [
                    'text' => 'Courses',
                    'url' => 'admin/courses',
                    'icon' => 'graduation-cap',
                    'can' =>  'edit-backend'
                ],            
                [
                    'text' => 'Sermons',
                    'url' => 'admin/series',
                    'icon' => 'microphone',
                    'can' =>  'edit-backend'
                ],
                [
                    'text' => 'Site structure',
                    'icon' => 'sitemap',
                    'can' => 'admin-backend',
                    'submenu' => [
                        [
                            'text' => 'Menus',
                            'url'  => 'admin/menus',
                            'icon' => 'bars',
                            'can' =>  'admin-backend'
                        ],            
                        [
                            'text' => 'Pages',
                            'url' => 'admin/pages',
                            'icon' => 'file',
                            'can' =>  'admin-backend'
                        ],
                        [
                            'text' => 'Slideshows',
                            'url' => 'admin/slideshows',
                            'icon' => 'video-camera',
                            'can' =>  'admin-backend'
                        ]
                    ]
                ],
                [
                    'text' => 'View website',
                    'url' => route('homepage'),
                    'icon' => 'globe',
                    'can' =>  'view-backend',
                    'target' => '_blank',
                    'active' => []
                ]
            );
            }
            if ($mods['bookshop_module']=="yes"){
                $event->menu->add([
                    'header' => 'BOOKSHOP',
                    'can' => 'edit-bookshop'
                ]);
                $event->menu->add([
                    'text' => 'Books',
                    'url' => 'admin/books',
                    'icon' => 'book',
                    'can' =>  'edit-bookshop'
                ],
                [
                    'text' => 'Suppliers',
                    'url' => 'admin/suppliers',
                    'icon' => 'archive',
                    'can' =>  'edit-bookshop'
                ],
                [
                    'text' => 'Transactions',
                    'url' => 'admin/transactions',
                    'icon' => 'shopping-cart',
                    'can' =>  'edit-bookshop'
                ]);
            }
            $event->menu->add([
                'header' => 'ADMINISTRATION',
                'can' => 'admin-backend'
            ]);
            $event->menu->add([
                'text' => 'User administration',
                'icon' => 'user',
                'can' =>  'admin-backend',
                'submenu' => [
                    [
                        'text' => 'Roles',
                        'url'  => 'admin/roles',
                        'icon' => 'eye',
                        'can' =>  'admin-backend'
                    ],
                    [
                        'text' => 'Users',
                        'url' => 'admin/users',
                        'icon' => 'user',
                        'can' =>  'admin-backend'
                    ],
                    [
                        'text' => 'Activate new users',
                        'url' => 'admin/users/activate',
                        'icon' => 'plug',
                        'can' =>  'admin-backend'
                    ]
                ]
            ]);
            $event->menu->add([
                'text' => 'General administration',
                'icon' => 'cog',
                'can' =>  'admin-backend',
                'submenu' => [
                    [
                        'text' => 'Google Analytics',
                        'url' => 'admin/analytics',
                        'icon' => 'area-chart',
                        'can' =>  'admin-backend'
                    ],
                    [
                        'text' => 'User logs',
                        'url' => 'admin/logs',
                        'icon' => 'pencil-square-o',
                        'can' =>  'admin-backend'
                    ],
                    [
                        'text' => 'System settings',
                        'url' =>  'admin/settings',
                        'icon' => 'cogs',
                        'can' =>  'admin-backend'
                    ]
                ]
            ]);
        });
        Form::component('bsText', 'connexion::components.text', ['name', 'label' => '', 'placeholder' => '', 'value' => null, 'attributes' => []]);
        Form::component('bsPassword', 'connexion::components.password', ['name', 'label' => '', 'placeholder' => '', 'value' => null, 'attributes' => []]);
        Form::component('bsTextarea', 'connexion::components.textarea', ['name', 'label' => '', 'placeholder' => '', 'value' => null, 'attributes' => []]);
        Form::component('bsThumbnail', 'connexion::components.thumbnail', ['source', 'width' => '100', 'label' => '']);
        Form::component('bsImgpreview', 'connexion::components.imgpreview', ['source', 'width' => '200', 'label' => '']);
        Form::component('bsHidden', 'connexion::components.hidden', ['name', 'value' => null]);
        Form::component('bsSelect', 'connexion::components.select', ['name', 'label' => '', 'options' => [], 'value' => null, 'attributes' => []]);
        Form::component('pgHeader', 'connexion::components.pgHeader', ['pgtitle', 'prevtitle', 'prevroute']);
        Form::component('pgButtons', 'connexion::components.pgButtons', ['actionLabel', 'cancelRoute']);
        Form::component('bsFile', 'connexion::components.file', ['name', 'attributes' => []]);
        if (count($finset)){
            config(['adminlte.title' => $finset['site_name']]);
            config(['adminlte.logo' => $finset['site_logo']]);
            config(['adminlte.logo_mini' => $finset['site_logo_mini']]);
        } else {
            config(['adminlte.title' => 'Connexion']);
            config(['adminlte.logo' => '<b>Connexion</b>']);
            config(['adminlte.logo_mini' => '<b>C</b>x']);
        }
        config(['adminlte.plugins.datatables' => false]);
        config(['adminlte.dashboard_url' => 'admin']);
        config(['adminlte.filters' => [
            \JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
            \JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
            \JeroenNoten\LaravelAdminLte\Menu\Filters\SubmenuFilter::class,
            \JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
            \JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class]]);
        //config(['laravel-google-calendar.client_secret_json' => public_path('vendor/bishopm/client_secret.json')]);
        //config(['laravel-google-calendar.calendar_id'=>'umhlalimethodist@gmail.com']);
        config(['analytics.service_account_credentials_json' => public_path('vendor/bishopm/service_account_credentials.json')]);
        config(['analytics.view_id' => $finset['google_analytics_view_id']]);
        config(['mediable.on_duplicate' => 'Plank\Mediable\MediaUploader::ON_DUPLICATE_REPLACE']);
        view()->composer('connexion::templates.*', \Bishopm\Connexion\Composers\MenuComposer::class);
        view()->composer('connexion::worship.page', \Bishopm\Connexion\Composers\SongComposer::class);
        view()->composer('connexion::site.*', \Bishopm\Connexion\Composers\SlideComposer::class);
        view()->composer('connexion::posts.*', \Bishopm\Connexion\Composers\SlideComposer::class);
        view()->composer('connexion::templates.webpage_no_sidebar', \Bishopm\Connexion\Composers\SlideComposer::class);
        view()->composer('connexion::templates.sidebar_right', \Bishopm\Connexion\Composers\SlideComposer::class);
        view()->composer('connexion::templates.map_page', \Bishopm\Connexion\Composers\SlideComposer::class);

        // Send errors to slack channel
        $monolog = Log::getMonolog();
        if (!\App::environment('local')) {
            $slackHandler = new SlackWebhookHandler($finset['slack_webhook'], $finset['admin_slack_username'], 'App Alerts', false, 'warning', true, true, Logger::ERROR);            
            $monolog->pushHandler($slackHandler);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        if (Schema::hasTable('settings')){
            $this->commands[]="Bishopm\Connexion\Console\SyncToodledoCommand";
        }
        $this->commands($this->commands);
        $this->app->register('Bishopm\Connexion\Providers\EventServiceProvider');
        $this->app->register('JeroenNoten\LaravelAdminLte\ServiceProvider');
        $this->app->register('Collective\Html\HtmlServiceProvider');
        $this->app->register('Cviebrock\EloquentSluggable\ServiceProvider');
        $this->app->register('Cartalyst\Tags\TagsServiceProvider');
        $this->app->register('Plank\Mediable\MediableServiceProvider');
        $this->app->register('Spatie\Menu\Laravel\MenuServiceProvider');
        //$this->app->register('Spatie\GoogleCalendar\GoogleCalendarServiceProvider');
        $this->app->register('Actuallymab\LaravelComment\LaravelCommentServiceProvider');
        $this->app->register('Felixkiss\UniqueWithValidator\ServiceProvider');
        $this->app->register('Jrean\UserVerification\UserVerificationServiceProvider');
        $this->app->register('LithiumDev\TagCloud\ServiceProvider');
        $this->app->register('Roumen\Feed\FeedServiceProvider');
        $this->app->register('Spatie\Activitylog\ActivitylogServiceProvider');
        $this->app->register('Spatie\Analytics\AnalyticsServiceProvider');
        AliasLoader::getInstance()->alias("UserVerification", 'Jrean\UserVerification\Facades\UserVerification');
        //AliasLoader::getInstance()->alias("GoogleCalendar", 'Spatie\GoogleCalendar\GoogleCalendarFacade');
        AliasLoader::getInstance()->alias("Menu", 'Spatie\Menu\Laravel\MenuFacade');
        AliasLoader::getInstance()->alias("Form",'Collective\Html\FormFacade');
        AliasLoader::getInstance()->alias("HTML",'Collective\Html\HtmlFacade');
        AliasLoader::getInstance()->alias("MediaUploader",'Plank\Mediable\MediaUploaderFacade');
        AliasLoader::getInstance()->alias("Feed",'Roumen\Feed\Feed');
        AliasLoader::getInstance()->alias("Analytics",'Spatie\Analytics\AnalyticsFacade');
        $this->app['router']->aliasMiddleware('isverified', 'Bishopm\Connexion\Middleware\IsVerified');
        $this->registerBindings();
        $this->registerUserPolicies();
    }

    public function registerUserPolicies()
    {
        Gate::define('admin-backend', function ($user) {
            return $user->hasAccess(['admin-backend']);
        });
        Gate::define('edit-backend', function ($user) {
            return $user->hasAccess(['edit-backend']);
        });
        Gate::define('view-backend', function ($user) {
            return $user->hasAccess(['view-backend']);
        });
        Gate::define('edit-comments', function ($user) {
            return $user->hasAccess(['edit-comments']);
        });
        Gate::define('edit-worship', function ($user) {
            return $user->hasAccess(['edit-worship']);
        });
        Gate::define('view-worship', function ($user) {
            return $user->hasAccess(['view-worship']);
        });
        Gate::define('edit-bookshop', function ($user) {
            return $user->hasAccess(['edit-bookshop']);
        });
        Gate::define('manager-role', function ($user) {
            return $user->inRole('manager');
        });
        Gate::define('administrator-role', function ($user) {
            return $user->inRole('administrator');
        });
        Gate::define('bookshop-manager-role', function ($user) {
            return $user->inRole('bookshop-manager');
        });
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Bishopm\Connexion\Repositories\ActionsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\ActionsRepository(new \Bishopm\Connexion\Models\Action());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\BlogsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\BlogsRepository(new \Bishopm\Connexion\Models\Blog());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\BooksRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\BooksRepository(new \Bishopm\Connexion\Models\Book());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\CommentsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\CommentsRepository(new \Actuallymab\LaravelComment\Models\Comment());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\CoursesRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\CoursesRepository(new \Bishopm\Connexion\Models\Course());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\FoldersRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\FoldersRepository(new \Bishopm\Connexion\Models\Folder());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\GroupsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\GroupsRepository(new \Bishopm\Connexion\Models\Group());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\HouseholdsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\HouseholdsRepository(new \Bishopm\Connexion\Models\Household());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\IndividualsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\IndividualsRepository(new \Bishopm\Connexion\Models\Individual());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\MeetingsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\MeetingsRepository(new \Bishopm\Connexion\Models\Meeting());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\MenusRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\MenusRepository(new \Bishopm\Connexion\Models\Menu());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\MenuitemsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\MenuitemsRepository(new \Bishopm\Connexion\Models\Menuitem());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\PagesRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\PagesRepository(new \Bishopm\Connexion\Models\Page());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\PastoralsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\PastoralsRepository(new \Bishopm\Connexion\Models\Pastoral());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\PaymentsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\PaymentsRepository(new \Bishopm\Connexion\Models\Payment());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\PlansRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\PlansRepository(new \Bishopm\Connexion\Models\Plan());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\PostsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\PostsRepository(new \Bishopm\Connexion\Models\Post());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\PreachersRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\PreachersRepository(new \Bishopm\Connexion\Models\Preacher());
                return $repository;
            }
        );        
        $this->app->bind(
            'Bishopm\Connexion\Repositories\ProjectsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\ProjectsRepository(new \Bishopm\Connexion\Models\Project());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\RostersRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\RostersRepository(new \Bishopm\Connexion\Models\Roster());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\SeriesRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\SeriesRepository(new \Bishopm\Connexion\Models\Series());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\SermonsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\SermonsRepository(new \Bishopm\Connexion\Models\Sermon());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\ServicesRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\ServicesRepository(new \Bishopm\Connexion\Models\Service());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\SettingsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\SettingsRepository(new \Bishopm\Connexion\Models\Setting());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\SlidesRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\SlidesRepository(new \Bishopm\Connexion\Models\Slide());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\SlideshowsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\SlideshowsRepository(new \Bishopm\Connexion\Models\Slideshow());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\SocietiesRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\SocietiesRepository(new \Bishopm\Connexion\Models\Society());
                return $repository;
            }
        );        
        $this->app->bind(
            'Bishopm\Connexion\Repositories\SpecialdaysRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\SpecialdaysRepository(new \Bishopm\Connexion\Models\Specialday());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\StatisticsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\StatisticsRepository(new \Bishopm\Connexion\Models\Statistic());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\SuppliersRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\SuppliersRepository(new \Bishopm\Connexion\Models\Supplier());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\TransactionsRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\TransactionsRepository(new \Bishopm\Connexion\Models\Transaction());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\UsersRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\UsersRepository(new \Bishopm\Connexion\Models\User());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Connexion\Repositories\WeekdaysRepository',
            function () {
                $repository = new \Bishopm\Connexion\Repositories\WeekdaysRepository(new \Bishopm\Connexion\Models\Weekday());
                return $repository;
            }
        );
    }
}
