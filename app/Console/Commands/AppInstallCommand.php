<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class AppInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:install
                            {--force : Force installation even if already installed}
                            {--defaults : Run without prompts using defaults}';

    /**
     * The console command description.
     */
    protected $description = 'Quick install command to jumpstart the Filament Starter Kit';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('🚀 Filament Starter Kit - Quick Install');
        $this->newLine();

        // Check if already installed
        if ($this->isAlreadyInstalled() && ! $this->option('force')) {
            if ($this->shouldInteract() && ! confirm('Application appears to be already installed. Continue anyway?', false)) {
                $this->components->warn('Installation cancelled.');

                return self::SUCCESS;
            }
        }

        // Step 1: Environment Setup
        $this->setupEnvironment();

        // Step 2: Generate App Key
        $this->generateAppKey();

        // Step 3: Setup Database
        $this->setupDatabase();

        // Step 4: Run Migrations
        $this->runMigrations();

        // Step 5: Setup Shield (roles & permissions)
        $this->setupShield();

        // Step 6: Create Admin User
        $this->createAdminUser();

        // Step 7: Build Frontend Assets
        $this->buildAssets();

        // Step 8: Clear & Optimize
        $this->optimizeApplication();

        // Step 9: Display Summary
        $this->displaySummary();

        return self::SUCCESS;
    }

    /**
     * Check if the application is already installed.
     */
    protected function isAlreadyInstalled(): bool
    {
        return File::exists(base_path('.env'))
            && File::exists(database_path('database.sqlite'))
            && ! empty(config('app.key'));
    }

    /**
     * Setup the environment file.
     */
    protected function setupEnvironment(): void
    {
        $this->components->task('Setting up environment file', function () {
            if (! File::exists(base_path('.env'))) {
                File::copy(base_path('.env.example'), base_path('.env'));
            }

            return true;
        });

        if ($this->shouldInteract()) {
            $appName = text(
                label: 'What is your application name?',
                placeholder: 'My Filament App',
                default: 'Filament Starter Kit',
                required: true
            );

            $appUrl = text(
                label: 'What is your application URL?',
                placeholder: 'http://localhost:8000',
                default: 'http://localhost:8000',
                required: true
            );

            $locale = select(
                label: 'What is your preferred locale?',
                options: [
                    'en' => 'English',
                    'es' => 'Español',
                ],
                default: 'en'
            );

            $this->updateEnvValue('APP_NAME', '"'.$appName.'"');
            $this->updateEnvValue('APP_URL', $appUrl);
            $this->updateEnvValue('APP_LOCALE', $locale);
        }
    }

    /**
     * Generate the application key.
     */
    protected function generateAppKey(): void
    {
        $this->components->task('Generating application key', function () {
            if (empty(config('app.key'))) {
                $this->callSilently('key:generate');
            }

            return true;
        });
    }

    /**
     * Setup the database.
     */
    protected function setupDatabase(): void
    {
        $this->components->task('Setting up database', function () {
            $dbPath = database_path('database.sqlite');

            if (! File::exists($dbPath)) {
                File::put($dbPath, '');
            }

            return true;
        });
    }

    /**
     * Run database migrations.
     */
    protected function runMigrations(): void
    {
        $this->components->task('Running database migrations', function () {
            $this->callSilently('migrate', ['--force' => true]);

            return true;
        });
    }

    /**
     * Setup Filament Shield roles and permissions.
     */
    protected function setupShield(): void
    {
        $this->components->task('Setting up Shield roles & permissions', function () {
            // Create super_admin and panel_user roles via seeder
            $this->callSilently('db:seed', [
                '--class' => 'Database\\Seeders\\ShieldSeeder',
                '--force' => true,
            ]);

            // Generate permissions for all resources in the admin panel
            $this->callSilently('shield:generate', [
                '--all' => true,
                '--option' => 'permissions',
                '--panel' => 'admin',
            ]);

            return true;
        });
    }

    /**
     * Create the admin user.
     */
    protected function createAdminUser(): void
    {
        $this->newLine();
        $this->components->info('👤 Create Admin User');

        $name = 'Admin';
        $email = 'admin@example.com';
        $password = 'password';

        if ($this->shouldInteract()) {
            $createAdmin = confirm(
                label: 'Would you like to create an admin user?',
                default: true
            );

            if (! $createAdmin) {
                $this->components->warn('Skipping admin user creation.');

                return;
            }

            $name = text(
                label: 'Admin name',
                placeholder: 'Admin',
                default: 'Admin',
                required: true
            );

            $email = text(
                label: 'Admin email',
                placeholder: 'admin@example.com',
                default: 'admin@example.com',
                required: true,
                validate: fn (string $value) => filter_var($value, FILTER_VALIDATE_EMAIL) ? null : 'Please enter a valid email address.'
            );

            $password = password(
                label: 'Admin password',
                placeholder: 'Enter a secure password',
                required: true,
                validate: fn (string $value) => strlen($value) >= 8 ? null : 'Password must be at least 8 characters.'
            );
        }

        $this->components->task('Creating admin user', function () use ($name, $email, $password) {
            $userClass = config('auth.providers.users.model', \App\Models\User::class);

            $user = $userClass::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                ]
            );

            // Assign super_admin role if Shield is installed
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $user->assignRole('super_admin');
            }

            return true;
        });
    }

    /**
     * Build frontend assets.
     */
    protected function buildAssets(): void
    {
        $this->newLine();

        if (! $this->isNpmInstalled()) {
            $this->components->warn('NPM not found. Skipping asset build.');
            $this->components->info('Run "npm install && npm run build" manually when ready.');

            return;
        }

        $buildAssets = true;
        if ($this->shouldInteract()) {
            $buildAssets = confirm(
                label: 'Would you like to install NPM dependencies and build assets?',
                default: true
            );
        }

        if (! $buildAssets) {
            $this->components->warn('Skipping asset build. Run "npm install && npm run build" manually.');

            return;
        }

        spin(
            message: 'Installing NPM dependencies...',
            callback: function () {
                Process::run('npm install');
            }
        );

        spin(
            message: 'Building frontend assets...',
            callback: function () {
                Process::run('npm run build');
            }
        );

        $this->components->info('✅ Assets built successfully.');
    }

    /**
     * Optimize the application.
     */
    protected function optimizeApplication(): void
    {
        $this->components->task('Optimizing application', function () {
            $this->callSilently('optimize:clear');
            $this->callSilently('filament:optimize');
            $this->callSilently('icons:cache');

            return true;
        });
    }

    /**
     * Display the installation summary.
     */
    protected function displaySummary(): void
    {
        $this->newLine(2);
        $this->components->info('✨ Installation Complete!');
        $this->newLine();

        $this->table(
            ['Setting', 'Value'],
            [
                ['App Name', config('app.name')],
                ['App URL', config('app.url')],
                ['Database', 'SQLite'],
                ['Locale', config('app.locale')],
            ]
        );

        $this->newLine();
        $this->components->info('📚 Quick Start Commands:');
        $this->newLine();

        $this->line('  <fg=yellow>composer run dev</>     - Start development server (app + queue + logs + vite)');
        $this->line('  <fg=yellow>composer run test</>    - Run the test suite');
        $this->line('  <fg=yellow>vendor/bin/pint</>      - Format code with Laravel Pint');

        $this->newLine();
        $this->components->info('🔗 Access your application at: '.config('app.url'));

        if (! $this->shouldInteract()) {
            $this->newLine();
            $this->components->warn('⚠️  Default admin credentials (if using defaults):');
            $this->line('   Email: admin@example.com');
            $this->line('   Password: password');
            $this->newLine();
            $this->components->warn('Please change these credentials in production!');
        }
    }

    /**
     * Determine if the command should prompt for user input.
     */
    protected function shouldInteract(): bool
    {
        return ! $this->option('defaults') && $this->input->isInteractive();
    }

    /**
     * Update a value in the .env file.
     */
    protected function updateEnvValue(string $key, string $value): void
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        if (str_contains($envContent, "{$key}=")) {
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContent
            );
        } else {
            $envContent .= "\n{$key}={$value}";
        }

        File::put($envPath, $envContent);
    }

    /**
     * Check if NPM is installed.
     */
    protected function isNpmInstalled(): bool
    {
        $result = Process::run('which npm');

        return $result->successful();
    }
}
