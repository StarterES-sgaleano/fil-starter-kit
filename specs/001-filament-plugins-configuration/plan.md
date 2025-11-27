# Implementation Plan: Filament Plugins Configuration

## Technical Context

| Aspect | Value |
|--------|-------|
| **PHP Version** | 8.4.15 |
| **Laravel Version** | 12.40.2 |
| **Filament Version** | 4.2.3 |
| **Database** | SQLite |
| **Site URL** | https://it-assets-manager.test |
| **Admin Path** | /admin |
| **Project Type** | IT Assets Manager (CRUD-based admin panel) |

### Project Structure

```
app/
├── Filament/
│   └── Resources/           # Filament resources (to be created)
├── Http/Controllers/        # Standard Laravel controllers
├── Models/
│   └── User.php             # Only User model exists
├── Policies/                # Authorization policies (to be created)
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php  # Main panel configuration
```

### Installed Plugins (18 total)

| Plugin | Purpose | Priority |
|--------|---------|----------|
| filament-shield | Roles & permissions | CRITICAL |
| filament-breezy | 2FA & profile | CRITICAL |
| filament-api-service | REST API generation | HIGH |
| filament-webhook-client | Webhook management | HIGH |
| commentions | Entity comments | HIGH |
| filament-apex-charts | Dashboard charts | MEDIUM |
| filament-quick-create | Quick create button | MEDIUM |
| guava/calendar | Calendar widget | MEDIUM |
| flowforge | Database workflows | MEDIUM |
| filament-excel | Excel export | LOW |
| filament-excel-import | Excel import | LOW |
| filament-badgeable-column | Badge columns | LOW |
| filament-modal-relation-managers | Modal relations | LOW |
| filament-layout-manager | Panel layout | LOW |
| guava/filament-icons | Icons | LOW |
| spatie-laravel-settings-plugin | App settings | MEDIUM |
| spatie-laravel-tags-plugin | Tagging | MEDIUM |
| filament-exceptions | Exception viewer | UTILITY |
| advanced-tables | Table features | UTILITY |
| support-bubble | Support widget | UTILITY |

---

## Constitution Check

> **Note**: Project constitution is template-only. Using Laravel/Filament best practices as guidelines.

### Applied Principles

| Principle | Application |
|-----------|-------------|
| **Test-First** | Each plugin configuration will have corresponding tests |
| **Library-First** | Plugins are self-contained, configured via providers |
| **Integration Testing** | Plugin interactions tested in feature tests |
| **Simplicity** | Configure only what's needed, defer complex features |

### Gate Evaluation

| Gate | Status | Notes |
|------|--------|-------|
| Security baseline | ✅ PASS | Shield + Breezy provide RBAC + 2FA |
| Test coverage | ⚠️ PENDING | Tests to be written per phase |
| Documentation | ✅ PASS | research.md + decisions.md complete |
| Breaking changes | ✅ PASS | Fresh project, no breaking changes |

---

## Phase 0: Research (COMPLETED)

All research completed and documented in `research.md`. Key findings:

### Plugin Dependencies

```
filament-shield (standalone)
    └── Requires: Spatie Permission (auto-installed)
    
filament-breezy (standalone)
    └── Requires: 2FA package for MFA feature
    
filament-api-service (depends on models)
    └── Requires: Models with resources to generate APIs
    
filament-webhook-client (standalone)
    └── Already registered in AdminPanelProvider
```

### Configuration Requirements

| Plugin | Migrations | Config Publish | Artisan Commands |
|--------|------------|----------------|------------------|
| Shield | ✅ Required | ✅ Required | `shield:install`, `shield:generate` |
| Breezy | ❌ None | ✅ Required | None |
| API Service | ❌ None | ✅ Required | `api:generate` |
| Webhook | ✅ Required | ✅ Required | Already configured |
| Commentions | ✅ Required | ✅ Required | None |
| ApexCharts | ❌ None | ❌ Optional | None |
| Excel | ❌ None | ✅ Required | None |
| Excel Import | ✅ Required | ✅ Required | None |

---

## Phase 1: Security Foundation

**Priority**: CRITICAL  
**Estimated Duration**: 2-3 hours  
**Dependencies**: None

