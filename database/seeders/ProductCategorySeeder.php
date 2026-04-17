<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCategory('Digital Marketing - Lead Generation', 'dm-lead-gen', [
            ['name' => 'Ad Creation',              'key' => 'ad_creation',            'field_type' => 'text',   'unit' => null,      'presets' => ['Included', 'Not Included']],
            ['name' => 'Ad Management',            'key' => 'ad_management',          'field_type' => 'text',   'unit' => null,      'presets' => ['Included', 'Not Included']],
            ['name' => 'Ad Optimization',          'key' => 'ad_optimization',        'field_type' => 'text',   'unit' => null,      'presets' => ['Weekly', 'Bi-Weekly', 'Monthly']],
            ['name' => 'Social Media Page Creation','key' => 'sm_page_creation',      'field_type' => 'text',   'unit' => null,      'presets' => ['Facebook', 'Instagram', 'Both']],
            ['name' => 'Weekly Review Calls',      'key' => 'weekly_review_calls',    'field_type' => 'text',   'unit' => null,      'presets' => ['1 Call/Week', '2 Calls/Week']],
            ['name' => 'Leads Per Month',          'key' => 'leads_per_month',        'field_type' => 'number', 'unit' => '+ Leads', 'presets' => ['30', '45', '60', '100']],
            ['name' => 'Per Day Budget',           'key' => 'per_day_budget',         'field_type' => 'number', 'unit' => '₹/day',   'presets' => ['300', '500', '750', '1000']],
            ['name' => 'Service Charge',           'key' => 'service_charge',         'field_type' => 'number', 'unit' => '₹/month', 'presets' => ['6000', '8000', '12000']],
            ['name' => 'Posters Count',            'key' => 'posters_count',          'field_type' => 'number', 'unit' => 'posters', 'presets' => ['4', '8', '12', '16']],
            ['name' => 'Video Count',              'key' => 'video_count',            'field_type' => 'number', 'unit' => 'videos',  'presets' => ['1', '2', '4']],
        ]);

        $this->seedCategory('Social Media Marketing', 'smm', [
            ['name' => 'Platforms',                'key' => 'platforms',              'field_type' => 'text',   'unit' => null,      'presets' => ['Facebook + Instagram', 'All Platforms']],
            ['name' => 'Posts Per Month',          'key' => 'posts_per_month',        'field_type' => 'number', 'unit' => 'posts',   'presets' => ['12', '20', '30']],
            ['name' => 'Stories Per Month',        'key' => 'stories_per_month',      'field_type' => 'number', 'unit' => 'stories', 'presets' => ['8', '16', '24']],
            ['name' => 'Reels Per Month',          'key' => 'reels_per_month',        'field_type' => 'number', 'unit' => 'reels',   'presets' => ['2', '4', '8']],
            ['name' => 'Content Strategy',         'key' => 'content_strategy',       'field_type' => 'text',   'unit' => null,      'presets' => ['Included', 'Premium']],
            ['name' => 'Analytics Report',         'key' => 'analytics_report',       'field_type' => 'text',   'unit' => null,      'presets' => ['Monthly', 'Weekly']],
        ]);

        $this->seedCategory('Software Development', 'software-dev', [
            ['name' => 'Technology Stack',         'key' => 'tech_stack',             'field_type' => 'text',   'unit' => null,      'presets' => ['Laravel + Vue', 'MERN', 'MEAN', 'Django']],
            ['name' => 'Number of Pages',          'key' => 'pages_count',            'field_type' => 'number', 'unit' => 'pages',   'presets' => ['5', '10', '20', '50']],
            ['name' => 'Revisions',                'key' => 'revisions',              'field_type' => 'number', 'unit' => null,      'presets' => ['2', '5', 'Unlimited']],
            ['name' => 'Delivery Time',            'key' => 'delivery_days',          'field_type' => 'number', 'unit' => 'days',    'presets' => ['30', '60', '90']],
            ['name' => 'Support Duration',         'key' => 'support_months',         'field_type' => 'number', 'unit' => 'months',  'presets' => ['1', '3', '6', '12']],
            ['name' => 'Source Code',              'key' => 'source_code',            'field_type' => 'text',   'unit' => null,      'presets' => ['Included', 'Not Included']],
        ]);

        $this->seedCategory('Mobile App Development', 'mobile-app', [
            ['name' => 'Platform',                 'key' => 'platform',               'field_type' => 'text',   'unit' => null,      'presets' => ['Android', 'iOS', 'Both']],
            ['name' => 'Technology',               'key' => 'technology',             'field_type' => 'text',   'unit' => null,      'presets' => ['Flutter', 'React Native', 'Native']],
            ['name' => 'Screens',                  'key' => 'screens_count',          'field_type' => 'number', 'unit' => 'screens', 'presets' => ['10', '20', '30', '50']],
            ['name' => 'API Integration',          'key' => 'api_integration',        'field_type' => 'text',   'unit' => null,      'presets' => ['Basic', 'Advanced', 'Custom']],
            ['name' => 'App Store Submission',     'key' => 'store_submission',       'field_type' => 'text',   'unit' => null,      'presets' => ['Included', 'Not Included']],
            ['name' => 'Push Notifications',       'key' => 'push_notifications',     'field_type' => 'text',   'unit' => null,      'presets' => ['Included', 'Not Included']],
        ]);

        $this->seedCategory('CRM', 'crm', [
            ['name' => 'Users',                    'key' => 'users_count',            'field_type' => 'number', 'unit' => 'users',   'presets' => ['5', '10', '25', 'Unlimited']],
            ['name' => 'Modules',                  'key' => 'modules',                'field_type' => 'text',   'unit' => null,      'presets' => ['Lead + Sales', 'Full Suite']],
            ['name' => 'API Access',               'key' => 'api_access',             'field_type' => 'text',   'unit' => null,      'presets' => ['Included', 'Not Included']],
            ['name' => 'White Label',              'key' => 'white_label',            'field_type' => 'text',   'unit' => null,      'presets' => ['Included', 'Not Included']],
            ['name' => 'Storage',                  'key' => 'storage',                'field_type' => 'text',   'unit' => null,      'presets' => ['5 GB', '20 GB', '100 GB']],
        ]);

        $this->seedCategory('Website Development', 'website-dev', [
            ['name' => 'Type',                     'key' => 'website_type',           'field_type' => 'text',   'unit' => null,      'presets' => ['Static', 'Dynamic', 'E-commerce']],
            ['name' => 'Pages',                    'key' => 'pages_count',            'field_type' => 'number', 'unit' => 'pages',   'presets' => ['5', '10', '20']],
            ['name' => 'Responsive Design',        'key' => 'responsive',             'field_type' => 'text',   'unit' => null,      'presets' => ['Included']],
            ['name' => 'SEO Setup',                'key' => 'seo_setup',              'field_type' => 'text',   'unit' => null,      'presets' => ['Basic', 'Advanced', 'Not Included']],
            ['name' => 'CMS',                      'key' => 'cms',                    'field_type' => 'text',   'unit' => null,      'presets' => ['WordPress', 'Custom', 'Not Included']],
            ['name' => 'Hosting',                  'key' => 'hosting',                'field_type' => 'text',   'unit' => null,      'presets' => ['1 Year Free', 'Not Included']],
        ]);

        $this->seedCategory('Digital Marketing - SEO', 'dm-seo', [
            ['name' => 'Keywords',                 'key' => 'keywords_count',         'field_type' => 'number', 'unit' => 'keywords','presets' => ['10', '20', '50']],
            ['name' => 'On-Page SEO',              'key' => 'on_page_seo',            'field_type' => 'text',   'unit' => null,      'presets' => ['Included']],
            ['name' => 'Off-Page SEO',             'key' => 'off_page_seo',           'field_type' => 'text',   'unit' => null,      'presets' => ['Included', 'Not Included']],
            ['name' => 'Backlinks Per Month',      'key' => 'backlinks_per_month',    'field_type' => 'number', 'unit' => 'links',   'presets' => ['10', '20', '50']],
            ['name' => 'Monthly Report',           'key' => 'monthly_report',         'field_type' => 'text',   'unit' => null,      'presets' => ['Included']],
            ['name' => 'Local SEO',                'key' => 'local_seo',              'field_type' => 'text',   'unit' => null,      'presets' => ['Included', 'Not Included']],
        ]);

        $this->seedCategory('Designing', 'designing', [
            ['name' => 'Logo Variants',            'key' => 'logo_variants',          'field_type' => 'number', 'unit' => null,      'presets' => ['2', '3', '5']],
            ['name' => 'Brochure Pages',           'key' => 'brochure_pages',         'field_type' => 'number', 'unit' => 'pages',   'presets' => ['4', '8', '12']],
            ['name' => 'Source Files',             'key' => 'source_files',           'field_type' => 'text',   'unit' => null,      'presets' => ['AI + PSD', 'PSD Only', 'Not Included']],
            ['name' => 'Revisions',                'key' => 'revisions',              'field_type' => 'number', 'unit' => null,      'presets' => ['2', '5', 'Unlimited']],
            ['name' => 'Formats',                  'key' => 'formats',                'field_type' => 'text',   'unit' => null,      'presets' => ['PNG + JPG', 'PNG + JPG + PDF + AI']],
        ]);
    }

    // ── Helper ────────────────────────────────────────────────────────
    private function seedCategory(string $name, string $slug, array $attributeDefs): void
    {
        $category = ProductCategory::firstOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'is_active' => true]
        );

        foreach ($attributeDefs as $order => $def) {
            $attribute = Attribute::firstOrCreate(
                ['product_category_id' => $category->id, 'key' => $def['key']],
                [
                    'name'       => $def['name'],
                    'field_type' => $def['field_type'],
                    'unit'       => $def['unit'],
                    'sort_order' => $order,
                    'is_active'  => true,
                    'is_required'=> false,
                ]
            );

            foreach ($def['presets'] ?? [] as $i => $presetVal) {
                AttributeValue::firstOrCreate(
                    ['attribute_id' => $attribute->id, 'value' => $presetVal],
                    ['sort_order' => $i]
                );
            }
        }

        $this->command->info("  ✓ Seeded category: {$name}");
    }
}
