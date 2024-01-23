<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Monitoring\Books\BooksMonitoring;
use App\Filament\Pages\Monitoring\Groups\GroupsMonitoring;
use App\Filament\Pages\Monitoring\Quizzes\QuizzesMonitoring;
use App\Filament\Pages\Monitoring\Readers\ReadersMonitoring;
use App\Filament\Resources\AcademicYearResource;
use App\Filament\Resources\BookAuthorResource;
use App\Filament\Resources\BookResource;
use App\Filament\Resources\CityResource;
use App\Filament\Resources\CountryResource;
use App\Filament\Resources\DirectionResource;
use App\Filament\Resources\FeedbackResource;
use App\Filament\Resources\GenreResource;
use App\Filament\Resources\GroupResource;
use App\Filament\Resources\GroupTypeResource;
use App\Filament\Resources\QuestionResource;
use App\Filament\Resources\QuizResource;
use App\Filament\Resources\ReaderRankResource;
use App\Filament\Resources\RegionResource;
use App\Filament\Resources\RoleResource;
use App\Filament\Resources\SchoolResource;
use App\Filament\Resources\SubjectResource;
use App\Filament\Resources\TestTypeResource;
use App\Filament\Resources\UserResource;
use App\Http\Middleware\AuthenticateFilament;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                AuthenticateFilament::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->items([
                    NavigationItem::make(__('sidebar.dashboard'))
                        ->icon('heroicon-o-home')
                        ->activeIcon('heroicon-s-home')
                        ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.pages.dashboard'))
                        ->url(fn(): string => Dashboard::getUrl()),
                    ])
                    ->groups([
                        NavigationGroup::make(__('sidebar.quick_menu'))
                            ->items([
                                ...QuizResource::getNavigationItems(),
                                ...QuestionResource::getNavigationItems(),
                                ...BookResource::getNavigationItems()
                            ]),
                    ])
                    ->groups([
                        NavigationGroup::make(__('models/book.plural'))
                            ->items([
                                ...BookResource::getNavigationItems(),
                                ...BookAuthorResource::getNavigationItems(),
                                ...GenreResource::getNavigationItems(),
                                ...ReaderRankResource::getNavigationItems(),
                                ...FeedbackResource::getNavigationItems()
                            ]),
                    ])
                    ->groups([
                        NavigationGroup::make(__('models/quiz.plural'))
                            ->items([
                                ...QuizResource::getNavigationItems(),
                                ...TestTypeResource::getNavigationItems(),
                                ...QuestionResource::getNavigationItems(),
                            ]),
                    ])
                    ->groups([
                        NavigationGroup::make(__('monitoring.title'))
                            ->items([
                                ...BooksMonitoring::getNavigationItems(),
                                ...GroupsMonitoring::getNavigationItems(),
                                ...ReadersMonitoring::getNavigationItems(),
                                ...QuizzesMonitoring::getNavigationItems()
                            ]),
                    ])
                    ->groups([
                        NavigationGroup::make(__('models/school.plural'))
                            ->items([
                                ...GroupResource::getNavigationItems(),
                                ...SchoolResource::getNavigationItems(),
                                ...AcademicYearResource::getNavigationItems(),
                                ...SubjectResource::getNavigationItems(),
                                ...DirectionResource::getNavigationItems(),
                                ...GroupTypeResource::getNavigationItems(),
                            ]),
                    ])
                    ->groups([
                        NavigationGroup::make(__('sidebar.others'))
                            ->items([
                                ...CountryResource::getNavigationItems(),
                                ...CityResource::getNavigationItems(),
                                ...RegionResource::getNavigationItems(),
                            ]),
                    ])
                    ->groups([
                        NavigationGroup::make(__('sidebar.user_management'))
                            ->items([
                                ...UserResource::getNavigationItems(),
                                ...RoleResource::getNavigationItems(),
                            ]),
                    ]);
            })
            ->topNavigation()
            ->maxContentWidth('full');
    }
}