### 1.1 Filament Shield Setup

```bash
# Commands to execute
php artisan vendor:publish --tag=filament-shield-config
php artisan shield:install --no-interaction
php artisan migrate
php artisan shield:generate --all --no-interaction
```

**Files to Create/Modify**:

1. `config/filament-shield.php` - Shield configuration
2. `app/Models/User.php` - Add HasRoles trait
3. `app/Policies/` - Auto-generated policies
4. `database/seeders/ShieldSeeder.php` - Default roles/permissions

**Configuration**:

```php
// config/filament-shield.php
return [
    'shield_resource' => [
        'should_register_navigation' => true,
        'slug' => 'shield/roles',
        'navigation_sort' => -1,
        'navigation_group' => 'Administración',
    ],
    'auth_provider_model' => [
        'fqcn' => 'App\\Models\\User',
    ],
    'super_admin' => [
        'enabled' => true,
        'name' => 'super_admin',
        'define_via_gate' => true,
        'intercept_gate' => 'before',
    ],
    'permission_prefixes' => [
        'resource' => [
            'view', 'view_any', 'create', 'update', 
            'delete', 'delete_any', 'restore', 'restore_any', 
            'force_delete', 'force_delete_any', 'replicate',
        ],
        'page' => 'page_',
        'widget' => 'widget_',
    ],
];
```

**User Model Update**:

```php
// app/Models/User.php
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles;
    
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // Or: $this->hasRole('super_admin');
    }
}
```

### 1.2 Filament Breezy Setup

```bash
php artisan vendor:publish --tag=filament-breezy-config
```

**Files to Create/Modify**:

1. `config/filament-breezy.php` - Breezy configuration
2. `app/Providers/Filament/AdminPanelProvider.php` - Register plugin

**Configuration**:

```php
// config/filament-breezy.php
return [
    'enable_2fa' => true,
    'show_2fa_page' => true,
    'should_show_avatar_on_profile' => true,
    'enable_profile_page' => true,
    'profile_page_password_update' => true,
    'profile_page_2fa' => true,
    'profile_page_info' => true,
    'session_management' => true,
    'browser_session_management' => true,
];
```

**AdminPanelProvider Update**:

```php
use Jeffgreco13\FilamentBreezy\BreezyCore;

->plugins([
    BreezyCore::make()
        ->myProfile(
            shouldRegisterUserMenu: true,
            shouldRegisterNavigation: false,
            navigationGroup: 'Settings',
            hasAvatars: true,
            slug: 'my-profile'
        )
        ->enableTwoFactorAuthentication(
            force: false,
        )
        ->enableSanctumTokens(),
    // ... other plugins
])
```

### 1.3 Tests for Phase 1

```php
// tests/Feature/Filament/ShieldTest.php
class ShieldTest extends TestCase
{
    public function test_super_admin_can_access_roles_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        
        $this->actingAs($admin)
            ->get('/admin/shield/roles')
            ->assertOk();
    }
    
    public function test_regular_user_cannot_access_roles_page(): void
    {
        $user = User::factory()->create();
        
        $this->actingAs($user)
            ->get('/admin/shield/roles')
            ->assertForbidden();
    }
}

// tests/Feature/Filament/BreezyTest.php
class BreezyTest extends TestCase
{
    public function test_user_can_access_profile_page(): void
    {
        $user = User::factory()->create();
        
        $this->actingAs($user)
            ->get('/admin/my-profile')
            ->assertOk();
    }
    
    public function test_2fa_section_is_visible(): void
    {
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(MyProfileComponent::class)
            ->assertSee('Two Factor Authentication');
    }
}
```

---

## Phase 2: API & Integration Layer

**Priority**: HIGH  
**Estimated Duration**: 2-3 hours  
**Dependencies**: Phase 1 complete, at least one Resource exists

### 2.1 Filament API Service Setup

```bash
php artisan vendor:publish --tag=filament-api-service-config
```

**Configuration**:

