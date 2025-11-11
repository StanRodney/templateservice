<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;

class TemplateSeeder extends Seeder
{
    public function run()
    {
        Template::create([
            'code' => 'welcome_email',
            'language' => 'en',
            'title' => 'Welcome, {{name}}',
            'body' => '<p>Hi {{name}}, welcome to our app. Click <a href="{{link}}">here</a>.</p>',
            'version' => 1,
            'active' => true,
        ]);

        Template::create([
            'code' => 'welcome_push',
            'language' => 'en',
            'title' => 'Welcome, {{name}}!',
            'body' => 'Hi {{name}}, tap to continue.',
            'version' => 1,
            'active' => true,
        ]);
    }
}