```php
// config/filament-api-service.php
return [
    'navigation' => [
        'token' => [
            'register' => true,
            'sort' => 80,
            'icon' => 'heroicon-o-key',
            'group' => 'API',
        ],
    ],
    'models' => [
        'token' => [
            'enable_policy' => true,
        ],
    ],
    'route' => [
        'panel_prefix' => true,
        'use_resource_middlewares' => true,
    ],
    'tenancy' => [
        'enabled' => false,
    ],
];
```

### 2.2 Webhook Client (Already Configured)

Current configuration in AdminPanelProvider:

```php
WebhookPlugin::make()
    ->enableApiRoutes()
    ->keepLogs()
    ->enablePlugin()
```

### 2.3 Commentions Setup

```bash
php artisan vendor:publish --tag=commentions-config
php artisan vendor:publish --tag=commentions-migrations
php artisan migrate
```

**Configuration**:

```php
// config/commentions.php
return [
    'model' => App\Models\User::class,
    'allow_guest_comments' => false,
    'enable_notifications' => true,
    'enable_mentions' => true,
    'max_depth' => 3,
];
```

**Usage in Resources**:

```php
use Parallax\FilamentCommentions\Infolists\Components\CommentsEntry;

public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            // ... other entries
            CommentsEntry::make('comments'),
        ]);
}
```

### 2.4 Tests for Phase 2

```php
// tests/Feature/Filament/ApiServiceTest.php
class ApiServiceTest extends TestCase
{
    public function test_user_can_create_api_token(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        
        $this->actingAs($user)
            ->get('/admin/api-tokens')
            ->assertOk();
    }
}

// tests/Feature/Filament/CommentionsTest.php
class CommentionsTest extends TestCase
{
    public function test_comments_can_be_added_to_model(): void
    {
        // Requires a model with HasComments trait
        $this->markTestIncomplete('Pending model with comments');
    }
}
```

---

## Phase 3: Panel Enhancement

**Priority**: MEDIUM  
**Estimated Duration**: 2-3 hours  
**Dependencies**: Phase 1 complete

### 3.1 ApexCharts Setup

No configuration needed. Usage example:

```php
// app/Filament/Widgets/AssetsChart.php
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AssetsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'assetsChart';
    protected static ?string $heading = 'Assets Overview';
    
    protected function getOptions(): array
    {
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Assets',
                    'data' => [7, 4, 6, 10, 14, 7],
                ],
            ],
            'xaxis' => [
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            ],
        ];
    }
}
```

### 3.2 Quick Create Setup

```php
// AdminPanelProvider.php
use Awcodes\FilamentQuickCreate\QuickCreatePlugin;

->plugins([
    QuickCreatePlugin::make()
        ->includes([
            // AssetResource::class,
            // LicenseResource::class,
        ])
        ->excludes([
            // Exclude resources from quick create
        ])
        ->sortBy('label')
        ->slideOver(),
])
```

### 3.3 Calendar Widget Setup

```php
// AdminPanelProvider.php
use Guava\Calendar\CalendarPlugin;

->plugins([
    CalendarPlugin::make(),
])

// Create calendar widget
// php artisan make:filament-widget AssetMaintenanceCalendar
```

### 3.4 FlowForge Setup

```bash
php artisan vendor:publish --tag=flowforge-config
php artisan vendor:publish --tag=flowforge-migrations
php artisan migrate
```

```php
// AdminPanelProvider.php
use Workbench\App\Filament\Resources\WorkflowResource;

// FlowForge auto-registers its resource
```

---

## Phase 4: Utility Features

**Priority**: LOW  
**Estimated Duration**: 1-2 hours  
**Dependencies**: At least one Resource with data

### 4.1 Excel Export/Import

```php
// In Resource class
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use EightyNine\ExcelImport\ExcelImportAction;

public static function table(Table $table): Table
{
    return $table
        ->columns([...])
        ->headerActions([
            ExportAction::make(),
            ExcelImportAction::make()
                ->color('primary'),
        ])
        ->bulkActions([
            ExportBulkAction::make(),
        ]);
}
```

### 4.2 Badgeable Column

```php
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;

BadgeableColumn::make('title')
    ->suffixBadges([
        Badge::make('status')
            ->label(fn ($record) => $record->status->label())
            ->color(fn ($record) => $record->status->color()),
    ]),
```

### 4.3 Modal Relation Managers

```php
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;

class LicenseRelationManager extends RelationManager
{
    use CanBeEmbeddedInModals;
}

// In Resource
public static function getPages(): array
{
    return [
        'index' => Pages\ListAssets::route('/'),
        'create' => Pages\CreateAsset::route('/create'),
        'view' => Pages\ViewAsset::route('/{record}'),
        // No edit page - relations shown in modal on view
    ];
}
```

---

## Quickstart Guide

### Prerequisites

1. ✅ Laravel 12 installed
2. ✅ Filament 4 installed
3. ✅ All 18 plugins installed via composer
4. ✅ Database configured (SQLite)

### Quick Setup Commands

```bash
# Phase 1: Security
php artisan vendor:publish --tag=filament-shield-config --no-interaction
php artisan shield:install --no-interaction
php artisan migrate
php artisan shield:generate --all --no-interaction

php artisan vendor:publish --tag=filament-breezy-config --no-interaction

# Phase 2: API
php artisan vendor:publish --tag=filament-api-service-config --no-interaction
php artisan vendor:publish --tag=commentions-config --no-interaction
php artisan vendor:publish --tag=commentions-migrations --no-interaction
php artisan migrate

# Phase 3: Enhancement
php artisan vendor:publish --tag=flowforge-config --no-interaction
php artisan vendor:publish --tag=flowforge-migrations --no-interaction
php artisan migrate

# Run Pint
./vendor/bin/pint --dirty

# Run tests
php artisan test
```

### Verification Checklist

- [ ] Can login to /admin
- [ ] Shield roles page accessible at /admin/shield/roles
- [ ] Profile page accessible at /admin/my-profile
- [ ] 2FA option visible in profile
- [ ] API tokens page accessible (after resource exists)
- [ ] Quick create button in header
- [ ] All tests passing

---

## Data Model

See `data-model.md` for entity definitions.

### Plugin-Related Tables

| Table | Plugin | Purpose |
|-------|--------|---------|
| `roles` | Shield | Role definitions |
| `permissions` | Shield | Permission definitions |
| `model_has_roles` | Shield | Role assignments |
| `model_has_permissions` | Shield | Direct permissions |
| `role_has_permissions` | Shield | Role-permission mapping |
| `comments` | Commentions | Entity comments |
| `personal_access_tokens` | Sanctum | API tokens |
| `webhook_calls` | Webhook | Webhook logs |
| `workflows` | FlowForge | Workflow definitions |
| `workflow_runs` | FlowForge | Workflow execution logs |

---

## Risk Assessment

| Risk | Impact | Mitigation |
|------|--------|------------|
| Shield conflicts with existing auth | HIGH | Install Shield first, before any resources |
| Plugin version incompatibility | MEDIUM | All plugins tested with Filament 4 |
| Database migration conflicts | LOW | Run migrations incrementally per phase |
| Performance impact from many plugins | LOW | Plugins are lazy-loaded by Filament |

---

## Success Criteria

### Phase 1 Complete When:
- [ ] Super admin role exists
- [ ] Regular users cannot access Shield pages
- [ ] 2FA can be enabled/disabled
- [ ] Profile page works
- [ ] All Phase 1 tests pass

### Phase 2 Complete When:
- [ ] API documentation accessible
- [ ] API tokens can be created
- [ ] Comments can be added to entities
- [ ] All Phase 2 tests pass

### Phase 3 Complete When:
- [ ] Dashboard has at least one chart
- [ ] Quick create shows resources
- [ ] Calendar widget functional
- [ ] All Phase 3 tests pass

### Phase 4 Complete When:
- [ ] Excel export works on table
- [ ] Excel import works
- [ ] Badgeable columns display correctly
- [ ] All Phase 4 tests pass

---

## Next Steps After This Plan

1. **Create first Resource** (e.g., AssetResource) - needed for API generation
2. **Run Phase 1 commands** - security baseline
3. **Create tests** - verify each phase
4. **Continue with remaining phases**

---

**Branch**: `001-filament-plugins-configuration`  
**Spec Path**: `/specs/001-filament-plugins-configuration/`  
**Created**: $(date +%Y-%m-%d)  
**Status**: Planning Complete - Ready for Implementation
